<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KardexEntry extends Model
{
    use HasFactory;

    protected $table = 'kardex_entries';

    protected $fillable = [
        'pet_id',
        'doctor_id',
        'entry_date',
        'animal_type',
        'parameters',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'parameters' => 'array',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
