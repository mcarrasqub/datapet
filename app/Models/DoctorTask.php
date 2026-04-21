<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorTask extends Model
{
    protected $fillable = [
        'doctor_id',
        'title',
        'description',
        'status',
        'due_date',
        'priority',
        'is_system',
        'source_type',
        'source_id',
        'task_key',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_system' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        if ($this->status === 'completed') {
            return false;
        }

        return strtotime((string) $this->due_date) < strtotime(today()->toDateString());
    }
}
