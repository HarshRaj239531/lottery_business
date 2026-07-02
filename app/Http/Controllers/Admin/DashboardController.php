<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    // 📊 Main Dashboard API
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getStats()
        ]);
    }

    // 📈 Monthly Profit API
    public function monthlyProfit()
    {
        return response()->json(
            $this->dashboardService->monthlyProfit()
        );
    }

    // 📅 Daily Collection API
    public function dailyCollection()
    {
        return response()->json(
            $this->dashboardService->dailyCollection()
        );
    }

    public function paidMembersList()
    {
        $paidMembers = \App\Models\User::role('member')
            ->whereHas('installments')
            ->whereDoesntHave('installments', function ($query) {
                $query->where('status', 'pending')->where('due_date', '<=', \Carbon\Carbon::today());
            })
            ->withSum(['installments as total_paid' => function($query) {
                $query->where('status', 'paid');
            }], 'amount')
            ->withSum(['installments as total_due' => function($query) {
                $query->where('status', 'pending');
            }], 'amount')
            ->get();

        return \App\Helpers\ApiResponse::success($paidMembers);
    }

    public function dueMembersList()
    {
        $dueMembers = \App\Models\User::role('member')
            ->whereHas('installments', function ($query) {
                $query->where('status', 'pending')->where('due_date', '<=', \Carbon\Carbon::today());
            })
            ->withSum(['installments as overdue_amount' => function($query) {
                $query->where('status', 'pending')->where('due_date', '<=', \Carbon\Carbon::today());
            }], 'amount')
            ->withSum(['installments as total_paid' => function($query) {
                $query->where('status', 'paid');
            }], 'amount')
            ->get();

        return \App\Helpers\ApiResponse::success($dueMembers);
    }
}