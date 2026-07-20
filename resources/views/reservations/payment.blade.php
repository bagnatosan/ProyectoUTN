@extends('layouts.app')

@section('title', 'Completar pago | ProyectoUTN')

@section('content')
@php
    $business  = $reservation->product->businessProfile;
    $product   = $reservation->product;
@endphp

<div class="max-w-xl mx-auto animate-fade-in space-y-6">

    {{-- Header --}}
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 mb-2">
            <svg class="w-7 h-7 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-white">¡Reserva creada!</h1>
        <p class="text-slate-400 text-sm">Completá el pago electrónico para confirmar tu turno.</p>
    </div>

    {{-- Resumen de la reserva --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Tu reserva</h2>
            <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 px-2.5 py-1 rounded-lg">
                #{{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
        @if($reservation->items && $reservation->items->isNotEmpty())
            <div class="space-y-1">
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Productos</span>
                @foreach($reservation->items as $item)
                    <div class="flex items-center justify-between text-sm bg-slate-950/20 border border-slate-850/60 p-2.5 rounded-xl">
                        <span class="text-slate-300 font-medium">{{ $item->product->name }} (x{{ $item->quantity }})</span>
                        <span class="font-bold text-slate-200">${{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400">Producto</span>
                <span class="text-sm font-semibold text-white">{{ $product->name }}</span>
            </div>
        @endif
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Emprendimiento</span>
            <span class="text-sm font-semibold text-white">{{ $business->business_name }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Fecha y hora</span>
            <span class="text-sm font-semibold text-white">
                {{ $reservation->reservation_date->format('d/m/Y') }} a las {{ \Illuminate\Support\Str::of($reservation->reservation_time)->substr(0, 5) }} hs
            </span>
        </div>
        @if(!$reservation->items || $reservation->items->isEmpty())
            @if(($reservation->quantity ?? 1) > 1)
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400">Cantidad</span>
                <span class="text-sm font-semibold text-white">{{ $reservation->quantity }} unidades</span>
            </div>
            @endif
        @endif
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Entrega</span>
            <span class="text-sm font-semibold text-white">
                {{ $reservation->delivery_type === 'delivery' ? '🏠 Envío a domicilio' : '🏪 Retiro en local' }}
            </span>
        </div>
        @if($reservation->delivery_type === 'delivery' && $reservation->shipping_address)
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Dirección</span>
            <span class="text-sm font-semibold text-white text-right max-w-xs">{{ $reservation->shipping_address }}</span>
        </div>
        @endif
        @php
            if ($reservation->items && $reservation->items->isNotEmpty()) {
                $subtotal = $reservation->items->sum(fn($item) => $item->unit_price * $item->quantity);
            } else {
                $subtotal = ($product->price ?? 0) * ($reservation->quantity ?? 1);
            }
            $shipping = $reservation->shipping_cost ?? 0;
            $total    = $subtotal + $shipping;
        @endphp
        @if($total > 0)
            @if($shipping > 0)
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400">Subtotal</span>
                <span class="text-sm text-white">${{ number_format($subtotal, 2, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400">Envío</span>
                <span class="text-sm text-white">${{ number_format($shipping, 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                <span class="text-sm font-bold text-slate-300">Total a pagar</span>
                <div class="text-right">
                    @if(!$reservation->items || $reservation->items->isEmpty())
                        @if(($reservation->quantity ?? 1) > 1 && $shipping == 0)
                            <p class="text-xs text-slate-500">${{ number_format($product->price, 2, ',', '.') }} × {{ $reservation->quantity }}</p>
                        @endif
                    @endif
                    <span class="text-lg font-extrabold text-emerald-400">${{ number_format($total, 2, ',', '.') }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Pago electrónico con Mercado Pago --}}
    @if(!empty($preferenceId))
    <div class="rounded-2xl border border-sky-500/20 bg-sky-500/5 p-5 space-y-4 text-center">
        <h2 class="text-xs font-bold uppercase tracking-wider text-sky-400">Pago Electrónico</h2>
        <p class="text-sm text-slate-300">Aboná de forma segura y al instante usando Mercado Pago.</p>
        
        @if(str_starts_with($preferenceId, 'mock_pref_'))
        <a href="{{ route('reservations.payment.simulate', $reservation) }}"
           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-white font-bold text-sm transition-colors cursor-pointer btn-mercadopago">
            <svg class="icon-mercadopago" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 12H9v-1.41l3.59-3.59H9V7.59L13.41 12H9v1.41l4.41-4.41z"/></svg>
            [Simulación] Pagar con Mercado Pago
        </a>
        @else
        <a href="{{ $initUrl ?? ('https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=' . $preferenceId) }}" target="_blank"
           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-white font-bold text-sm transition-colors cursor-pointer btn-mercadopago">
            <svg class="icon-mercadopago" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 12H9v-1.41l3.59-3.59H9V7.59L13.41 12H9v1.41l4.41-4.41z"/></svg>
            Pagar con Mercado Pago
        </a>
        @endif
    </div>
    @endif

    {{-- Error si tiene MP pero la preferencia falló --}}
    @if(!empty($business->mp_access_token) && empty($preferenceId))
    <div class="rounded-2xl border border-red-500/20 bg-red-500/5 p-5 text-center text-red-400">
        <svg class="icon-error-payment text-red-450" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm font-semibold">Error al conectar con Mercado Pago</p>
        <p class="text-xs text-slate-400 mt-1">No se pudo generar la orden de pago. Verifique que las credenciales del vendedor sean válidas o intente nuevamente más tarde.</p>
    </div>
    @endif

    {{-- Error si NO tiene Mercado Pago configurado --}}
    @if(empty($business->mp_access_token))
    <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-5 text-center text-amber-300">
        <svg class="icon-error-payment text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm font-semibold">El emprendedor todavía no asoció ningún método de pago.</p>
        <p class="text-xs text-slate-400 mt-1">Por favor, contactalo directamente para coordinar el pago.</p>
    </div>
    @endif

</div>

@push('scripts')
<script>
// Clear cart on client side if flash session requires it
@if(session('clear_cart_id'))
    localStorage.removeItem('cocinet_cart_' + "{{ session('clear_cart_id') }}");
@endif
</script>
@endpush
@endsection

