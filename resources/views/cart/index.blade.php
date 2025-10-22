@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Tu carrito de compras</h1>
            <p class="text-muted mb-0">Revisa los productos seleccionados antes de continuar con tu compra.</p>
        </div>
        <div class="text-muted small">
            <i class="bi bi-bag-check me-1"></i>{{ $itemCount }} {{ \Illuminate\Support\Str::plural('artículo', $itemCount) }} en el carrito
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if ($cart->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x display-4 text-muted mb-3"></i>
                <h2 class="h4">Tu carrito está vacío</h2>
                <p class="text-muted">Explora nuestro catálogo y agrega tus productos favoritos.</p>
                <a href="{{ route('welcome') }}" class="btn btn-primary"><i class="bi bi-bag-plus me-2"></i>Descubrir productos</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Producto</th>
                                        <th scope="col" class="text-center">Precio</th>
                                        <th scope="col" class="text-center">Cantidad</th>
                                        <th scope="col" class="text-end pe-4">Subtotal</th>
                                        <th scope="col" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart as $item)
                                        <tr class="border-top">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded"
                                                        style="width: 72px; height: 72px; object-fit: cover;">
                                                    <div>
                                                        <h3 class="h6 mb-1">{{ $item['name'] }}</h3>
                                                        <p class="text-muted small mb-0">{{ $item['brand'] ?? 'Sin marca asignada' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold">${{ number_format($item['price'], 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="d-inline-flex align-items-center justify-content-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label for="quantity-{{ $item['id'] }}" class="visually-hidden">Cantidad</label>
                                                    <input type="number" name="quantity" id="quantity-{{ $item['id'] }}"
                                                        class="form-control form-control-sm text-center" value="{{ $item['quantity'] }}"
                                                        min="1" max="99">
                                                    <button class="btn btn-outline-primary btn-sm" type="submit">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="fw-semibold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Resumen</h2>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total de artículos</span>
                            <span>{{ $itemCount }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-semibold">Total a pagar</span>
                            <span class="fw-bold fs-5 text-primary">${{ number_format($total, 2) }}</span>
                        </div>
                        <a href="{{ route('cart.checkout') }}" class="btn btn-primary w-100 mb-2" role="button"><i class="bi bi-credit-card me-2"></i>Proceder al pago</a>
                        <a href="{{ route('welcome') }}" class="btn btn-outline-primary w-100"><i class="bi bi-bag me-2"></i>Seguir comprando</a>
                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-x-circle me-1"></i>Vaciar carrito</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
