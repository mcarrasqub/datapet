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
            'notes' => 'nullable|string'
        ];
    }
}