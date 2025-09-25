@extends('layouts.app')
@section('content')
<div class="row mb-3">
    <div class="col">
        <h1>Movimientos</h1>
    </div>
    <div class="col-lg-2 mb-2 mb-lg-0">
        <button class="btn btn-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtros" aria-expanded="false">
            <i class="bi bi-filter me-2"></i>Filtros
        </button>
    </div>
    <div class="col-lg-2 mb-2 mb-lg-0">
        <button class="btn btn-primary w-100"  data-bs-toggle="modal" data-bs-target="#modalMovement"><i class="bi bi-plus-circle me-2"></i>Nuevo</button>
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
<div class="row">
    <div class="col table-responsive">
        <table class="table table-striped table-bordered text-center" id="movementsTable"></table>
    </div>
</div>
<div class="row">
    <div class="col text-center">
        <p class="display-5 fw-bold text-success">Total de ventas: Q.<span id="totalVentas"></span></p>
    </div>
</div>

<div class="modal fade" id="modalMovement" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalMovementTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMovementTitle">Nuevo movimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formMovement" method="POST" action="/api/movements" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col">
                            <label for="date">Fecha</label>
                            <input type="datetime-local" name="date" id="date" class="form-control" value="{{ Date::now()}}">
                            <div class="invalid-feedback" id="dateFeedback"></div>
                        </div>
                     
                    </div>
                    <div class="container border rounder bg-light p-2 mb-3">
                        <div class="row mb-3">
                            <div class="col-lg-8">
                                <h5>Productos</h5>
                            </div>
                            <div class="col-lg-2 mb-2 mb-lg-0">
                                <button type="button" id="buttonAdd" class="btn btn-sm w-100 btn-success"><i class="bi bi-plus-circle"></i></button>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" id="buttonDelete" class="btn btn-sm w-100 btn-danger"><i class="bi bi-dash-circle"></i></button>
                            </div>
                        </div>
                        <hr>
                        <p class="text-muted">Presione el botón (+) para agregar productos o el botón (-) para quitarlos</p>
                        <div id="divInputs">
                        </div>
                        <div id="products"></div>
                        <div id="amounts"></div>
                        <div class="invalid-feedback" id="productsFeedback"></div>
                        <div class="invalid-feedback" id="amountsFeedback"></div>
                    </div>
                    <div class="row mb-3 justify-content-end">
                        <div class="col-lg-2 d-flex mb-2 mb-lg-0">
                            <input type="radio" class="btn-check" name="type" id="type" value="1" checked >
                            <label class="btn btn-outline-primary w-100" for="type">In</label>
                        </div>
                        <div class="col-lg-2 d-flex ">
                            <input type="radio" class="btn-check" name="type" id="type2" value="2" >
                            <label class="btn btn-outline-primary w-100" for="type2">Out</label>
                        </div>
                        
                    </div>
                    <div class="row mb-3 justify-content-end">
                        <div class="invalid-feedback" id="typeFeedback"></div>
                    </div>
                </form>
 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formMovement" id="btnGuardar" class="btn btn-primary"><span class="spinner-border spinner-border-sm me-2" role="status" id="spinnerGuardar" aria-hidden="true"></span>Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetalle" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalDetalleTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleTitle">Detalle de movimientos - <span id="productTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="in-tab" data-bs-toggle="tab" data-bs-target="#in-tab-pane" type="button" role="tab" aria-controls="in-tab-pane" aria-selected="true">Ingresos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="out-pane" data-bs-toggle="tab" data-bs-target="#out-tab-pane" type="button" role="tab" aria-controls="out-tab-pane" aria-selected="false">Egresos</button>
                    </li>
                  </ul>
                  <div class="tab-content border border-top-0 p-3" id="myTabContent">
                    <div class="tab-pane fade show active " id="in-tab-pane" role="tabpanel" aria-labelledby="in-tab" tabindex="0">
                        
                        <table id="ingresosTable" class="table table-bordered table-stripped"></table>
                
                    </div>
                    <div class="tab-pane fade" id="out-tab-pane" role="tabpanel" aria-labelledby="out-pane" tabindex="0">
                        <table id="egresosTable" class="table table-bordered table-stripped" ></table>
                    </div>
                  </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    @vite(['resources/js/movements/index.js'])
@endsection