<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutConfirmation;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));
        $total = $cart->reduce(fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);

        return view('cart.index', [
            'cart' => $cart,
            'total' => $total,
            'itemCount' => $cart->sum('quantity'),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $quantity = $data['quantity'] ?? 1;

        $cart = $request->session()->get('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $image = $product->images()->first();
            $imageUrl = $image
                ? (Str::startsWith($image->url, ['http://', 'https://'])
                    ? $image->url
                    : Storage::disk('public')->url($image->url))
                : asset('images/bodegona_logo.png');

            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'brand' => optional($product->brand)->name,
                'image' => $imageUrl,
            ];
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $request->session()->get('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $data['quantity'];
            $request->session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cantidad actualizada correctamente.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            $request->session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Carrito vaciado correctamente.');
    }

    public function checkout(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $total = $cart->reduce(fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);

        return view('cart.checkout', [
            'cart' => $cart,
            'total' => $total,
            'itemCount' => $cart->sum('quantity'),
        ]);
    }

    public function processCheckout(Request $request): RedirectResponse
    {
        $cart = collect($request->session()->get('cart', []));

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'payment_method' => ['required', 'in:card,cash'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $orderReference = Str::upper(Str::random(8));
        $total = $cart->reduce(fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);

        $confirmationData = [
            'reference' => $orderReference,
            'name' => $data['customer_name'],
            'email' => $data['customer_email'],
            'payment_method' => $data['payment_method'],
            'payment_method_label' => $data['payment_method'] === 'card'
                ? 'Tarjeta de crédito o débito'
                : 'Contraentrega en efectivo',
            'notes' => $data['notes'] ?? null,
            'total' => $total,
            'items' => $cart->values()->all(),
        ];

        $request->session()->put('checkout_confirmation', $confirmationData);

        $pendingMail = Mail::to($data['customer_email']);
        $notificationEmails = config('checkout.notification_emails');

        if (!empty($notificationEmails)) {
            $pendingMail->bcc($notificationEmails);
        }

        $pendingMail->send(new CheckoutConfirmation($confirmationData));

        $request->session()->forget('cart');

        return redirect()->route('cart.checkout.confirmation');
    }

    public function confirmation(Request $request)
    {
        $confirmation = $request->session()->pull('checkout_confirmation');

        if (!$confirmation) {
            return redirect()->route('cart.index');
        }

        return view('cart.confirmation', [
            'confirmation' => $confirmation,
        ]);
    }
}
