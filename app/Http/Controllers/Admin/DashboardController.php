<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Helpers\ApiResponse;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * MAIN DASHBOARD
     */
    public function index()
    {
        try {
            return ApiResponse::success(
                $this->dashboardService->getStats(),
                'Dashboard data fetched'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Dashboard error', 500);
        }
    }

    /**
     * MONTHLY PROFIT
     */
    public function monthlyProfit()
    {
        try {
            return ApiResponse::success(
                $this->dashboardService->monthlyProfit()
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error fetching monthly profit', 500);
        }
    }

    /**
     * DAILY COLLECTION
     */
    public function dailyCollection()
    {
        try {
            return ApiResponse::success(
                $this->dashboardService->dailyCollection()
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error fetching daily collection', 500);
        }
    }

    /**
     * PAID MEMBERS
     */
    public function paidMembersList()
    {
        $today = Carbon::today();

        $paidMembers = User::role('member')
            ->whereHas('installments')
            ->whereDoesntHave('installments', function ($q) use ($today) {
                $q->where('status', 'pending')
                  ->whereDate('due_date', '<=', $today);
            })
            ->withSum(['installments as total_paid' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->withSum(['installments as total_due' => function ($q) {
                $q->where('status', 'pending');
            }], 'amount')
            ->paginate(10);

        return ApiResponse::success($paidMembers);
    }

    /**
     * DUE MEMBERS
     */
    public function dueMembersList()
    {
        $today = Carbon::today();

        $dueMembers = User::role('member')
            ->whereHas('installments', function ($q) use ($today) {
                $q->where('status', 'pending')
                  ->whereDate('due_date', '<=', $today);
            })
            ->withSum(['installments as overdue_amount' => function ($q) use ($today) {
                $q->where('status', 'pending')
                  ->whereDate('due_date', '<=', $today);
            }], 'amount')
            ->withSum(['installments as total_paid' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->paginate(10);

        return ApiResponse::success($dueMembers);
    }
}