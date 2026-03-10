<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pet;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
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
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'] . ' ' . $data['apellido'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $photoPath = null;
        if (request()->hasFile('photo')) {
            $photoPath = request()->file('photo')->store('pets', 'public');
        }

        Pet::create([
            'user_id' => $user->id,
            'name' => $data['pet_name'],
            'species' => $data['species'],
            'breed' => $data['breed'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'],
            'color' => $data['color'] ?? null,
            'weight' => $data['weight'] ?? null,
            'photo' => $photoPath,
            'notes' => $data['notes'] ?? null,
        ]);

        return $user;
    }
}