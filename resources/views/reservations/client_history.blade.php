@extends('layouts.app')

@section('title', 'Mis Reservas | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-4xl')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div>
        <span class="auth-role-badge auth-role-badge-client">Cliente</span>
        <h1 class="text-2xl md:text-3xl font-extrabold mt-2">Mis reservas</h1>
        <p class="text-sm text-slate-400 mt-1">Turnos y pedidos que hiciste desde la plataforma.</p>
    </div>

    @if($reservations->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-800 bg-slate-900/40 p-12 text-center">
            <p class="text-slate-400 font-medium">Todavía no tenés reservas registradas.</p>
            <p class="text-sm text-slate-500 mt-2">Explorá los catálogos y reservá un producto o turno.</p>
            <a href="{{ route('dashboard') }}" class="inline-flex mt-6 auth-role-btn auth-role-btn-client auth-role-btn-inline px-6">
                Ver catálogos
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reservations as $reservation)
                @php
                    $statusLabels = [
                        'pending' => ['Pendiente', 'bg-amber-500/10 text-amber-700 border-amber-500/20'],
                        'confirmed' => ['Confirmada', 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20'],
                        'completed' => ['Completada', 'bg-slate-500/10 text-slate-600 border-slate-500/20'],
                        'cancelled' => ['Cancelada', 'bg-rose-500/10 text-rose-700 border-rose-500/20'],
                    ];
                    $status = $statusLabels[$reservation->status] ?? ['Desconocido', 'bg-slate-500/10 text-slate-600 border-slate-500/20'];
                @endphp
                <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <p class="font-bold text-lg">{{ $reservation->product->name ?? 'Producto' }}</p>
                            <p class="text-sm text-slate-400 mt-1">
                                {{ $reservation->reservation_date->format('d/m/Y') }}
                                · {{ \Illuminate\Support\Str::of($reservation->reservation_time)->substr(0, 5) }}
                            </p>
                            @if($reservation->notes)
                                <p class="text-xs text-slate-500 mt-2">{{ $reservation->notes }}</p>
                            @endif
                        </div>
                        <span class="inline-flex self-start px-2.5 py-1 rounded-full text-xs font-semibold border {{ $status[1] }}">
                            {{ $status[0] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
