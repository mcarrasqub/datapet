<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalOrder extends Model
{
    protected $fillable = [
        'pet_id',
        'doctor_id',
        'order_date',
        'order_type',
        'description',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class, 'medical_order_id');
    }
}
