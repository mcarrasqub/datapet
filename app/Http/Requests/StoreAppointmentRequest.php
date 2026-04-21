<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => 'required|exists:users,id',
            'pet_id' => 'required|exists:pets,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $overlap = Appointment::where('doctor_id', $this->doctor_id)
                ->where('date', $this->date)
                ->where('status', 'scheduled')
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('start_time', '<', $this->end_time)
                          ->where('end_time', '>', $this->start_time);
                    });
                })
                ->exists();

            if ($overlap) {
                $validator->errors()->add('start_time', 'El doctor ya tiene una cita programada en este horario.');
            }
        });
    }
}
