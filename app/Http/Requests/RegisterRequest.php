<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'contacto_emergencia' => ['nullable', 'string', 'max:255'],
            'tel_emergencia' => ['nullable', 'string', 'max:20'],
            'pet_name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,unknown'],
            'breed' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'notes' => ['nullable', 'string']
        ];
    }
}