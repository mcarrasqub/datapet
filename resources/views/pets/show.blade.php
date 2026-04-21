@extends($layout)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Perfil de {{ $viewData['pet']->getName() }}</h4>
                <small class="text-muted">Ficha de mascota y documentos clínicos</small>
            </div>
            <a href="{{ route('home.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-2 text-center">
                        @if($viewData['pet']->getPhoto())
                            <img src="{{ asset('storage/' . $viewData['pet']->getPhoto()) }}" alt="Foto"
                                class="rounded-circle object-fit-cover" style="width:120px;height:120px;">
                        @else
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width:120px;height:120px;">
                                <span
                                    class="fw-bold text-secondary">{{ strtoupper(substr($viewData['pet']->getName(), 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <div class="row g-3">
                            <div class="col-md-3"><small class="text-muted d-block">Especie</small><span
                                    class="fw-semibold">{{ $viewData['pet']->getSpecies() }}</span></div>
                            <div class="col-md-3"><small class="text-muted d-block">Raza</small><span
                                    class="fw-semibold">{{ $viewData['pet']->getBreed() ?? 'N/D' }}</span></div>
                            <div class="col-md-3"><small class="text-muted d-block">Edad</small><span
                                    class="fw-semibold">{{ $viewData['pet']->getAge() ? $viewData['pet']->getAge() . ' años' : 'N/D' }}</span>
                            </div>
                            <div class="col-md-3"><small class="text-muted d-block">Peso</small><span
                                    class="fw-semibold">{{ $viewData['pet']->getWeight() ? $viewData['pet']->getWeight() . ' kg' : 'N/D' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Resumen clínico</h6>
                <p class="text-muted mb-4">Desde esta vista puedes consultar la información general de la mascota.</p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('pets.exams', ['pet_id' => $viewData['pet']->getId()]) }}"
                        class="btn btn-pet-green text-white">
                        <i class="bi bi-journal-medical me-2"></i>Ver Exámenes
                    </a>
                    <a href="{{ route('home.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver a Mis Mascotas
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection