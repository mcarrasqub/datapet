@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Solicitudes de Adopción</h3>

    @foreach($viewData['requests'] as $request)
        <div class="card p-3 mb-3 shadow-sm">

            <p class="mb-1">
                <strong>Usuario:</strong> {{ $request->user->name ?? 'Usuario' }}
            </p>

            <p class="mb-1">
                <strong>Mascota:</strong> {{ $request->pet->name ?? 'Mascota' }}
            </p>

            <p class="mb-2">
                <strong>Estado:</strong> {{ $request->status }}
            </p>

            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('adoption.approve', $request->id) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success btn-sm">Aprobar</button>
                </form>

                <form method="POST" action="{{ route('adoption.reject', $request->id) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-danger btn-sm">Rechazar</button>
                </form>
            </div>

        </div>
    @endforeach

</div>
@endsection