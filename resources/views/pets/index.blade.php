@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Mascotas Registradas</h2>
        </div>
        <a href="{{ route('pets.create') }}" class="btn btn-pet-primary px-4 py-2 fw-bold text-white">
            Registrar Mascota
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @forelse($pets as $pet)
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $pet->name }}</h5>
                        <p class="text-muted small">{{ $pet->species }} - {{ $pet->breed }}</p>
                        <p class="text-muted small">Dueño: {{ $pet->owner->name }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No hay mascotas registradas</div>
            </div>
        @endforelse
    </div>
</div>
@endsection