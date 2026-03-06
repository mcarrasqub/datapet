<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />

    <title>@yield('title' , 'DataPet')</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

</head>

<body>
        <div class="container-fluid py-2 border-bottom bg-white">
            <div class="d-flex justify-content-between align-items-center px-4">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" width="90" class="me-3">
                    <div class="lh-1">
                        <h5 class="fw-bold mb-0">DataPet</h5>
                        <small class="text-muted">Bienvenido/a, María González</small>
                    </div>
                </div>
                <button class="btn btn-outline-secondary rounded-3 d-flex align-items-center px-3 py-1">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </button>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-0">
            <div class="container-fluid">
                <button class="navbar-toggler my-2" type="button" data-bs-toggle="collapse" data-bs-target="#navLinks">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navLinks">
                    <ul class="navbar-nav w-100 justify-content-around align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active-pill" href="#"><i class="bi bi-house-door me-2"></i>Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-file-earmark-text me-2"></i>Historial Clínico</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-calendar4-event me-2"></i>Citas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-upload me-2"></i>Subir Exámenes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium position-relative" href="#">
                                <i class="bi bi-bell me-2"></i>Notificaciones
                                <span class="badge rounded-circle bg-danger badge-custom">3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-heart me-2"></i>Adopciones</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
</body>

</html>