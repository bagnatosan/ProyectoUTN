@extends('layouts.app')

@section('title', 'Ingredientes | ProyectoUTN')

@section('content')
@php
    $businessProfile = auth()->user()->businessProfile;
    $ingredients = $businessProfile
        ? \App\Models\Ingredient::where('business_profile_id', $businessProfile->id)->orderBy('name')->get()
        : collect();
@endphp

<div class="max-w-4xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <div class="flex justify-between items-center">
            <div>
                <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                    Inventario de Insumos
                </span>
                <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
                    Ingredientes
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    Carga materia prima con su costo unitario para calcular recetas.
                </p>
            </div>
            <a href="{{ route('ingredients.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors">
                + Nuevo Ingrediente
            </a>
        </div>

        @if($ingredients->isEmpty())
            <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-12 text-center text-slate-500 text-sm">
                <p>No hay ingredientes cargados todavía. Creá uno para usarlo en una receta.</p>
            </div>
        @else
            <div class="mt-8 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                            <th class="py-3 pr-4">Nombre</th>
                            <th class="py-3 pr-4">Unidad</th>
                            <th class="py-3 pr-4">Costo Unitario</th>
                            <th class="py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/70">
                        @foreach($ingredients as $ingredient)
                            <tr>
                                <td class="py-4 pr-4 text-sm font-semibold text-white">{{ $ingredient->name }}</td>
                                <td class="py-4 pr-4 text-sm text-slate-300">{{ $ingredient->unit_measure }}</td>
                                <td class="py-4 pr-4 text-sm text-slate-300">${{ number_format($ingredient->unit_cost, 2) }}</td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('ingredients.edit', $ingredient) }}" class="text-xs text-indigo-300 hover:text-indigo-200 mr-3">Editar</a>
                                    <form action="{{ route('ingredients.destroy', $ingredient) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-300 hover:text-rose-200 cursor-pointer" onclick="return confirm('¿Eliminar ingrediente?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
