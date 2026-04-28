<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'doctor', 'client'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'medical_record_id' => ['nullable', 'integer', 'exists:medical_records,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'category' => ['nullable', 'string', 'max:100'],
            'exam_date' => ['nullable', 'date'],
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Debes seleccionar al menos un archivo.',
            'files.array' => 'El campo de archivos no es válido.',
            'files.min' => 'Debes seleccionar al menos un archivo.',
            'files.max' => 'Solo puedes cargar hasta 10 archivos por envío.',
            'files.*.mimes' => 'Formato no permitido. Solo se aceptan PDF, JPG, JPEG y PNG.',
            'files.*.max' => 'Cada archivo debe pesar máximo 5 MB.',
            'medical_record_id.exists' => 'La consulta seleccionada no existe.',
        ];
    }
}
