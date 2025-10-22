@extends('layouts.app')
@section('content')

    <div class="row align-items-center g-5 py-4">
        <div class="col-12 col-lg-6">
            <h1 class="display-5 fw-bold text-primary mb-3">Descubre la experiencia Bodegona</h1>
            <p class="lead text-muted">Explora nuestro catálogo y encuentra los productos ideales para tu hogar y negocio. Actualizamos
                constantemente nuestro inventario para ofrecerte las mejores opciones.</p>
            <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">Iniciar sesión</a>
                @else
                    <a href="{{ route('movements.view') }}" class="btn btn-primary btn-lg px-4">Ir al panel</a>
                    <a href="{{ route('products.view') }}" class="btn btn-outline-primary btn-lg px-4">Gestionar productos</a>
                @endguest
            </div>
        </div>
        <div class="col-12 col-lg-6 text-center">
            <img src="{{ asset('./images/bodegona_logo.png') }}" class="img-fluid" alt="Logo Bodegona" style="max-height: 320px">
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Catálogo de productos</h2>
            <p class="text-muted mb-0">Precios mostrados en moneda local. Imágenes referenciales.</p>
        </div>
        <div class="text-muted small mt-3 mt-md-0">
            @auth
                <i class="bi bi-check-circle-fill text-success me-1"></i> Inventario sincronizado para administradores.
            @else
                <i class="bi bi-info-circle-fill text-primary me-1"></i> Inicia sesión para realizar compras o gestionar productos.
            @endauth
        </div>
    </div>

    <div class="row g-4">
        @forelse ($products as $product)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    @php
                        $imageUrls = $product->images
                            ->map(function ($image) {
                                return \Illuminate\Support\Str::startsWith($image->url, ['http://', 'https://'])
                                    ? $image->url
                                    : \Illuminate\Support\Facades\Storage::disk('public')->url($image->url);
                            })
                            ->values();
                    @endphp
                    @if ($imageUrls->isNotEmpty())
                        <div class="ratio ratio-4x3 bg-light rounded-top overflow-hidden">
                            <div id="productCarousel{{ $product->id }}" class="carousel slide h-100" data-bs-ride="false">
                                <div class="carousel-inner h-100">
                                    @foreach ($imageUrls as $url)
                                        <div class="carousel-item h-100 {{ $loop->first ? 'active' : '' }}">
                                            <img src="{{ $url }}" class="d-block w-100 h-100 object-fit-cover" alt="Imagen {{ $loop->iteration }} de {{ $product->name }}">
                                        </div>
                                    @endforeach
                                </div>
                                @if ($imageUrls->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Siguiente</span>
                                    </button>
                                    <div class="carousel-indicators">
                                        @foreach ($imageUrls as $indicatorIndex => $url)
                                            <button type="button" data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide-to="{{ $indicatorIndex }}" class="{{ $loop->first ? 'active' : '' }}" @if ($loop->first) aria-current="true" @endif aria-label="Imagen {{ $indicatorIndex + 1 }}"></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="ratio ratio-4x3 bg-light rounded-top overflow-hidden">
                            <img src="{{ asset('images/bodegona_logo.png') }}" class="w-100 h-100 object-fit-cover" alt="Imagen de {{ $product->name }}">
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $product->name }}</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ $product->brand->name ?? 'Sin marca' }}</span>
                        </div>
                        @if ($product->description)
                            <p class="card-text text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 110) }}</p>
                        @else
                            <p class="card-text text-muted small flex-grow-1">Producto sin descripción disponible.</p>
                        @endif
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">${{ number_format($product->price, 2) }}</span>
                            @auth
                                <a href="{{ route('products.view') }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            @else
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-cart-plus me-1"></i>Agregar
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    No hay productos disponibles en este momento. Vuelve pronto para descubrir nuestras novedades.
                </div>
            </div>
        @endforelse
    </div>
@endsection
