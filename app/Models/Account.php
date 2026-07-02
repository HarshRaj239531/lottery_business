<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'balance'];

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}
