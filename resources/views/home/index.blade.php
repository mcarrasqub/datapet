@extends($layout)

@section('content')
<div class="container py-4">
  <div class="mb-4">
    <h3 class="fw-bold mb-1" style="color: #1e293b;">Mis Mascotas Exóticas</h3>
    <p class="text-muted mb-4" style="font-size: 0.95rem;">Información general de tus mascotas exóticas registradas</p>
  </div>

  @if($viewData['pets']->count() > 0)
    <div class="row g-4 mb-5">
      @foreach($viewData['pets'] as $pet)
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            {{-- Image on top --}}
            @if($pet->getPhoto())
              <img src="{{ asset('storage/' . $pet->getPhoto()) }}" class="card-img-top" alt="{{ $pet->getName() }}" style="height: 180px; object-fit: cover;">
            @else
              <div class="bg-light d-flex align-items-center justify-content-center card-img-top" style="height: 180px;">
                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
              </div>
            @endif

            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <h4 class="fw-bold mb-0" style="color: #1e293b;">{{ $pet->getName() }}</h4>
                <span class="badge" style="background-color: #65a34e; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 6px;">Vacunas al día</span>
              </div>
              
              <p class="text-muted small mb-4">
                {{ $pet->getSpecies() }} 
                @if($pet->getBirthDate())
                  &bull; {{ \Carbon\Carbon::parse($pet->getBirthDate())->age }} años
                @endif
              </p>

              <div class="row mb-4">
                <div class="col-6">
                  <div class="d-flex align-items-start">
                    <i class="bi bi-bag text-muted me-2 mt-1"></i>
                    <div>
                      <small class="text-muted d-block" style="font-size: 0.75rem;">Peso</small>
                      <span class="fw-bold" style="color: #1e293b;">{{ $pet->getWeight() ? $pet->getWeight() . ' kg' : 'N/D' }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex align-items-start">
                    <i class="bi bi-calendar text-muted me-2 mt-1"></i>
                    <div>
                      <small class="text-muted d-block" style="font-size: 0.75rem;">Próxima Cita</small>
                      <span class="fw-bold" style="color: #1e293b;">Por agendar</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Divider line -->
              <div class="border-top mb-3"></div>

              <div class="d-flex gap-2">
                <a href="{{ route('pets.show', $pet->getId()) }}" class="btn w-100 text-white fw-bold d-flex align-items-center justify-content-center" style="background-color: #65a34e; border-radius: 8px; padding: 10px;">
                  <i class="bi bi-activity me-2"></i> Ver Detalles Completos
                </a>
                
                {{-- Maintain edit and delete functionalities as small buttons next to details --}}
                <a href="{{ route('pets.edit', $pet->getId()) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; padding: 10px;" title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('pets.destroy', $pet->getId()) }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('¿Eliminar esta mascota?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger d-flex align-items-center justify-content-center h-100" style="border-radius: 8px; padding: 10px;" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Bottom Summary Cards --}}
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <h6 class="text-muted mb-4">Total de Mascotas</h6>
            <h2 class="fw-bold mb-0" style="color: #65a34e;">{{ $viewData['pets']->count() }}</h2>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <h6 class="text-muted mb-4">Próximas Citas</h6>
            <h2 class="fw-bold mb-0" style="color: #65a34e;">0</h2>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <h6 class="text-muted mb-4">Recordatorios Activos</h6>
            <h2 class="fw-bold mb-0" style="color: #65a34e;">0</h2>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
      <i class="bi bi-info-circle me-2"></i>Aún no tienes mascotas registradas.
    </div>
  @endif
</div>
@endsection
