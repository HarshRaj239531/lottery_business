<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Installment;
use App\Models\LoanInstallment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // 📊 Agent Dashboard Stats (Figma Synced)
    public function dashboard(Request $request)
    {
        $agentId = $request->user()->id;
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's Collection
        $todayCollection = App\Models\AgentCollection::where('agent_id', $agentId)
            ->whereDate('collected_at', $today)
            ->sum('amount_collected');

        // This Month's Collection
        $thisMonthCollection = App\Models\AgentCollection::where('agent_id', $agentId)
            ->where('collected_at', '>=', $thisMonth)
            ->sum('amount_collected');

        // Total Collection (Lifetime)
        $totalCollection = App\Models\AgentCollection::where('agent_id', $agentId)
            ->sum('amount_collected');

        // Recent Activity (Mapped for UI)
        $recentActivity = App\Models\AgentCollection::with(['member'])
            ->where('agent_id', $agentId)
            ->orderBy('collected_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'member_name' => $item->member->name ?? 'Unknown',
                    'amount' => $item->amount_collected,
                    'date' => $item->collected_at,
                    'status' => ucfirst($item->status), // Pending, Approved, Rejected
                    'type' => $item->collection_type === 'committee' ? 'Committee Collection' : 'Loan EMI Collection'
                ];
            });

        return ApiResponse::success([
            'today_collection' => $todayCollection,
            'this_month_collection' => $thisMonthCollection,
            'total_collection' => $totalCollection,
            'recent_activity' => $recentActivity
        ], 'Agent dashboard data fetched');
    }
}
