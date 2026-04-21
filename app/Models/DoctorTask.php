<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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
                if (preg_match('/├|¡|²|³|¸|º/', $value)) {
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
        // Sin fecha límite, no puede estar vencida
        if (!$this->due_date) {
            return false;
        }

        // Si está completada, no está vencida
        if ($this->status === 'completed') {
            return false;
        }

        // Si ya está marcada como vencida explícitamente, es vencida
        if ($this->status === 'overdue') {
            return true;
        }

        // Si la fecha límite pasó, está vencida
        $today = Carbon::today();
        $dueDate = Carbon::parse($this->due_date);

        return $dueDate->isBefore($today);
    }
}
