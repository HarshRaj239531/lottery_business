<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'price', 'unit', 'image_url', 'status'];

    public function stocks()
    {
        return $this->hasMany(MaterialStock::class);
    }
}
