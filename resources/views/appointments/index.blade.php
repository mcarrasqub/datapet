@extends('layouts.dashboard')

@section('title', 'Calendario de Citas')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-dark fw-bold"><i class="bi bi-calendar-check me-2"></i>Gestión de Citas</h2>
            <p class="text-muted">Organiza las consultas médicas de forma eficiente.</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-pet-green rounded-pill" onclick="openCreateModal()">
                <i class="bi bi-plus-circle me-1"></i> Nueva Cita
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal Create/Edit Appointment -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-pet-green text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="appointmentModalLabel">Gestionar Cita</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="appointmentForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="doctor_id" class="form-label fw-medium">Doctor Asignado</label>
                        <select class="form-select" id="doctor_id" name="doctor_id" required>
                            <option value="">Seleccione un doctor...</option>
                            @foreach($viewData['doctors'] as $doctor)
                                <option value="{{ $doctor->getId() }}">{{ $doctor->getName() }} {{ $doctor->getLastname() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="pet_id" class="form-label fw-medium">Paciente (Mascota)</label>
                        <select class="form-select" id="pet_id" name="pet_id" required>
                            <option value="">Seleccione una mascota...</option>
                            @foreach($viewData['pets'] as $pet)
                                <option value="{{ $pet->getId() }}">{{ $pet->getName() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="date" class="form-label fw-medium">Fecha</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label fw-medium">Hora Inicio</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label fw-medium">Hora Fin</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="mb-3" id="statusGroup" style="display: none;">
                        <label for="status" class="form-label fw-medium">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="scheduled">Programada</option>
                            <option value="canceled">Cancelada</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-medium">Motivo / Notas (Opcional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4" id="btnDelete" style="display: none;" onclick="deleteAppointment()">Eliminar Físicamente</button>
                    <button type="submit" class="btn btn-pet-green rounded-pill px-4">Guardar Cita</button>
                </div>
            </form>
            
            <form id="deleteForm" method="POST" action="" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
    window.CalendarConfig = {
        eventsUrl: '{{ route("appointments.index") }}',
        storeUrl: '{{ route("appointments.store") }}'
    };
</script>
<script src="{{ asset('js/admin-calendar.js') }}"></script>
@endpush
