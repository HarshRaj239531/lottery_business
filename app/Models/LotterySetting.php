<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotterySetting extends Model
{
    protected $fillable = [
        'grand_draw_title',
        'grand_draw_description',
        'grand_draw_date'
    ];
}
