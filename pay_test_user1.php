<?php

$installments = App\Models\Installment::where('user_id', 1)->take(5)->get();
foreach($installments as $inst) {
    $inst->status = 'paid';
    $inst->paid_date = now();
    $inst->save();
    echo "Paid: " . $inst->id . " for user " . $inst->user_id . PHP_EOL;
}
