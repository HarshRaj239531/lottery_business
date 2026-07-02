<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Committee;
use Carbon\Carbon;

class ReportController extends Controller
{
    // 📊 Total Collection Report
    public function collectionReport()
    {
        $total = Installment::sum('amount');

        return response()->json([
            'status' => true,
            'total_collection' => $total
        ]);
    }

    // 📅 Monthly Collection Report
    public function monthlyReport()
    {
        $data = Installment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->get();

        return response()->json($data);
    }

    // 📈 Committee Report
    public function committeeReport()
    {
        $data = Committee::withCount('installments')->get();

        return response()->json($data);
    }

    // 📆 Date Range Report
    public function dateRangeReport($start, $end)
    {
        $data = Installment::whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return response()->json([
            'total' => $data
        ]);
    }
}