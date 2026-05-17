<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UserRequest extends FormRequest
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
        // Capturamos el ID del usuario si estamos en la ruta de actualización
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            // Ignora el email del usuario actual si se está editando
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'role' => ['required', 'in:admin,doctor,client'],
            // La contraseña es obligatoria solo al crear (store), al actualizar es opcional (nullable)
            'password' => $userId ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
        ];
    }
}
