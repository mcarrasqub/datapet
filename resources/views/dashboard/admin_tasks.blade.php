@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Tareas Pendientes de Doctores</h4>
                <p class="text-muted mb-0">Supervisa pendientes por doctor, identifica atrasos y actualiza estados.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body"><small class="text-muted">Total tareas</small>
                        <h4 class="fw-bold mb-0">{{ $metrics['total'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body"><small class="text-muted">Pendientes</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ $metrics['pending'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body"><small class="text-muted">En proceso</small>
                        <h4 class="fw-bold mb-0 text-primary">{{ $metrics['in_progress'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body"><small class="text-muted">Completadas</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $metrics['completed'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body"><small class="text-muted">Vencidas</small>
                        <h4 class="fw-bold mb-0 text-danger">{{ $metrics['overdue'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('tasks.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Doctor</label>
                        <select name="doctor_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($allDoctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected((string) $doctorFilter === (string) $doctor->id)>
                                    {{ $doctor->name }} {{ $doctor->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pending" @selected($statusFilter === 'pending')>Pendiente</option>
                            <option value="in_progress" @selected($statusFilter === 'in_progress')>En proceso</option>
                            <option value="completed" @selected($statusFilter === 'completed')>Completada</option>
                            <option value="overdue" @selected($statusFilter === 'overdue')>Vencida</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-pet-green text-white">Filtrar</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse($doctors as $doctor)
                @php
                    $doctorTasks = $doctor->doctorTasks;
                    $doctorOverdue = $doctorTasks->filter(function ($task) {
                        return $task->is_overdue || $task->status === 'overdue';
                    })->count();
                    $doctorPending = $doctorTasks->where('status', 'pending')->count();
                    $doctorInProgress = $doctorTasks->where('status', 'in_progress')->count();
                    $doctorCompleted = $doctorTasks->where('status', 'completed')->count();
                @endphp

                <div class="card border-0 shadow-sm rounded-3">
                    <div
                        class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-1">Dr(a). {{ $doctor->name }} {{ $doctor->lastname }}</h6>
                            <small class="text-muted">{{ $doctor->email }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span
                                class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2">Pendientes:
                                {{ $doctorPending }}</span>
                            <span
                                class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-2">En proceso:
                                {{ $doctorInProgress }}</span>
                            <span
                                class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-2">Completadas:
                                {{ $doctorCompleted }}</span>
                            <span
                                class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-2">Vencidas:
                                {{ $doctorOverdue }}</span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($doctorTasks->count() > 0)
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Tarea</th>
                                            <th>Origen</th>
                                            <th>Prioridad</th>
                                            <th>Fecha límite</th>
                                            <th>Estado</th>
                                            <th class="text-end pe-4">Actualizar estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($doctorTasks as $task)
                                            @php
                                                $isOverdue = $task->is_overdue || $task->status === 'overdue';
                                            @endphp
                                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                                <td class="ps-4">
                                                    <div class="fw-semibold">{{ $task->title }}</div>
                                                    @if($task->description)
                                                        <small class="text-muted">{{ $task->description }}</small>
                                                    @endif
                                                    @if($isOverdue)
                                                        <div><span class="badge bg-danger mt-1">Vencida</span></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->source_type === 'medical_exam')
                                                        <span class="badge bg-info-subtle text-info-emphasis">Examen cliente</span>
                                                    @elseif($task->source_type === 'medical_record')
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Historia clínica</span>
                                                    @else
                                                        <span class="badge bg-light text-dark border">Manual</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->priority === 'high')
                                                        <span class="badge bg-danger-subtle text-danger-emphasis">Alta</span>
                                                    @elseif($task->priority === 'medium')
                                                        <span class="badge bg-warning-subtle text-warning-emphasis">Media</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Baja</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'Sin fecha' }}</td>
                                                <td>
                                                    @if($task->status === 'pending')
                                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                                    @elseif($task->status === 'in_progress')
                                                        <span class="badge bg-primary">En proceso</span>
                                                    @elseif($task->status === 'completed')
                                                        <span class="badge bg-success">Completada</span>
                                                    @else
                                                        <span class="badge bg-danger">Vencida</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <form action="{{ route('tasks.updateStatus', $task) }}" method="POST"
                                                        class="d-inline-flex gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" class="form-select form-select-sm">
                                                            <option value="pending" @selected($task->status === 'pending')>Pendiente
                                                            </option>
                                                            <option value="in_progress" @selected($task->status === 'in_progress')>En
                                                                proceso</option>
                                                            <option value="completed" @selected($task->status === 'completed')>Completada
                                                            </option>
                                                            <option value="overdue" @selected($task->status === 'overdue')>Vencida
                                                            </option>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-outline-pet-green">Guardar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-4 py-4 text-muted">Este doctor no tiene tareas con los filtros aplicados.</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5 text-muted">
                        No hay doctores registrados o no hay resultados para los filtros aplicados.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection