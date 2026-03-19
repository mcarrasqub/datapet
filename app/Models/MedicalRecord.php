<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

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
        */
    
    protected $fillable = [
        'pet_id',
        'doctor_id',
        'visited_at',
        'reason',
        'diagnosis',
        'treatment',
        'notes',
    ];

    /** 
    *public function pet()
    *{
    *    return $this->belongsTo(Pet::class);
    *}
    */
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}