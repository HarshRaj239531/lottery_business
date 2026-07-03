<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status' // open, in_progress, resolved
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
