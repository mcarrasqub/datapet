@extends($layout)

@section('title', 'Notificaciones - DataPet')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="bg-pet-green-10 p-2 rounded-3 me-3 text-pet-green">
                        <i class="bi bi-bell fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">Centro de Notificaciones</h3>
                        <p class="text-muted small mb-0">Gestiona las alertas y avisos de vacunación de tus mascotas</p>
                    </div>
                </div>
                @if(isset($viewData['reminders']) && $viewData['reminders']->count() > 0)
                    <span class="badge bg-pet-green text-white rounded-pill px-3 py-2 fw-bold">
                        {{ $viewData['reminders']->count() }} {{ $viewData['reminders']->count() == 1 ? 'Mensaje' : 'Mensajes' }}
                    </span>
                @endif
            </div>

            <!-- Notifications List -->
            @if(isset($viewData['reminders']) && $viewData['reminders']->count() > 0)
                <div class="d-flex flex-column gap-3">                    @foreach($viewData['reminders'] as $reminder)
                        @php
                            $isVaccine = !is_null($reminder->vaccination_id);
                            $borderLeftColor = $isVaccine 
                                ? ($reminder->status === 'completed' ? '#76a75d' : '#f0ad4e')
                                : '#3b82f6';
                        @endphp
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden position-relative transition-hover" style="border-left: 5px solid {{ $borderLeftColor }} !important;">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start">
                                        <!-- Icon and Mascot Circle -->
                                        <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            @if($isVaccine)
                                                <span class="fs-4">🐾</span>
                                            @else
                                                <span class="fs-4">📋</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($isVaccine)
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;">
                                                    Alerta de Vacuna para {{ $reminder->pet?->getName() }}
                                                </h6>
                                                <p class="text-secondary small mb-2">
                                                    Tu mascota <strong>{{ $reminder->pet?->getName() }}</strong> ({{ $reminder->pet?->getSpecies() }}) tiene programada su vacuna contra <strong>{{ $reminder->vaccination?->vaccine_type }}</strong> para el próximo <strong>{{ $reminder->vaccination?->next_due_date?->format('Y-m-d') }}</strong>.
                                                </p>
                                            @else
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;">
                                                    Nueva Orden Clínica para {{ $reminder->pet?->getName() }}
                                                </h6>
                                                <p class="text-secondary small mb-2">
                                                    {{ $reminder->message }}
                                                </p>
                                            @endif
                                            
                                            <!-- Status Badges -->
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                @if($isVaccine)
                                                    @if($reminder->status === 'completed')
                                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-10 rounded-pill px-2 py-1 fs-85">
                                                            <i class="bi bi-check2-circle me-1"></i> Atendido por Clínica
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-10 rounded-pill px-2 py-1 fs-85">
                                                            <i class="bi bi-clock-history me-1"></i> Pendiente de Aplicar
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-10 rounded-pill px-2 py-1 fs-85">
                                                        <i class="bi bi-clipboard-pulse me-1"></i> Orden Emitida
                                                    </span>
                                                @endif
                                                <small class="text-muted fs-80"><i class="bi bi-calendar-event me-1"></i> Notificado el {{ $reminder->created_at->format('d/m/Y') }}</small>
                                            </div>

                                            @if(!$isVaccine)
                                                <div class="mt-2">
                                                    <a href="{{ route('pets.exams', ['pet_id' => $reminder->pet_id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 fs-85 transition-all">
                                                        <i class="bi bi-eye"></i> Ver Orden y Cargar Examen
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Button (Ocultar) -->
                                    <div>
                                        <form action="{{ route('reminders.dismiss', $reminder) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-link text-muted p-2 rounded-circle hover-bg-light transition-all" title="Ocultar Notificación">
                                                <i class="bi bi-x-lg fs-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5 px-4 bg-white rounded-3 shadow-sm border mt-2">
                    <div class="bg-light rounded-circle p-4 d-inline-flex mb-4 text-muted align-items-center justify-content-center" style="width: 90px; height: 90px;">
                        <i class="bi bi-bell-slash fs-1 text-secondary"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¡Todo al día!</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 380px;">
                        No tienes recordatorios de vacunas pendientes u otras notificaciones activas en este momento.
                    </p>
                    <a href="{{ route('home.index') }}" class="btn btn-pet-green rounded-pill px-4 fw-medium shadow-sm">
                        <i class="bi bi-house-door me-2"></i> Volver al Inicio
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05) !important;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        color: #dc3545 !important;
    }
    .transition-all {
        transition: all 0.2s ease;
    }
</style>
@endsection
