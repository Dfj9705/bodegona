@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col">
        <h1>Estadísticas</h1>
    </div>
    <div class="col-lg-2 mb-2 mb-lg-0">
        <button class="btn btn-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtros" aria-expanded="false">
            <i class="bi bi-filter me-2"></i>Filtros
        </button>
    </div>
</div>
<div class="row mb-3 collapse" id="filtros">
    <div class="col mb-2 mb-lg-0">
        <label for="fechaInicio">Fecha inicial</label>
        <input type="datetime-local" name="fechaInicio" id="fechaInicio" class="form-control" >
    </div>
    <div class="col mb-2 mb-lg-0">
        <label for="fechaFinal">Fecha final</label>
        <input type="datetime-local" name="fechaFinal" id="fechaFinal" class="form-control"  >
    </div>
    <div class="col-lg-1 d-flex flex-column justify-content-end">
        <button id="btnAplicar" class="btn btn-info">Aplicar</button>
    </div>
</div>
<div class="row mb-3">
    <div class="col p-5 border rounded">
        <h2>Movimientos por producto</h2>
        <canvas id="chartMovements"></canvas>
    </div>
</div>
<div class="row mb-3">
    <div class="col p-5 border rounded">
        <h2>Ventas por mes <span id="yearChartSales"></span> </h2>
        <canvas id="chartSales"></canvas>
    </div>
</div>
@endsection
@section('scripts')
@vite(['resources/js/charts/index.js'])
@endsection