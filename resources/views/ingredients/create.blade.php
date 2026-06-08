@extends('layouts.app')

@section('title', 'Nuevo Ingrediente | ProyectoUTN')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Inventario de Insumos
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Agregar Nuevo Ingrediente
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Este costo se usa después para calcular automáticamente el costo de cada producto.
        </p>

        <form action="{{ route('ingredients.store') }}" method="POST" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Nombre</label>
                <input id="name" name="name" value="{{ old('name') }}" required class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500" placeholder="Ej: Harina, Azúcar, Manteca">
                @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="unit_measure" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Unidad de medida</label>
                    <select id="unit_measure" name="unit_measure" required class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500">
                        @foreach(['gr', 'kg', 'ml', 'litro', 'unidad'] as $unit)
                            <option value="{{ $unit }}" {{ old('unit_measure') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit_measure') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="unit_cost" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Costo unitario</label>
                    <input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" value="{{ old('unit_cost') }}" required class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500" placeholder="0.00">
                    @error('unit_cost') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('ingredients.index') }}" class="px-5 py-2.5 bg-slate-950 border border-slate-800 text-slate-300 rounded-xl text-sm font-semibold">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold cursor-pointer">Crear Ingrediente</button>
            </div>
        </form>
    </div>
</div>
@endsection
