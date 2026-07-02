<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'user_id',
        'committee_id',
        'amount',
        'paid_date',
        'due_date',
        'status',
        'collected_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}