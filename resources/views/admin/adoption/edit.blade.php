@extends('layouts.dashboard')

@section('content')
  <div class="container py-4">
    <div class="mb-4">
      <h3 class="fw-bold mb-1" style="color: #1e293b;">Editar Mascota en Adopción</h3>
      <p class="text-muted mb-4" style="font-size: 0.95rem;">
        Actualiza el perfil y datos de adopción de la mascota
      </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0" role="alert" style="background-color: #fce8e6; color: #a51d24;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
      <div class="card-body p-4">

        <form action="{{ route('admin.adoptions.update', $viewData['pet']->getId()) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row g-4">

            {{-- Nombre --}}
            <div class="col-md-6">
              <label class="form-label fw-bold small">Nombre</label>
              <input 
                type="text" 
                name="name" 
                class="form-control" 
                value="{{ old('name', $viewData['pet']->getName()) }}"
                placeholder="Nombre de la mascota" 
                required>
            </div>

            {{-- Especie --}}
            <div class="col-md-6">
              <label class="form-label fw-bold small">Especie</label>
              <input 
                type="text" 
                name="species" 
                class="form-control" 
                value="{{ old('species', $viewData['pet']->getSpecies()) }}"
                placeholder="Ej: Iguana, Loro, Hurón" 
                required>
            </div>

            {{-- Edad --}}
            <div class="col-md-4">
              <label class="form-label fw-bold small">Edad</label>
              <input 
                type="number" 
                name="age" 
                class="form-control" 
                value="{{ old('age', $viewData['pet']->getAge()) }}"
                placeholder="Años">
            </div>

            {{-- Peso --}}
            <div class="col-md-4">
              <label class="form-label fw-bold small">Peso (kg)</label>
              <input 
                type="number" 
                step="0.1"
                name="weight" 
                class="form-control" 
                value="{{ old('weight', $viewData['pet']->getWeight()) }}"
                placeholder="Ej: 2.5">
            </div>

            {{-- Disponible para adopción --}}
            <div class="col-md-4 d-flex align-items-end">
              <div class="form-check mb-2">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="available_for_adoption" 
                  value="1" 
                  {{ old('available_for_adoption', $viewData['pet']->getAvailableForAdoption()) ? 'checked' : '' }}>
                <label class="form-check-label small fw-bold">
                  Disponible para adopción
                </label>
              </div>
            </div>

            {{-- Descripción / Notas de Adopción --}}
            <div class="col-12">
              <label class="form-label fw-bold small">Notas de Adopción</label>
              <textarea 
                name="adoption_description" 
                class="form-control" 
                rows="4"
                placeholder="Describe comportamiento, cuidados, etc.">{{ old('adoption_description', $viewData['pet']->getAdoptionDescription()) }}</textarea>
            </div>

            {{-- Foto --}}
            <div class="col-12">
              <label class="form-label fw-bold small d-block">Foto</label>
              @if($viewData['pet']->getPhoto())
                <div class="mb-3">
                  <small class="text-muted d-block mb-2">Foto actual:</small>
                  <img src="{{ asset('storage/' . $viewData['pet']->getPhoto()) }}" alt="Foto" class="rounded object-fit-cover shadow-sm" style="width: 120px; height: 120px;">
                </div>
              @endif
              <input 
                type="file" 
                name="photo" 
                class="form-control">
              <small class="text-muted mt-1 d-block">Sube un archivo para reemplazar la foto actual.</small>
            </div>

          </div>

          <div class="d-flex gap-2 mt-4">
            <button 
              type="submit" 
              class="btn text-white fw-bold d-flex align-items-center justify-content-center"
              style="background-color: #65a34e; border-radius: 8px; padding: 10px 20px;">
              <i class="bi bi-check-circle me-2"></i>Guardar Cambios
            </button>

            <a 
              href="{{ route('adoption.admin.index') }}" 
              class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
              style="border-radius: 8px; padding: 10px 20px;">
              <i class="bi bi-arrow-left me-2"></i>Cancelar
            </a>
          </div>

        </form>

      </div>
    </div>
  </div>
@endsection
