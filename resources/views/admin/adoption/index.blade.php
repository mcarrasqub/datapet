@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    {{-- Session alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0" role="alert" style="background-color: #eaf5e6; color: #3b612c;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1e293b;">Administración de Adopciones</h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Gestiona las solicitudes de adopción y los perfiles de mascotas.</p>
        </div>
        <a href="{{ route('admin.adoptions.create') }}" class="btn text-white fw-bold d-flex align-items-center" style="background-color: #65a34e; border-radius: 8px; padding: 10px 20px;">
            <i class="bi bi-plus-circle me-2"></i>Crear Mascota en Adopción
        </a>
    </div>

    <div class="row g-4">
        {{-- Left Column: Adoption Requests --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center">
                        <i class="bi bi-file-earmark-person text-pet-green me-2 fs-5"></i>Solicitudes de Adopción
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($viewData['requests'] as $request)
                        <div class="card p-3 mb-3 border shadow-none rounded-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $request->user->name ?? 'Usuario' }} {{ $request->user->lastname ?? '' }}</h6>
                                    <small class="text-muted d-block"><i class="bi bi-envelope me-1"></i>{{ $request->user->email ?? 'N/A' }}</small>
                                    <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $request->user->phone ?? 'N/A' }}</small>
                                </div>
                                <span class="badge rounded-pill px-3 py-1 fs-80 
                                    @if($request->status === 'approved') bg-success 
                                    @elseif($request->status === 'rejected') bg-danger 
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($request->status === 'pending' ? 'Pendiente' : ($request->status === 'approved' ? 'Aprobada' : 'Rechazada')) }}
                                </span>
                            </div>
                            
                            <hr class="my-2 text-muted opacity-25">

                            <p class="mb-2 fs-90">
                                <strong>Mascota solicitada:</strong> 
                                <span class="text-pet-green fw-bold">{{ $request->pet->name ?? 'Mascota' }}</span> 
                                <span class="text-muted">({{ $request->pet->species ?? 'N/A' }})</span>
                            </p>

                            @if($request->status === 'pending')
                                <div class="d-flex gap-2 mt-2">
                                    <form method="POST" action="{{ route('adoption.approve', $request->id) }}" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success btn-sm rounded-pill px-3">
                                            <i class="bi bi-check2 me-1"></i>Aprobar
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('adoption.reject', $request->id) }}" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-danger btn-sm rounded-pill px-3">
                                            <i class="bi bi-x-lg me-1"></i>Rechazar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-chat-left-quote fs-1 d-block mb-2 opacity-50"></i>
                            No hay solicitudes de adopción registradas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Column: Pets in Adoption --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center">
                        <i class="bi bi-heart text-pet-green me-2 fs-5"></i>Mascotas en Adopción
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($viewData['pets'] as $pet)
                        <div class="card p-3 mb-3 border shadow-none rounded-3">
                            <div class="d-flex gap-3">
                                {{-- Thumbnail Photo --}}
                                <div class="flex-shrink-0">
                                    @if($pet->getPhoto())
                                        <img src="{{ asset('storage/' . $pet->getPhoto()) }}" alt="Foto" class="rounded object-fit-cover" style="width: 70px; height: 70px;">
                                    @else
                                        <div class="bg-pet-green text-white rounded d-flex align-items-center justify-content-center fw-bold" style="width: 70px; height: 70px; font-size: 1.2rem;">
                                            {{ strtoupper(substr($pet->getName(), 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                {{-- Pet Info --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $pet->getName() }}</h6>
                                            <small class="text-muted">{{ $pet->getSpecies() }} @if($pet->getBreed()) • {{ $pet->getBreed() }} @endif</small>
                                        </div>
                                        <span class="badge rounded-pill px-2 py-1 fs-75 {{ $pet->getAvailableForAdoption() ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $pet->getAvailableForAdoption() ? 'Disponible' : 'No disponible' }}
                                        </span>
                                    </div>
                                    
                                    <p class="mb-2 mt-1 text-muted fs-85 text-truncate-2">
                                        {{ $pet->getAdoptionDescription() ?? 'Sin descripción de adopción' }}
                                    </p>

                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('admin.adoptions.edit', $pet->getId()) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-pencil me-1"></i>Editar Mascota
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-heartbreak fs-1 d-block mb-2 opacity-50"></i>
                            No hay mascotas en adopción registradas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection