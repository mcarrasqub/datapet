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
                            <a href="#historia"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center active fw-medium py-3 px-3 rounded-3 mb-1 bg-pet-green-dark text-white js-pet-section-link"
                                data-section="historia">
                                <span><i class="bi bi-file-earmark-medical me-3"></i>Historia</span>
                            </a>
                            <a href="#consultas"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary js-pet-section-link"
                                data-section="consultas">
                                <span><i class="bi bi-stethoscope me-3"></i>Consultas</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalRecords) }}</span>
                            </a>
                            <a href="#kardex"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary js-pet-section-link"
                                data-section="kardex">
                                <span><i class="bi bi-journal-text me-3"></i>Kardex</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($kardexEntries) }}</span>
                            </a>
                            <a href="#vacunas"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary js-pet-section-link"
                                data-section="vacunas">
                                <span><i class="bi bi-droplet me-3"></i>Vacunaciones</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($vaccinations) }}</span>
                            </a>
                            <a href="#formulas"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary js-pet-section-link"
                                data-section="formulas">
                                <span><i class="bi bi-capsule me-3"></i>Fórmulas médicas</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalFormulas) }}</span>
                            </a>
                            <a href="#ordenes"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary js-pet-section-link"
                                data-section="ordenes">
                                <span><i class="bi bi-card-text me-3"></i>Órdenes clínicas</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalOrders) }}</span>
                            </a>
                            <a href="#examenes"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 mb-1 text-secondary js-pet-section-link"
                                data-section="examenes">
                                <span><i class="bi bi-activity me-3"></i>Exámenes de laboratorio</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalExams) }}</span>
                            </a>
                            <a href="#citas"
                                class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center fw-medium py-3 px-3 rounded-3 text-secondary js-pet-section-link"
                                data-section="citas">
                                <span><i class="bi bi-calendar3 me-3"></i>Citas</span>
                                <span class="badge bg-pet-green rounded-pill">{{ count($appointments) }}</span>
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
                                            class="btn btn-pet-green btn-sm rounded-circle position-absolute avatar-edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editPetModal">
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
                                              <span class="fw-medium text-dark">{{ $selectedPet->getColor() ?? 'N/A' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">Peso:</small>
                                              <span
                                                  class="fw-medium text-dark">{{ $selectedPet->getWeight() ? $selectedPet->getWeight() . ' Kilogramos' : 'N/A' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">Talla:</small>
                                              <span class="fw-medium text-dark">{{ $selectedPet->getSize() ?? 'N/A' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">E. Reproductivo:</small>
                                              <span class="fw-medium text-dark">{{ $selectedPet->getReproductiveStatus() ?? 'N/A' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">Edad:</small>
                                              <span
                                                  class="fw-medium text-dark">{{ $selectedPet->getAge() ? $selectedPet->getAge() . ' años' : 'N/A' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">Fallecido:</small>
                                              <span
                                                  class="fw-medium text-dark">{{ $selectedPet->getIsDeceased() ? 'Sí' : 'No' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">Apoyo emocional:</small>
                                              <span class="fw-medium text-dark">{{ $selectedPet->getEmotionalSupport() ? 'Sí' : 'No' }}</span>
                                          </div>
                                          <div class="col-md-4">
                                              <small class="text-muted d-block mb-1 fs-75">Animal de servicio:</small>
                                              <span class="fw-medium text-dark">{{ $selectedPet->getServiceAnimal() ? 'Sí' : 'No' }}</span>
                                          </div>
                                      </div>
                                  </div>
                              </div>
  
                              <!-- Datos extra -->
                              <div class="row gy-3 pt-3 border-top mt-3">
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Alimento:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getDiet() ?? 'N/A' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Cantidad de alimento:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getDietQuantity() ?? 'N/D' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Frecuencia de alimento:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getDietFrequency() ?? 'N/D' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Vivienda:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getHousing() ?? 'N/D' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Frecuencia baño:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getBathFrequency() ?? 'N/D' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Productos de baño:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getBathProducts() ?? 'N/D' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Otras mascotas, ¿cuáles?:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getOtherPets() ?? 'N/D' }}</span>
                                  </div>
                                  <div class="col-md-3">
                                      <small class="text-muted d-block mb-1 fs-75">Último calor:</small>
                                      <span class="fw-medium text-dark">{{ $selectedPet->getLastHeat() ?? 'N/D' }}</span>
                                  </div>
                              </div>
                          </div>
                      </div>

                    <div class="card border-0 shadow rounded-4">
                        <div class="card-body p-4">
                            <div id="historia" class="js-pet-section">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold mb-0 text-dark">Historia clínica</h6>
                                    <span class="badge bg-light text-secondary border">Resumen general</span>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <div class="border rounded-4 p-4 h-100 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold mb-0">Consultas hechas</h6>
                                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalRecords) }}</span>
                                            </div>
                                            @forelse($medicalRecords as $record)
                                                <div class="mb-3 pb-3 border-bottom">
                                                    <div class="fw-semibold text-dark">{{ $record->visited_at->format('Y-m-d') }}</div>
                                                    <div class="text-secondary fs-90">{{ $record->reason }}</div>
                                                    <div class="text-secondary fs-90">{{ $record->getDiagnosis() ?? 'Sin diagnóstico' }}</div>
                                                </div>
                                            @empty
                                                <div class="text-muted">No hay consultas registradas.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="border rounded-4 p-4 h-100 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold mb-0">Vacunas aplicadas</h6>
                                                <span class="badge bg-pet-green rounded-pill">{{ count($vaccinations) }}</span>
                                            </div>
                                            @forelse($vaccinations as $vaccination)
                                                <div class="mb-3 pb-3 border-bottom">
                                                    <div class="fw-semibold text-dark">{{ $vaccination->vaccine_type }}</div>
                                                    <div class="text-secondary fs-90">Aplicada: {{ $vaccination->vaccinated_at->format('Y-m-d') }}</div>
                                                    <div class="text-secondary fs-90">Siguiente dosis: {{ $vaccination->next_due_date ? $vaccination->next_due_date->format('Y-m-d') : 'No programada' }}</div>
                                                </div>
                                            @empty
                                                <div class="text-muted">No hay vacunas registradas.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="border rounded-4 p-4 h-100 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold mb-0">Exámenes hechos</h6>
                                                <span class="badge bg-pet-green rounded-pill">{{ count($medicalExams) }}</span>
                                            </div>
                                            @forelse($medicalExams as $exam)
                                                <div class="mb-3 pb-3 border-bottom">
                                                    <div class="fw-semibold text-dark">{{ $exam->title ?? $exam->original_name }}</div>
                                                    <div class="text-secondary fs-90">{{ $exam->category ?? 'Sin categoría' }}</div>
                                                    <div class="text-secondary fs-90">{{ $exam->uploaded_at ? $exam->uploaded_at->format('Y-m-d') : 'N/A' }}</div>
                                                </div>
                                            @empty
                                                <div class="text-muted">No hay exámenes registrados.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="consultas" class="js-pet-section d-none">
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
                                                    <span class="badge bg-light text-secondary border">{{ count($record->observations) }}</span>
                                                </div>

                                                @forelse($record->observations->sortByDesc('created_at') as $observation)
                                                    <div class="bg-light rounded-4 p-3 mb-2 border">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <small class="text-muted fw-medium">{{ $observation->created_at?->format('Y-m-d H:i') }}</small>
                                                            <small class="text-muted">Dr(a). {{ $observation->doctor?->getName() }} {{ $observation->doctor?->getLastname() }}</small>
                                                        </div>
                                                        <div class="text-secondary fs-85">{{ $observation->getObservation() }}</div>
                                                        <div class="d-flex justify-content-end gap-2 mt-2">
                                                            <a href="{{ route('clinical_observations.edit', $observation) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Editar</a>
                                                            <form action="{{ route('clinical_observations.destroy', $observation) }}" method="POST" onsubmit="return confirm('¿Eliminar esta observación?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-muted fs-85">No hay observaciones clínicas registradas para esta consulta.</div>
                                                @endforelse
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

                            <div id="kardex" class="js-pet-section d-none">
                                <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                                    <h6 class="fw-bold mb-0 text-dark">Kardex Clínico (Mascotas Exóticas)</h6>
                                    <span class="badge bg-light text-secondary border">{{ count($kardexEntries) }}</span>
                                </div>

                                <!-- Formulario de registro -->
                                <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-file-earmark-plus text-pet-green-dark fs-5 me-2"></i>
                                        <h6 class="fw-bold mb-0 text-dark">Registrar Nueva Ficha de Kardex</h6>
                                    </div>

                                    <form action="{{ route('kardex.store', $selectedPet) }}" method="POST" id="kardex_form">
                                        @csrf
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="kardex_entry_date" class="form-label fw-semibold text-secondary">Fecha del Registro</label>
                                                <input type="date" name="entry_date" id="kardex_entry_date" class="form-control rounded-3" value="{{ now()->toDateString() }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="kardex_animal_type" class="form-label fw-semibold text-secondary">Tipo de Animal Exótico</label>
                                                <select name="animal_type" id="kardex_animal_type" class="form-select rounded-3" required>
                                                    <option value="" disabled selected>-- Seleccione Especie --</option>
                                                    <option value="huron">Hurón</option>
                                                    <option value="loro">Loro</option>
                                                    <option value="conejo">Conejo</option>
                                                    <option value="erizo">Erizo</option>
                                                    <option value="iguana">Iguana</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Campos dinámicos renderizados por JS -->
                                        <div id="kardex_fields_wrapper" class="border rounded-4 p-3 bg-white mb-3 d-none">
                                            <h6 class="fw-bold text-pet-green mb-3 fs-90"><i class="bi bi-activity me-1"></i>Parámetros Clínicos Específicos</h6>
                                            <div id="kardex_dynamic_fields" class="row g-3">
                                                <!-- Cargado por JS -->
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-pet-green text-white px-4 py-2 fw-semibold rounded-3 shadow-sm" id="kardex_submit_btn" disabled>
                                                <i class="bi bi-save me-1"></i> Guardar en Kardex
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Listado histórico -->
                                <div class="d-flex flex-column gap-3">
                                    <h6 class="fw-bold text-dark mb-1 mt-2"><i class="bi bi-clock-history me-2"></i>Historial de Registros de Kardex</h6>
                                    @forelse($kardexEntries as $entry)
                                        <div class="border rounded-4 p-4 bg-light position-relative border-start-pet-green-dark">
                                            <div class="position-absolute top-0 end-0 p-3 d-flex gap-2 align-items-center">
                                                <span class="badge bg-pet-green-dark text-white rounded-pill px-3 py-1 fs-75 text-uppercase">
                                                    {{ $entry->animal_type === 'huron' ? 'Hurón' : ucfirst($entry->animal_type) }}
                                                </span>
                                                <form action="{{ route('kardex.destroy', $entry) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('¿Seguro que deseas eliminar esta entrada del Kardex?')">
                                                        <i class="bi bi-trash fs-110"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="mb-3">
                                                <div class="fw-bold text-dark fs-105 mb-1">
                                                    <i class="bi bi-calendar-check text-pet-green-dark me-2"></i>{{ $entry->entry_date->format('Y-m-d') }}
                                                </div>
                                                <small class="text-muted">Registrado por: Dr(a). {{ $entry->doctor->name }} {{ $entry->doctor->lastname }}</small>
                                            </div>

                                            <!-- Cuadrícula de parámetros específicos -->
                                            <div class="row g-2 bg-white rounded-3 p-3 border">
                                                @foreach($entry->parameters as $key => $value)
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="p-2 border-bottom">
                                                            <small class="text-muted d-block fs-75 text-capitalize">{{ str_replace('_', ' ', $key) }}:</small>
                                                            <span class="fw-semibold text-dark fs-90">
                                                                {{ $value }}
                                                                @if(in_array($key, ['frecuencia_cardiaca', 'fc'])) lpm
                                                                @elseif(in_array($key, ['frecuencia_respiratoria', 'fr'])) rpm
                                                                @elseif(in_array($key, ['temperatura', 'temperatura_cloacal', 'temperatura_terrario'])) °C
                                                                @elseif(in_array($key, ['glicemia'])) mg/dL
                                                                @elseif(in_array($key, ['hidratacion'])) %
                                                                @elseif(in_array($key, ['peso'])) g
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted bg-light rounded-4">
                                            <i class="bi bi-journal-x fs-1 mb-3 d-block text-secondary"></i>
                                            El kardex todavía no tiene contenido.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div id="vacunas" class="js-pet-section d-none">
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
                                        <div class="text-center py-4 text-muted bg-light rounded-4">No hay vacunas registradas para esta mascota.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div id="formulas" class="js-pet-section d-none">
                                <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                                    <h6 class="fw-bold mb-0 text-dark">Fórmulas Médicas (Recetas)</h6>
                                    <span class="badge bg-light text-secondary border">{{ count($medicalFormulas) }}</span>
                                </div>

                                <!-- Formulario de registro -->
                                <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-capsule-take-half text-pet-green-dark fs-5 me-2"></i>
                                        <h6 class="fw-bold mb-0 text-dark">Emitir Nueva Fórmula Médica</h6>
                                    </div>

                                    <form action="{{ route('formulas.store', $selectedPet) }}" method="POST" id="formula_form">
                                        @csrf
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="formula_date" class="form-label fw-semibold text-secondary">Fecha de Emisión</label>
                                                <input type="date" name="formula_date" id="formula_date" class="form-control rounded-3" value="{{ now()->toDateString() }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="formula_instructions" class="form-label fw-semibold text-secondary">Indicaciones / Recomendaciones Generales</label>
                                                <input type="text" name="instructions" id="formula_instructions" class="form-control rounded-3" placeholder="Ej: Administrar con alimento, reposar...">
                                            </div>
                                        </div>

                                        <!-- Contenedor dinámico de medicamentos -->
                                        <div class="border rounded-4 p-3 bg-white mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                <h6 class="fw-bold text-pet-green mb-0 fs-90"><i class="bi bi-list-check me-1"></i>Lista de Medicamentos</h6>
                                                <button type="button" class="btn btn-sm btn-outline-pet-green px-3 rounded-pill fw-semibold" id="btn_add_medication">
                                                    <i class="bi bi-plus-circle me-1"></i> Agregar Medicamento
                                                </button>
                                            </div>

                                            <div id="medication_rows_container" class="d-flex flex-column gap-3">
                                                <!-- Fila base inicial -->
                                                <div class="row g-2 align-items-end medication-row">
                                                    <div class="col-md-3 col-sm-12">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">Medicamento</label>
                                                        <input type="text" name="medications[0][name]" class="form-control rounded-3" placeholder="Ej: Meloxicam" required>
                                                    </div>
                                                    <div class="col-md-3 col-sm-12">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">Dosis</label>
                                                        <input type="text" name="medications[0][dose]" class="form-control rounded-3" placeholder="Ej: 0.2 ml" required>
                                                    </div>
                                                    <div class="col-md-3 col-sm-12">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">Frecuencia</label>
                                                        <input type="text" name="medications[0][frequency]" class="form-control rounded-3" placeholder="Ej: Cada 24 horas" required>
                                                    </div>
                                                    <div class="col-md-2 col-sm-10">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">Duración</label>
                                                        <input type="text" name="medications[0][duration]" class="form-control rounded-3" placeholder="Ej: 5 días" required>
                                                    </div>
                                                    <div class="col-md-1 col-sm-2 text-end">
                                                        <button type="button" class="btn btn-outline-danger w-100 rounded-3 btn-remove-row" disabled>
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-pet-green text-white px-4 py-2 fw-semibold rounded-3 shadow-sm">
                                                <i class="bi bi-file-earmark-medical me-1"></i> Guardar Fórmula Médica
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Historial de fórmulas -->
                                <div class="d-flex flex-column gap-3">
                                    <h6 class="fw-bold text-dark mb-1 mt-2"><i class="bi bi-clock-history me-2"></i>Historial de Fórmulas Emitidas</h6>
                                    @forelse($medicalFormulas as $formula)
                                        <div class="border rounded-4 p-4 bg-light position-relative border-start-pet-green">
                                            <div class="position-absolute top-0 end-0 p-3 d-flex gap-2 align-items-center">
                                                <form action="{{ route('formulas.destroy', $formula) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('¿Seguro que deseas eliminar esta fórmula médica?')">
                                                        <i class="bi bi-trash fs-110"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="mb-3">
                                                <div class="fw-bold text-dark fs-105 mb-1">
                                                    <i class="bi bi-calendar-check text-pet-green-dark me-2"></i>{{ $formula->formula_date->format('Y-m-d') }}
                                                </div>
                                                <small class="text-muted">Prescrito por: Dr(a). {{ $formula->doctor->name }} {{ $formula->doctor->lastname }}</small>
                                            </div>

                                            <!-- Listado de medicamentos prescritos -->
                                            <div class="table-responsive bg-white rounded-3 p-3 border mb-3">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr class="text-secondary small">
                                                            <th>Medicamento</th>
                                                            <th>Dosis</th>
                                                            <th>Frecuencia</th>
                                                            <th>Duración</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($formula->medications as $med)
                                                            <tr>
                                                                <td class="fw-semibold text-dark">{{ $med['name'] ?? '' }}</td>
                                                                <td><span class="badge bg-light text-dark border px-2 py-1 rounded-pill">{{ $med['dose'] ?? '' }}</span></td>
                                                                <td><span class="badge bg-light text-dark border px-2 py-1 rounded-pill">{{ $med['frequency'] ?? '' }}</span></td>
                                                                <td><span class="badge bg-light text-dark border px-2 py-1 rounded-pill">{{ $med['duration'] ?? '' }}</span></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            @if($formula->instructions)
                                                <div class="p-3 bg-white rounded-3 border-start border-start-width-3 border-pet-green text-secondary fs-90">
                                                    <strong class="d-block text-dark mb-1"><i class="bi bi-info-circle me-1"></i>Recomendaciones Generales:</strong>
                                                    {{ $formula->instructions }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted bg-light rounded-4">
                                            <i class="bi bi-capsule fs-1 mb-3 d-block text-secondary"></i>
                                            La mascota no cuenta con fórmulas médicas registradas.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div id="ordenes" class="js-pet-section d-none">
                                <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                                    <h6 class="fw-bold mb-0 text-dark">Órdenes Clínicas Solicitadas</h6>
                                    <span class="badge bg-light text-secondary border">{{ count($medicalOrders) }}</span>
                                </div>

                                <!-- Formulario de registro -->
                                <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-file-earmark-plus-fill text-pet-green-dark fs-5 me-2"></i>
                                        <h6 class="fw-bold mb-0 text-dark">Emitir Nueva Orden Clínica</h6>
                                    </div>

                                    <form action="{{ route('orders.store', $selectedPet) }}" method="POST" id="order_form">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="order_date" class="form-label fw-semibold text-secondary">Fecha de la Orden</label>
                                                <input type="date" name="order_date" id="order_date" class="form-control rounded-3" value="{{ now()->toDateString() }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="order_type" class="form-label fw-semibold text-secondary">Tipo de Orden</label>
                                                <select name="order_type" id="order_type" class="form-select rounded-3" required>
                                                    <option value="Laboratorio">Laboratorio</option>
                                                    <option value="Imagenología">Imagenología</option>
                                                    <option value="Cirugía / Procedimiento">Cirugía / Procedimiento</option>
                                                    <option value="Otros">Otros</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="order_description" class="form-label fw-semibold text-secondary">Detalle / Instrucciones de la Orden</label>
                                                <textarea name="description" id="order_description" rows="3" class="form-control rounded-3" placeholder="Ej: Realizar hemograma y perfil bioquímico completo en ayuno de 8 horas." required></textarea>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-pet-green text-white px-4 py-2 rounded-pill fw-semibold shadow-sm">
                                                <i class="bi bi-file-earmark-medical me-1"></i> Generar Orden
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Historial de órdenes -->
                                <div class="d-flex flex-column gap-3">
                                    @forelse($medicalOrders as $order)
                                        <div class="card border border-light-subtle rounded-4 p-4 shadow-sm bg-white position-relative">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="fs-95 fw-bold text-dark">
                                                            @if($order->order_type === 'Laboratorio')
                                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill text-xs">
                                                                    <i class="bi bi-activity me-1"></i> Laboratorio
                                                                </span>
                                                            @elseif($order->order_type === 'Imagenología')
                                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill text-xs">
                                                                    <i class="bi bi-image me-1"></i> Imagenología
                                                                </span>
                                                            @elseif($order->order_type === 'Cirugía / Procedimiento')
                                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill text-xs">
                                                                    <i class="bi bi-heart-pulse me-1"></i> Cirugía / Procedimiento
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill text-xs">
                                                                    <i class="bi bi-file-earmark me-1"></i> Otros
                                                                </span>
                                                            @endif
                                                        </span>
                                                        <span class="text-muted small">| Solicitada por: <strong>Dr. {{ $order->doctor?->name ?? 'Veterinario' }}</strong></span>
                                                    </div>
                                                    <div class="text-secondary small mt-1">
                                                        <i class="bi bi-calendar-event me-1"></i> Fecha: {{ $order->order_date->format('Y-m-d') }}
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <!-- Estado actual badge -->
                                                    @if($order->status === 'completed')
                                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-85">
                                                            <i class="bi bi-check-circle me-1"></i> Realizada
                                                        </span>
                                                    @elseif($order->status === 'cancelled')
                                                        <span class="badge bg-secondary text-white px-3 py-2 rounded-pill fs-85">
                                                            <i class="bi bi-x-circle me-1"></i> Cancelada
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-85">
                                                            <i class="bi bi-hourglass-split me-1"></i> Pendiente
                                                        </span>
                                                    @endif

                                                    <!-- Botón de eliminar -->
                                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta orden clínica?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2" title="Eliminar orden">
                                                            <i class="bi bi-trash fs-95"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="p-3 bg-light rounded-3 text-secondary fs-95 mb-3">
                                                <strong class="text-dark d-block mb-1">Descripción:</strong>
                                                {{ $order->description }}
                                            </div>

                                            <!-- Control de cambio de estado rápido -->
                                            <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-2">
                                                <span class="text-muted small me-2"><i class="bi bi-arrow-left-right me-1"></i> Cambiar estado:</span>
                                                <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="btn btn-xs btn-outline-warning rounded-pill px-3 py-1 fs-80 {{ $order->status === 'pending' ? 'active' : '' }}">Pendiente</button>
                                                </form>
                                                <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-xs btn-outline-success rounded-pill px-3 py-1 fs-80 {{ $order->status === 'completed' ? 'active' : '' }}">Realizada</button>
                                                </form>
                                                <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1 fs-80 {{ $order->status === 'cancelled' ? 'active' : '' }}">Cancelada</button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted bg-light rounded-4">
                                            <i class="bi bi-file-earmark-text fs-1 mb-3 d-block text-secondary"></i>
                                            La mascota no cuenta con órdenes clínicas registradas.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div id="examenes" class="js-pet-section d-none">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4 pb-2 border-bottom">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                                            <i class="bi bi-journal-medical me-2 text-pet-green-dark"></i>Exámenes y archivos médicos
                                        </h5>
                                        <p class="text-muted mb-0 fs-90">Gestiona resultados clínicos y documentos adjuntos de la mascota.</p>
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
                                                            <div class="fw-semibold text-dark">{{ $exam->title ?? $exam->original_name }}</div>
                                                            <small class="text-muted">{{ $exam->original_name }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-pet-green-10 text-pet-green-dark fw-semibold px-3 py-2 rounded-pill">{{ $exam->category ?? 'Sin categoría' }}</span>
                                                        </td>
                                                        <td>{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'N/A' }}</td>
                                                        <td>{{ $exam->uploaded_at ? $exam->uploaded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                                        <td>{{ optional($exam->uploader)->name ? $exam->uploader->name . ' ' . $exam->uploader->lastname : 'N/A' }}</td>
                                                        <td class="text-end">
                                                            <div class="d-inline-flex gap-2">
                                                                <a href="{{ route('medical_exams.view', $exam->id) }}" class="btn btn-sm btn-outline-secondary px-3" target="_blank">Ver</a>
                                                                <a href="{{ route('medical_exams.download', $exam->id) }}" class="btn btn-sm btn-pet-green text-white px-3">Descargar</a>
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

                                        <form action="{{ route('medical_exams.store', $selectedPet->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row g-3 g-lg-4 mb-2">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Título</label>
                                                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Ej: Hemograma completo">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Categoría</label>
                                                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="Ej: Laboratorio">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Fecha del examen</label>
                                                    <input type="date" name="exam_date" class="form-control" value="{{ old('exam_date') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label fw-medium">Descripción</label>
                                                    <textarea name="description" class="form-control" rows="3" placeholder="Notas opcionales del examen">{{ old('description') }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium">Vincular a consulta (opcional)</label>
                                                    <select name="medical_record_id" class="form-select">
                                                        <option value="">Sin vínculo específico</option>
                                                        @foreach($medicalRecords as $record)
                                                            <option value="{{ $record->id }}" @selected((string) old('medical_record_id') === (string) $record->id)>{{ $record->visited_at->format('Y-m-d') }} - {{ $record->reason }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium">Archivos</label>
                                                    <input type="file" name="files[]" class="form-control" multiple required accept=".pdf,.jpg,.jpeg,.png">
                                                    <small class="text-muted d-block mt-2">Formatos permitidos: PDF, JPG, JPEG, PNG. Máximo 5MB por archivo.</small>
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

                            <div id="citas" class="js-pet-section d-none">
                                <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                                    <h6 class="fw-bold mb-0 text-dark">Historial de Citas</h6>
                                    <span class="badge bg-light text-secondary border">{{ count($appointments) }}</span>
                                </div>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    @forelse($appointments as $appointment)
                                        <div class="border rounded-4 p-4 bg-light position-relative border-start-pet-green-dark">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="fw-semibold text-dark fs-110 mb-2">
                                                        <i class="bi bi-calendar-event text-pet-green-dark me-2"></i>{{ $appointment->getDate() }}
                                                    </div>
                                                    <div class="text-dark fw-medium fs-90 mb-1">
                                                        Hora: <span class="text-secondary fw-normal">{{ substr($appointment->getStartTime(), 0, 5) }} - {{ substr($appointment->getEndTime(), 0, 5) }}</span>
                                                    </div>
                                                    <div class="text-dark fw-medium fs-90 mb-1">
                                                        Doctor Asignado: <span class="text-secondary fw-normal">Dr(a). {{ $appointment->doctor->name }} {{ $appointment->doctor->lastname }}</span>
                                                    </div>
                                                    <div class="text-dark fw-medium fs-90 mb-1">
                                                        Motivo: <span class="text-secondary fw-normal">{{ $appointment->getReason() ?? 'No especificado' }}</span>
                                                    </div>
                                                    <div class="text-dark fw-medium fs-90">
                                                        Estado: 
                                                        @if($appointment->getStatus() === 'scheduled')
                                                            <span class="badge bg-success rounded-pill px-3 py-1 fs-75">Programada</span>
                                                        @elseif($appointment->getStatus() === 'canceled')
                                                            <span class="badge bg-danger rounded-pill px-3 py-1 fs-75">Cancelada</span>
                                                        @else
                                                            <span class="badge bg-secondary rounded-pill px-3 py-1 fs-75">{{ ucfirst($appointment->getStatus()) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted bg-light rounded-4">
                                            <i class="bi bi-calendar-x fs-1 mb-3 d-block text-secondary"></i>
                                            La mascota no tiene citas registradas.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- MODAL PARA EDITAR MASCOTA -->
            <div class="modal fade" id="editPetModal" tabindex="-1" aria-labelledby="editPetModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form action="{{ route('medical_records.update_pet', $selectedPet->getId()) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="modal-header bg-pet-green text-white py-3 border-0 rounded-top-4">
                                <h5 class="modal-title fw-bold" id="editPetModalLabel">
                                    <i class="bi bi-pencil-square me-2"></i>Editar Datos de Mascota
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                                <!-- Sección 1: Datos Básicos e Imagen -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-pet-green-dark border-bottom pb-2 mb-3">
                                        <i class="bi bi-card-list me-2"></i>Datos Básicos e Imagen
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="modal_name" class="form-label small fw-semibold text-secondary">Nombre *</label>
                                            <input type="text" name="name" id="modal_name" value="{{ $selectedPet->getName() }}" class="form-control rounded-3 shadow-sm border" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_species" class="form-label small fw-semibold text-secondary">Especie *</label>
                                            <input type="text" name="species" id="modal_species" value="{{ $selectedPet->getSpecies() }}" class="form-control rounded-3 shadow-sm border" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_breed" class="form-label small fw-semibold text-secondary">Raza/Subespecie</label>
                                            <input type="text" name="breed" id="modal_breed" value="{{ $selectedPet->getBreed() }}" class="form-control rounded-3 shadow-sm border">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_gender" class="form-label small fw-semibold text-secondary">Género *</label>
                                            <select name="gender" id="modal_gender" class="form-select rounded-3 shadow-sm border" required>
                                                <option value="male" @selected($selectedPet->getGender() === 'male')>Macho (Male)</option>
                                                <option value="female" @selected($selectedPet->getGender() === 'female')>Hembra (Female)</option>
                                                <option value="unknown" @selected($selectedPet->getGender() === 'unknown')>Desconocido (Unknown)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_age" class="form-label small fw-semibold text-secondary">Edad (años)</label>
                                            <input type="number" name="age" id="modal_age" value="{{ $selectedPet->getAge() }}" min="0" max="200" class="form-control rounded-3 shadow-sm border">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_weight" class="form-label small fw-semibold text-secondary">Peso (Kilogramos)</label>
                                            <input type="number" name="weight" id="modal_weight" value="{{ $selectedPet->getWeight() }}" step="0.01" min="0" class="form-control rounded-3 shadow-sm border">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_color" class="form-label small fw-semibold text-secondary">Color</label>
                                            <input type="text" name="color" id="modal_color" value="{{ $selectedPet->getColor() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Sable, Gris">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_size" class="form-label small fw-semibold text-secondary">Talla</label>
                                            <input type="text" name="size" id="modal_size" value="{{ $selectedPet->getSize() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Pequeña, Mediana, Grande">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_reproductive_status" class="form-label small fw-semibold text-secondary">Estado Reproductivo</label>
                                            <input type="text" name="reproductive_status" id="modal_reproductive_status" value="{{ $selectedPet->getReproductiveStatus() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Esterilizado, Entero">
                                        </div>
                                        <div class="col-12">
                                            <label for="modal_photo" class="form-label small fw-semibold text-secondary">Foto de la Mascota</label>
                                            <input type="file" name="photo" id="modal_photo" class="form-control rounded-3 shadow-sm border" accept="image/*">
                                            <small class="text-muted d-block mt-1">Formatos aceptados: JPG, JPEG, PNG, WEBP. Máx: 2MB.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección 2: Condiciones Clínicas -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-pet-green-dark border-bottom pb-2 mb-3">
                                        <i class="bi bi-heart-pulse me-2"></i>Condiciones Clínicas y Especiales
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="modal_is_deceased" class="form-label small fw-semibold text-secondary">¿Fallecido? *</label>
                                            <select name="is_deceased" id="modal_is_deceased" class="form-select rounded-3 shadow-sm border" required>
                                                <option value="0" @selected(!$selectedPet->getIsDeceased())>No</option>
                                                <option value="1" @selected($selectedPet->getIsDeceased())>Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_emotional_support" class="form-label small fw-semibold text-secondary">¿Apoyo Emocional? *</label>
                                            <select name="emotional_support" id="modal_emotional_support" class="form-select rounded-3 shadow-sm border" required>
                                                <option value="0" @selected(!$selectedPet->getEmotionalSupport())>No</option>
                                                <option value="1" @selected($selectedPet->getEmotionalSupport())>Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_service_animal" class="form-label small fw-semibold text-secondary">¿Animal de Servicio? *</label>
                                            <select name="service_animal" id="modal_service_animal" class="form-select rounded-3 shadow-sm border" required>
                                                <option value="0" @selected(!$selectedPet->getServiceAnimal())>No</option>
                                                <option value="1" @selected($selectedPet->getServiceAnimal())>Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_last_heat" class="form-label small fw-semibold text-secondary">Último Calor / Celo</label>
                                            <input type="text" name="last_heat" id="modal_last_heat" value="{{ $selectedPet->getLastHeat() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Hace 2 meses, N/A">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección 3: Alimentación y Hábitat -->
                                <div>
                                    <h6 class="fw-bold text-pet-green-dark border-bottom pb-2 mb-3">
                                        <i class="bi bi-house-door me-2"></i>Alimentación, Hábitat y Cuidados
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="modal_diet" class="form-label small fw-semibold text-secondary">Alimento (Dieta)</label>
                                            <input type="text" name="diet" id="modal_diet" value="{{ $selectedPet->getDiet() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Tenebrios y vegetales">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_diet_quantity" class="form-label small fw-semibold text-secondary">Cantidad de Alimento</label>
                                            <input type="text" name="diet_quantity" id="modal_diet_quantity" value="{{ $selectedPet->getDietQuantity() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: 50gr diarios">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="modal_diet_frequency" class="form-label small fw-semibold text-secondary">Frecuencia de Alimento</label>
                                            <input type="text" name="diet_frequency" id="modal_diet_frequency" value="{{ $selectedPet->getDietFrequency() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: 2 veces al día">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_housing" class="form-label small fw-semibold text-secondary">Vivienda (Jaula/Terrario)</label>
                                            <input type="text" name="housing" id="modal_housing" value="{{ $selectedPet->getHousing() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Terrario con manta térmica">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_bath_frequency" class="form-label small fw-semibold text-secondary">Frecuencia de Baño</label>
                                            <input type="text" name="bath_frequency" id="modal_bath_frequency" value="{{ $selectedPet->getBathFrequency() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Cada 2 meses, No requiere">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_bath_products" class="form-label small fw-semibold text-secondary">Productos de Baño</label>
                                            <input type="text" name="bath_products" id="modal_bath_products" value="{{ $selectedPet->getBathProducts() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Champú pH neutro avena">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="modal_other_pets" class="form-label small fw-semibold text-secondary">Otras Mascotas (¿Cuáles?)</label>
                                            <input type="text" name="other_pets" id="modal_other_pets" value="{{ $selectedPet->getOtherPets() }}" class="form-control rounded-3 shadow-sm border" placeholder="Ej: Un gato, Ninguna">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                                <button type="button" class="btn btn-outline-secondary px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-pet-green text-white px-4 rounded-3 fw-medium">
                                    <i class="bi bi-save me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const links = document.querySelectorAll('.js-pet-section-link');
            const sections = document.querySelectorAll('.js-pet-section');

            const activateSection = function (sectionId) {
                sections.forEach(function (section) {
                    section.classList.toggle('d-none', section.id !== sectionId);
                });

                links.forEach(function (link) {
                    const isActive = link.dataset.section === sectionId;
                    link.classList.toggle('active', isActive);
                    link.classList.toggle('bg-pet-green-dark', isActive);
                    link.classList.toggle('text-white', isActive);
                    link.classList.toggle('text-secondary', ! isActive);
                });
            };

            const initialSection = window.location.hash.replace('#', '') || 'historia';
            activateSection(initialSection);

            links.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    const sectionId = link.dataset.section;
                    if (! sectionId) {
                        return;
                    }

                    event.preventDefault();
                    history.replaceState(null, '', '#' + sectionId);
                    activateSection(sectionId);
                });
            });

            // Dynamic Kardex fields generation based on species select
            const animalTypeSelect = document.getElementById('kardex_animal_type');
            const fieldsWrapper = document.getElementById('kardex_fields_wrapper');
            const dynamicFieldsContainer = document.getElementById('kardex_dynamic_fields');
            const submitBtn = document.getElementById('kardex_submit_btn');

            if (animalTypeSelect) {
                animalTypeSelect.addEventListener('change', function () {
                    const species = this.value;
                    let html = '';

                    if (species) {
                        fieldsWrapper.classList.remove('d-none');
                        submitBtn.removeAttribute('disabled');
                    } else {
                        fieldsWrapper.classList.add('d-none');
                        submitBtn.setAttribute('disabled', 'true');
                        return;
                    }

                    if (species === 'huron') {
                        html = `
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Cardíaca (lpm)</label>
                                <input type="number" name="parameters[frecuencia_cardiaca]" class="form-control rounded-3" placeholder="Ej: 200 (Normal: 180-250)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Respiratoria (rpm)</label>
                                <input type="number" name="parameters[frecuencia_respiratoria]" class="form-control rounded-3" placeholder="Ej: 35 (Normal: 30-40)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Temperatura (°C)</label>
                                <input type="number" step="0.1" name="parameters[temperatura]" class="form-control rounded-3" placeholder="Ej: 38.5 (Normal: 37.8-40)" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Glicemia (mg/dL)</label>
                                <input type="number" name="parameters[glicemia]" class="form-control rounded-3" placeholder="Ej: 90 (Alerta < 60)" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Hidratación (%)</label>
                                <input type="number" name="parameters[hidratacion]" class="form-control rounded-3" placeholder="Ej: 100" required>
                            </div>
                        `;
                    } else if (species === 'loro') {
                        html = `
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Respiratoria (rpm)</label>
                                <input type="number" name="parameters[frecuencia_respiratoria]" class="form-control rounded-3" placeholder="Ej: 25 (Normal: 15-40)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Temperatura Cloacal (°C)</label>
                                <input type="number" step="0.1" name="parameters[temperatura_cloacal]" class="form-control rounded-3" placeholder="Ej: 41.0 (Normal: 40-42)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Estado de Plumaje</label>
                                <select name="parameters[plumaje]" class="form-select rounded-3" required>
                                    <option value="Excelente">Excelente</option>
                                    <option value="Bueno">Bueno</option>
                                    <option value="Malo/Picaje">Malo / Picaje</option>
                                    <option value="Mudando">Mudando</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Consistencia de Heces</label>
                                <select name="parameters[consistencia_heces]" class="form-select rounded-3" required>
                                    <option value="Normal">Normal</option>
                                    <option value="Liquida/Diarreica">Líquida / Diarreica</option>
                                    <option value="Con Uratos Anormales">Con Uratos Anormales</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Comportamiento</label>
                                <select name="parameters[comportamiento]" class="form-select rounded-3" required>
                                    <option value="Activo/Alerta">Activo / Alerta</option>
                                    <option value="Deprimido/Letargico">Deprimido / Letárgico</option>
                                    <option value="Embolado">Embolado</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Estado del Buche</label>
                                <select name="parameters[estado_buche]" class="form-select rounded-3" required>
                                    <option value="Vacio">Vacío</option>
                                    <option value="Lleno/Normal">Lleno / Normal</option>
                                    <option value="Demorado/Estasis">Demorado / Estasis</option>
                                </select>
                            </div>
                        `;
                    } else if (species === 'conejo') {
                        html = `
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Cardíaca (lpm)</label>
                                <input type="number" name="parameters[frecuencia_cardiaca]" class="form-control rounded-3" placeholder="Ej: 220 (Normal: 130-325)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Respiratoria (rpm)</label>
                                <input type="number" name="parameters[frecuencia_respiratoria]" class="form-control rounded-3" placeholder="Ej: 45 (Normal: 30-60)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Temperatura (°C)</label>
                                <input type="number" step="0.1" name="parameters[temperatura]" class="form-control rounded-3" placeholder="Ej: 39.2 (Normal: 38.5-40)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Motilidad Intestinal</label>
                                <select name="parameters[motilidad_intestinal]" class="form-select rounded-3" required>
                                    <option value="Normal">Normal</option>
                                    <option value="Disminuida">Disminuida</option>
                                    <option value="Ileo Clinico">Íleo Clínico (Alerta)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Heces / Cecotrofos</label>
                                <select name="parameters[cecotrofos]" class="form-select rounded-3" required>
                                    <option value="Normal/Heces Firmes">Normal / Heces Firmes</option>
                                    <option value="Heces Blandas/Diarrea">Heces Blandas / Diarrea</option>
                                    <option value="Ausencia de heces">Ausencia total (Alerta)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Estado Dental</label>
                                <input type="text" name="parameters[estado_dental]" class="form-control rounded-3" placeholder="Ej: Incisivos normales" required>
                            </div>
                        `;
                    } else if (species === 'erizo') {
                        html = `
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Cardíaca (lpm)</label>
                                <input type="number" name="parameters[frecuencia_cardiaca]" class="form-control rounded-3" placeholder="Ej: 210 (Normal: 180-280)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Respiratoria (rpm)</label>
                                <input type="number" name="parameters[frecuencia_respiratoria]" class="form-control rounded-3" placeholder="Ej: 35 (Normal: 25-50)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Temperatura (°C)</label>
                                <input type="number" step="0.1" name="parameters[temperatura]" class="form-control rounded-3" placeholder="Ej: 36.1 (Normal: 35-37.5)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Estado de Piel/Púas</label>
                                <input type="text" name="parameters[estado_piel_puas]" class="form-control rounded-3" placeholder="Ej: Sin descamación" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Grado de Enrollamiento</label>
                                <select name="parameters[enrollamiento]" class="form-select rounded-3" required>
                                    <option value="Completo/Firme">Completo / Firme</option>
                                    <option value="Debil/Incompleto">Débil / Incompleto</option>
                                    <option value="Ausente">Ausente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Peso Corporal (g)</label>
                                <input type="number" name="parameters[peso]" class="form-control rounded-3" placeholder="Ej: 450" required>
                            </div>
                        `;
                    } else if (species === 'iguana') {
                        html = `
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Frecuencia Cardíaca (lpm)</label>
                                <input type="number" name="parameters[frecuencia_cardiaca]" class="form-control rounded-3" placeholder="Ej: 50 (Normal: 40-60)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Temperatura Terrario (°C)</label>
                                <input type="number" step="0.1" name="parameters[temperatura_terrario]" class="form-control rounded-3" placeholder="Ej: 32" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Muda de Piel</label>
                                <select name="parameters[muda_piel]" class="form-select rounded-3" required>
                                    <option value="Completa/Saludable">Completa / Saludable</option>
                                    <option value="Disecdiasis (Muda retenida)">Disecdiasis (Muda retenida)</option>
                                    <option value="No corresponde">No corresponde</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Hidratación (Pliegues)</label>
                                <select name="parameters[hidratacion]" class="form-select rounded-3" required>
                                    <option value="Normal/Turgente">Normal / Turgente</option>
                                    <option value="Deshidratacion Leve">Deshidratación Leve</option>
                                    <option value="Deshidratacion Moderada/Severa">Deshidratación Severa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Cola y Extremidades</label>
                                <input type="text" name="parameters[cola_extremidades]" class="form-control rounded-3" placeholder="Ej: Integros" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Coloración / Estrés</label>
                                <select name="parameters[coloracion]" class="form-select rounded-3" required>
                                    <option value="Brillante/Verde Intenso">Brillante / Verde Intenso</option>
                                    <option value="Opaca/Oscura (Estres)">Opaca / Oscura (Estrés)</option>
                                </select>
                            </div>
                        `;
                    }

                    dynamicFieldsContainer.innerHTML = html;
                });
            }

            // Dynamic Prescriptions medication rows generator
            const btnAddMedication = document.getElementById('btn_add_medication');
            const rowsContainer = document.getElementById('medication_rows_container');

            if (btnAddMedication && rowsContainer) {
                let rowCount = 1;

                btnAddMedication.addEventListener('click', function () {
                    const newRow = document.createElement('div');
                    newRow.className = 'row g-2 align-items-end medication-row mt-2';
                    newRow.innerHTML = `
                        <div class="col-md-3 col-sm-12">
                            <input type="text" name="medications[${rowCount}][name]" class="form-control rounded-3" placeholder="Ej: Meloxicam" required>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <input type="text" name="medications[${rowCount}][dose]" class="form-control rounded-3" placeholder="Ej: 0.2 ml" required>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <input type="text" name="medications[${rowCount}][frequency]" class="form-control rounded-3" placeholder="Ej: Cada 24 horas" required>
                        </div>
                        <div class="col-md-2 col-sm-10">
                            <input type="text" name="medications[${rowCount}][duration]" class="form-control rounded-3" placeholder="Ej: 5 días" required>
                        </div>
                        <div class="col-md-1 col-sm-2 text-end">
                            <button type="button" class="btn btn-outline-danger w-100 rounded-3 btn-remove-row">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                    rowsContainer.appendChild(newRow);
                    rowCount++;

                    updateRemoveRowButtons();
                });

                rowsContainer.addEventListener('click', function (e) {
                    const removeBtn = e.target.closest('.btn-remove-row');
                    if (removeBtn && !removeBtn.disabled) {
                        const row = removeBtn.closest('.medication-row');
                        row.remove();
                        updateRemoveRowButtons();
                    }
                });

                function updateRemoveRowButtons() {
                    const rows = rowsContainer.querySelectorAll('.medication-row');
                    rows.forEach(function (row, idx) {
                        const removeBtn = row.querySelector('.btn-remove-row');
                        if (removeBtn) {
                            removeBtn.disabled = (rows.length === 1);
                        }
                    });
                }
            }
        });
    </script>
@endpush