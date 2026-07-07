<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'total_members',
        'duration',
        'payment_frequency',
        'start_date',
        'end_date',
        'status',
        'return_percentage',
        'trending'
    ];

    protected $casts = [
        'trending' => 'boolean',
    ];

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'committee_user')
            ->withPivot('joined_at', 'status')
            ->withTimestamps();
    }

    public function lotteries()
    {
        return $this->hasMany(Lottery::class);
    }
}