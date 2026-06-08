@extends('layouts.app')

@section('title', 'Constructor de Receta | ProyectoUTN')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Costos de Receta
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Receta de <span class="text-indigo-400">{{ $product->name }}</span>
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Ingresá la cantidad usada de cada ingrediente. Al guardar se recalculan el costo estimado y el precio sugerido.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Costo estimado actual</p>
                <p class="text-2xl font-bold text-white mt-1">${{ number_format($product->estimated_cost ?? 0, 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Precio sugerido actual</p>
                <p class="text-2xl font-bold text-indigo-300 mt-1">${{ number_format($product->suggested_price ?? 0, 2) }}</p>
            </div>
        </div>

        @if($ingredients->isEmpty())
            <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-10 text-center text-slate-500 text-sm">
                <p>No tenés ingredientes cargados todavía.</p>
                <a href="{{ route('ingredients.create') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold">
                    Crear ingrediente
                </a>
            </div>
        @else
            <form action="{{ route('recipes.update', $product) }}" method="POST" class="mt-8">
                @csrf
                @method('PUT')

                <div class="overflow-x-auto border border-slate-800 rounded-xl">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/40 text-xs uppercase tracking-wider text-slate-400">
                                <th class="p-4">Ingrediente</th>
                                <th class="p-4">Costo unitario</th>
                                <th class="p-4">Cantidad usada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            @foreach($ingredients as $ingredient)
                                @php
                                    $currentQuantity = optional($product->ingredients->firstWhere('id', $ingredient->id)?->pivot)->quantity;
                                @endphp
                                <tr>
                                    <td class="p-4">
                                        <input type="hidden" name="ingredients[{{ $loop->index }}][ingredient_id]" value="{{ $ingredient->id }}">
                                        <p class="text-sm font-semibold text-white">{{ $ingredient->name }}</p>
                                        <p class="text-xs text-slate-500">Unidad: {{ $ingredient->unit_measure }}</p>
                                    </td>
                                    <td class="p-4 text-sm text-slate-300">${{ number_format($ingredient->unit_cost, 2) }}</td>
                                    <td class="p-4">
                                        <input
                                            type="number"
                                            name="ingredients[{{ $loop->index }}][quantity]"
                                            value="{{ old('ingredients.' . $loop->index . '.quantity', $currentQuantity) }}"
                                            min="0"
                                            step="0.01"
                                            placeholder="0"
                                            class="w-28 bg-slate-950/80 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-slate-500 mt-3">
                    Dejá la cantidad en 0 o vacía para no incluir ese ingrediente en la receta.
                </p>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-800">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-slate-950 border border-slate-800 text-slate-300 rounded-xl text-sm font-semibold">Volver</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold cursor-pointer">
                        Guardar Receta y Recalcular
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
