<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    protected $fillable = [
        'committee_id',
        'winner_id',
        'draw_date'
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}