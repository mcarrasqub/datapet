@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm rounded-4 border-0 border-top-pet-green">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-pencil-square me-2 text-pet-green"></i>Editar observación clínica
                    </h5>
                    <p class="text-muted mb-4">
                        <i class="bi bi-paw-fill me-2"></i><strong>Paciente:</strong> {{ $pet->name }} - {{ $pet->species }}
                    </p>
                    <p class="text-muted mb-4">
                        <strong>Consulta:</strong> {{ $medicalRecord->visited_at->format('Y-m-d') }}
                    </p>

                    <form action="{{ route('clinical_observations.update', $observation) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="observation" class="form-label fw-bold">Observación clínica</label>
                            <textarea class="form-control @error('observation') is-invalid @enderror"
                                      id="observation" name="observation" rows="6" required
                                      style="border-color: #76a75d;">{{ old('observation', $observation->observation) }}</textarea>
                            @error('observation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-pet-green">
                                <i class="bi bi-check-circle me-2"></i>Actualizar
                            </button>
                            <a href="{{ route('medical_records.show', $pet) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
