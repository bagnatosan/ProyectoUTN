@extends('layouts.app')

@section('title', 'Completar pago | ProyectoUTN')

@section('content')
@php
    $business  = $reservation->product->businessProfile;
    $product   = $reservation->product;
    $hasBankData = $business->bank_cbu || $business->bank_alias;
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
        <p class="text-slate-400 text-sm">Para confirmar tu turno, realizá la transferencia y subí el comprobante.</p>
    </div>

    {{-- Resumen de la reserva --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Tu reserva</h2>
            <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 px-2.5 py-1 rounded-lg">
                #{{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Producto</span>
            <span class="text-sm font-semibold text-white">{{ $product->name }}</span>
        </div>
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
        @if(($reservation->quantity ?? 1) > 1)
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Cantidad</span>
            <span class="text-sm font-semibold text-white">{{ $reservation->quantity }} unidades</span>
        </div>
        @endif
        @if($product->price > 0)
        @php $total = $product->price * ($reservation->quantity ?? 1); @endphp
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <span class="text-sm font-bold text-slate-300">Total a pagar</span>
            <div class="text-right">
                @if(($reservation->quantity ?? 1) > 1)
                    <p class="text-xs text-slate-500">${{ number_format($product->price, 2) }} × {{ $reservation->quantity }}</p>
                @endif
                <span class="text-lg font-extrabold text-emerald-400">${{ number_format($total, 2) }}</span>
            </div>
        </div>
        @endif
    </div>

    {{-- Datos bancarios --}}
    @if($hasBankData)
    <div class="rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-5 space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-400">Datos para transferir</h2>

        @if($business->bank_account_holder)
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Titular</span>
            <span class="text-sm font-semibold text-white">{{ $business->bank_account_holder }}</span>
        </div>
        @endif

        @if($business->bank_name)
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-400">Banco / Billetera</span>
            <span class="text-sm font-semibold text-white">{{ $business->bank_name }}</span>
        </div>
        @endif

        @if($business->bank_cbu)
        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-slate-400 shrink-0">CBU</span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-sm font-mono font-semibold text-white truncate" id="cbu-value">{{ $business->bank_cbu }}</span>
                <button type="button" onclick="copyToClipboard('cbu-value', this)"
                    class="shrink-0 text-xs text-indigo-400 hover:text-indigo-300 transition-colors font-medium">
                    Copiar
                </button>
            </div>
        </div>
        @endif

        @if($business->bank_alias)
        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-slate-400 shrink-0">Alias</span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-sm font-mono font-semibold text-white truncate" id="alias-value">{{ $business->bank_alias }}</span>
                <button type="button" onclick="copyToClipboard('alias-value', this)"
                    class="shrink-0 text-xs text-indigo-400 hover:text-indigo-300 transition-colors font-medium">
                    Copiar
                </button>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4 text-sm text-amber-300">
        El emprendedor aún no cargó sus datos bancarios. Podés contactarlo directamente para coordinar el pago.
    </div>
    @endif

    {{-- Formulario subir comprobante --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 space-y-5">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Subir comprobante</h2>

        @if($errors->any())
        <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('reservations.payment.upload', $reservation) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300" for="transfer_amount">Monto transferido ($)</label>
                <input type="number" name="transfer_amount" id="transfer_amount" step="0.01" min="0.01"
                    value="{{ old('transfer_amount', $product->price > 0 ? $product->price * ($reservation->quantity ?? 1) : '') }}"
                    class="w-full rounded-xl bg-slate-900 border border-slate-700 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 text-slate-200 px-4 py-2.5 text-sm focus:outline-none transition-all @error('transfer_amount') border-red-500/50 @enderror"
                    placeholder="Ej. 1500.00" required>
            </div>

            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300" for="transfer_date">Fecha de la transferencia</label>
                <input type="date" name="transfer_date" id="transfer_date"
                    value="{{ old('transfer_date', now()->format('Y-m-d')) }}"
                    max="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-xl bg-slate-900 border border-slate-700 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 text-slate-200 px-4 py-2.5 text-sm focus:outline-none transition-all @error('transfer_date') border-red-500/50 @enderror"
                    required>
            </div>

            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300" for="receipt">Comprobante (JPG, PNG o PDF, máx. 5 MB)</label>
                <label for="receipt"
                    class="flex flex-col items-center justify-center gap-2 w-full rounded-xl border-2 border-dashed border-slate-700 hover:border-emerald-500/50 bg-slate-900 px-4 py-6 cursor-pointer transition-colors group @error('receipt') border-red-500/50 @enderror"
                    id="receipt-label">
                    <svg class="w-8 h-8 text-slate-500 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors" id="receipt-filename">
                        Hacé click para seleccionar el archivo
                    </span>
                    <input type="file" name="receipt" id="receipt" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required
                        onchange="document.getElementById('receipt-filename').textContent = this.files[0]?.name ?? 'Hacé click para seleccionar el archivo'">
                </label>
            </div>

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                Enviar comprobante
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-500">
        ¿Ya enviaste el pago antes?
        <a href="{{ route('reservations.index') }}" class="text-indigo-400 hover:text-indigo-300">Ver mis reservas</a>
    </p>
</div>

@push('scripts')
<script>
function copyToClipboard(elementId, btn) {
    var text = document.getElementById(elementId).textContent.trim();
    navigator.clipboard.writeText(text).then(function() {
        var original = btn.textContent;
        btn.textContent = '¡Copiado!';
        btn.classList.add('text-emerald-400');
        setTimeout(function() {
            btn.textContent = original;
            btn.classList.remove('text-emerald-400');
        }, 2000);
    });
}
</script>
@endpush
@endsection
