@extends('layouts.dashboard')

@section('title', 'Editar Usuario')

@section('content')
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="fw-bold mb-1 text-pet-green">Editar Usuario</h2>
            <p class="text-muted small">Modifica la información del usuario del sistema</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card register-container shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title text-pet-green mb-1">
                            <i class="bi bi-person-edit me-2"></i>Información del Usuario
                        </h5>
                        <p class="text-muted small mb-4">Actualiza los datos del usuario</p>

                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Nombres</label>
                                    <input name="name" value="{{ old('name', $user->name) }}" type="text"
                                        class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                        placeholder="Nombres" required>
                                    @error('name')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Correo Electrónico</label>
                                    <input name="email" value="{{ old('email', $user->email) }}" type="email"
                                        class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                        placeholder="correo@datapet.com" required>
                                    @error('email')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Rol del Usuario</label>
                                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="" disabled>Selecciona un rol</option>
                                        @foreach($roles as $key => $label)
                                            <option value="{{ $key }}" {{ old('role', $user->role) === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">
                                        <i class="bi bi-info-circle text-muted"
                                            title="Déjalo vacío para mantener la contraseña actual"></i>
                                        Nueva Contraseña (opcional)
                                    </label>
                                    <input name="password" type="password"
                                        class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror"
                                        placeholder="Déjalo vacío para mantener la contraseña actual">
                                    @error('password')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <small class="text-muted d-block mb-3">
                                        <i class="bi bi-info-circle"></i>
                                        Si deseas cambiar la contraseña, ingresa una nueva. De lo contrario, déjalo en
                                        blanco.
                                    </small>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver
                                </a>
                                <button type="submit" class="btn btn-pet-primary text-white px-4">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection