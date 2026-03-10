<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'birth_date',
        'gender',
        'color',
        'weight',
        'photo',
        'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}