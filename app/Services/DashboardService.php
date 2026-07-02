<?php

namespace App\Services;

use App\Models\User;
use App\Models\Committee;
use App\Models\Installment;
use App\Models\Lottery;
use Carbon\Carbon;

class DashboardService
{
    // 📊 Main Dashboard Stats
    public function getStats()
    {
        return [
            'total_members' => User::count(),
            'paid_members_count' => User::role('member')->whereHas('installments')->whereDoesntHave('installments', function ($query) {
                $query->where('status', 'pending')->where('due_date', '<=', Carbon::today());
            })->count(),
            'today_collection' => (int) Installment::whereDate('created_at', Carbon::today())->sum('amount'),
            'total_outstanding' => (int) Installment::where('status', 'pending')->sum('amount'),
            'total_due_amount' => (int) Installment::where('status', 'pending')->where('due_date', '<=', Carbon::today())->sum('amount'),
            'total_profit' => 120000, 
            'cash_balance' => 40000, 
            'bank_balance' => 80000, 
        ];
    }

    // 📈 Monthly Profit
    public function monthlyProfit()
    {
        return Installment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->get();
    }

    // 📅 Daily Collection
    public function dailyCollection()
    {
        return Installment::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
    }

    // 🎯 Lottery Summary
    public function lotteryStats()
    {
        return [
            'total_draws' => Lottery::count(),
            'today_draws' => Lottery::whereDate('created_at', Carbon::today())->count(),
        ];
    }
}