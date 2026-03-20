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
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
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