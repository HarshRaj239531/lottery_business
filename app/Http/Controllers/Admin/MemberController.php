<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\MemberRequest;
use App\Models\Committee;
use App\Models\Installment;
use App\Helpers\ApiResponse;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // 📋 Get All Members and Agents
    public function index(Request $request)
    {
        $query = User::role(['member', 'agent'])->with(['roles', 'loans'])->withCount(['committees', 'loans']);
        
        if ($request->ajax() || $request->has('draw')) {
            return DataTables::of($query)->make(true);
        }

        // Fallback for API clients that just want the raw list
        return response()->json($query->paginate(50));
    }

    // ➕ Create Member
    public function store(MemberRequest $request)
    {
        $member = User::create($request->validated());
        
        $role = $request->input('role', 'member');
        if ($role === 'agent') {
            $member->assignRole('agent');
        } else {
            $member->assignRole('member');
        }

        return response()->json([
            'status' => true,
            'message' => $role === 'agent' ? 'Agent Registered' : 'Member Created',
            'data' => $member
        ]);
    }

    // 👁️ Show Member
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    // ✏️ Update Member
    public function update(MemberRequest $request, $id)
    {
        $member = User::findOrFail($id);
        $member->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Member Updated'
        ]);
    }

    // ❌ Delete Member
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Member Deleted'
        ]);
    }

    // 🎓 Enroll Member in Committee
    public function enroll(\Illuminate\Http\Request $request, $id, \App\Services\NotificationService $notify)
    {
        $request->validate([
            'committee_id' => 'required|exists:committees,id'
        ]);

        $user = User::findOrFail($id);
        $committee = \App\Models\Committee::findOrFail($request->committee_id);

        if (!$user->hasRole('member')) {
            return response()->json(['status' => false, 'message' => 'Only members can be enrolled'], 400);
        }

        if ($committee->status !== 'active') {
            return response()->json(['status' => false, 'message' => 'Committee is not active.'], 400);
        }

        if ($committee->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['status' => false, 'message' => 'User is already enrolled in this committee.'], 400);
        }

        $committee->members()->syncWithoutDetaching([$user->id]);

        $startDate = now();
        $endDate = now()->addMonths($committee->duration);
        
        $i = 0;
        while (true) {
            $dueDate = $startDate->copy();
            if ($committee->payment_frequency === 'daily') $dueDate->addDays($i);
            elseif ($committee->payment_frequency === 'weekly') $dueDate->addWeeks($i);
            else $dueDate->addMonths($i);

            if ($dueDate->gte($endDate)) {
                break;
            }

            \App\Models\Installment::firstOrCreate([
                'user_id' => $user->id,
                'committee_id' => $committee->id,
                'due_date' => $dueDate->format('Y-m-d'),
            ], [
                'amount' => $committee->amount,
                'status' => 'pending',
            ]);
            $i++;
        }

        $notify->sendNotification($user, "Committee Joined", "You have been enrolled in the {$committee->name} committee by an Admin.");

        return response()->json([
            'status' => true,
            'message' => 'Member enrolled successfully'
        ]);
    }

    // 👤 Impersonate Member
    public function impersonate($id)
    {
        $user = User::findOrFail($id);
        
        // Generate a token for the user to login as them
        $token = $user->createToken('admin_impersonation')->plainTextToken;
        
        return ApiResponse::success([
            'token' => $token,
            'user' => $user,
            'redirect_url' => '/member/dashboard' // frontend will use this to navigate
        ], 'Impersonation successful.');
    }

    // 🔑 Change Member Password
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($id);
        
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.'
        ]);
    }
}