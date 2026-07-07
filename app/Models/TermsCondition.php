<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
    protected $table = 'terms_conditions';

    protected $fillable = [
        'title',
        'content'
    ];
}
