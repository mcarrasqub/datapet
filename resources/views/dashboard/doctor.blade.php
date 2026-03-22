@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Panel Médico</h4>
        <p class="text-muted">Resumen de tu jornada y pacientes del día</p>
    </div>

    <div class="row g-3 mb-4">
        <x-stat-card icon="bi-calendar-check" title="Citas Hoy" value="4" subtitle="1 urgentes" />
        <x-stat-card icon="bi-people" title="Pacientes Totales" value="{{ $totalPatients ?? 0 }}" subtitle="Bajo tu atención" />
        <x-stat-card icon="bi-file-earmark-medical" title="Exámenes Pendientes" value="2" subtitle="Por revisar" />
        <x-stat-card icon="bi-graph-up-arrow" title="Consultas Este Mes" value="{{ $consultasMes ?? 0 }}" subtitle="+12% vs mes anterior" />
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
                    <p class="text-muted small mb-4">domingo, 22 de marzo de 2026</p>

                    <div class="d-flex flex-column gap-3">
                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                    <i class="bi bi-clock text-pet-green"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Kiwi</h6>
                                    <small class="text-muted d-block">María González</small>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Control de Rutina</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-pet-green rounded-pill px-3 py-1 mb-1 d-block">10:00 AM</span>
                                <span class="badge bg-pet-green px-2 py-1" style="font-size: 0.7rem;">Confirmada</span>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                    <i class="bi bi-clock text-pet-green"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Copo</h6>
                                    <small class="text-muted d-block">María González</small>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Revisión Dental</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-pet-green rounded-pill px-3 py-1 mb-1 d-block">11:30 AM</span>
                                <span class="badge bg-pet-green px-2 py-1" style="font-size: 0.7rem;">Confirmada</span>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                    <i class="bi bi-clock text-pet-green"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Rex</h6>
                                    <small class="text-muted d-block">Carlos Pérez</small>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Control de Temperatura</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-pet-green rounded-pill px-3 py-1 mb-1 d-block">2:00 PM</span>
                                <span class="badge bg-secondary px-2 py-1" style="font-size: 0.7rem;">Pendiente</span>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-pet-green-10 icon-circle rounded-circle me-3">
                                    <i class="bi bi-clock text-pet-green"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Pipo</h6>
                                    <small class="text-muted d-block">Ana Martínez</small>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Emergencia</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-pet-green rounded-pill px-3 py-1 mb-1 d-block">3:30 PM</span>
                                <span class="badge bg-danger px-2 py-1" style="font-size: 0.7rem;">Urgente</span>
                            </div>
                        </div>
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
                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Kiwi</h6>
                                <small class="text-muted d-block">María González</small>
                                <small class="text-muted d-block">Radiografía Ala</small>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Subido: 15 Ene 2026</small>
                            </div>
                            <button class="btn btn-pet-green btn-sm px-3 rounded-3">Revisar</button>
                        </div>

                        <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Rex</h6>
                                <small class="text-muted d-block">Ana Martínez</small>
                                <small class="text-muted d-block">Radiografía Columna</small>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Subido: 3 Feb 2026</small>
                            </div>
                            <button class="btn btn-pet-green btn-sm px-3 rounded-3">Revisar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
