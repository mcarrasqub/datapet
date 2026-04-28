<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaccinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vaccine_type' => 'required|string|max:255',
            'vaccinated_at' => 'required|date|before_or_equal:today',
            'next_due_date' => 'nullable|date|after_or_equal:vaccinated_at',
            'notes' => 'nullable|string',
        ];
    }
}
