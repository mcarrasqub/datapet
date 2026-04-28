@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4 py-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">
                <strong>No se pudo completar la operación:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Volver a Clientes -->
        <div class="mb-3">
            <a href="{{ route('clients.index') }}"
                class="btn btn-outline-pet-green bg-white text-pet-green-dark border-pet-green-dark fw-medium px-3 py-1 rounded-3 d-inline-flex align-items-center">
                <i class="bi bi-chevron-left me-1"></i> Volver a Clientes
            </a>
        </div>

        @if($selectedPet)
            <div class="row g-4">
                <!-- MENU LATERAL IZQUIERDO -->
                <div class="col-md-2 min-w-250">
                    <div class="card border-0 shadow rounded-4">
                        <div class="list-group list-group-flush rounded-4 py-2 px-2 fs-90">
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center active fw-medium py-3 px-3 rounded-3 mb-1 bg-pet-green-dark text-white">
                                <span><i class="bi bi-file-earmark-medical me-3"></i>Historia</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-stethoscope me-3"></i>Consultas</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalRecords) }}</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-droplet me-3"></i>Vacunaciones</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-capsule me-3"></i>Fórmulas médicas</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-bug me-3"></i>Desparasitaciones</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-hospital me-3"></i>Hospitalizaciones/a...</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-scissors me-3"></i>Cirugías/procedimie...</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-card-text me-3"></i>Órdenes</span>
                            </a>
                            <a href="#examenes"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-activity me-3"></i>Exámenes de laboratorio</span>
                                @if(count($medicalExams) > 0)
                                    <span class="badge bg-pet-green rounded-pill">{{ count($medicalExams) }}</span>
                                @endif
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-image me-3"></i>Imágenes diagnósticas</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-house-door me-3"></i>Guardería</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-journal-check me-3"></i>Seguimientos</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-file-earmark me-3"></i>Documentos</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary">
                                <span><i class="bi bi-send me-3"></i>Remisiones</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 text-secondary">
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
                                            <img src="{{ asset('storage/' . $selectedPet->getPhoto()) }}" alt="Foto"
                                                class="rounded-circle object-fit-cover shadow-sm avatar-140">
                                        @else
                                            <div
                                                class="bg-light text-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold avatar-140 fs-200">
                                                {{ strtoupper(substr($selectedPet->getName(), 0, 2)) }}
                                            </div>
                                        @endif
                                        <button
                                            class="btn btn-pet-green btn-sm rounded-circle position-absolute avatar-edit-btn">
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
                                            <span
                                                class="fw-medium text-dark">{{ $selectedPet->getBreed() ?? 'No especificada' }}</span>
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
                                            <span
                                                class="fw-medium text-dark">{{ $selectedPet->getWeight() ? $selectedPet->getWeight() . ' Kilogramos' : 'N/A' }}</span>
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
                                            <span
                                                class="fw-medium text-dark">{{ $selectedPet->getAge() ? $selectedPet->getAge() . ' años' : 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block mb-1 fs-75">Fallecido:</small>
                                            <span
                                                class="fw-medium text-dark">{{ strtolower(trim($selectedPet->getNotes() ?? '')) === 'fallecido' ? 'Sí' : 'No' }}</span>
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
                                <button
                                    class="btn btn-pet-green text-white rounded-pill flex-grow-1 fw-medium py-2 shadow-sm border-0 fs-90">
                                    <i class="bi bi-stethoscope me-2"></i> Visitas/Consultas
                                </button>
                                <button class="btn text-secondary rounded-pill flex-grow-1 fw-medium py-2 border-0 fs-90">
                                    <i class="bi bi-journal-text me-2"></i> Kardex
                                </button>
                                <button class="btn text-secondary rounded-pill flex-grow-1 fw-medium py-2 border-0 fs-90">
                                    <i class="bi bi-capsule me-2"></i> Vacunas
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                                <h6 class="fw-bold mb-0 text-dark">Vacunación</h6>
                                <span class="badge bg-light text-secondary border">{{ count($vaccinations) }}</span>
                            </div>

                            <form action="{{ route('vaccinations.store', $selectedPet) }}" method="POST" class="row g-3 mb-4">
                                @csrf
                                <div class="col-md-5">
                                    <label for="vaccine_type" class="form-label">Tipo de vacuna</label>
                                    <input type="text" name="vaccine_type" id="vaccine_type" value="{{ old('vaccine_type') }}" class="form-control @error('vaccine_type') is-invalid @enderror" placeholder="Ej: Antirrábica" required>
                                    @error('vaccine_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="vaccinated_at" class="form-label">Fecha de aplicación</label>
                                    <input type="date" name="vaccinated_at" id="vaccinated_at" value="{{ old('vaccinated_at') }}" class="form-control @error('vaccinated_at') is-invalid @enderror" required>
                                    @error('vaccinated_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="next_due_date" class="form-label">Próxima dosis</label>
                                    <input type="date" name="next_due_date" id="next_due_date" value="{{ old('next_due_date') }}" class="form-control @error('next_due_date') is-invalid @enderror">
                                    @error('next_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Observaciones opcionales">{{ old('notes') }}</textarea>
                                    @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-pet-green text-white px-4">Registrar vacuna</button>
                                </div>
                            </form>

                            <div class="d-flex flex-column gap-3 mb-4">
                                @forelse($vaccinations as $vaccination)
                                    <div class="border rounded-4 p-4 bg-light">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $vaccination->vaccine_type }}</div>
                                                <div class="text-secondary fs-90">Aplicada: {{ $vaccination->vaccinated_at->format('Y-m-d') }}</div>
                                                <div class="text-secondary fs-90">Siguiente dosis: {{ $vaccination->next_due_date ? $vaccination->next_due_date->format('Y-m-d') : 'No programada' }}</div>
                                                <div class="text-secondary fs-90">Registrada por: Dr(a). {{ optional($vaccination->doctor)->name }} {{ optional($vaccination->doctor)->lastname }}</div>
                                            </div>
                                            @if($vaccination->notes)
                                                <span class="badge bg-pet-green-10 text-pet-green-dark rounded-pill">Con notas</span>
                                            @endif
                                        </div>
                                        @if($vaccination->notes)
                                            <div class="mt-3 text-secondary fs-90">{{ $vaccination->notes }}</div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted bg-light rounded-4">
                                        No hay vacunas registradas para esta mascota.
                                    </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold mb-0 text-dark">Historial de Consultas</h6>
                                <a href="{{ route('medical_records.create', $selectedPet->getId()) }}"
                                    class="btn btn-pet-green text-white btn-sm px-3 rounded-3 fw-medium">
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
                                            <span class="text-dark fw-medium fs-90">Dr(a). {{ $record->doctor->getName() }}
                                                {{ $record->doctor->getLastname() }}</span>
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
                                            <span class="text-dark fw-medium fs-85">Observación clínica:</span><br>
                                            <span class="text-secondary fs-85">{{ $record->getObservation() ?? 'Ninguna' }}</span>
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <form action="{{ route('clinical_observations.store', $record) }}" method="POST" class="mb-3">
                                                @csrf
                                                <label for="observation-{{ $record->getId() }}" class="form-label fw-medium fs-85 mb-2">Nueva observación</label>
                                                <textarea id="observation-{{ $record->getId() }}" name="observation" rows="3"
                                                          class="form-control form-control-sm @error('observation') is-invalid @enderror"
                                                          placeholder="Escribe un hallazgo, evolución o conducta clínica" required></textarea>
                                                @error('observation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                <div class="text-end mt-2">
                                                    <button type="submit" class="btn btn-pet-green btn-sm text-white">
                                                        <i class="bi bi-plus-circle me-1"></i>Guardar observación
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="text-dark fw-medium fs-85">Observaciones clínicas</span>
                                                <span class="badge bg-light text-secondary border">
                                                    {{ count($record->observations) }}
                                                </span>
                                            </div>

                                            @forelse($record->observations->sortByDesc('created_at') as $observation)
                                                <div class="bg-light rounded-4 p-3 mb-2 border">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-muted fw-medium">
                                                            {{ $observation->created_at?->format('Y-m-d H:i') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            Dr(a). {{ $observation->doctor?->getName() }} {{ $observation->doctor?->getLastname() }}
                                                        </small>
                                                    </div>
                                                    <div class="text-secondary fs-85">
                                                        {{ $observation->getObservation() }}
                                                    </div>
                                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                                        <a href="{{ route('clinical_observations.edit', $observation) }}" class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-pencil me-1"></i>Editar
                                                        </a>
                                                        <form action="{{ route('clinical_observations.destroy', $observation) }}" method="POST" onsubmit="return confirm('¿Eliminar esta observación?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="bi bi-trash me-1"></i>Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted fs-85">
                                                    No hay observaciones clínicas registradas para esta consulta.
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="position-absolute top-15-px right-15-px">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('medical_records.edit', $record->getId()) }}"
                                                    class="btn text-secondary border-0"><i class="bi bi-pencil"></i></a>
                                                <form action="{{ route('medical_records.destroy', $record->getId()) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn text-danger border-0"
                                                        onclick="return confirm('¿Eliminar este registro?')"><i
                                                            class="bi bi-trash"></i></button>
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

                    <div class="card border-0 shadow rounded-4 mt-4 exam-card" id="examenes">
                        <div class="card-body p-4 p-lg-5">
                            <div
                                class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4 pb-2 border-bottom">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                                        <i class="bi bi-journal-medical me-2 text-pet-green-dark"></i>Exámenes y archivos
                                        médicos
                                    </h5>
                                    <p class="text-muted mb-0 fs-90">Gestiona resultados clínicos y documentos adjuntos de la
                                        mascota.</p>
                                </div>
                                <div class="exam-counter-pill">
                                    <span class="fw-semibold me-2">Total</span>
                                    <span class="badge bg-pet-green rounded-pill px-3 py-2">{{ count($medicalExams) }}</span>
                                </div>
                            </div>

                            <div class="exam-table-wrap mb-4">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 exam-table">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Categoría</th>
                                                <th>Fecha examen</th>
                                                <th>Fecha carga</th>
                                                <th>Subido por</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($medicalExams as $exam)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold text-dark">
                                                            {{ $exam->title ?? $exam->original_name }}</div>
                                                        <small class="text-muted">{{ $exam->original_name }}</small>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-pet-green-10 text-pet-green-dark fw-semibold px-3 py-2 rounded-pill">
                                                            {{ $exam->category ?? 'Sin categoría' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $exam->uploaded_at ? $exam->uploaded_at->format('Y-m-d H:i') : 'N/A' }}
                                                    </td>
                                                    <td>{{ optional($exam->uploader)->name ? $exam->uploader->name . ' ' . $exam->uploader->lastname : 'N/A' }}
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-inline-flex gap-2">
                                                            <a href="{{ route('medical_exams.view', $exam->id) }}"
                                                                class="btn btn-sm btn-outline-secondary px-3"
                                                                target="_blank">Ver</a>
                                                            <a href="{{ route('medical_exams.download', $exam->id) }}"
                                                                class="btn btn-sm btn-pet-green text-white px-3">Descargar</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5">
                                                        <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                                        <span class="text-muted">No hay exámenes cargados para esta mascota.</span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if(in_array(auth()->user()->role, ['admin', 'doctor']))
                                <div class="exam-upload-panel p-4 p-lg-4 rounded-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-cloud-upload text-pet-green-dark fs-5 me-2"></i>
                                        <h6 class="fw-bold mb-0">Cargar nuevos exámenes</h6>
                                    </div>

                                    <form action="{{ route('medical_exams.store', $selectedPet->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row g-3 g-lg-4 mb-2">
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium">Título</label>
                                                <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                                    placeholder="Ej: Hemograma completo">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium">Categoría</label>
                                                <input type="text" name="category" class="form-control"
                                                    value="{{ old('category') }}" placeholder="Ej: Laboratorio">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium">Fecha del examen</label>
                                                <input type="date" name="exam_date" class="form-control"
                                                    value="{{ old('exam_date') }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-medium">Descripción</label>
                                                <textarea name="description" class="form-control" rows="3"
                                                    placeholder="Notas opcionales del examen">{{ old('description') }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">Vincular a consulta (opcional)</label>
                                                <select name="medical_record_id" class="form-select">
                                                    <option value="">Sin vínculo específico</option>
                                                    @foreach($medicalRecords as $record)
                                                        <option value="{{ $record->id }}" @selected((string) old('medical_record_id') === (string) $record->id)>
                                                            {{ $record->visited_at->format('Y-m-d') }} - {{ $record->reason }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">Archivos</label>
                                                <input type="file" name="files[]" class="form-control" multiple required
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <small class="text-muted d-block mt-2">Formatos permitidos: PDF, JPG, JPEG, PNG.
                                                    Máximo 5MB por archivo.</small>
                                            </div>
                                        </div>

                                        <div class="pt-3">
                                            <button type="submit" class="btn btn-pet-green text-white px-4 py-2 fw-medium">
                                                <i class="bi bi-upload me-2"></i>Subir exámenes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
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