@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="h3 mb-2">¡Gracias por tu compra!</h1>
                    <p class="text-muted mb-1">Tu pedido ha sido recibido y estamos procesándolo.</p>
                    <p class="text-muted">Número de referencia: <span class="fw-semibold">{{ $confirmation['reference'] }}</span></p>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0">Resumen del pedido</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Nombre</span>
                            <span class="fw-semibold">{{ $confirmation['name'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Correo electrónico</span>
                            <span class="fw-semibold">{{ $confirmation['email'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Método de pago</span>
                            <span class="fw-semibold">{{ $confirmation['payment_method_label'] }}</span>
                        </div>
                        @if (!empty($confirmation['notes']))
                            <div class="mt-3">
                                <span class="text-muted d-block">Notas</span>
                                <span>{{ $confirmation['notes'] }}</span>
                            </div>
                        @endif
                    </div>

                    <ul class="list-group list-group-flush mb-3">
                        @foreach ($confirmation['items'] as $item)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block fw-semibold">{{ $item['name'] }}</span>
                                    <span class="text-muted small">Cantidad: {{ $item['quantity'] }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="d-block">${{ number_format($item['price'], 2) }}</span>
                                    <span class="text-muted small">Subtotal: ${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Total pagado</span>
                        <span class="fw-bold fs-5 text-primary">${{ number_format($confirmation['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('welcome') }}" class="btn btn-primary flex-fill"><i class="bi bi-house-door me-2"></i>Volver al inicio</a>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary flex-fill"><i class="bi bi-bag me-2"></i>Seguir comprando</a>
            </div>
        </div>
    </div>
@endsection
