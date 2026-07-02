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
        return response()->json(Lottery::with(['committee', 'winner'])->get());
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
}