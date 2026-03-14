@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Editar Mascota</h2>
        <p class="text-muted small">Actualiza los datos de {{ $pet->name }}</p>
    </div>

    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('pets.update', $pet->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold small">Nombre *</label>
                        <input id="name" type="text" class="form-control bg-light border-0 py-2" name="name" value="{{ $pet->name }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="species" class="form-label fw-bold small">Especie *</label>
                        <select id="species" class="form-select bg-light border-0 py-2" name="species" required>
                            <option value="Perro" {{ $pet->species == 'Perro' ? 'selected' : '' }}>Perro</option>
                            <option value="Gato" {{ $pet->species == 'Gato' ? 'selected' : '' }}>Gato</option>
                            <option value="Ave" {{ $pet->species == 'Ave' ? 'selected' : '' }}>Ave</option>
                            <option value="Conejo" {{ $pet->species == 'Conejo' ? 'selected' : '' }}>Conejo</option>
                            <option value="Hamster" {{ $pet->species == 'Hamster' ? 'selected' : '' }}>Hamster</option>
                            <option value="Reptil" {{ $pet->species == 'Reptil' ? 'selected' : '' }}>Reptil</option>
                            <option value="Otro" {{ $pet->species == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="breed" class="form-label fw-bold small">Raza</label>
                        <input id="breed" type="text" class="form-control bg-light border-0 py-2" name="breed" value="{{ $pet->breed }}">
                    </div>

                    <div class="col-md-6">
                        <label for="birth_date" class="form-label fw-bold small">Fecha de Nacimiento</label>
                        <input id="birth_date" type="date" class="form-control bg-light border-0 py-2" name="birth_date" value="{{ $pet->birth_date ? $pet->birth_date->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-4">
                        <label for="gender" class="form-label fw-bold small">Género *</label>
                        <select id="gender" class="form-select bg-light border-0 py-2" name="gender" required>
                            <option value="male" {{ $pet->gender == 'male' ? 'selected' : '' }}>Macho</option>
                            <option value="female" {{ $pet->gender == 'female' ? 'selected' : '' }}>Hembra</option>
                            <option value="unknown" {{ $pet->gender == 'unknown' ? 'selected' : '' }}>Desconocido</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="color" class="form-label fw-bold small">Color</label>
                        <input id="color" type="text" class="form-control bg-light border-0 py-2" name="color" value="{{ $pet->color }}">
                    </div>

                    <div class="col-md-4">
                        <label for="weight" class="form-label fw-bold small">Peso (kg)</label>
                        <input id="weight" type="number" step="0.01" class="form-control bg-light border-0 py-2" name="weight" value="{{ $pet->weight }}">
                    </div>

                    <div class="col-md-6">
                        <label for="photo" class="form-label fw-bold small">Foto</label>
                        <input id="photo" type="file" class="form-control bg-light border-0 py-2" name="photo" accept="image/*">
                        @if($pet->photo)
                            <small class="text-muted">Foto actual: {{ basename($pet->photo) }}</small>
                        @endif
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-bold small">Notas</label>
                        <textarea id="notes" class="form-control bg-light border-0" name="notes" rows="3">{{ $pet->notes }}</textarea>
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