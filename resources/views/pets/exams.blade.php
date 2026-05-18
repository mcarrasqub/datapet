@extends('layouts.app')

@section('content')
    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                <strong>No se pudo completar la carga:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">Exámenes de Mis Mascotas</h3>
                <p class="text-muted mb-0">Consulta y descarga los resultados médicos de tus mascotas.</p>
            </div>
            <a href="{{ route('home.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('pets.exams') }}" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label for="pet_id" class="form-label text-muted small mb-1">Filtrar por mascota</label>
                        <select name="pet_id" id="pet_id" class="form-select">
                            <option value="">Todas mis mascotas</option>
                            @foreach($viewData['pets'] as $pet)
                                <option value="{{ $pet->getId() }}" @selected((int) ($viewData['selectedPetId'] ?? 0) === $pet->getId())>
                                    {{ $pet->getName() }} ({{ $pet->getSpecies() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-pet-green text-white w-100">Aplicar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-cloud-upload text-pet-green me-2"></i>
                    <h6 class="fw-bold mb-0">Subir examen externo</h6>
                </div>
                <p class="text-muted small mb-4">
                    Si la clínica te pidió realizar un examen con un tercero, puedes cargar el resultado aquí para que quede
                    en el historial de tu mascota.
                </p>

                @if(($viewData['selectedPetId'] ?? 0) > 0)
                    <form method="POST" action="{{ route('medical_exams.store', $viewData['selectedPetId']) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Título</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                    placeholder="Ej: Hemograma externo">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                                    placeholder="Ej: Laboratorio externo">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha del examen</label>
                                <input type="date" name="exam_date" class="form-control" value="{{ old('exam_date') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-secondary">Vincular a orden médica pendiente (opcional)</label>
                                <select name="medical_order_id" class="form-select rounded-3">
                                    <option value="">-- No vincular a ninguna orden --</option>
                                    @foreach($viewData['pendingOrders'] as $order)
                                        <option value="{{ $order->id }}">
                                            Orden del {{ $order->order_date->format('Y-m-d') }} - {{ $order->order_type }}: {{ Str::limit($order->description, 60) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" rows="2" class="form-control"
                                    placeholder="Notas opcionales (ej: examen solicitado por la clínica en laboratorio externo)">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Archivos</label>
                                <input type="file" name="files[]" class="form-control" multiple required
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Formatos permitidos: PDF, JPG, JPEG, PNG. Máximo 5MB por
                                    archivo.</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pet-green text-white mt-3">
                            <i class="bi bi-upload me-1"></i>Subir examen
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mb-0">
                        Primero selecciona una mascota en el filtro para habilitar la carga de exámenes.
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mascota</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Fecha examen</th>
                                <th>Fecha carga</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($viewData['medicalExams'] as $exam)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $exam->pet?->getName() ?? 'N/D' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $exam->title ?? $exam->original_name }}</div>
                                        @if($exam->description)
                                            <small class="text-muted">{{ $exam->description }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $exam->category ?? 'Sin categoría' }}</td>
                                    <td>{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>{{ $exam->uploaded_at ? $exam->uploaded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('medical_exams.view', $exam->id) }}"
                                            class="btn btn-sm btn-outline-secondary" target="_blank">Ver</a>
                                        <a href="{{ route('medical_exams.download', $exam->id) }}"
                                            class="btn btn-sm btn-pet-green text-white">Descargar</a>
                                        @if(auth()->user()->role === 'client' && (int) $exam->uploaded_by === (int) auth()->id())
                                            <a href="{{ route('medical_exams.edit', $exam->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Modificar Examen">Editar</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        No hay exámenes disponibles para el filtro seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection