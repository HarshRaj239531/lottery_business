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

    // ⚙️ Show Settings
    public function showSettings()
    {
        $setting = \App\Models\LotterySetting::first() ?? new \App\Models\LotterySetting([
            'grand_draw_title' => 'The Wealth Multiplier',
            'grand_draw_description' => 'Join the elite circle of participants...',
            'grand_draw_date' => now()->addDays(2)->toDateTimeString()
        ]);
        
        return ApiResponse::success($setting, 'Lottery settings fetched');
    }
}
