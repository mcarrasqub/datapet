<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * ATTRIBUTES APPOINTMENT
     * this->attributes['id'] - int
     * this->attributes['doctor_id'] - int
     * this->attributes['pet_id'] - int
     * this->attributes['date'] - string (date)
     * this->attributes['start_time'] - string (time)
     * this->attributes['end_time'] - string (time)
     * this->attributes['status'] - string ('scheduled', 'canceled')
     * this->attributes['reason'] - string|null
     * this->attributes['created_at'] - datetime
     * this->attributes['updated_at'] - datetime
     */

    protected $fillable = [
        'doctor_id',
        'pet_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'reason',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    // Getters
    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getDoctorId(): int
    {
        return $this->attributes['doctor_id'];
    }

    public function getPetId(): int
    {
        return $this->attributes['pet_id'];
    }

    public function getDate(): string
    {
        return $this->attributes['date'];
    }

    public function getStartTime(): string
    {
        return $this->attributes['start_time'];
    }

    public function getEndTime(): string
    {
        return $this->attributes['end_time'];
    }

    public function getStatus(): string
    {
        return $this->attributes['status'];
    }

    public function getReason(): ?string
    {
        return $this->attributes['reason'] ?? null;
    }

    public function getCreatedAt(): \Illuminate\Support\Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \Illuminate\Support\Carbon
    {
        return $this->updated_at;
    }

    // Setters
    public function setDoctorId(int $doctorId): void
    {
        $this->attributes['doctor_id'] = $doctorId;
    }

    public function setPetId(int $petId): void
    {
        $this->attributes['pet_id'] = $petId;
    }

    public function setDate(string $date): void
    {
        $this->attributes['date'] = $date;
    }

    public function setStartTime(string $startTime): void
    {
        $this->attributes['start_time'] = $startTime;
    }

    public function setEndTime(string $endTime): void
    {
        $this->attributes['end_time'] = $endTime;
    }

    public function setStatus(string $status): void
    {
        $this->attributes['status'] = $status;
    }

    public function setReason(?string $reason): void
    {
        $this->attributes['reason'] = $reason;
    }
}
