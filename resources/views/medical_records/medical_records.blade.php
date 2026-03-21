@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- SIDEBAR DE MASCOTAS (BÚSQUEDA Y LISTADO) -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm" style="border-radius: 15px; border-top: 4px solid #76a75d;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-hospital me-2"></i>Pacientes
                    </h5>
                    
                    <!-- BUSCADOR -->
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Buscar paciente o dueño..." id="searchPets" style="border-color: #76a75d;">
                        <button class="btn" type="button" style="background-color: #76a75d; color: white;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <!-- LISTADO DE MASCOTAS -->
                    <ul class="list-group list-group-flush" id="petsList">
                        @forelse($pets as $pet)
                        <a href="{{ route('medical_records.show', $pet) }}" 
                           class="list-group-item list-group-item-action pet-item"
                           style="border-radius: 10px; margin-bottom: 8px; border-left: 4px solid transparent; transition: all 0.2s;"
                           data-pet-name="{{ $pet->name }}"
                           data-owner-name="{{ $pet->owner->name }} {{ $pet->owner->lastname }}"
                           @if($selectedPet && $selectedPet->id === $pet->id)
                           style="border-radius: 10px; margin-bottom: 8px; border-left: 4px solid #76a75d; background-color: #f8fff3; color: #76a75d;"
                           @endif>
                            <strong>{{ $pet->name }}</strong>
                            @if($pet->birth_date)
                                <span class="badge bg-success" style="margin-left: auto; display: inline-block;">{{ \Carbon\Carbon::parse($pet->birth_date)->age }} años</span>
                            @endif
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-paw-fill me-1"></i>{{ $pet->species }}
                                @if($pet->breed)
                                    - {{ $pet->breed }}
                                @endif
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $pet->owner->name }} {{ $pet->owner->lastname }}
                            </small>
                        </a>
                        @empty
                        <li class="list-group-item text-muted text-center py-4">
                            <i class="bi bi-inbox"></i><br>
                            No hay mascotas
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- DETALLE Y HISTORIAL DE MASCOTA -->
        <div class="col-md-9">
            @if($selectedPet)
                <!-- PERFIL DEL PACIENTE -->
                <div class="card shadow-sm mb-4" style="border-radius: 15px; border-top: 4px solid #76a75d;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-paw-fill me-2" style="color: #76a75d;"></i>{{ $selectedPet->name }}
                                </h5>
                                <p class="mb-2">
                                    <strong><i class="bi bi-tag me-2" style="color: #76a75d;"></i>Especie:</strong> {{ $selectedPet->species }}
                                </p>
                                <p class="mb-2">
                                    <strong><i class="bi bi-bookmark me-2" style="color: #76a75d;"></i>Raza:</strong> 
                                    {{ $selectedPet->breed ?? 'No especificada' }}
                                </p>
                                <p class="mb-2">
                                    <strong><i class="bi bi-person-circle me-2" style="color: #76a75d;"></i>Propietario:</strong> 
                                    {{ $selectedPet->owner->name }} {{ $selectedPet->owner->lastname }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="p-3" style="background-color: #f8fff3; border-radius: 10px; border-left: 4px solid #76a75d;">
                                    <small class="text-muted d-block">Última visita</small>
                                    <strong style="color: #76a75d; font-size: 1.2rem;">
                                        @if($lastVisit)
                                            {{ $lastVisit->visited_at->format('d M Y') }}
                                        @else
                                            Sin visitas
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HISTORIAL Y TABS -->
                <div class="card shadow-sm" style="border-radius: 15px;">
                    <div class="card-header" style="background-color: white; border-bottom: 2px solid #e9ecef; border-radius: 15px 15px 0 0;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-file-earmark-text me-2" style="color: #76a75d;"></i>Registros Médicos
                            </h5>
                            <a href="{{ route('medical_records.create', $selectedPet) }}" class="btn btn-sm" style="background-color: #76a75d; color: white; border-radius: 25px;">
                                <i class="bi bi-plus-circle me-1"></i>Nueva Entrada
                            </a>
                        </div>

                        <!-- TABS -->
                        <ul class="nav nav-tabs border-0" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab" style="color: #76a75d; font-weight: bold; border-bottom: 3px solid #76a75d;">
                                    <i class="bi bi-file-earmark-text me-1"></i>Historial
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kardex-tab" data-bs-toggle="tab" data-bs-target="#kardex" type="button" role="tab" style="color: #6c757d;">
                                    <i class="bi bi-bookmark me-1"></i>Kardex
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="vacunas-tab" data-bs-toggle="tab" data-bs-target="#vacunas" type="button" role="tab" style="color: #6c757d;">
                                    <i class="bi bi-capsule me-1"></i>Vacunas
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- TAB CONTENT -->
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- TAB: HISTORIAL -->
                            <div class="tab-pane fade show active" id="historial" role="tabpanel" tabindex="0">
                                @forelse($medicalRecords as $record)
                                <div class="card shadow-sm mb-3" style="border-radius: 15px; border-left: 4px solid #76a75d;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">
                                                    <i class="bi bi-calendar-event me-2" style="color: #76a75d;"></i>{{ $record->visited_at->format('d M Y') }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-person-circle me-1"></i>Dr. {{ $record->doctor->name }} {{ $record->doctor->lastname }}
                                                </small>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('medical_records.edit', $record) }}" class="btn btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('medical_records.destroy', $record) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este registro?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted"><i class="bi bi-question-circle me-1" style="color: #76a75d;"></i>Motivo</small>
                                                <p class="mb-0 fw-medium">{{ $record->reason }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted"><i class="bi bi-stethoscope me-1" style="color: #76a75d;"></i>Diagnóstico</small>
                                                <p class="mb-0 fw-medium">{{ $record->diagnosis }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted"><i class="bi bi-pill me-1" style="color: #76a75d;"></i>Tratamiento</small>
                                            <p class="mb-0 fw-medium">{{ $record->treatment }}</p>
                                        </div>

                                        @if($record->notes)
                                        <div class="mb-3">
                                            <small class="text-muted"><i class="bi bi-chat-left-text me-1" style="color: #76a75d;"></i>Notas</small>
                                            <p class="mb-0 fw-medium">{{ $record->notes }}</p>
                                        </div>
                                        @endif

                                        <!-- FOTOS -->
                                        @if($record->photos && count($record->photos) > 0)
                                        <div class="mt-3 pt-3" style="border-top: 1px solid #e9ecef;">
                                            <small class="text-muted"><i class="bi bi-image me-1" style="color: #76a75d;"></i>Fotos</small>
                                            <div class="d-flex gap-2 mt-2" style="overflow-x: auto;">
                                                @foreach($record->photos as $photo)
                                                <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $photo) }}" alt="Foto médica" 
                                                         style="width: 120px; height: 120px; object-fit: cover; border-radius: 10px; border: 2px solid #76a75d; cursor: pointer; transition: transform 0.2s;"
                                                         onmouseover="this.style.transform='scale(1.05)'"
                                                         onmouseout="this.style.transform='scale(1)'">
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="alert alert-info" style="border-radius: 15px; border-left: 4px solid #76a75d;">
                                    <i class="bi bi-info-circle me-2"></i>No hay registros médicos aún. 
                                    <a href="{{ route('medical_records.create', $selectedPet) }}" style="color: #76a75d; font-weight: bold;">Crear uno</a>
                                </div>
                                @endforelse
                            </div>

                            <!-- TAB: KARDEX -->
                            <div class="tab-pane fade" id="kardex" role="tabpanel" tabindex="0">
                                <div class="text-center py-5">
                                    <i class="bi bi-hourglass-split" style="font-size: 2.5rem; color: #76a75d;"></i>
                                    <p class="text-muted mt-3">Próximamente...</p>
                                </div>
                            </div>

                            <!-- TAB: VACUNAS -->
                            <div class="tab-pane fade" id="vacunas" role="tabpanel" tabindex="0">
                                <div class="text-center py-5">
                                    <i class="bi bi-capsule" style="font-size: 2.5rem; color: #76a75d;"></i>
                                    <p class="text-muted mt-3">Próximamente...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
            <div class="alert alert-info" style="border-radius: 15px; border-left: 4px solid #76a75d;">
                <i class="bi bi-info-circle me-2"></i>Selecciona una mascota para ver su historial
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Búsqueda de mascotas por nombre O nombre del dueño
    document.getElementById('searchPets').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const petItems = document.querySelectorAll('.pet-item');
        
        petItems.forEach(item => {
            const petName = item.getAttribute('data-pet-name').toLowerCase();
            const ownerName = item.getAttribute('data-owner-name').toLowerCase();
            
            if (petName.includes(searchTerm) || ownerName.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Cambiar color de las tabs
    const tabs = document.querySelectorAll('.nav-link');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function() {
            tabs.forEach(t => {
                t.style.color = '#6c757d';
                t.style.borderBottom = 'none';
            });
            this.style.color = '#76a75d';
            this.style.borderBottom = '3px solid #76a75d';
            this.style.fontWeight = 'bold';
        });
    });
</script>
@endsection
