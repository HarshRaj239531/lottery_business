<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Lottery;

class LotteryController extends Controller
{
    // 🏆 Winners Circle (Recent Winners)
    public function winners(Request $request)
    {
        $winners = Lottery::with(['winner', 'committee'])
            ->orderBy('draw_date', 'desc')
            ->take(10)
            ->get();

        return ApiResponse::success($winners, 'Recent winners fetched');
    }

    // 📜 Past Results History (Paginated)
    public function history(Request $request)
    {
        $history = Lottery::with(['winner', 'committee'])
            ->orderBy('draw_date', 'desc')
            ->paginate(15);

        return ApiResponse::success($history, 'Lottery history fetched');
    }
}
