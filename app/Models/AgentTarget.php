<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentTarget extends Model
{
    protected $fillable = [
        'agent_id',
        'admin_id',
        'target_type',
        'target_value',
        'start_date',
        'end_date',
        'status',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
