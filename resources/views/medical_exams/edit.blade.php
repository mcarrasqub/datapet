@extends($layout)

@section('title', 'Modificar Examen Médico - DataPet')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <!-- Breadcrumb / Volver -->
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">Modificar Examen Médico</h3>
                    <p class="text-muted small mb-0">Mascota: <strong class="text-dark">{{ $viewData['pet']->getName() }} ({{ $viewData['pet']->getSpecies() }})</strong></p>
                </div>
                @if(auth()->user()->role === 'client')
                    <a href="{{ route('pets.exams', ['pet_id' => $viewData['pet']->id]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                @else
                    <a href="{{ route('medical_records.show', $viewData['pet']->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                @endif
            </div>

            <!-- Card del Formulario -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-pet-green text-white p-3 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-pencil-square fs-4 me-2"></i>
                        <h6 class="fw-bold mb-0">Editar detalles del examen</h6>
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <!-- Errores de Validación -->
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <strong class="small">Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-1 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('medical_exams.update', $viewData['medicalExam']->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Título -->
                            <div class="col-md-6">
                                <label for="title" class="form-label fw-semibold text-secondary">Título del examen</label>
                                <input type="text" name="title" id="title" class="form-control rounded-3" 
                                    value="{{ old('title', $viewData['medicalExam']->title) }}" placeholder="Ej: Hemograma externo" required>
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-semibold text-secondary">Categoría / Tipo</label>
                                <input type="text" name="category" id="category" class="form-control rounded-3" 
                                    value="{{ old('category', $viewData['medicalExam']->category) }}" placeholder="Ej: Laboratorio clínico">
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-6">
                                <label for="exam_date" class="form-label fw-semibold text-secondary">Fecha del examen</label>
                                <input type="date" name="exam_date" id="exam_date" class="form-control rounded-3" 
                                    value="{{ old('exam_date', $viewData['medicalExam']->exam_date ? $viewData['medicalExam']->exam_date->format('Y-m-d') : '') }}">
                            </div>

                            <!-- Vincular Orden Médica -->
                            <div class="col-md-6">
                                <label for="medical_order_id" class="form-label fw-semibold text-secondary">Vincular a orden médica (opcional)</label>
                                <select name="medical_order_id" id="medical_order_id" class="form-select rounded-3">
                                    <option value="">-- Sin orden vinculada --</option>
                                    @if($viewData['medicalExam']->medicalOrder)
                                        <option value="{{ $viewData['medicalExam']->medical_order_id }}" selected>
                                            [Actual] Orden del {{ $viewData['medicalExam']->medicalOrder->order_date->format('Y-m-d') }} - {{ $viewData['medicalExam']->medicalOrder->order_type }}
                                        </option>
                                    @endif
                                    @foreach($viewData['pendingOrders'] as $order)
                                        @if($viewData['medicalExam']->medical_order_id != $order->id)
                                            <option value="{{ $order->id }}">
                                                Orden del {{ $order->order_date->format('Y-m-d') }} - {{ $order->order_type }}: {{ Str::limit($order->description, 50) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-12">
                                <label for="description" class="form-label fw-semibold text-secondary">Descripción / Observaciones adicionales</label>
                                <textarea name="description" id="description" rows="3" class="form-control rounded-3" 
                                    placeholder="Detalles sobre los resultados o indicaciones médicas...">{{ old('description', $viewData['medicalExam']->description) }}</textarea>
                            </div>

                            <!-- Archivo Existente -->
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-pet-green-10 p-2 rounded-2 text-pet-green me-3">
                                                <i class="bi bi-file-earmark-medical fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 0.8rem;">Documento cargado actualmente</small>
                                                <strong class="text-dark small" style="word-break: break-all;">{{ $viewData['medicalExam']->original_name }}</strong>
                                                <span class="badge bg-secondary rounded-pill ms-2" style="font-size: 0.75rem;">
                                                    {{ number_format($viewData['medicalExam']->file_size / 1024, 1) }} KB
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('medical_exams.view', $viewData['medicalExam']->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> Visualizar Archivo
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Reemplazar Archivo -->
                            <div class="col-md-12">
                                <label for="file" class="form-label fw-semibold text-danger">Reemplazar documento (opcional)</label>
                                <input type="file" name="file" id="file" class="form-control rounded-3" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted d-block mt-1">Selecciona un archivo nuevo solo si deseas sustituir el documento actual. Formatos admitidos: PDF, JPG, JPEG, PNG (Máx. 5MB).</small>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            @if(auth()->user()->role === 'client')
                                <a href="{{ route('pets.exams', ['pet_id' => $viewData['pet']->id]) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-medium">
                                    Cancelar
                                </a>
                            @else
                                <a href="{{ route('medical_records.show', $viewData['pet']->id) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-medium">
                                    Cancelar
                                </a>
                            @endif
                            <button type="submit" class="btn btn-pet-green text-white rounded-pill px-4 fw-medium shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
