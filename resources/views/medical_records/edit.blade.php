@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-radius: 15px; border-top: 4px solid #76a75d;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-pencil-circle me-2" style="color: #76a75d;"></i>Editar Entrada Médica
                    </h5>
                    <p class="text-muted mb-4">
                        <i class="bi bi-paw-fill me-2"></i><strong>Paciente:</strong> {{ $pet->name }} - {{ $pet->species }}
                    </p>

                    <form action="{{ route('medical_records.update', $record) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="visited_at" class="form-label fw-bold">
                                <i class="bi bi-calendar-event me-2" style="color: #76a75d;"></i>Fecha de Visita
                            </label>
                            <input type="date" class="form-control @error('visited_at') is-invalid @enderror" 
                                   id="visited_at" name="visited_at" value="{{ old('visited_at', $record->visited_at->format('Y-m-d')) }}" required
                                   style="border-color: #76a75d;">
                            @error('visited_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="reason" class="form-label fw-bold">
                                <i class="bi bi-question-circle me-2" style="color: #76a75d;"></i>Motivo de Visita
                            </label>
                            <input type="text" class="form-control @error('reason') is-invalid @enderror" 
                                   id="reason" name="reason" value="{{ old('reason', $record->reason) }}" required
                                   style="border-color: #76a75d;">
                            @error('reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="diagnosis" class="form-label fw-bold">
                                <i class="bi bi-stethoscope me-2" style="color: #76a75d;"></i>Diagnóstico
                            </label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" 
                                      id="diagnosis" name="diagnosis" rows="3" required
                                      style="border-color: #76a75d;">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                            @error('diagnosis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="treatment" class="form-label fw-bold">
                                <i class="bi bi-pill me-2" style="color: #76a75d;"></i>Tratamiento
                            </label>
                            <textarea class="form-control @error('treatment') is-invalid @enderror" 
                                      id="treatment" name="treatment" rows="3" required
                                      style="border-color: #76a75d;">{{ old('treatment', $record->treatment) }}</textarea>
                            @error('treatment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">
                                <i class="bi bi-chat-left-text me-2" style="color: #76a75d;"></i>Notas Adicionales
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2"
                                      style="border-color: #76a75d;">{{ old('notes', $record->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <!-- FOTOS EXISTENTES -->
                        @if($record->photos && count($record->photos) > 0)
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-image me-2" style="color: #76a75d;"></i>Fotos Actuales
                            </label>
                            <div class="d-flex gap-2 mb-3">
                                @foreach($record->photos as $index => $photo)
                                <div style="position: relative;" data-photo-index="{{ $index }}">
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Foto" 
                                         style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 2px solid #76a75d;">
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="removePhoto(this)" data-index="{{ $index }}"
                                            style="position: absolute; top: 5px; right: 5px; border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <input type="hidden" id="photosToRemove" name="photos_to_remove" value="">
                        @endif

                        <!-- AGREGAR MÁS FOTOS -->
                        @if(!$record->photos || count($record->photos) < 3)
                        <div class="mb-4">
                            <label for="photos" class="form-label fw-bold">
                                <i class="bi bi-plus-circle me-2" style="color: #76a75d;"></i>Agregar Más Fotos
                            </label>
                            <input type="file" class="form-control @error('photos.*') is-invalid @enderror" 
                                   id="photos" name="photos[]" multiple accept="image/*"
                                   style="border-color: #76a75d;">
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>Puedes subir hasta {{ 3 - count($record->photos ?? []) }} fotos más
                            </small>
                            @error('photos.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <!-- Vista previa de nuevas fotos -->
                        <div id="photoPreview" class="mb-4"></div>
                        @endif

                        <div class="d-flex gap-2 mt-5">
                            <button type="submit" class="btn" style="background-color: #76a75d; color: white; font-weight: bold;">
                                <i class="bi bi-check-circle me-2"></i>Actualizar Registro
                            </button>
                            <a href="{{ route('medical_records.show', $pet) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function removePhoto(button) {
        const index = button.getAttribute('data-index');
        const input = document.getElementById('photosToRemove');
        const current = input.value ? input.value.split(',') : [];
        current.push(index);
        input.value = current.join(',');
        
        // Eliminar visualmente
        button.closest('[data-photo-index]').remove();
    }

    document.getElementById('photos')?.addEventListener('change', function(e) {
        const preview = document.getElementById('photoPreview');
        preview.innerHTML = '';
        
        const currentCount = document.querySelectorAll('img[alt="Foto"]').length;
        if (currentCount + this.files.length > 3) {
            alert('Máximo 3 fotos permitidas en total');
            this.value = '';
            return;
        }

        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.height = '150px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '10px';
                img.style.marginRight = '10px';
                img.style.marginBottom = '10px';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection
