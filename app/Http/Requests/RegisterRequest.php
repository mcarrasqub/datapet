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
            'name'                  => ['required', 'string', 'max:255'],
            'lastname'              => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'                 => ['required', 'string', 'max:20'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'address'               => ['nullable', 'string', 'max:255'],
            'emergency_contact'     => ['nullable', 'string', 'max:255'],
            'emergency_phone'       => ['nullable', 'string', 'max:20'],

            // Pet fields
            'pet_name'   => ['required', 'string', 'max:255'],
            'species'    => ['required', 'string'],
            'breed'      => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender'     => ['required', 'in:male,female,unknown'],
            'weight'     => ['nullable', 'numeric', 'min:0'],
            'photo'      => ['nullable', 'image', 'max:2048'],
            'notes'      => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre es obligatorio.',
            'lastname.required' => 'El apellido es obligatorio.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'phone.required'    => 'El teléfono es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'Las contraseñas no coinciden.',
            'pet_name.required' => 'El nombre de la mascota es obligatorio.',
            'species.required'  => 'La especie es obligatoria.',
            'gender.required'   => 'El género de la mascota es obligatorio.',
            'photo.image'       => 'El archivo debe ser una imagen.',
            'photo.max'         => 'La imagen no debe superar los 2MB.',
        ];
    }
}
