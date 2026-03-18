<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pet;
use App\Http\Requests\RegisterRequest;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->createUser($request->validated());
        
        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    protected function createUser(array $data): User
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
            'weight' => $data['weight'] ?? null,
            'photo' => $photoPath,
            'notes' => $data['notes'] ?? null,
        ]);

        return $user;
    }
}