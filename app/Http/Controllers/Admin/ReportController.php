<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Committee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\ApiResponse;

class ReportController extends Controller
{
    // 📊 Total Collection Report
    public function collectionReport()
    {
        try {
            $total = Installment::where('status', 'paid')->sum('amount');

            return ApiResponse::success([
                'total_collection' => $total
            ], 'Total collection fetched');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // 📅 Monthly Collection Report (YEAR + MONTH FIXED ✅)
    public function monthlyReport()
    {
        try {
            $data = Installment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
                ->where('status', 'paid')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            return ApiResponse::success($data, 'Monthly report');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // 📈 Committee Report
    public function committeeReport()
    {
        try {
            $data = Committee::withCount('installments')
                ->withSum('installments', 'amount')
                ->get();

            return ApiResponse::success($data, 'Committee report');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // 📆 Date Range Report (VALIDATED + SAFE)
    public function dateRangeReport(Request $request)
    {
        try {
            $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start'
            ]);

            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();

            $total = Installment::whereBetween('created_at', [$start, $end])
                ->where('status', 'paid')
                ->sum('amount');

            return ApiResponse::success([
                'start_date' => $start,
                'end_date' => $end,
                'total' => $total
            ], 'Date range report');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}