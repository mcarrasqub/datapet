<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalExam extends Model
{
    protected $fillable = [
        'pet_id',
        'medical_record_id',
        'uploaded_by',
        'reviewed_by_doctor_id',
        'title',
        'description',
        'category',
        'exam_date',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_at',
        'reviewed_by_doctor_at',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'uploaded_at' => 'datetime',
        'reviewed_by_doctor_at' => 'datetime',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_doctor_id');
    }
}
