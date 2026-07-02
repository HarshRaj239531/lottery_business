<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lottery;
use App\Models\Committee;
use App\Models\Installment;
use Carbon\Carbon;

class LotteryService
{
    protected $notify;

    public function __construct(NotificationService $notify)
    {
        $this->notify = $notify;
    }

    public function draw($committee_id)
    {
        // Get members of committee based on those who have paid an installment for this committee
        $memberId = Installment::where('committee_id', $committee_id)
            ->select('user_id')
            ->distinct()
            ->inRandomOrder()
            ->value('user_id');

        if (!$memberId) {
            throw new \Exception("No members found in this committee to draw from.");
        }

        $member = User::find($memberId);
        $committee = Committee::find($committee_id);

        $lottery = Lottery::create([
            'committee_id' => $committee_id,
            'winner_id' => $member->id,
            'draw_date' => Carbon::today()
        ]);

        $this->notify->sendNotification(
            $member, 
            "Lottery Winner", 
            "Congratulations! You have been selected as the winner for the {$committee->name} draw!"
        );

        return $lottery;
    }
}