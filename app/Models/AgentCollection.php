<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentCollection extends Model
{
    protected $fillable = [
        'agent_id',
        'member_id',
        'collection_type',
        'amount_collected',
        'details',
        'status',
        'installment_id',
        'loan_installment_id',
        'collected_at',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class, 'installment_id');
    }

    public function loanInstallment()
    {
        return $this->belongsTo(LoanInstallment::class, 'loan_installment_id');
    }
}
