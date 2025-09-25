@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col">
        <h1>Marcas</h1>
    </div>
    <div class="col-lg-2">
        <button class="btn btn-primary w-100"  data-bs-toggle="modal" data-bs-target="#modalCreateBrand"><i class="bi bi-plus-circle me-2"></i> Crear</button>
    </div>
</div>
<div class="row">
    <div class="col table-responsive">
        <table class="table table-striped table-bordered text-center" id="brandTable"></table>
    </div>
</div>

<div class="modal fade" id="modalCreateBrand" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createBrandTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBrandTitle">Crear marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBrand" method="POST" action="/api/brands" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-2">
                        <div class="col">
                            <label for="name">Nombre de la marca</label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm">
                            <div class="invalid-feedback" id="nameFeedback"></div>
                        </div>
                    </div>
  
                </form>
 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="btnModificar" class="btn btn-warning">Modificar</button>
                <button type="submit" form="formBrand" id="btnGuardar" class="btn btn-primary"><span class="spinner-border spinner-border-sm me-2" role="status" id="spinnerGuardar" aria-hidden="true"></span>Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@vite(['resources/js/brands/index.js'])
@endsection