<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdoptionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // detectar si es creación de mascota o solicitud de adopción
        if ($this->routeIs('admin.adoptions.store')) {
            return [
                'name' => 'required|string|max:255',
                'species' => 'required|string|max:255',
                'age' => 'nullable|integer|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:999.99',
                'adoption_description' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'available_for_adoption' => 'nullable|boolean',
            ];
        }

        // solicitud de adopción (cliente)
        return [
            'pet_id' => 'required|exists:pets,id',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'experience' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        if ($this->routeIs('admin.adoptions.store')) {
            return [
                'name.required' => 'El nombre es obligatorio',
                'species.required' => 'La especie es obligatoria',
                'photo.image' => 'El archivo debe ser una imagen válida',
            ];
        }

        return [
            'pet_id.required' => 'La mascota es requerida',
            'pet_id.exists' => 'La mascota no existe',
            'full_name.required' => 'El nombre completo es requerido',
            'phone.required' => 'El teléfono es requerido',
        ];
    }
}
