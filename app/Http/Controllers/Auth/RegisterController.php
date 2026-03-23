<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pet;
use App\Http\Requests\RegisterRequest;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application registration form.
     * Overridden to pass existing clients.
     */
    public function showRegistrationForm()
    {
        $clients = User::where('role', 'client')->get();
        return view('auth.register', compact('clients'));
    }

    /**
     * Handle a registration request.
     * Overridden to prevent auto-login of the new user.
     */
    public function register(RegisterRequest $request)
    {
        if ($request->registration_type === 'existing_client') {
            $user = User::findOrFail($request->user_id);
        } else {
            $user = $this->create($request->validated());
        }

        // Create pet if pet data was provided
        if ($request->filled('pet_name')) {
            $pet = new Pet();
            $pet->name = $request->pet_name;
            $pet->species = $request->species;
            $pet->breed = $request->breed;
            $pet->birth_date = $request->birth_date;
            $pet->gender = $request->gender;
            $pet->weight = $request->weight;
            $pet->notes = $request->notes;
            $pet->user_id = $user->id;

            if ($request->hasFile('photo')) {
                $pet->photo = $request->file('photo')->store('pets', 'public');
            }

            $pet->save();
        }

        $message = ($request->registration_type === 'existing_client') 
            ? 'Mascota agregada al cliente exitosamente.' 
            : 'Cliente y mascota registrados exitosamente.';

        return redirect()->route('register')->with('success', $message);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'specialty' => $data['specialty'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => $data['status'] ?? 1,
            'role' => $data['role'] ?? 'client',
        ]);
    }
}
