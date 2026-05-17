<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalFormula extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'doctor_id',
        'formula_date',
        'instructions',
        'medications',
    ];

    protected $casts = [
        'formula_date' => 'date',
        'medications' => 'array',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
