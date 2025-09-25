@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col">
        <h1>Productos</h1>
    </div>
    <div class="col-lg-2">
        <button class="btn btn-primary w-100"  data-bs-toggle="modal" data-bs-target="#modalCreateProduct"><i class="bi bi-plus-circle me-2"></i> Crear</button>
    </div>
</div>
<div class="row">
    <div class="col table-responsive">
        <table class="table table-striped table-bordered text-center" id="productTable"></table>
    </div>
</div>

<div class="modal fade" id="modalCreateProduct" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createProductTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProductTitle">Crear producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProduct" method="POST" action="/api/products" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-2">
                        <div class="col-lg-8">
                            <label for="name">Nombre del producto</label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm">
                            <div class="invalid-feedback" id="nameFeedback"></div>
                        </div>
                        <div class="col-lg-4">
                            <label for="price">Precio del producto</label>
                                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control form-control-sm">
                            <div class="invalid-feedback" id="priceFeedback"></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="brand_id">Marca del producto</label>
                            <select name="brand_id" id="brand_id" class="form-control form-control-sm">
                                <option value="">Seleccione</option>
                                @foreach ($brands as $brand)
                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="brand_idFeedback"></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="description">Descripción del producto (opcional)</label>
                            <textarea name="description" id="description" rows="10" class="form-control form-control-sm"></textarea>
                            <div class="invalid-feedback" id="descriptionFeedback"></div>
                        </div>
                    </div>
                    {{-- <div class="row mb-2">
                        <div class="col">
                            <label for="images">Imágenes del producto</label>
                            <input type="file" accept="image/*" name="images" id="images" class="form-control">
                            <div class="invalid-feedback" id="imagesFeedback"></div>
                        </div>

                    </div> --}}
                </form>
 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="btnModificar" class="btn btn-warning">Modificar</button>
                <button type="submit" form="formProduct" id="btnGuardar" class="btn btn-primary"><span class="spinner-border spinner-border-sm me-2" role="status" id="spinnerGuardar" aria-hidden="true"></span>Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalImages" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="imageTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageTitle">Imágenes del producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" id="bodyCarousel">
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                    </button>
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
@vite(['resources/js/products/index.js'])
@endsection