<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />

    <title>@yield('title' , 'DataPet')</title>

    <link href="{{ asset('css/login.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <title>Document</title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center"">
        <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 450px; border-radius: 15px;">
            <div class="card-body text-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="DataPet Logo" class="img-fluid mb-3" style="max-height: 100px;">

                <h2 class="fw-bold mb-1">DataPet</h2>
                <p class="text-muted small mb-4">Hospital Veterinario - Especialistas en Mascotas No Convencionales</p>

                <form method="POST" action="{{ route('login') }}" class="text-start">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small">{{ __('Correo electrónico') }}</label>
                        <input id="email" type="email" class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror" name="email" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required autofocus>
                        @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold small">{{ __('Contraseña') }}</label>
                        <input id="password" type="password" class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror" name="password" placeholder="********" required>
                        @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-pet-primary w-100 py-2 fw-bold text-white mb-4">
                        {{ __('Iniciar Sesión') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
