@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Mascotas en Adopción</h3>

    <div class="row">
        @forelse($viewData['pets'] as $pet)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    @if($pet->photo)
                        <img src="{{ asset('storage/'.$pet->photo) }}" class="card-img-top">
                    @endif

                    <div class="card-body">
                        <h5 class="fw-bold">{{ $pet->name }}</h5>
                        <p class="text-muted mb-1">{{ $pet->species }}</p>
                        <p>{{ $pet->adoption_description }}</p>

                        <a href="{{ route('adoption.show', $pet->id) }}"
                           class="btn btn-success w-100">
                           Ver detalle
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p>No hay mascotas disponibles.</p>
        @endforelse
    </div>
</div>
@endsection