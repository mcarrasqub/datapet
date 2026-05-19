<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationReminder extends Model
{
    protected $fillable = [
        'vaccination_id',
        'medical_order_id',
        'pet_id',
        'user_id',
        'phone',
        'message',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function vaccination(): BelongsTo
    {
        return $this->belongsTo(Vaccination::class, 'vaccination_id');
    }

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class, 'medical_order_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
