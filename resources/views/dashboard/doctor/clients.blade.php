@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0" role="alert" style="background-color: #eaf5e6; color: #3b612c;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0" role="alert" style="background-color: #fce8e6; color: #a51d24;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card border-0 shadow rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-diagram-3 text-pet-green fs-4 me-2"></i>
                <h5 class="fw-bold mb-0">Lista de Clientes y Mascotas</h5>
            </div>

            <!-- Formularios de búsqueda -->
            <form method="GET" action="{{ route('clients.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-medium">Cliente</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="client_search" value="{{ $clientSearch ?? '' }}" class="form-control bg-light border-start-0 input-no-outline" placeholder="Buscar por identificación, teléfono o nombre del cliente">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-medium">Mascota</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="pet_search" value="{{ $petSearch ?? '' }}" class="form-control bg-light border-start-0 input-no-outline" placeholder="Buscar por nombre o identificador de la mascota">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end justify-content-center">
                        <button type="submit" class="btn btn-pet-green text-white fw-medium w-100 py-2 rounded-3">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle border-top mb-0 min-w-900">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3 text-muted fw-bold fs-85 w-30">Nombre</th>
                            <th class="py-3 px-3 text-muted fw-bold fs-85 w-15">Identificador</th>
                            <th class="py-3 px-3 text-muted fw-bold fs-85 w-15">Teléfono</th>
                            <th class="py-3 px-3 text-muted fw-bold fs-85 w-30">Mascotas</th>
                            <th class="py-3 px-3 text-muted fw-bold fs-85 w-10">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            @php
                                $initials = strtoupper(substr($client->getName(), 0, 1) . substr($client->getLastname(), 0, 1));
                            @endphp
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold avatar-40">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-95">{{ $client->getName() }} {{ $client->getLastname() }}</h6>
                                            <small class="text-muted d-block fs-80">{{ $client->getEmail() }}</small>
                                            <small class="text-muted d-block fs-75">Creado el {{ $client->getCreatedAt()->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 text-secondary fs-90">
                                    {{ $client->getId() }}
                                </td>
                                <td class="px-3 text-secondary fs-90">
                                    {{ $client->getPhone() ?? 'N/A' }}
                                </td>
                                <td class="px-3">
                                    <div class="d-flex flex-column gap-2">
                                        @forelse($client->pets as $pet)
                                            <a href="{{ route('medical_records.show', $pet->getId()) }}" class="pet-hover-card pet-hover-card-bg text-decoration-none d-flex align-items-center justify-content-between p-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    @if($pet->getPhoto())
                                                        <img src="{{ asset('storage/' . $pet->getPhoto()) }}" alt="Foto" class="rounded-circle me-3 object-fit-cover avatar-32">
                                                    @else
                                                        <div class="bg-pet-green text-white rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold avatar-32 fs-80">
                                                            {{ strtoupper(substr($pet->getName(), 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="d-flex align-items-center">
                                                            <h6 class="pet-name mb-0 fw-bold me-2 text-dark fs-90">{{ $pet->getName() }}</h6>
                                                            @if($pet->getIsDeceased())
                                                                <span class="badge bg-danger rounded-pill px-2 py-0 fs-65">Fallecido</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted fs-80">{{ ucfirst($pet->getGender()) }}</small>
                                                    </div>
                                                </div>
                                                <i class="bi bi-chevron-right text-muted fs-80"></i>
                                            </a>
                                        @empty
                                            <span class="text-muted small">Sin mascotas</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-3">
                                    <div class="d-flex gap-2">
                                        <a href="{{ $client->pets->isNotEmpty() ? route('medical_records.show', $client->pets->first()->getId()) : '#' }}" class="btn btn-outline-pet-green bg-white rounded-4 btn-sm px-3 py-1 fw-medium fs-85">
                                            Ver detalles
                                        </a>
                                        <button type="button" 
                                            class="btn btn-outline-primary bg-white rounded-4 btn-sm px-3 py-1 fw-medium fs-85"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editClientModal{{ $client->getId() }}">
                                            <i class="bi bi-pencil me-1"></i> Editar
                                        </button>
                                    </div>

                                    <!-- Modal de Edición de Cliente -->
                                    <div class="modal fade" id="editClientModal{{ $client->getId() }}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editClientModalLabel{{ $client->getId() }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow rounded-4">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-pet-green" id="editClientModalLabel{{ $client->getId() }}">
                                                        <i class="bi bi-person-gear me-2"></i>Editar Datos de Cliente
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('doctor.clients.update', $client->getId()) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4">
                                                        <p class="text-muted small mb-4">Actualiza la información personal del propietario. Cédula: <strong>{{ $client->getId() }}</strong></p>
                                                        
                                                        <div class="row g-3 text-start">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold small text-secondary">Nombre *</label>
                                                                <input type="text" name="name" class="form-control bg-light border-0 py-2" value="{{ $client->getName() }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold small text-secondary">Apellido *</label>
                                                                <input type="text" name="lastname" class="form-control bg-light border-0 py-2" value="{{ $client->getLastname() }}" required>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-bold small text-secondary">Correo Electrónico *</label>
                                                                <input type="email" name="email" class="form-control bg-light border-0 py-2" value="{{ $client->getEmail() }}" required>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-bold small text-secondary">Teléfono *</label>
                                                                <input type="text" name="phone" class="form-control bg-light border-0 py-2" value="{{ $client->getPhone() }}" required>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-bold small text-secondary">Dirección</label>
                                                                <input type="text" name="address" class="form-control bg-light border-0 py-2" value="{{ $client->getAddress() }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-pet-green text-white rounded-pill px-4">Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No se encontraron clientes.
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
