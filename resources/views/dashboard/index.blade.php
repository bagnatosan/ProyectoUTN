@extends('layouts.app')

@section('title', 'Dashboard del Emprendedor | ProyectoUTN')

@section('content')
@php
    $statusStyles = [
        'pending' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
        'confirmed' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20',
        'completed' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
        'cancelled' => 'bg-rose-500/10 text-rose-300 border-rose-500/20',
    ];
    $statusBarStyles = [
        'pending' => 'bg-amber-400',
        'confirmed' => 'bg-indigo-400',
        'completed' => 'bg-emerald-400',
        'cancelled' => 'bg-rose-400',
    ];
    $maxStatusCount = max($metrics['status_counts'] ?: [0]);
    $healthTone = $metrics['data_quality']['health_score'] >= 80
        ? 'text-emerald-300'
        : ($metrics['data_quality']['health_score'] >= 50 ? 'text-amber-300' : 'text-rose-300');
@endphp

<div class="max-w-7xl mx-auto py-8 space-y-8">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 border-b border-slate-800/70 pb-6">
        <div>
            <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                Dashboard Comercial
            </span>
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-white mt-4">
                Métricas de {{ $businessProfile->business_name }}
            </h1>
            <p class="text-slate-400 mt-2 text-sm">
                Facturación, rentabilidad y gestión de reservas del negocio.
            </p>
        </div>

        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-900/60 text-sm font-semibold text-slate-300 hover:text-white hover:border-slate-700 transition-colors">
            Ver productos
        </a>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 shadow-xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Salud del negocio</p>
                <p class="text-5xl font-extrabold {{ $healthTone }} mt-2">{{ $metrics['data_quality']['health_score'] }}%</p>
                <p class="text-xs text-slate-500 mt-2">
                    {{ $metrics['data_quality']['issue_count'] }} alertas sobre {{ $metrics['data_quality']['total_products'] }} productos.
                </p>
            </div>
            <div class="lg:col-span-2">
                <div class="h-3 rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-rose-500 via-amber-400 to-emerald-400" style="width: {{ $metrics['data_quality']['health_score'] }}%"></div>
                </div>
                <div class="grid grid-cols-3 gap-3 mt-4">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-3">
                        <p class="text-xs text-slate-500">Sin receta</p>
                        <p class="text-xl font-bold text-white">{{ $metrics['data_quality']['products_without_recipe']->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-3">
                        <p class="text-xs text-slate-500">Sin costo</p>
                        <p class="text-xl font-bold text-white">{{ $metrics['data_quality']['products_without_cost']->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-3">
                        <p class="text-xs text-slate-500">Margen bajo</p>
                        <p class="text-xl font-bold text-white">{{ $metrics['data_quality']['low_margin_products']->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($metrics['data_quality']['products_without_recipe']->isNotEmpty() || $metrics['data_quality']['products_without_cost']->isNotEmpty() || $metrics['data_quality']['low_margin_products']->isNotEmpty())
        <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-6 shadow-xl">
            <div class="flex items-start gap-3">
                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-300 shrink-0"></div>
                <div>
                    <h2 class="text-lg font-bold text-amber-100">Alertas de calidad de datos</h2>
                    <p class="text-sm text-amber-200/80 mt-1">
                        Revisá estos productos antes de tomar decisiones con las métricas.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-5">
                @if($metrics['data_quality']['products_without_recipe']->isNotEmpty())
                    <div class="rounded-xl border border-amber-500/20 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-wider text-amber-300 font-semibold">Sin receta</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $metrics['data_quality']['products_without_recipe']->count() }}</p>
                        <p class="text-xs text-slate-400 mt-2">
                            {{ $metrics['data_quality']['products_without_recipe']->pluck('name')->take(3)->join(', ') }}
                        </p>
                    </div>
                @endif

                @if($metrics['data_quality']['products_without_cost']->isNotEmpty())
                    <div class="rounded-xl border border-amber-500/20 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-wider text-amber-300 font-semibold">Sin costo calculado</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $metrics['data_quality']['products_without_cost']->count() }}</p>
                        <p class="text-xs text-slate-400 mt-2">
                            {{ $metrics['data_quality']['products_without_cost']->pluck('name')->take(3)->join(', ') }}
                        </p>
                    </div>
                @endif

                @if($metrics['data_quality']['low_margin_products']->isNotEmpty())
                    <div class="rounded-xl border border-amber-500/20 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-wider text-amber-300 font-semibold">Margen bajo</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $metrics['data_quality']['low_margin_products']->count() }}</p>
                        <p class="text-xs text-slate-400 mt-2">
                            {{ $metrics['data_quality']['low_margin_products']->pluck('name')->take(3)->join(', ') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 shadow-xl">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Facturación mensual</p>
            <p class="text-3xl font-bold text-white mt-2">${{ number_format($metrics['monthly_revenue'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $metrics['completed_this_month'] }} reservas completadas este mes</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 shadow-xl">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Ganancia estimada</p>
            <p class="text-3xl font-bold text-emerald-300 mt-2">${{ number_format($metrics['monthly_profit'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">Costo estimado: ${{ number_format($metrics['monthly_cost'], 2) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 shadow-xl">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Reservas activas</p>
            <p class="text-3xl font-bold text-indigo-300 mt-2">{{ $metrics['active_reservations'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Pendientes o confirmadas</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 shadow-xl">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Ticket promedio</p>
            <p class="text-3xl font-bold text-white mt-2">${{ number_format($metrics['average_ticket'], 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $metrics['today_reservations'] }} reservas para hoy</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-white">Pedidos de hoy</h2>
                <p class="text-xs text-slate-500 mt-1">Vista rápida de turnos del día con acciones directas.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.metrics', request()->except('today_status')) }}" class="px-3 py-1.5 rounded-full border text-xs font-semibold {{ empty($filters['today_status']) ? 'border-indigo-500/40 bg-indigo-500/10 text-indigo-300' : 'border-slate-800 text-slate-400 hover:text-white' }}">
                    Todos
                </a>
                @foreach($statuses as $status => $label)
                    <a href="{{ route('dashboard.metrics', array_merge(request()->except('today_status'), ['today_status' => $status])) }}" class="px-3 py-1.5 rounded-full border text-xs font-semibold {{ ($filters['today_status'] ?? '') === $status ? 'border-indigo-500/40 bg-indigo-500/10 text-indigo-300' : 'border-slate-800 text-slate-400 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                        <th class="py-3 pr-4">Horario</th>
                        <th class="py-3 pr-4">Cliente</th>
                        <th class="py-3 pr-4">Pedido</th>
                        <th class="py-3 pr-4">Estado</th>
                        <th class="py-3 text-right">Acciones rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($todayReservations as $reservation)
                        <tr>
                            <td class="py-4 pr-4 text-sm font-semibold text-white">{{ substr((string) $reservation->reservation_time, 0, 5) }}</td>
                            <td class="py-4 pr-4">
                                <p class="text-sm font-semibold text-white">{{ $reservation->client_name }}</p>
                                <p class="text-xs text-slate-500">{{ $reservation->client_phone ?? $reservation->client_email }}</p>
                            </td>
                            <td class="py-4 pr-4">
                                <p class="text-sm text-slate-200">{{ $reservation->product?->name ?? 'Producto eliminado' }}</p>
                                <p class="text-xs text-slate-500">${{ number_format($reservation->product?->price ?? 0, 2) }}</p>
                            </td>
                            <td class="py-4 pr-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusStyles[$reservation->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                                    {{ $statuses[$reservation->status] ?? $reservation->status }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    @php
                                        $quickActions = [
                                            'pending' => ['confirmed' => 'Confirmar', 'cancelled' => 'Cancelar'],
                                            'confirmed' => ['completed' => 'Completar', 'cancelled' => 'Cancelar'],
                                            'completed' => [],
                                            'cancelled' => [],
                                        ][$reservation->status] ?? [];
                                    @endphp

                                    @forelse($quickActions as $status => $label)
                                        <form action="{{ route('dashboard.reservations.status', $reservation) }}?{{ http_build_query(request()->query()) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white hover:border-slate-700 cursor-pointer">
                                                {{ $label }}
                                            </button>
                                        </form>
                                    @empty
                                        <span class="text-xs text-slate-500">Sin acciones</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-sm text-slate-500">No hay pedidos de hoy para este filtro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-2xl border border-slate-800 bg-slate-900/40 p-6 shadow-xl">
            <h2 class="text-lg font-bold text-white">Reservas por estado</h2>
            <div class="mt-5 space-y-3">
                @foreach($statuses as $status => $label)
                    @php
                        $count = $metrics['status_counts'][$status];
                        $width = $maxStatusCount > 0 ? ($count / $maxStatusCount) * 100 : 0;
                    @endphp
                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusStyles[$status] }}">
                                {{ $label }}
                            </span>
                            <span class="text-xl font-bold text-white">{{ $count }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-800 overflow-hidden mt-3">
                            <div class="h-full {{ $statusBarStyles[$status] }}" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="xl:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/40 p-6 shadow-xl">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-white">Rentabilidad por producto</h2>
                    <p class="text-xs text-slate-500 mt-1">Ordenado por ganancia acumulada en reservas completadas.</p>
                </div>
            </div>

            @if($metrics['product_profitability']->isNotEmpty())
                @php
                    $topProducts = $metrics['product_profitability']->take(3);
                    $maxProfit = max($topProducts->pluck('profit')->map(fn ($profit) => max(0, $profit))->all() ?: [0]);
                @endphp
                <div class="mt-5 space-y-3">
                    @foreach($topProducts as $row)
                        @php
                            $profitWidth = $maxProfit > 0 ? (max(0, $row['profit']) / $maxProfit) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-300">{{ $row['product']->name }}</span>
                                <span class="text-emerald-300">${{ number_format($row['profit'], 2) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-800 overflow-hidden mt-1">
                                <div class="h-full bg-emerald-400" style="width: {{ $profitWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                            <th class="py-3 pr-4">Producto</th>
                            <th class="py-3 pr-4">Completadas</th>
                            <th class="py-3 pr-4">Ingresos</th>
                            <th class="py-3 pr-4">Ganancia</th>
                            <th class="py-3">Margen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/70">
                        @forelse($metrics['product_profitability']->take(6) as $row)
                            <tr>
                                <td class="py-4 pr-4">
                                    <p class="text-sm font-semibold text-white">{{ $row['product']->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $row['product']->category?->name ?? 'Sin categoría' }}</p>
                                </td>
                                <td class="py-4 pr-4 text-sm text-slate-300">{{ $row['completed_count'] }}</td>
                                <td class="py-4 pr-4 text-sm text-slate-300">${{ number_format($row['revenue'], 2) }}</td>
                                <td class="py-4 pr-4 text-sm font-semibold {{ $row['profit'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                    ${{ number_format($row['profit'], 2) }}
                                </td>
                                <td class="py-4">
                                    <div class="w-28 h-2 rounded-full bg-slate-800 overflow-hidden">
                                        <div class="h-full bg-indigo-500" style="width: {{ min(100, max(0, $row['margin'])) }}%"></div>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">{{ $row['margin'] }}%</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500">Todavía no hay productos para analizar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-white">Reservas y pedidos</h2>
                <p class="text-xs text-slate-500 mt-1">Buscá por cliente, email, teléfono o producto.</p>
            </div>
        </div>

        <form action="{{ route('dashboard.metrics') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-5">
            <input
                type="text"
                name="search"
                value="{{ $filters['search'] ?? '' }}"
                placeholder="Cliente, email o producto"
                class="md:col-span-2 bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
            >

            <select name="status" class="bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todos los estados</option>
                @foreach($statuses as $status => $label)
                    <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <input
                type="date"
                name="date_from"
                value="{{ $filters['date_from'] ?? '' }}"
                class="bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500"
            >

            <input
                type="date"
                name="date_to"
                value="{{ $filters['date_to'] ?? '' }}"
                class="bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500"
            >

            <div class="md:col-span-5 flex justify-end gap-2">
                <a href="{{ route('dashboard.metrics') }}" class="px-4 py-2 rounded-xl border border-slate-800 text-sm font-semibold text-slate-400 hover:text-white">Limpiar</a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-sm font-semibold text-white cursor-pointer">Filtrar</button>
            </div>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                        <th class="py-3 pr-4">Cliente</th>
                        <th class="py-3 pr-4">Producto</th>
                        <th class="py-3 pr-4">Fecha</th>
                        <th class="py-3 pr-4">Estado</th>
                        <th class="py-3 text-right">Cambiar estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($reservations as $reservation)
                        <tr>
                            <td class="py-4 pr-4">
                                <p class="text-sm font-semibold text-white">{{ $reservation->client_name }}</p>
                                <p class="text-xs text-slate-500">{{ $reservation->client_email }}</p>
                            </td>
                            <td class="py-4 pr-4">
                                <p class="text-sm text-slate-200">{{ $reservation->product?->name ?? 'Producto eliminado' }}</p>
                                <p class="text-xs text-slate-500">${{ number_format($reservation->product?->price ?? 0, 2) }}</p>
                            </td>
                            <td class="py-4 pr-4">
                                <p class="text-sm text-slate-300">{{ $reservation->reservation_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500">{{ substr((string) $reservation->reservation_time, 0, 5) }}</p>
                            </td>
                            <td class="py-4 pr-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusStyles[$reservation->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                                    {{ $statuses[$reservation->status] ?? $reservation->status }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <form action="{{ route('dashboard.reservations.status', $reservation) }}?{{ http_build_query(request()->query()) }}" method="POST" class="inline-flex items-center justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="bg-slate-950/80 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                                        @foreach($statuses as $status => $label)
                                            <option value="{{ $status }}" {{ $reservation->status === $status ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-white cursor-pointer">
                                        Guardar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-sm text-slate-500">No hay reservas que coincidan con los filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection
