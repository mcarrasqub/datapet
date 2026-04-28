@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Panel Médico</h4>
            <p class="text-muted">Resumen de tu jornada y pacientes del día</p>
        </div>

        <div class="row g-3 mb-4">
            <x-stat-card icon="bi-calendar-check" title="Consultas Hoy" value="{{ $consultasHoy ?? 0 }}"
                subtitle="Atenciones registradas" />
            <x-stat-card icon="bi-people" title="Pacientes Totales" value="{{ $totalPatients ?? 0 }}"
                subtitle="Bajo tu atención" />
            <x-stat-card icon="bi-file-earmark-medical" title="Exámenes Pendientes" value="{{ $examsPendientes ?? 0 }}"
                subtitle="Por revisar" />
            <x-stat-card icon="bi-graph-up-arrow" title="Consultas Este Mes" value="{{ $consultasMes ?? 0 }}"
                subtitle="Historial del mes" />
        </div>

        <div class="row g-3">
            <!-- Agenda del Día -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-calendar-day text-pet-green me-2"></i>
                            <h6 class="fw-bold mb-0">Agenda del Día</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            @forelse(($agendaHoy ?? collect()) as $cita)
                                <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">
                                            {{ $cita->pet?->getName() ?? 'Mascota' }}
                                        </h6>
                                        <small class="text-muted d-block">{{ $cita->pet?->owner?->getName() ?? 'Sin propietario' }}</small>
                                        <small class="text-muted d-block">Motivo: {{ $cita->getReason() ?: 'No especificado' }}</small>
                                        <small class="text-muted d-block fw-medium mt-1">
                                            <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($cita->getStartTime())->format('g:i A') }} - {{ \Carbon\Carbon::parse($cita->getEndTime())->format('g:i A') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-outline-pet-green btn-sm px-3 rounded-3">Ver Calendario</a>
                                </div>
                            @empty
                                <div class="border rounded-3 p-4 bg-light text-center">
                                    <p class="mb-0 text-muted">No tienes citas programadas para hoy.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exámenes Pendientes de Revisión -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-exclamation-circle text-pet-green me-2"></i>
                            <h6 class="fw-bold mb-0">Exámenes Pendientes de Revisión</h6>
                        </div>
                        <p class="text-muted small mb-4">Requieren tu atención</p>

                        <div class="d-flex flex-column gap-3">
                            @forelse(($pendingExams ?? collect()) as $exam)
                                <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">
                                            {{ $exam->pet?->name ?? 'Mascota sin nombre' }}</h6>
                                        <small
                                            class="text-muted d-block">{{ $exam->pet?->owner?->name ?? 'Sin propietario' }}</small>
                                        <small class="text-muted d-block">{{ $exam->title ?: $exam->original_name }}</small>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            Subido: {{ optional($exam->uploaded_at)->format('d M Y') ?? 'Sin fecha' }}
                                        </small>
                                    </div>
                                    <a href="{{ route('medical_exams.view', $exam) }}"
                                        class="btn btn-pet-green btn-sm px-3 rounded-3">Revisar</a>
                                </div>
                            @empty
                                <div class="border rounded-3 p-4 bg-light text-center">
                                    <p class="mb-0 text-muted">No tienes exámenes pendientes por revisar.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection