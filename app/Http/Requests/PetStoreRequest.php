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
      'notes' => 'nullable|string|max:1000',
      'available_for_adoption' => 'nullable|boolean',
      'photo' => 'nullable|image|max:2048',
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