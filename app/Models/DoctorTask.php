<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Accessor para corregir caracteres UTF-8 dañados en el título
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Detectar y reparar UTF-8 corrupto (resultado de codificación incorrecta)
                // Si contiene caracteres como ├ ¡ etc, intentar recuperar
                if (preg_match('/[├¡²³¸º]/', $value)) {
                    // Intentar convertir de UTF-8 a Latin1 y viceversa
                    $recovered = @iconv('CP1252', 'UTF-8//TRANSLIT', iconv('UTF-8', 'CP1252//TRANSLIT', $value));
                    if ($recovered && $recovered !== false) {
                        return $recovered;
                    }
                }

                return $value;
            }
        );
    }

    /**
     * Determina si una tarea está vencida.
     * Una tarea está vencida si:
     * - Tiene una fecha límite
     * - La fecha límite es anterior a hoy
     * - El estado NO es 'completed'
     * - El estado NO es 'overdue' (ya está marcada explícitamente)
     */
    public function getIsOverdueAttribute(): bool
    {
        $isOverdue = false;

        // Sin fecha límite o completada, no puede estar vencida.
        if ($this->due_date && $this->status !== 'completed') {
            // Si ya está marcada como vencida explícitamente, es vencida.
            if ($this->status === 'overdue') {
                $isOverdue = true;
            } else {
                // Si la fecha límite pasó, está vencida.
                $today = Carbon::today();
                $dueDate = Carbon::parse($this->due_date);
                $isOverdue = $dueDate->isBefore($today);
            }
        }

        return $isOverdue;
    }
}
