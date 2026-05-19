<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'adoption_description' => 'nullable|string|max:1000',
            'available_for_adoption' => 'nullable|boolean',
            'photo' => 'nullable|image|max:2048',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'reproductive_status' => 'nullable|string|max:255',
            'is_deceased' => 'nullable|boolean',
            'emotional_support' => 'nullable|boolean',
            'service_animal' => 'nullable|boolean',
            'diet' => 'nullable|string|max:1000',
            'diet_quantity' => 'nullable|string|max:255',
            'diet_frequency' => 'nullable|string|max:255',
            'housing' => 'nullable|string|max:1000',
            'bath_frequency' => 'nullable|string|max:255',
            'bath_products' => 'nullable|string|max:255',
            'other_pets' => 'nullable|string|max:255',
            'last_heat' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'species.required' => 'La especie es obligatoria',
            'photo.image' => 'El archivo debe ser una imagen',
        ];
    }
}
