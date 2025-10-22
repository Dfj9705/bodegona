@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Finalizar compra</h1>
            <p class="text-muted mb-0">Selecciona tu método de pago y confirma los datos para completar tu pedido.</p>
        </div>
        <div class="text-muted small">
            <i class="bi bi-bag-check me-1"></i>{{ $itemCount }} {{ \Illuminate\Support\Str::plural('artículo', $itemCount) }} en el carrito
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0">Resumen de productos</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($cart as $item)
                            <li class="list-group-item px-0 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded"
                                        style="width: 64px; height: 64px; object-fit: cover;">
                                    <div>
                                        <h3 class="h6 mb-1">{{ $item['name'] }}</h3>
                                        <p class="text-muted small mb-0">Cantidad: {{ $item['quantity'] }}</p>
                                    </div>
                                </div>
                                <div class="text-sm-end">
                                    <span class="fw-semibold d-block">${{ number_format($item['price'], 2) }}</span>
                                    <span class="text-muted small">Subtotal: ${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between">
                    <span class="fw-semibold">Total</span>
                    <span class="fw-bold fs-5 text-primary">${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 mb-3">Detalles del pedido</h2>
                    <form action="{{ route('cart.checkout.process') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nombre completo</label>
                            <input type="text" id="customer_name" name="customer_name"
                                class="form-control @error('customer_name') is-invalid @enderror"
                                value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Utilizaremos esta información para tu confirmación.</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Correo electrónico</label>
                            <input type="email" id="customer_email" name="customer_email"
                                class="form-control @error('customer_email') is-invalid @enderror"
                                value="{{ old('customer_email') }}" required>
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Enviaremos el detalle de tu pedido a este correo.</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <span class="form-label d-block">Método de pago</span>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="card"
                                    {{ old('payment_method', 'card') === 'card' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_card">
                                    Tarjeta de crédito o débito
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash"
                                    {{ old('payment_method') === 'cash' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_cash">
                                    Contraentrega en efectivo
                                </label>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notas adicionales <span class="text-muted small">(opcional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Indícanos alguna instrucción especial">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-circle me-2"></i>Confirmar pedido</button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary w-100"><i class="bi bi-arrow-left me-2"></i>Volver al carrito</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
