@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Información del Cliente -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-person-circle me-2"></i>Mi Perfil
                    </h5>
                    <p class="mb-2"><strong>Nombre:</strong> {{ $user->name }}</p>
                    <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Mascotas del Cliente -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clipboard-heart me-2"></i>Mis Mascotas
                </h5>
            </div>

            @if($pets->count() > 0)
                <div class="row g-3">
                    @foreach($pets as $pet)
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100" style="border-radius: 15px;">
                                @if($pet->photo)
                                    <img src="{{ asset('storage/' . $pet->photo) }}" class="card-img-top" alt="{{ $pet->name }}" style="height: 200px; object-fit: cover; border-radius: 15px 15px 0 0;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; border-radius: 15px 15px 0 0;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2">{{ $pet->name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-paw-fill me-1"></i>
                                        {{ $pet->species }}
                                        @if($pet->breed)
                                            - {{ $pet->breed }}
                                        @endif
                                    </p>
                                    @if($pet->birth_date)
                                        <p class="text-muted small mb-1">
                                            <i class="bi bi-calendar me-1"></i>
                                            Nacimiento: {{ $pet->birth_date->format('d/m/Y') }}
                                        </p>
                                    @endif
                                    @if($pet->weight)
                                        <p class="text-muted small mb-1">
                                            <i class="bi bi-speedometer me-1"></i>
                                            Peso: {{ $pet->weight }} kg
                                        </p>
                                    @endif
                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-gender-{{ $pet->gender == 'male' ? 'male' : 'female' }} me-1"></i>
                                        {{ $pet->gender == 'male' ? 'Macho' : ($pet->gender == 'female' ? 'Hembra' : 'Desconocido') }}
                                    </p>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pets.edit', $pet->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="bi bi-pencil me-1"></i>Editar
                                        </a>
                                        <form action="{{ route('pets.destroy', $pet->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('¿Eliminar esta mascota?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="bi bi-trash me-1"></i>Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Aún no tienes mascotas registradas.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection