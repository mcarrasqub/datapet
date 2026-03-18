<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use App\Http\Requests\PetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
  public function index(): View
  {
    $viewData = [];
    $viewData['pets'] = Pet::with('owner')->get();
    return view('pets.index')->with('viewData', $viewData);
  }

  public function create(): View
  {
    $viewData = [];
    $viewData['clients'] = User::all();
    return view('pets.create')->with('viewData', $viewData);
  }

  public function store(PetRequest $request): RedirectResponse
  {
    $data = $request->validated();

    if ($request->hasFile('photo')) {
      $data['photo'] = $request->file('photo')->store('pets', 'public');
    }

    Pet::create($data);

    return redirect()->route('home.index')->with('success', 'Mascota registrada exitosamente');
  }

  public function show(Pet $pet): View
  {
    $viewData = [];
    $viewData['pet'] = $pet;
    return view('pets.show')->with('viewData', $viewData);
  }

  public function edit(Pet $pet): View
  {
    $viewData = [];
    $viewData['pet'] = $pet;
    $viewData['clients'] = User::all();
    return view('pets.edit')->with('viewData', $viewData);
  }

  public function update(PetRequest $request, Pet $pet): RedirectResponse
  {
    $data = $request->validated();

    if ($request->hasFile('photo')) {
      if ($pet->getPhoto()) {
        Storage::disk('public')->delete($pet->getPhoto());
      }
      $data['photo'] = $request->file('photo')->store('pets', 'public');
    }

    $pet->update($data);

    return redirect()->route('home.index')->with('success', 'Mascota actualizada');
  }

  public function destroy(Pet $pet): RedirectResponse
  {
    if ($pet->getPhoto()) {
      Storage::disk('public')->delete($pet->getPhoto());
    }
    
    $pet->delete();
    
    return redirect()->route('home.index')->with('success', 'Mascota eliminada');
  }
}