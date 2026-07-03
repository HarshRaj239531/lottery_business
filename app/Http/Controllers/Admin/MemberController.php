<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Committee;
use App\Models\Installment;
use App\Http\Requests\MemberRequest;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 📋 LIST MEMBERS
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = User::role(['member', 'agent'])
            ->with(['roles', 'loans'])
            ->withCount(['committees', 'loans']);

        if ($request->ajax() || $request->has('draw')) {
            return DataTables::of($query)->make(true);
        }

        return ApiResponse::success(
            $query->paginate(50),
            'Members fetched successfully'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ➕ CREATE MEMBER / AGENT
    |--------------------------------------------------------------------------
    */
    public function store(MemberRequest $request)
    {
        $this->authorize('create', User::class);

        $member = User::create($request->validated());

        $role = $request->input('role', 'member');
        $member->assignRole($role === 'agent' ? 'agent' : 'member');

        return ApiResponse::success(
            $member,
            'User created successfully'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 👁️ SHOW MEMBER
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $this->authorize('view', User::class);

        $user = User::findOrFail($id);

        return ApiResponse::success(
            $user,
            'User details fetched'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ✏️ UPDATE MEMBER
    |--------------------------------------------------------------------------
    */
    public function update(MemberRequest $request, $id)
    {
        $this->authorize('update', User::class);

        $member = User::findOrFail($id);
        $member->update($request->validated());

        return ApiResponse::success(
            null,
            'Member updated successfully'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ❌ DELETE MEMBER
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $this->authorize('delete', User::class);

        $member = User::findOrFail($id);
        $member->delete();

        return ApiResponse::success(
            null,
            'Member deleted successfully'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 🎓 ENROLL MEMBER IN COMMITTEE
    |--------------------------------------------------------------------------
    */
    public function enroll(Request $request, $id, \App\Services\NotificationService $notify)
    {
        $this->authorize('update', User::class);

        $request->validate([
            'committee_id' => 'required|exists:committees,id'
        ]);

        return DB::transaction(function () use ($request, $id, $notify) {

            $user = User::findOrFail($id);
            $committee = Committee::findOrFail($request->committee_id);

            // ❌ Only member allowed
            if (!$user->hasRole('member')) {
                return ApiResponse::error('Only members can be enrolled');
            }

            // ❌ Committee must be active
            if ($committee->status !== 'active') {
                return ApiResponse::error('Committee is not active');
            }

            // ❌ Already enrolled check
            if ($committee->members()->where('user_id', $user->id)->exists()) {
                return ApiResponse::error('User already enrolled');
            }

            // ✅ Attach member
            $committee->members()->syncWithoutDetaching([$user->id]);

            // 📅 Generate Installments
            $startDate = now();
            $endDate   = now()->addMonths($committee->duration);

            for ($i = 0; $i < 1000; $i++) {
                $dueDate = $startDate->copy();

                if ($committee->payment_frequency === 'daily') {
                    $dueDate->addDays($i);
                } elseif ($committee->payment_frequency === 'weekly') {
                    $dueDate->addWeeks($i);
                } else {
                    $dueDate->addMonths($i);
                }

                if ($dueDate->gte($endDate)) {
                    break;
                }

                Installment::firstOrCreate(
                    [
                        'user_id'       => $user->id,
                        'committee_id'  => $committee->id,
                        'due_date'      => $dueDate->format('Y-m-d'),
                    ],
                    [
                        'amount' => $committee->amount,
                        'status' => 'pending',
                    ]
                );
            }

            // 🔔 Send Notification
            $notify->sendNotification(
                $user,
                "Committee Joined",
                "You have been enrolled in {$committee->name}"
            );

            return ApiResponse::success(
                null,
                'Member enrolled successfully'
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 👤 IMPERSONATE USER (SECURE)
    |--------------------------------------------------------------------------
    */
    public function impersonate($id)
    {
        $this->authorize('loginAsUser', User::class);

        $user = User::findOrFail($id);

        // 🔒 Remove old tokens
        $user->tokens()->delete();

        // 🔑 Create short-lived token (30 min)
        $token = $user->createToken(
            'impersonation',
            ['*'],
            now()->addMinutes(30)
        )->plainTextToken;

        return ApiResponse::success([
            'token'        => $token,
            'user'         => $user,
            'redirect_url' => '/member/dashboard'
        ], 'Impersonation successful (30 min)');
    }


    /*
    |--------------------------------------------------------------------------
    | 🔑 CHANGE PASSWORD
    |--------------------------------------------------------------------------
    */
    public function changePassword(Request $request, $id)
    {
        $this->authorize('update', User::class);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return ApiResponse::success(
            null,
            'Password changed successfully'
        );
    }
}
