@extends('layouts.app')

@section('title', 'Mis Ingredientes')

@section('content')
<div class="w-full max-w-3xl mx-auto px-2 sm:px-6 py-2">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Mis ingredientes</h2>
            <p class="text-slate-400 text-xs sm:text-sm mt-0.5">Catálogo de materias primas y sus costos por unidad de compra</p>
        </div>

        <a href="{{ route('ingredients.create') }}"
           class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center justify-center shadow-md shadow-emerald-500/5 whitespace-nowrap">
            + Nuevo Ingrediente
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-slate-800/50 bg-slate-950/20">
        <table class="w-full text-left text-xs sm:text-sm text-slate-300">
            <thead>
                <tr class="border-b border-slate-800 bg-slate-900/50 text-[10px] uppercase text-slate-400 tracking-wider">
                    <th class="p-2 sm:p-3">Ingrediente</th>
                    <th class="p-2 sm:p-3">Unidad de compra</th>
                    <th class="p-2 sm:p-3">Costo</th>
                    <th class="p-2 sm:p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($ingredients as $ingredient)
                    <tr class="hover:bg-slate-900/20 transition-colors">
                        <td class="p-2 sm:p-3 font-medium text-slate-200">{{ $ingredient->name }}</td>

                        <td class="p-2 sm:p-3 text-slate-400 font-mono text-xs">
                            {{ $ingredient->unit_measure }}
                        </td>

                        <td class="p-2 sm:p-3 text-emerald-400 font-semibold font-mono whitespace-nowrap">
                            $ {{ number_format($ingredient->unit_cost, 2, ',', '.') }}
                        </td>

                        <td class="p-2 sm:p-3 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('ingredients.edit', $ingredient->id) }}"
                               class="inline-flex items-center text-[10px] sm:text-[11px] bg-slate-800 hover:bg-slate-700 text-indigo-300 px-2 py-1 rounded transition-colors">
                                Editar
                            </a>

                            <form action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Seguro querés eliminar este ingrediente? Si está usado en alguna receta, esa receta puede dejar de calcular bien el costo.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] sm:text-[11px] bg-slate-800/80 hover:bg-rose-950 hover:text-rose-400 text-slate-400 px-2 py-1 rounded transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500 text-xs italic">
                            Todavía no creaste ningún ingrediente. Hacé click en "+ Nuevo Ingrediente" para empezar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-[11px] text-slate-500 mt-4">
        Estos ingredientes están disponibles para usar en cualquiera de tus recetas desde el
        <a href="{{ route('recipes.index') }}" class="text-indigo-300 underline">módulo de costos</a>.
    </p>

</div>
@endsection