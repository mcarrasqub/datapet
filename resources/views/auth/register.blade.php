@extends('layouts.app')
@section('content')
<link href="{{ asset('css/register.css') }}" rel="stylesheet">
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Registrar Nuevo Cliente</h2>
        <p class="text-muted small">Registra un nuevo cliente y su mascota en el sistema</p>
    </div>

    <div class="d-flex justify-content-center align-items-center mb-4">
        <div class="d-flex align-items-center">
            <span class="badge rounded-circle bg-pet-green p-2 me-2" style="width: 30px; height: 30px;">1</span>
            <span class="fw-bold text-pet-green">Datos del Cliente</span>
        </div>
        <div class="mx-3" style="width: 50px; height: 1px; background-color: #dee2e6;"></div>
        <div class="d-flex align-items-center">
            <span class="badge rounded-circle bg-light text-muted border p-2 me-2" style="width: 30px; height: 30px;">2</span>
            <span class="text-muted">Datos de la Mascota</span>
        </div>
    </div>

    <div class="card register-container shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h5 class="card-title text-pet-green mb-1">
                <i class="bi bi-person me-2"></i>Información del Cliente
            </h5>
            <p class="text-muted small mb-4">Completa los datos personales del propietario</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold small">Nombre *</label>
                        <input id="name" type="text" class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Nombre" required autofocus>
                        @error('name')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="apellido" class="form-label fw-bold small">Apellido *</label>
                        <input id="apellido" type="text" class="form-control bg-light border-0 py-2" name="apellido" placeholder="Apellido" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold small">Correo Electrónico *</label>
                        <input id="email" type="email" class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" required>
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telefono" class="form-label fw-bold small">Teléfono *</label>
                        <input id="telefono" type="text" class="form-control bg-light border-0 py-2" name="telefono" placeholder="(123) 456-7890" required>
                    </div>

                    <div class="col-12">
                        <label for="direccion" class="form-label fw-bold small">Dirección</label>
                        <input id="direccion" type="text" class="form-control bg-light border-0 py-2" name="direccion" placeholder="Dirección completa">
                    </div>

                    <div class="col-md-6">
                        <label for="contacto_emergencia" class="form-label fw-bold small">Contacto de Emergencia</label>
                        <input id="contacto_emergencia" type="text" class="form-control bg-light border-0 py-2" name="contacto_emergencia" placeholder="Nombre completo">
                    </div>

                    <div class="col-md-6">
                        <label for="tel_emergencia" class="form-label fw-bold small">Teléfono de Emergencia</label>
                        <input id="tel_emergencia" type="text" class="form-control bg-light border-0 py-2" name="tel_emergencia" placeholder="(123) 456-7890">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-pet-primary w-100 py-2 fw-bold text-white">
                        Continuar a Datos de Mascota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection