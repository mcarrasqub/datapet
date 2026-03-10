@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Registrar Nueva Mascota</h2>
        <p class="text-muted small">Complete los datos de la mascota</p>
    </div>

    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('pets.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label fw-bold small">Cliente (Propietario) *</label>
                        <select id="user_id" class="form-select bg-light border-0 py-2 @error('user_id') is-invalid @enderror" name="user_id" required>
                            <option value="">Seleccione un cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->email }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold small">Nombre de la Mascota *</label>
                        <input id="name" type="text" class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Ej: Max" required>
                        @error('name')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="species" class="form-label fw-bold small">Especie *</label>
                        <select id="species" class="form-select bg-light border-0 py-2" name="species" required>
                            <option value="">Seleccione</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Ave">Ave</option>
                            <option value="Conejo">Conejo</option>
                            <option value="Reptil">Reptil</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="breed" class="form-label fw-bold small">Raza</label>
                        <input id="breed" type="text" class="form-control bg-light border-0 py-2" name="breed" placeholder="Ej: Golden Retriever">
                    </div>

                    <div class="col-md-4">
                        <label for="birth_date" class="form-label fw-bold small">Fecha de Nacimiento</label>
                        <input id="birth_date" type="date" class="form-control bg-light border-0 py-2" name="birth_date">
                    </div>

                    <div class="col-md-4">
                        <label for="gender" class="form-label fw-bold small">Género *</label>
                        <select id="gender" class="form-select bg-light border-0 py-2" name="gender" required>
                            <option value="male">Macho</option>
                            <option value="female">Hembra</option>
                            <option value="unknown">Desconocido</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="color" class="form-label fw-bold small">Color</label>
                        <input id="color" type="text" class="form-control bg-light border-0 py-2" name="color" placeholder="Ej: Café">
                    </div>

                    <div class="col-md-6">
                        <label for="weight" class="form-label fw-bold small">Peso (kg)</label>
                        <input id="weight" type="number" step="0.01" class="form-control bg-light border-0 py-2" name="weight" placeholder="5.5">
                    </div>

                    <div class="col-md-6">
                        <label for="photo" class="form-label fw-bold small">Foto</label>
                        <input id="photo" type="file" class="form-control bg-light border-0 py-2" name="photo" accept="image/*">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-bold small">Notas</label>
                        <textarea id="notes" class="form-control bg-light border-0" name="notes" rows="3"></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-pet-primary px-4 py-2 fw-bold text-white">
                        Registrar Mascota
                    </button>
                    <a href="{{ route('pets.index') }}" class="btn btn-outline-secondary px-4 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection