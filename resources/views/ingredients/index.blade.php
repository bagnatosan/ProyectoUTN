@extends('layouts.app')

@section('title', 'Mis Ingredientes')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@section('content')
<div class="page-banner" style="margin-bottom:1.5rem;">
    <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__content">
        <h1 class="page-banner__title">Mis Ingredientes</h1>
        <p class="page-banner__subtitle">Administrá las materias primas y sus costos.</p>
    </div>
</div>
<div class="w-full max-w-6xl mx-auto px-2 sm:px-6 py-2">

    <div class="mb-6 flex items-center justify-between gap-4 pb-5">
        <form method="GET" action="{{ route('ingredients.index') }}" class="relative w-1/2" id="ingredient-search-form">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input
                type="text"
                name="search"
                id="ingredient-search"
                value="{{ $search }}"
                placeholder="Buscar por nombre..."
                autocomplete="off"
                class="w-full bg-slate-900/40 border border-slate-800/80 rounded-xl pl-10 pr-4 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 transition-all duration-200"
            >
        </form>
        <a href="{{ route('ingredients.create') }}"
           class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center justify-center shadow-md shadow-emerald-500/5 whitespace-nowrap">
            + Nuevo Ingrediente
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-slate-800/50 bg-slate-950/20">
        <table class="w-full text-left text-xs sm:text-sm text-slate-300">
            <thead>
                <tr class="border-b border-slate-800 bg-slate-900/50 text-[10px] uppercase text-slate-400 tracking-wider">
                    <th class="px-4 py-3">Ingrediente</th>
                    <th class="px-4 py-3">Unidad de compra</th>
                    <th class="px-4 py-3">Costo</th>
                    <th class="px-4 py-3 hidden sm:table-cell">Stock</th>
                    <th class="px-4 py-3 hidden sm:table-cell">Proveedor</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($ingredients as $ingredient)
                    <tr class="hover:bg-slate-900/20 transition-colors">
                        <td class="px-4 py-3 font-medium text-slate-200">{{ $ingredient->name }}</td>

                        <td class="px-4 py-3 text-slate-400 font-mono text-xs">
                            {{ $ingredient->unit_measure }}
                        </td>

                        <td class="px-4 py-3 text-emerald-400 font-semibold font-mono whitespace-nowrap">
                            $ {{ number_format($ingredient->unit_cost, 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 hidden sm:table-cell text-xs font-mono whitespace-nowrap">
                            @if($ingredient->stock !== null)
                                @php
                                    $bajo = $ingredient->stock_minimo !== null && $ingredient->stock <= $ingredient->stock_minimo;
                                    $sinStock = $ingredient->stock == 0;
                                @endphp
                                @if($sinStock)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/15 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                        Sin stock
                                    </span>
                                @elseif($bajo)
                                    <span class="inline-flex items-center gap-1 text-amber-400">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                        {{ number_format((float)$ingredient->stock, 2, ',', '.') }} {{ $ingredient->unit_measure }}
                                    </span>
                                @else
                                    <span class="text-emerald-400">{{ number_format((float)$ingredient->stock, 2, ',', '.') }} {{ $ingredient->unit_measure }}</span>
                                @endif
                            @else
                                <span class="text-slate-700 italic text-[10px]">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 hidden sm:table-cell text-slate-500 text-xs max-w-[12rem]">
                            @if($ingredient->supplier_notes)
                                <span title="{{ $ingredient->supplier_notes }}" class="cursor-help">
                                    {{ Str::limit($ingredient->supplier_notes, 40) }}
                                </span>
                            @else
                                <span class="italic text-slate-700">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('ingredients.edit', $ingredient->id) }}"
                               class="inline-flex items-center text-[10px] sm:text-[11px] bg-slate-800 hover:bg-slate-700 text-indigo-300 px-2 py-1 rounded transition-colors">
                                Editar
                            </a>

                            <form action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] sm:text-[11px] bg-slate-800/80 hover:bg-rose-950 hover:text-rose-400 text-slate-400 px-2 py-1 rounded transition-colors"
                                        data-confirm="¿Eliminar «{{ $ingredient->name }}»? Si está en alguna receta puede afectar el cálculo de costos.">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-500 text-xs italic">
                            Todavía no creaste ningún ingrediente. Hacé click en "+ Nuevo Ingrediente" para empezar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ingredients->hasPages())
    <div class="mt-4">
        {{ $ingredients->links() }}
    </div>
    @endif

    <p class="text-[11px] text-slate-500 mt-4 text-center">
        Estos ingredientes están disponibles para usar en cualquiera de tus recetas desde el
        <a href="{{ route('recipes.index') }}" class="text-indigo-300 underline">módulo de costos</a>.
    </p>

</div>
<script>
(function () {
    var input = document.getElementById('ingredient-search');
    var form  = document.getElementById('ingredient-search-form');
    if (!input || !form) return;
    var timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { form.submit(); }, 400);
    });
})();
</script>
@endsection