<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentTarget;
use App\Models\AgentCollection;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class AgentDashboardController extends Controller
{
    // Fetch Committee Members
    public function committeeMembers()
    {
        // OPTIMIZATION: Query pending installments directly and group by user
        // This is ~10x faster than querying Users -> whereHas(installments)
        $unpaidInstallments = \App\Models\Installment::with(['user', 'committee'])
            ->where('status', 'pending')
            ->get();

        $unpaidMembers = $unpaidInstallments->groupBy('user_id')->map(function ($installments) {
            $user = $installments->first()->user;
            if ($user) {
                $user = clone $user;
                $user->setRelation('installments', $installments->values());
            }
            return $user;
        })->filter()->values();

        // For paid members, skip role check and limit results to prevent huge payloads
        $paidMembers = \App\Models\User::whereHas('installments', function ($query) {
                $query->where('status', 'paid');
            })
            ->whereDoesntHave('installments', function ($query) {
                $query->where('status', 'pending');
            })
            ->with(['installments' => function($query) {
                $query->where('status', 'paid')->orderBy('due_date', 'desc')->take(5)->with('committee');
            }])
            ->limit(50)
            ->get();

        return ApiResponse::success([
            'unpaid' => $unpaidMembers,
            'paid' => $paidMembers
        ], 'Committee Members');
    }

    // Fetch Loan Members
    public function loanMembers()
    {
        // OPTIMIZATION: Query pending loan installments directly and group by loan/user
        $unpaidLoanInstallments = \App\Models\LoanInstallment::with(['loan.user'])
            ->where('status', 'pending')
            ->get();

        $unpaidMembers = $unpaidLoanInstallments->groupBy(function ($inst) {
            return $inst->loan ? $inst->loan->user_id : null;
        })->map(function ($installments, $userId) {
            if (!$userId) return null;
            $user = $installments->first()->loan->user;
            if (!$user) return null;
            $user = clone $user;

            $loans = $installments->groupBy('loan_id')->map(function ($insts) {
                $loan = clone $insts->first()->loan;
                $loan->setRelation('installments', $insts->values());
                return $loan;
            })->values();

            $user->setRelation('loans', $loans);
            return $user;
        })->filter()->values();

        $paidMembers = \App\Models\User::whereHas('loans.installments', function ($query) {
                $query->where('status', 'paid');
            })
            ->whereDoesntHave('loans.installments', function ($query) {
                $query->where('status', 'pending');
            })
            ->with(['loans.installments' => function($query) {
                $query->where('status', 'paid')->orderBy('due_date', 'desc')->take(5);
            }])
            ->limit(50)
            ->get();

        return ApiResponse::success([
            'unpaid' => $unpaidMembers,
            'paid' => $paidMembers
        ], 'Loan Members');
    }

    // Submit Cash Collection
    public function submitCollection(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
            'collection_type' => 'required|in:committee,loan',
            'installment_id' => 'required_if:collection_type,committee|exists:installments,id',
            'loan_installment_id' => 'required_if:collection_type,loan|exists:loan_installments,id',
            'amount_collected' => 'required|numeric|min:1',
            'details' => 'nullable|string',
        ]);

        $collection = AgentCollection::create([
            'agent_id' => auth()->id(),
            'member_id' => $request->member_id,
            'collection_type' => $request->collection_type,
            'installment_id' => $request->installment_id,
            'loan_installment_id' => $request->loan_installment_id,
            'amount_collected' => $request->amount_collected,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return ApiResponse::success($collection, 'Cash collection submitted and waiting for admin approval.');
    }

    // View My Targets
    public function myTargets()
    {
        $targets = AgentTarget::where('agent_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return ApiResponse::success($targets, 'Agent Targets');
    }

    // View My Collections
    public function myCollections()
    {
        $collections = AgentCollection::where('agent_id', auth()->id())
            ->with('member:id,name,phone')
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success([
            'collections' => $collections
        ], 'Agent Collections');
    }

    // 🕵️‍♂️ Agent Profile
    public function profile(Request $request)
    {
        $user = $request->user();
        
        $activeTargetsCount = AgentTarget::where('agent_id', $user->id)
            ->where('status', 'pending')
            ->count();
            
        $collectionsCount = AgentCollection::where('agent_id', $user->id)->count();
        
        return ApiResponse::success([
            'agent' => $user,
            'agent_code' => 'AGENT_' . strtoupper(substr($user->name, 0, 4)) . '_' . $user->id, // Generating a mock agent code based on name and ID
            'stats' => [
                'active_targets' => $activeTargetsCount,
                'total_collections' => $collectionsCount
            ]
        ], 'Agent profile retrieved successfully.');
    }

    // 🔍 Search Member
    public function searchMember(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return ApiResponse::success([], 'Please provide a search query.');
        }

        $members = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'member');
            })
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
                
                if (is_numeric($query)) {
                    $q->orWhere('id', $query);
                }
            })
            ->select('id', 'name', 'phone', 'photo')
            ->limit(10)
            ->get();

        return ApiResponse::success($members, 'Members retrieved successfully.');
    }
}
