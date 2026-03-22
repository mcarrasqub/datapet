@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Panel de Control Administrativo</h4>
        <p class="text-muted">Vista general del sistema y estadísticas de la veterinaria</p>
    </div>

    <div class="row g-3 mb-4">
        <x-stat-card icon="bi-person" title="Usuarios del Sistema" value="{{ $totalUsers }}" subtitle="{{ $totalDoctors }} doctores &bull; {{ $totalAdmins }} admins" />
        <x-stat-card icon="bi-person-check" title="Clientes Activos" value="{{ $totalClients }}" subtitle="+12 este mes" />
        <x-stat-card icon="bi-calendar-event" title="Citas Esta Semana" value="45" subtitle="8 programadas hoy" />
        <x-stat-card icon="bi-graph-up-arrow" title="Crecimiento" value="+24%" subtitle="vs mes anterior" />
    </div>

    <div class="row g-3">
        <!-- Actividad Reciente -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-activity text-pet-green me-2"></i>
                        <h6 class="fw-bold mb-0">Actividad Reciente del Sistema</h6>
                    </div>
                    <p class="text-muted small mb-4">Últimas acciones en la plataforma</p>

                    <div class="d-flex flex-column gap-3">
                        <div class="border rounded-3 p-3 d-flex align-items-center">
                            <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                <i class="bi bi-person-plus text-pet-green"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="font-size: 0.95rem;">Nuevo doctor registrado</h6>
                                <p class="text-muted small mb-0">Dra. Patricia López se unió al equipo<br><span style="font-size: 0.75rem;">Hace 2 horas</span></p>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 d-flex align-items-center">
                            <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                <i class="bi bi-person-add text-pet-green"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="font-size: 0.95rem;">Nuevo cliente registrado</h6>
                                <p class="text-muted small mb-0">Pedro Ruiz registró a su hurón<br><span style="font-size: 0.75rem;">Hace 5 horas</span></p>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 d-flex align-items-center">
                            <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                <i class="bi bi-calendar-check text-pet-green"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="font-size: 0.95rem;">Citas programadas</h6>
                                <p class="text-muted small mb-0">8 citas agendadas para mañana<br><span style="font-size: 0.75rem;">Hace 1 día</span></p>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 d-flex align-items-center">
                            <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                <i class="bi bi-gear text-pet-green"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="font-size: 0.95rem;">Configuración actualizada</h6>
                                <p class="text-muted small mb-0">Se agregó nueva especialidad: Anfibios<br><span style="font-size: 0.75rem;">Hace 2 días</span></p>
                            </div>
                        </div>
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
                    <p class="text-muted small mb-4">domingo, 22 de marzo de 2026</p>

                    <div class="d-flex flex-column gap-3">
                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                    <i class="bi bi-stethoscope text-pet-green"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Kiwi <span class="text-muted fw-normal">- María González</span></h6>
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
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Copo <span class="text-muted fw-normal">- María González</span></h6>
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
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Rex <span class="text-muted fw-normal">- Carlos Pérez</span></h6>
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
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Pipo <span class="text-muted fw-normal">- Ana Martínez</span></h6>
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
