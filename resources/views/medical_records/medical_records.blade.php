@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Volver a Clientes -->
    <div class="mb-3">
        <a href="{{ route('clients.index') }}" class="btn btn-outline-pet-green bg-white text-pet-green-dark border-pet-green-dark fw-medium px-3 py-1 rounded-3 d-inline-flex align-items-center">
            <i class="bi bi-chevron-left me-1"></i> Volver a Clientes
        </a>
    </div>

    @if($selectedPet)
    <div class="row g-4">
        <!-- MENU LATERAL IZQUIERDO -->
        <div class="col-md-2 min-w-250">
            <div class="card border-0 shadow rounded-4">
                <div class="list-group list-group-flush rounded-4 py-2 px-2 fs-90">
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center active fw-medium py-3 px-3 rounded-3 mb-1 bg-pet-green-dark text-white">
                        <span><i class="bi bi-file-earmark-medical me-3"></i>Historia</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-stethoscope me-3"></i>Consultas</span>
                        <span class="badge bg-pet-green rounded-pill">{{ count($medicalRecords) }}</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-droplet me-3"></i>Vacunaciones</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-capsule me-3"></i>Fórmulas médicas</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-bug me-3"></i>Desparasitaciones</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-hospital me-3"></i>Hospitalizaciones/a...</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-scissors me-3"></i>Cirugías/procedimie...</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-card-text me-3"></i>Órdenes</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-activity me-3"></i>Exámenes de laboratorio</span>
                        @if(count($medicalRecords) > 0)
                            <span class="badge bg-pet-green rounded-pill">2</span>
                        @endif
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-image me-3"></i>Imágenes diagnósticas</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-house-door me-3"></i>Guardería</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-journal-check me-3"></i>Seguimientos</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-file-earmark me-3"></i>Documentos</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                        <span><i class="bi bi-send me-3"></i>Remisiones</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 text-secondary">
                        <span><i class="bi bi-calendar3 me-3"></i>Citas</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- AREA PRINCIPAL -->
        <div class="col">
            <!-- TARJETA INFO MASCOTA -->
            <div class="card border-0 shadow rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row gx-5 border-bottom pb-4 mb-4">
                        <div class="col-md-2 d-flex justify-content-center align-items-center position-relative">
                            <div class="position-relative">
                                @if($selectedPet->getPhoto())
                                    <img src="{{ asset('storage/' . $selectedPet->getPhoto()) }}" alt="Foto" class="rounded-circle object-fit-cover shadow-sm avatar-140">
                                @else
                                    <div class="bg-light text-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold avatar-140 fs-200">
                                        {{ strtoupper(substr($selectedPet->getName(), 0, 2)) }}
                                    </div>
                                @endif
                                <button class="btn btn-pet-green btn-sm rounded-circle position-absolute avatar-edit-btn">
                                    <i class="bi bi-pencil text-white fs-80"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row gy-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Especie:</small>
                                    <span class="fw-medium text-dark">{{ $selectedPet->getSpecies() }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Raza/Subespecie:</small>
                                    <span class="fw-medium text-dark">{{ $selectedPet->getBreed() ?? 'No especificada' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Género:</small>
                                    <span class="fw-medium text-dark">{{ ucfirst($selectedPet->getGender()) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Color:</small>
                                    <span class="fw-medium text-dark">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Peso:</small>
                                    <span class="fw-medium text-dark">{{ $selectedPet->getWeight() ? $selectedPet->getWeight() . ' Kilogramos' : 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Talla:</small>
                                    <span class="fw-medium text-dark">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">E. Reproductivo:</small>
                                    <span class="fw-medium text-dark">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Edad:</small>
                                    <span class="fw-medium text-dark">{{ $selectedPet->getAge() ? $selectedPet->getAge() . ' años' : 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Fallecido:</small>
                                    <span class="fw-medium text-dark">{{ strtolower(trim($selectedPet->getNotes() ?? '')) === 'fallecido' ? 'Sí' : 'No' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Apoyo emocional:</small>
                                    <span class="fw-medium text-dark">No</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1 fs-75">Animal de servicio:</small>
                                    <span class="fw-medium text-dark">No</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos extra -->
                    <div class="row gy-3">
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Alimento:</small>
                            <span class="fw-medium text-dark">N/A</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Cantidad de alimento:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Frecuencia de alimento:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Vivienda:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Frecuencia baño:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Productos de baño:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Otras mascotas, ¿cuales?:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-1 fs-75">Último calor:</small>
                            <span class="fw-medium text-dark">N/D</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TARJETA TAB INFO (CONSULTAS, KARDEX, VACUNAS) -->
            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-4">
                    <!-- TABS Píldoras -->
                    <div class="d-flex bg-light rounded-pill p-1 mb-4 max-w-100">
                        <button class="btn btn-pet-green text-white rounded-pill flex-grow-1 fw-medium py-2 shadow-sm border-0 fs-90">
                            <i class="bi bi-stethoscope me-2"></i> Visitas/Consultas
                        </button>
                        <button class="btn text-secondary rounded-pill flex-grow-1 fw-medium py-2 border-0 fs-90">
                            <i class="bi bi-journal-text me-2"></i> Kardex
                        </button>
                        <button class="btn text-secondary rounded-pill flex-grow-1 fw-medium py-2 border-0 fs-90">
                            <i class="bi bi-capsule me-2"></i> Vacunas
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0 text-dark">Historial de Consultas</h6>
                        <a href="{{ route('medical_records.create', $selectedPet->getId()) }}" class="btn btn-pet-green text-white btn-sm px-3 rounded-3 fw-medium">
                            Nueva Consulta
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        @forelse($medicalRecords as $record)
                        <div class="border rounded-4 p-4 position-relative border-start-pet-green-dark">
                            <div class="badge bg-pet-green rounded-pill mb-3 px-3 py-2 fs-75">
                                {{ $record->getVisitedAt()->format('Y-m-d') }}
                            </div>
                            <div class="mb-2">
                                <span class="text-dark fw-medium fs-90">Dr(a). {{ $record->doctor->getName() }} {{ $record->doctor->getLastname() }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-dark fw-medium fs-85">Motivo de consulta:</span><br>
                                <span class="text-secondary fs-85">{{ $record->getReason() }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-dark fw-medium fs-85">Diagnóstico:</span><br>
                                <span class="text-secondary fs-85">{{ $record->getDiagnosis() ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-dark fw-medium fs-85">Observaciones:</span><br>
                                <span class="text-secondary fs-85">{{ $record->getNotes() ?? 'Ninguna' }}</span>
                            </div>

                            <div class="position-absolute top-15-px right-15-px">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('medical_records.edit', $record->getId()) }}" class="btn text-secondary border-0"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('medical_records.destroy', $record->getId()) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn text-danger border-0" onclick="return confirm('¿Eliminar este registro?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted bg-light rounded-4">
                            <i class="bi bi-inbox fs-1 mb-3 d-block text-secondary"></i>
                            El paciente no cuenta con consultas previas.
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info border-0 shadow-sm rounded-4 text-center mt-3 p-5">
            <i class="bi bi-info-circle mb-3 fs-3 d-block"></i>
            No se seleccionó ninguna mascota o la mascota no existe.
            <br><a href="{{ route('clients.index') }}" class="btn btn-pet-green mt-3">Volver</a>
        </div>
    @endif
</div>
@endsection
