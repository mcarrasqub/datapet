@extends('layouts.app')
@section('content')
<link href="{{ asset('css/register.css') }}" rel="stylesheet">
<div class="container py-4">
    <!-- Indicador de Pasos -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <div class="d-flex align-items-center">
            <span class="badge rounded-circle p-2 me-2" id="step1-indicator" style="width: 30px; height: 30px; background-color: #28a745;">1</span>
            <span class="fw-bold" id="step1-text" style="color: #28a745;">Datos del Cliente</span>
        </div>
        <div class="mx-3" style="width: 50px; height: 1px; background-color: #dee2e6;"></div>
        <div class="d-flex align-items-center">
            <span class="badge rounded-circle bg-light text-muted border p-2 me-2" id="step2-indicator" style="width: 30px; height: 30px;">2</span>
            <span class="text-muted" id="step2-text">Datos de la Mascota</span>
        </div>
    </div>

    <div class="card register-container shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <!-- PASO 1: TU FORMULARIO ORIGINAL DEL CLIENTE -->
            <div id="step1" class="step-content">
                <h5 class="card-title text-pet-green mb-1">
                    <i class="bi bi-person me-2"></i>Información del Cliente
                </h5>
                <p class="text-muted small mb-4">Completa los datos personales del propietario</p>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
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
                            <input id="apellido" type="text" class="form-control bg-light border-0 py-2" name="apellido" value="{{ old('apellido') }}" placeholder="Apellido" required>
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
                            <input id="telefono" type="text" class="form-control bg-light border-0 py-2" name="telefono" value="{{ old('telefono') }}" placeholder="(123) 456-7890" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small">Contraseña *</label>
                            <input id="password" type="password" class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold small">Confirmar Contraseña *</label>
                            <input id="password_confirmation" type="password" class="form-control bg-light border-0 py-2" name="password_confirmation" required>
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label fw-bold small">Dirección</label>
                            <input id="direccion" type="text" class="form-control bg-light border-0 py-2" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección completa">
                        </div>

                        <div class="col-md-6">
                            <label for="contacto_emergencia" class="form-label fw-bold small">Contacto de Emergencia</label>
                            <input id="contacto_emergencia" type="text" class="form-control bg-light border-0 py-2" name="contacto_emergencia" value="{{ old('contacto_emergencia') }}" placeholder="Nombre completo">
                        </div>

                        <div class="col-md-6">
                            <label for="tel_emergencia" class="form-label fw-bold small">Teléfono de Emergencia</label>
                            <input id="tel_emergencia" type="text" class="form-control bg-light border-0 py-2" name="tel_emergencia" value="{{ old('tel_emergencia') }}" placeholder="(123) 456-7890">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-pet-primary w-100 py-2 fw-bold text-white" onclick="goToStep2(); return false;">
                            Continuar a Datos de Mascota
                        </button>
                    </div>
                </form>
            </div>

            <!-- PASO 2: FORMULARIO DE MASCOTA -->
            <div id="step2" class="step-content" style="display: none;">
                <h5 class="card-title text-pet-green mb-1">
                    <i class="bi bi-clipboard-heart me-2"></i>Información de la Mascota
                </h5>
                <p class="text-muted small mb-4">Completa los datos de tu mascota</p>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerFormStep2">
                    @csrf
                    
                    <!-- Campos ocultos con datos del paso 1 -->
                    <input type="hidden" name="name" id="hidden_name">
                    <input type="hidden" name="apellido" id="hidden_apellido">
                    <input type="hidden" name="email" id="hidden_email">
                    <input type="hidden" name="telefono" id="hidden_telefono">
                    <input type="hidden" name="password" id="hidden_password">
                    <input type="hidden" name="password_confirmation" id="hidden_password_confirmation">
                    <input type="hidden" name="direccion" id="hidden_direccion">
                    <input type="hidden" name="contacto_emergencia" id="hidden_contacto_emergencia">
                    <input type="hidden" name="tel_emergencia" id="hidden_tel_emergencia">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pet_name" class="form-label fw-bold small">Nombre de la Mascota *</label>
                            <input id="pet_name" type="text" class="form-control bg-light border-0 py-2" name="pet_name" placeholder="Ej: Max" required>
                        </div>

                        <div class="col-md-6">
                            <label for="species" class="form-label fw-bold small">Especie *</label>
                            <select id="species" class="form-select bg-light border-0 py-2" name="species" required>
                                <option value="">Seleccione</option>
                                <option value="Perro">Perro</option>
                                <option value="Gato">Gato</option>
                                <option value="Ave">Ave</option>
                                <option value="Conejo">Conejo</option>
                                <option value="Hamster">Hamster</option>
                                <option value="Reptil">Reptil</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="breed" class="form-label fw-bold small">Raza</label>
                            <input id="breed" type="text" class="form-control bg-light border-0 py-2" name="breed" placeholder="Ej: Golden Retriever">
                        </div>

                        <div class="col-md-6">
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

                        <div class="col-md-4">
                            <label for="weight" class="form-label fw-bold small">Peso (kg)</label>
                            <input id="weight" type="number" step="0.01" class="form-control bg-light border-0 py-2" name="weight" placeholder="5.5">
                        </div>

                        <div class="col-md-6">
                            <label for="photo" class="form-label fw-bold small">Foto de la Mascota</label>
                            <input id="photo" type="file" class="form-control bg-light border-0 py-2" name="photo" accept="image/*">
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label fw-bold small">Notas Adicionales</label>
                            <textarea id="notes" class="form-control bg-light border-0" name="notes" rows="3" placeholder="Información adicional sobre la mascota"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="goToStep1(); return false;">
                            Volver
                        </button>
                        <button type="submit" class="btn btn-pet-primary flex-fill py-2 fw-bold text-white">
                            Completar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function goToStep2() {
    // Validar campos obligatorios del paso 1
    const name = document.getElementById('name').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const email = document.getElementById('email').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const password = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;

    if (!name || !apellido || !email || !telefono || !password || !password_confirmation) {
        alert('Por favor complete todos los campos obligatorios (*)');
        return false;
    }

    if (password !== password_confirmation) {
        alert('Las contraseñas no coinciden');
        return false;
    }

    if (password.length < 8) {
        alert('La contraseña debe tener al menos 8 caracteres');
        return false;
    }

    // Copiar valores a campos ocultos
    document.getElementById('hidden_name').value = name;
    document.getElementById('hidden_apellido').value = apellido;
    document.getElementById('hidden_email').value = email;
    document.getElementById('hidden_telefono').value = telefono;
    document.getElementById('hidden_password').value = password;
    document.getElementById('hidden_password_confirmation').value = password_confirmation;
    document.getElementById('hidden_direccion').value = document.getElementById('direccion').value;
    document.getElementById('hidden_contacto_emergencia').value = document.getElementById('contacto_emergencia').value;
    document.getElementById('hidden_tel_emergencia').value = document.getElementById('tel_emergencia').value;

    // Cambiar de paso
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';

    // Actualizar indicadores
    document.getElementById('step2-indicator').style.backgroundColor = '#28a745';
    document.getElementById('step2-indicator').classList.remove('bg-light', 'text-muted', 'border');
    document.getElementById('step2-text').style.color = '#28a745';
    document.getElementById('step2-text').classList.remove('text-muted');

    return false;
}

function goToStep1() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';

    // Actualizar indicadores
    document.getElementById('step2-indicator').style.backgroundColor = '';
    document.getElementById('step2-indicator').classList.add('bg-light', 'text-muted', 'border');
    document.getElementById('step2-text').style.color = '';
    document.getElementById('step2-text').classList.add('text-muted');

    return false;
}
</script>
@endsection