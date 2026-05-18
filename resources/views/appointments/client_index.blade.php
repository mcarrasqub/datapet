@extends($layout)

@section('title', 'Mis Citas - DataPet')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="bg-pet-green-10 p-2 rounded-3 me-3 text-pet-green" style="background-color: #e6f0e3;">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">Mis Citas</h3>
                        <p class="text-muted small mb-0">Gestiona y revisa las citas médicas de tus mascotas</p>
                    </div>
                </div>
                <div>
                    @php
                        // Obtenemos el número desde el archivo .env, configurado en config/app.php
                        $clinicWhatsApp = config('app.veterinary_phone', '+573000000000'); 
                        $whatsappMessage = urlencode('Hola, me gustaría agendar una cita para mi mascota.');
                    @endphp
                    <a href="https://wa.me/{{ $clinicWhatsApp }}?text={{ $whatsappMessage }}" target="_blank" class="btn btn-pet-green text-white rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center" style="background-color: #65a34e; border-color: #65a34e;">
                        <i class="bi bi-whatsapp me-2"></i> Solicitar Cita
                    </a>
                </div>
            </div>

            <!-- Appointments List -->
            @if(isset($viewData['appointments']) && $viewData['appointments']->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($viewData['appointments'] as $appointment)
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden position-relative transition-hover" style="border-left: 5px solid {{ $appointment->getStatus() === 'scheduled' ? '#76a75d' : '#dc3545' }} !important;">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start w-100">
                                        <!-- Icon and Mascot Circle -->
                                        <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <span class="fs-4">📅</span>
                                        </div>
                                        <div class="w-100">
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;">
                                                Cita para {{ $appointment->pet?->getName() }}
                                            </h6>
                                            <div class="row mt-2">
                                                <div class="col-sm-6 mb-2 mb-sm-0">
                                                    <p class="text-secondary small mb-1">
                                                        <i class="bi bi-clock me-1"></i> <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->getDate())->format('d/m/Y') }}
                                                    </p>
                                                    <p class="text-secondary small mb-1">
                                                        <i class="bi bi-alarm me-1"></i> <strong>Hora:</strong> {{ \Carbon\Carbon::parse($appointment->getStartTime())->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->getEndTime())->format('H:i') }}
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="text-secondary small mb-1">
                                                        <i class="bi bi-person-badge me-1"></i> <strong>Doctor:</strong> {{ $appointment->doctor?->getName() }} {{ $appointment->doctor?->getLastname() }}
                                                    </p>
                                                    @if($appointment->getReason())
                                                        <p class="text-secondary small mb-1 text-truncate" title="{{ $appointment->getReason() }}">
                                                            <i class="bi bi-card-text me-1"></i> <strong>Motivo:</strong> {{ $appointment->getReason() }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Status Badges -->
                                            <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                                                @if($appointment->getStatus() === 'scheduled')
                                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-10 rounded-pill px-2 py-1 fs-85">
                                                        <i class="bi bi-calendar-check me-1"></i> Programada
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-10 rounded-pill px-2 py-1 fs-85">
                                                        <i class="bi bi-x-circle me-1"></i> Cancelada
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
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
                        <i class="bi bi-calendar-x fs-1 text-secondary"></i>
                    </div>
                    <h5 class="fw-bold mb-2">No hay citas registradas</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 380px;">
                        Actualmente no tienes citas programadas para tus mascotas. Puedes solicitar una cita con nuestra veterinaria usando el botón de WhatsApp.
                    </p>
                    <a href="{{ route('home.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-medium shadow-sm">
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
    .text-pet-green {
        color: #65a34e !important;
    }
    .btn-pet-green:hover {
        background-color: #558a42 !important;
        border-color: #558a42 !important;
    }
</style>
@endsection
