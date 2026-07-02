<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $fillable = [
        'loan_id',
        'due_date',
        'total_amount',
        'principal_component',
        'interest_component',
        'status',
        'paid_date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
