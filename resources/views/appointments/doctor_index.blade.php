@extends('layouts.dashboard')

@section('title', 'Mi Agenda')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-dark fw-bold"><i class="bi bi-calendar-event me-2"></i>Agenda del Día</h2>
            <p class="text-muted">Visualiza tus citas y consultas programadas.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal View Appointment (Read-Only) -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-pet-green text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="appointmentModalLabel">Detalle de la Cita</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small mb-1">PACIENTE (MASCOTA)</label>
                    <p class="fs-5 mb-0" id="pet_name"></p>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold text-muted small mb-1">HORA INICIO</label>
                        <p class="fs-6 mb-0" id="start_time"></p>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-muted small mb-1">HORA FIN</label>
                        <p class="fs-6 mb-0" id="end_time"></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small mb-1">ESTADO</label>
                    <p class="fs-6 mb-0"><span class="badge" id="status"></span></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small mb-1">MOTIVO / NOTAS</label>
                    <p class="fs-6 mb-0" id="reason"></p>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
    window.DoctorCalendarConfig = {
        eventsUrl: '{{ route("doctor.appointments.events") }}'
    };
</script>
<script src="{{ asset('js/doctor-calendar.js') }}"></script>
@endpush
