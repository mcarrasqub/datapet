<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdoptionRequestRequest;
use App\Http\Requests\PetStoreRequest;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdoptionController extends Controller
{
  public function index(): View
  {
    $viewData = [];
    $viewData['pets'] = Pet::where('available_for_adoption', true)->get();

    return view('adoption.index')->with('viewData', $viewData);
  }

  public function show(Pet $pet): View
  {
    if (!$pet->getAvailableForAdoption()) {
      abort(404, 'Esta mascota no está disponible para adopción');
    }

    $viewData = [];
    $viewData['pet'] = $pet;

    return view('adoption.show')->with('viewData', $viewData);
  }

  public function store(AdoptionRequestRequest $request): RedirectResponse
  {
    $data = $request->validated();
    $data['user_id'] = Auth::id();
    $data['status'] = 'pending';

    AdoptionRequest::create($data);

    return redirect()->route('adoption.index')
      ->with('success', 'Solicitud enviada exitosamente');
  }

  public function adminIndex(): View
  {
    $viewData = [];
    $viewData['requests'] = AdoptionRequest::with(['pet', 'user'])
      ->orderByDesc('created_at')
      ->get();

    return view('admin.adoption.index')->with('viewData', $viewData);
  }

  public function approve(AdoptionRequest $adoptionRequest): RedirectResponse
  {
    $adoptionRequest->setStatus('approved');
    $adoptionRequest->save();

    return redirect()->back()->with('success', 'Solicitud aprobada');
  }

  public function reject(AdoptionRequest $adoptionRequest): RedirectResponse
  {
    $adoptionRequest->setStatus('rejected');
    $adoptionRequest->save();

    return redirect()->back()->with('success', 'Solicitud rechazada');
  }

  public function create(): View
  {
    return view('admin.adoption.create');
  }

  public function storePet(PetStoreRequest $request): RedirectResponse
  {
    $data = $request->validated();

    // Asignar usuario creador
    $data['user_id'] = Auth::id();

    // Manejo de imagen
    if ($request->hasFile('photo')) {
      $data['photo'] = $request->file('photo')->store('pets', 'public');
    }

    // Checkbox
    $data['available_for_adoption'] = $request->has('available_for_adoption');

    Pet::create($data);

    return redirect()->route('adoption.admin.index')
      ->with('success', 'Mascota creada correctamente');
  }
}