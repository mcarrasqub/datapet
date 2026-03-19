<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />

    <title>@yield('title', 'DataPet')</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
</head>

<body>
    <div class="container-fluid py-4 border-bottom bg-white shadow-sm">
        <div class="d-flex justify-content-between align-items-center px-4">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" width="90" class="me-3">
                <div class="lh-1">
                    <h5 class="fw-bold mb-0">
                        @auth
                            @if(auth()->user()->role === 'admin')
                                Panel de Administración - DataPet
                            @else
                                Panel Médico - DataPet
                            @endif
                        @else
                            DataPet
                        @endauth
                    </h5>
                    <small class="text-muted">
                        @auth
                            Bienvenido/a, {{ auth()->user()->name }}
                        @else
                            Bienvenido/a
                        @endauth
                    </small>
                </div>
            </div>

            @auth
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary rounded-3 d-flex align-items-center px-3 py-1">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-3 d-flex align-items-center px-3 py-1">
                    <i class="bi bi-box-arrow-right me-2"></i> Iniciar Sesión
                </a>
            @endauth
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
                        <a class="nav-link {{ request()->routeIs('home.index') ? 'active-pill' : 'text-dark fw-medium' }}" href="{{ route('home.index') }}"><i class="bi bi-activity me-2"></i>Inicio</a>
                    </li>

                    {{-- Doctor links --}}
                    @if(auth()->check() && auth()->user()->role === 'doctor')
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-calendar4-event me-2"></i>Mis Citas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-file-earmark-text me-2"></i>Historiales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="{{ route('clients.create') }}"><i class="bi bi-person-plus me-2"></i>Nuevo Cliente</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-journal-medical me-2"></i>Exámenes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-prescription me-2"></i>Recetas</a>
                        </li>
                    @endif

                    {{-- Admin links --}}
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-graph-up me-2"></i>Estadísticas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-check2-square me-2"></i>Tareas Doctores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active-pill' : 'text-dark fw-medium' }}" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i>Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="{{ route('clients.create') }}"><i class="bi bi-person-lines-fill me-2"></i>Clientes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-calendar3 me-2"></i>Agenda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#"><i class="bi bi-gear me-2"></i>Configuración</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
