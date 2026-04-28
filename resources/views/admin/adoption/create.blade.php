@extends('layouts.dashboard')

@section('content')
  <div class="container py-4">
    <div class="mb-4">
      <h3 class="fw-bold mb-1" style="color: #1e293b;">Crear Mascota en Adopción</h3>
      <p class="text-muted mb-4" style="font-size: 0.95rem;">
        Registra una nueva mascota disponible para adopción
      </p>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
      <div class="card-body p-4">

        <form action="{{ route('admin.adoptions.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row g-4">

            {{-- Nombre --}}
            <div class="col-md-6">
              <label class="form-label fw-bold small">Nombre</label>
              <input 
                type="text" 
                name="name" 
                class="form-control" 
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
                placeholder="Ej: 2.5">
            </div>

            {{-- Disponible para adopción --}}
            <div class="col-md-4 d-flex align-items-end">
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="available_for_adoption" 
                  value="1" 
                  checked>
                <label class="form-check-label small fw-bold">
                  Disponible para adopción
                </label>
              </div>
            </div>

            {{-- Descripción --}}
            <div class="col-12">
              <label class="form-label fw-bold small">Descripción de adopción</label>
              <textarea 
                name="adoption_description" 
                class="form-control" 
                rows="4"
                placeholder="Describe comportamiento, cuidados, etc."></textarea>
            </div>

            {{-- Notas --}}
            <div class="col-12">
              <label class="form-label fw-bold small">Notas adicionales</label>
              <textarea 
                name="notes" 
                class="form-control" 
                rows="3"
                placeholder="Información médica u observaciones"></textarea>
            </div>

            {{-- Foto --}}
            <div class="col-12">
              <label class="form-label fw-bold small">Foto</label>
              <input 
                type="file" 
                name="photo" 
                class="form-control">
            </div>

          </div>

          <div class="d-flex gap-2 mt-4">
            <button 
              type="submit" 
              class="btn text-white fw-bold d-flex align-items-center justify-content-center"
              style="background-color: #65a34e; border-radius: 8px; padding: 10px 20px;">
              <i class="bi bi-check-circle me-2"></i>Crear Mascota
            </button>

            <a 
              href="{{ route('adoption.index') }}" 
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