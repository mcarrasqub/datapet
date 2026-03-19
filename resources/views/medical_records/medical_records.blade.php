@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    Pacientes
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Buscar paciente...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item active">
                            <b>Kiwi</b><br>
                            <small>Loro Gris Africano</small><br>
                            <small>María González</small>
                        </li>
                        <li class="list-group-item">
                            <b>Copo</b><br>
                            <small>Conejo Enano Holandés</small><br>
                            <small>María González</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>Kiwi</h3>
                            <p>Loro Gris Africano • María González</p>
                        </div>
                        <div>
                            <small>Última visita</small><br>
                            <b>28 Ene 2026</b>
                        </div>
                    </div>
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Historial</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Kardex</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Vacunas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">+ Nueva Entrada</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    {{-- El contenido de las pestañas se cargará aquí --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
