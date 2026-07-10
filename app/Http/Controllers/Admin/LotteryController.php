<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use App\Services\LotteryService;

class LotteryController extends Controller
{
    protected $service;

    public function __construct(LotteryService $service)
    {
        $this->service = $service;
    }

    // 🎯 Draw Winner
    public function draw($committee_id)
    {
        $result = $this->service->draw($committee_id);

        return response()->json([
            'status' => true,
            'message' => 'Lottery Draw Completed',
            'data' => $result
        ]);
    }

    // 📋 All Draws
    public function index()
    {
        return response()->json(Lottery::with(['committee', 'winner'])->paginate(15));
    }

    // 👁️ Show Draw
    public function show($id)
    {
        return response()->json(Lottery::with(['committee', 'winner'])->findOrFail($id));
    }

    // ❌ Delete Draw
    public function destroy($id)
    {
        Lottery::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Draw Deleted'
        ]);
    }

    // ⚙️ Show Settings
    public function showSettings()
    {
        $setting = \App\Models\LotterySetting::first() ?? new \App\Models\LotterySetting([
            'grand_draw_title' => 'The Wealth Multiplier',
            'grand_draw_description' => 'Join the elite circle of participants...',
            'grand_draw_date' => now()->addDays(2)->toDateTimeString()
        ]);
        
        return response()->json([
            'status' => true,
            'success' => true,
            'data' => $setting
        ]);
    }

    // ⚙️ Update Settings
    public function updateSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'grand_draw_title' => 'required|string|max:255',
            'grand_draw_description' => 'required|string',
            'grand_draw_date' => 'required|date'
        ]);

        $setting = \App\Models\LotterySetting::first() ?? new \App\Models\LotterySetting();
        $setting->grand_draw_title = $request->grand_draw_title;
        $setting->grand_draw_description = $request->grand_draw_description;
        $setting->grand_draw_date = $request->grand_draw_date;
        $setting->save();

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Lottery Settings Updated',
            'data' => $setting
        ]);
    }

    // 🎯 Manual Draw Winner
    public function manualDraw(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'committee_id' => 'required|exists:committees,id',
            'winner_id' => 'required|exists:users,id',
            'draw_date' => 'required|date'
        ]);

        $lottery = Lottery::create([
            'committee_id' => $request->committee_id,
            'winner_id' => $request->winner_id,
            'draw_date' => $request->draw_date
        ]);

        // Trigger notification
        try {
            $member = \App\Models\User::find($request->winner_id);
            $committee = \App\Models\Committee::find($request->committee_id);
            
            $notify = app(\App\Services\NotificationService::class);
            $notify->sendNotification(
                $member, 
                "Lottery Winner", 
                "Congratulations! You have been selected as the winner for the {$committee->name} draw!"
            );
        } catch (\Exception $e) {
            // ignore
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Manual Lottery Draw Completed',
            'data' => $lottery
        ]);
    }

    // 📝 Update Draw Details
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'committee_id' => 'required|exists:committees,id',
            'winner_id' => 'required|exists:users,id',
            'draw_date' => 'required|date'
        ]);

        $lottery = Lottery::findOrFail($id);
        $lottery->committee_id = $request->committee_id;
        $lottery->winner_id = $request->winner_id;
        $lottery->draw_date = $request->draw_date;
        $lottery->save();

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Lottery Draw Updated',
            'data' => $lottery
        ]);
    }
}