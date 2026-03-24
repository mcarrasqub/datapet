<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
        /**
        * ATTRIBUTES MEDICAL RECORD
        * this->attributes['id'] - int - contains the unique identifier for the medical record.
        * this->attributes['pet_id'] - int - contains the ID of the pet associated with the medical record.
        * this->attributes['doctor_id'] - int - contains the ID of the doctor associated with the medical record.
        * this->attributes['visited_at'] - datetime - contains the date and time when the pet visited the doctor.
        * this->attributes['reason'] - string - contains the reason for the visit.
        * this->attributes['diagnosis'] - string - contains the diagnosis made by the doctor.
        * this->attributes['treatment'] - string - contains the treatment plan.
        * this->attributes['notes'] - string - contains any additional notes.
        * this->attributes['photos'] - array - contains an array of photo URLs related to the medical record.
        */
    
    protected $fillable = [
        'pet_id',
        'doctor_id',
        'visited_at',
        'reason',
        'diagnosis',
        'treatment',
        'notes',
        'photos',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'photos' => 'array',
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