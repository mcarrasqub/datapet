@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Panel de Control Administrativo</h4>
            <p class="text-muted">Vista general del sistema y estadísticas de la veterinaria</p>
        </div>
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.adoptions.create') }}"
                class="btn text-white fw-bold d-flex align-items-center"
                style="background-color: #65a34e; border-radius: 8px; padding: 10px 16px;">
                <i class="bi bi-plus-circle me-2"></i>Crear Mascota en Adopción
            </a>
        </div>

        <div class="row g-3 mb-4">
            <x-stat-card icon="bi-person" title="Usuarios del Sistema" value="{{ $totalUsers }}"
                subtitle="{{ $totalDoctors }} doctores &bull; {{ $totalAdmins }} admins" />
            <x-stat-card icon="bi-person-check" title="Clientes Activos" value="{{ $totalClients }}"
                subtitle="Registrados en el sistema" />
            <x-stat-card icon="bi-calendar-event" title="Consultas Esta Semana" value="{{ $consultasSemana }}"
                subtitle="{{ $consultasHoy }} registradas hoy" />
            <x-stat-card icon="bi-graph-up-arrow" title="Crecimiento"
                value="{{ $growthPercentage >= 0 ? '+' . $growthPercentage : $growthPercentage }}%"
                subtitle="consultas vs mes anterior" />
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-activity text-pet-green me-2"></i>
                            <h6 class="fw-bold mb-0">Actividad Reciente del Sistema</h6>
                        </div>
                        <p class="text-muted small mb-4">Últimas acciones en la plataforma</p>

                        <div class="d-flex flex-column gap-3">
                            @forelse($recentActivities as $activity)
                                <div class="border rounded-3 p-3 d-flex align-items-center">
                                    <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                        <i class="bi {{ $activity['icon'] }} text-pet-green"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold" style="font-size: 0.95rem;">{{ $activity['title'] }}</h6>
                                        <p class="text-muted small mb-0">{{ $activity['description'] }}<br><span
                                                style="font-size: 0.75rem;">{{ optional($activity['time'])->diffForHumans() ?? 'Sin fecha' }}</span></p>
                                    </div>
                                </div>
                            @empty
                                <div class="border rounded-3 p-3 text-muted">
                                    Aún no hay actividad reciente para mostrar.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agenda del Día -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-calendar-day text-pet-green me-2"></i>
                            <h6 class="fw-bold mb-0">Agenda del Día</h6>
                        </div>
                        <p class="text-muted small mb-4">Citas programadas para hoy en la clínica</p>

                        <div class="d-flex flex-column gap-3">
                            @forelse(($agendaHoy ?? collect()) as $cita)
                                <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">
                                            {{ $cita->pet?->getName() ?? 'Mascota' }}
                                        </h6>
                                        <small class="text-muted d-block">Propietario: {{ $cita->pet?->owner?->getName() ?? 'Sin propietario' }}</small>
                                        <small class="text-muted d-block">Médico: Dr. {{ $cita->doctor?->getName() ?? 'No asignado' }}</small>
                                        <small class="text-muted d-block">Motivo: {{ $cita->getReason() ?: 'No especificado' }}</small>
                                        <small class="text-muted d-block fw-medium mt-1">
                                            <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($cita->getStartTime())->format('g:i A') }} - {{ \Carbon\Carbon::parse($cita->getEndTime())->format('g:i A') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-pet-green btn-sm px-3 rounded-3">Ver Calendario</a>
                                </div>
                            @empty
                                <div class="border rounded-3 p-4 bg-light text-center">
                                    <p class="mb-0 text-muted">No hay citas programadas para hoy.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection