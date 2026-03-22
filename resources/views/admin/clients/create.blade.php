@extends('layouts.dashboard')

@section('title', 'Crear Nuevo Cliente')

@section('content')
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Crear Nuevo Cliente</h2>
            <p class="text-muted small">Registra un nuevo cliente en el sistema</p>
        </div>

        <div class="card register-container shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-4">
                <h5 class="card-title text-pet-green mb-1">
                    <i class="bi bi-person me-2"></i>Información del Cliente
                </h5>
                <p class="text-muted small mb-4">Completa los datos personales del nuevo cliente</p>

                <form method="POST" action="{{ route('clients.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold small">Nombre *</label>
                            <input id="name" type="text"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" placeholder="Nombre" required autofocus>
                            @error('name')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lastname" class="form-label fw-bold small">Apellido *</label>
                            <input id="lastname" type="text" class="form-control bg-light border-0 py-2" name="lastname"
                                placeholder="Apellido" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold small">Correo Electrónico *</label>
                            <input id="email" type="email"
                                class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" required>
                            @error('email')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold small">Teléfono *</label>
                            <input id="phone" type="text" class="form-control bg-light border-0 py-2" name="phone"
                                placeholder="(123) 456-7890" required>
                        </div>

                        <div class="col-md-12">
                            <label for="address" class="form-label fw-bold small">Dirección</label>
                            <input id="address" type="text" class="form-control bg-light border-0 py-2" name="address"
                                placeholder="Dirección completa">
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small">Contraseña *</label>
                            <input id="password" type="password" class="form-control bg-light border-0 py-2"
                                @error('password') is-invalid @enderror name="password" utocomplete="new-password"
                                placeholder="Contraseña">

                            @error('password')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password-confirm" class="form-label fw-bold small">Confirmar Contraseña</label>
                            <input id="password-confirm" type="password" class="form-control bg-light border-0 py-2"
                                @error('password_confirmation') is-invalid @enderror name="password_confirmation" required
                                autocomplete="new-password" placeholder="Contraseña">
                            @error('password_confirmation')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-pet-primary w-100 py-2 fw-bold text-white">
                            Crear Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
