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
            'registration_type'     => ['required', 'in:new_client,existing_client'],
            'user_id'               => ['required_if:registration_type,existing_client'],
            'name'                  => ['required_if:registration_type,new_client', 'string', 'max:255'],
            'lastname'              => ['required_if:registration_type,new_client', 'string', 'max:255'],
            'email'                 => ['required_if:registration_type,new_client', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'                 => ['required_if:registration_type,new_client', 'string', 'max:20'],
            'password'              => ['required_if:registration_type,new_client', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required_if:registration_type,new_client'],
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
            'user_id.required_if' => 'Debe seleccionar un cliente existente.',
            'name.required_if'     => 'El nombre es obligatorio para nuevos clientes.',
            'lastname.required_if' => 'El apellido es obligatorio para nuevos clientes.',
            'email.required_if'    => 'El correo electrónico es obligatorio para nuevos clientes.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'phone.required_if'    => 'El teléfono es obligatorio para nuevos clientes.',
            'password.required_if' => 'La contraseña es obligatoria para nuevos clientes.',
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
