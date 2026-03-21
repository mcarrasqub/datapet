@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-radius: 15px; border-top: 4px solid #76a75d;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-plus-circle me-2" style="color: #76a75d;"></i>Nueva Entrada Médica
                    </h5>
                    <p class="text-muted mb-4">
                        <i class="bi bi-paw-fill me-2"></i><strong>Paciente:</strong> {{ $pet->name }} - {{ $pet->species }}
                    </p>

                    <form action="{{ route('medical_records.store', $pet) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="visited_at" class="form-label fw-bold">
                                <i class="bi bi-calendar-event me-2" style="color: #76a75d;"></i>Fecha de Visita
                            </label>
                            <input type="date" class="form-control @error('visited_at') is-invalid @enderror" 
                                   id="visited_at" name="visited_at" value="{{ old('visited_at', now()->format('Y-m-d')) }}" required
                                   style="border-color: #76a75d;">
                            @error('visited_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="reason" class="form-label fw-bold">
                                <i class="bi bi-question-circle me-2" style="color: #76a75d;"></i>Motivo de Visita
                            </label>
                            <input type="text" class="form-control @error('reason') is-invalid @enderror" 
                                   id="reason" name="reason" value="{{ old('reason') }}" 
                                   placeholder="Ej: Vacunación, Revisión general" required
                                   style="border-color: #76a75d;">
                            @error('reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="diagnosis" class="form-label fw-bold">
                                <i class="bi bi-stethoscope me-2" style="color: #76a75d;"></i>Diagnóstico
                            </label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" 
                                      id="diagnosis" name="diagnosis" rows="3" 
                                      placeholder="Describe el diagnóstico" required
                                      style="border-color: #76a75d;">{{ old('diagnosis') }}</textarea>
                            @error('diagnosis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="treatment" class="form-label fw-bold">
                                <i class="bi bi-pill me-2" style="color: #76a75d;"></i>Tratamiento
                            </label>
                            <textarea class="form-control @error('treatment') is-invalid @enderror" 
                                      id="treatment" name="treatment" rows="3" 
                                      placeholder="Describe el tratamiento recomendado" required
                                      style="border-color: #76a75d;">{{ old('treatment') }}</textarea>
                            @error('treatment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">
                                <i class="bi bi-chat-left-text me-2" style="color: #76a75d;"></i>Notas Adicionales
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2" 
                                      placeholder="Notas opcionales"
                                      style="border-color: #76a75d;">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2 mt-5">
                            <button type="submit" class="btn" style="background-color: #76a75d; color: white; font-weight: bold;">
                                <i class="bi bi-check-circle me-2"></i>Guardar Registro
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