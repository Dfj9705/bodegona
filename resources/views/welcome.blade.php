@extends('layouts.app')
@section('content')

    <div class="row justify-content-center h-100 mt-5">
        <div class="col-11 mb-2 mb-lg-0 col-lg-4 rounded shadow p-0">
            <img class="w-100" src="{{ asset('./images/bodegona_logo.png') }}" alt="logo EA">
        </div>
        <div class="col-lg-4 d-flex flex-column justify-content-center">
            @guest
            <h1 class="text-center">¡Bienvenido!</h1>
            <p class="text- lead text-center">Inicia sesión para acceder al sistema</p>
            <a href="{{ route('login') }}" class="btn btn-primary w-100">Iniciar sesión</a>
            @else
            <div class="accordion" id="accordionMenu">
                <h1 class="text-center">¡Bienvenido, {{ auth()->user()->name }}!</h1>
                <p class="text- lead text-center">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="tituloDos">
                        <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#submenuDos" aria-expanded="true" aria-controls="submenuDos">
                            <i class="bi bi-boxes me-2"></i>Inventario
                        </button>
                    </h2>
                    <div id="submenuDos" class="accordion-collapse collapse show" aria-labelledby="tituloDos" data-bs-parent="#accordionMenu">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <a href="{{route('movements.view')}}" class="list-group-item list-group-item-action"><i class="bi bi-arrow-left-right me-2"></i>Movimientos</a>
                                @if (auth()->check() && auth()->user()->hasRole('administrador'))
                                <a href="{{route('chart.view')}}" class="list-group-item list-group-item-action"><i class="bi bi-graph-up me-2"></i>Estadísticas</a>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @if (auth()->check() && auth()->user()->hasRole('administrador'))
                <div class="accordion-item">
                    <h2 class="accordion-header" id="tituloUno">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#submenuUno" aria-expanded="false" aria-controls="submenuUno">
                            <i class="bi bi-gear me-2"></i>Administración
                        </button>
                    </h2>
                    <div id="submenuUno" class="accordion-collapse collapse" aria-labelledby="tituloUno" data-bs-parent="#accordionMenu">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <a href="{{route('user.view')}}" class="list-group-item list-group-item-action"><i class="bi bi-people-fill me-2"></i>Crear usuario</a>
                                <a href="{{route('products.view')}}" class="list-group-item list-group-item-action"><i class="bi bi-boxes me-2"></i>Crear producto</a>
                                <a href="{{route('brand.view')}}" class="list-group-item list-group-item-action"><i class="bi bi-tag me-2"></i>Crear marca</a>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endguest
        </div>
    </div>
@endsection
