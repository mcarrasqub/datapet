@extends('layouts.dashboard')

@section('title', 'Usuarios')

@section('content')
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="fw-bold mb-1 text-pet-green">Gestión de Usuarios</h2>
            <p class="text-muted small">Administra los usuarios y permisos del sistema</p>
        </div>

        <div class="d-flex align-items-start justify-content-between mb-4">
            <div></div>

            <button class="btn btn-pet-primary rounded-pill px-4 py-2 fw-bold text-white" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus me-2 "></i>Crear Usuario
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Total Usuarios</h6>
                        <p class="fs-1 fw-bold text-pet-green mb-0">{{ $counts['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Administradores</h6>
                        <p class="fs-1 fw-bold text-pet-green mb-0">{{ $counts['admin'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Doctores</h6>
                        <p class="fs-1 fw-bold text-pet-green mb-0">{{ $counts['doctor'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Clientes</h6>
                        <p class="fs-1 fw-bold text-pet-green mb-0">{{ $counts['client'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Filtros</h5>
                <form id="filterForm" class="row gy-3 gx-3 align-items-end" method="GET" action="{{ route('users.index') }}">
                    <div class="col-md-8">
                        <label class="form-label">Buscar Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input name="search" value="{{ $searchInput }}" type="text"
                                class="form-control border-start-0" placeholder="Buscar por nombre o correo...">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Rol</label>
                        <select name="role" id="roleFilter" class="form-select">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ $roleInput === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('roleFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Hacer que el campo de búsqueda envíe el formulario con Enter
            document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('filterForm').submit();
                }
            });
        </script>

        <div class="row gy-4">
            @forelse($users as $user)
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ trim($user->name . ' ' . $user->lastname) }}</h5>
                                <div class="text-muted mb-2">
                                    {{ $user->email }}
                                    <span class="mx-2">•</span>
                                    Creado:
                                    {{ optional($user->created_at)->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-start">
                                    @php
                                        $roleLabel = $roles[$user->role] ?? ucfirst($user->role);
                                    @endphp
                                    <span class="badge bg-pet-green">{{ $roleLabel }}</span>
                                    @if($user->status)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-pet-green">Inactivo</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="#" class="btn btn-outline-pet-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form action="{{ route('users.toggleStatus', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-warning btn-sm">
                                        {{ $user->status ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        No se encontraron usuarios con estos criterios.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content register-container shadow-sm">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="createUserModalLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <h5 class="card-title text-pet-green mb-1">
                        <i class="bi bi-person me-2"></i>Información del Usuario
                    </h5>
                    <p class="text-muted small mb-4">Completa la información del nuevo usuario del sistema</p>

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nombres</label>
                                <input name="name" value="{{ old('name') }}" type="text"
                                    class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                    placeholder="Nombres" required>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lastname" class="form-label fw-bold small">Apellidos</label>
                                <input id="lastname" name="lastname" value="{{ old('lastname') }}" type="text"
                                    class="form-control bg-light border-0 py-2 @error('lastname') is-invalid @enderror"
                                    placeholder="Apellidos" required>
                                @error('lastname')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Correo Electrónico</label>
                                <input name="email" value="{{ old('email') }}" type="email"
                                    class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                    placeholder="correo@datapet.com" required>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Rol del Usuario</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="" disabled selected>Selecciona un rol</option>
                                    @foreach($roles as $key => $label)
                                        @if($key !== 'client')
                                            <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('role')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Contraseña</label>
                                <input name="password" type="password"
                                    class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror"
                                    placeholder="Contraseña" required>
                                @error('password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contraseña</label>
                                <input id="password-confirm" type="password" class="form-control bg-light border-0 py-2"
                                    @error('password_confirmation') is-invalid @enderror name="password_confirmation" required
                                    autocomplete="new-password" placeholder="Contraseña">
                                @error('password_confirmation')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-pet-primary">Crear Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
                createUserModal.show();
            });
        </script>
    @endif

@endsection
