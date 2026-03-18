@extends('layouts.app')
@section('content')
<div class="container py-4">
  <div class="mb-4">
    <h2 class="fw-bold mb-1">Editar Mascota</h2>
    <p class="text-muted small">Actualiza los datos de {{ $viewData['pet']->getName() }}</p>
  </div>
  <div class="card shadow-sm" style="border-radius: 15px;">
    <div class="card-body p-4">
      <form method="POST" action="{{ route('pets.update', $viewData['pet']->getId()) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label fw-bold small">Nombre *</label>
            <input id="name" type="text" class="form-control bg-light border-0 py-2" name="name" value="{{ $viewData['pet']->getName() }}" required>
          </div>
          <div class="col-md-6">
            <label for="species" class="form-label fw-bold small">Especie *</label>
            <select id="species" class="form-select bg-light border-0 py-2" name="species" required>
              <option value="Perro" {{ $viewData['pet']->getSpecies() == 'Perro' ? 'selected' : '' }}>Perro</option>
              <option value="Gato" {{ $viewData['pet']->getSpecies() == 'Gato' ? 'selected' : '' }}>Gato</option>
              <option value="Ave" {{ $viewData['pet']->getSpecies() == 'Ave' ? 'selected' : '' }}>Ave</option>
              <option value="Conejo" {{ $viewData['pet']->getSpecies() == 'Conejo' ? 'selected' : '' }}>Conejo</option>
              <option value="Hamster" {{ $viewData['pet']->getSpecies() == 'Hamster' ? 'selected' : '' }}>Hamster</option>
              <option value="Reptil" {{ $viewData['pet']->getSpecies() == 'Reptil' ? 'selected' : '' }}>Reptil</option>
              <option value="Otro" {{ $viewData['pet']->getSpecies() == 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="breed" class="form-label fw-bold small">Raza</label>
            <input id="breed" type="text" class="form-control bg-light border-0 py-2" name="breed" value="{{ $viewData['pet']->getBreed() }}">
          </div>
          <div class="col-md-6">
            <label for="birth_date" class="form-label fw-bold small">Fecha de Nacimiento</label>
            <input id="birth_date" type="date" class="form-control bg-light border-0 py-2" name="birth_date" value="{{ $viewData['pet']->getBirthDate() }}">
          </div>
          <div class="col-md-6">
            <label for="gender" class="form-label fw-bold small">Género *</label>
            <select id="gender" class="form-select bg-light border-0 py-2" name="gender" required>
              <option value="male" {{ $viewData['pet']->getGender() == 'male' ? 'selected' : '' }}>Macho</option>
              <option value="female" {{ $viewData['pet']->getGender() == 'female' ? 'selected' : '' }}>Hembra</option>
              <option value="unknown" {{ $viewData['pet']->getGender() == 'unknown' ? 'selected' : '' }}>Desconocido</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="weight" class="form-label fw-bold small">Peso (kg)</label>
            <input id="weight" type="number" step="0.01" class="form-control bg-light border-0 py-2" name="weight" value="{{ $viewData['pet']->getWeight() }}">
          </div>
          <div class="col-md-6">
            <label for="photo" class="form-label fw-bold small">Foto</label>
            <input id="photo" type="file" class="form-control bg-light border-0 py-2" name="photo" accept="image/*">
            @if($viewData['pet']->getPhoto())
              <small class="text-muted">Foto actual: {{ basename($viewData['pet']->getPhoto()) }}</small>
            @endif
          </div>
          <div class="col-12">
            <label for="notes" class="form-label fw-bold small">Notas</label>
            <textarea id="notes" class="form-control bg-light border-0" name="notes" rows="3">{{ $viewData['pet']->getNotes() }}</textarea>
          </div>
        </div>
        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-pet-primary px-4 py-2 fw-bold text-white">
            <i class="bi bi-save me-2"></i>Guardar Cambios
          </button>
          <a href="{{ route('home.index') }}" class="btn btn-outline-secondary px-4 py-2">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection