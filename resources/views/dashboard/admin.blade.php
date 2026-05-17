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
                        <p class="text-muted small mb-4">Bloque temporal de visualización (módulo en construcción)</p>

                        <div class="d-flex flex-column gap-3">
                            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                        <i class="bi bi-stethoscope text-pet-green"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Kiwi <span
                                                class="text-muted fw-normal">- María González</span></h6>
                                        <small class="text-muted">Control de Rutina</small>
                                    </div>
                                </div>
                                <span class="badge bg-pet-green rounded-pill px-3 py-2">10:00 AM</span>
                            </div>

                            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                        <i class="bi bi-stethoscope text-pet-green"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Copo <span
                                                class="text-muted fw-normal">- María González</span></h6>
                                        <small class="text-muted">Revisión Dental</small>
                                    </div>
                                </div>
                                <span class="badge bg-pet-green rounded-pill px-3 py-2">11:30 AM</span>
                            </div>

                            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                        <i class="bi bi-stethoscope text-pet-green"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Rex <span
                                                class="text-muted fw-normal">- Carlos Pérez</span></h6>
                                        <small class="text-muted">Control de Temperatura</small>
                                    </div>
                                </div>
                                <span class="badge bg-pet-green rounded-pill px-3 py-2">2:00 PM</span>
                            </div>

                            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                        <i class="bi bi-stethoscope text-pet-green"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Pipo <span
                                                class="text-muted fw-normal">- Ana Martínez</span></h6>
                                        <small class="text-muted">Emergencia</small>
                                    </div>
                                </div>
                                <span class="badge bg-pet-green rounded-pill px-3 py-2">3:30 PM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection