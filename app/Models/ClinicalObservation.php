<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalObservation extends Model
{
    /**
     * ATTRIBUTES CLINICAL OBSERVATION
     * this->attributes['id'] - int - contains the unique identifier for the observation.
     * this->attributes['medical_record_id'] - int - contains the ID of the medical record associated.
     * this->attributes['doctor_id'] - int - contains the ID of the doctor who made the observation.
     * this->attributes['observation'] - string - contains the observation text.
     * this->attributes['created_at'] - timestamp - contains the timestamp when the observation was created.
     * this->attributes['updated_at'] - timestamp - contains the timestamp when the observation was last updated.
     */
    protected $fillable = [
        'medical_record_id',
        'doctor_id',
        'observation',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Getters

    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getMedicalRecordId(): int
    {
        return $this->attributes['medical_record_id'];
    }

    public function getDoctorId(): int
    {
        return $this->attributes['doctor_id'];
    }

    public function getObservation(): string
    {
        return $this->attributes['observation'];
    }

    public function getCreatedAt(): \Illuminate\Support\Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \Illuminate\Support\Carbon
    {
        return $this->updated_at;
    }
}
