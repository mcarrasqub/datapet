<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:200',
            'gender' => 'required|in:male,female,unknown',
            'weight' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
}
