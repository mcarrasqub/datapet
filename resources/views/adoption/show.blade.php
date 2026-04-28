@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="card shadow-sm">
        @if($viewData['pet']->photo)
            <img src="{{ asset('storage/'.$viewData['pet']->photo) }}" class="card-img-top">
        @endif

        <div class="card-body">
            <h3 class="fw-bold">{{ $viewData['pet']->name }}</h3>

            <p class="text-muted mb-1">
                {{ $viewData['pet']->species }}
            </p>

            <p>
                {{ $viewData['pet']->adoption_description }}
            </p>

            <hr>

            <h5 class="fw-bold">Solicitar adopción</h5>

            <form action="{{ route('adoption.store') }}" method="POST">
                @csrf

                <input type="hidden" name="pet_id" value="{{ $viewData['pet']->id }}">

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Experiencia</label>
                    <textarea name="experience" class="form-control"></textarea>
                </div>

                <button class="btn btn-success w-100">
                    Enviar solicitud
                </button>
            </form>

        </div>
    </div>

</div>
@endsection