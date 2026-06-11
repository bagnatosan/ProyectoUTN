@extends('layouts.app')

@section('title', 'Nuevo Ingrediente - ProyectoUTN')

@section('content')
<div class="w-full max-w-2xl mx-auto px-4 py-6">
    
   <div class="mb-4">
    <a href="{{ url('/recipes/1/edit') }}" class="text-xs text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1">
        ← Volver al Módulo de Costos
    </a>
    </div>

    <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-6 backdrop-blur-sm shadow-xl">
        <div class="border-b border-slate-800 pb-4 mb-6">
            <h2 class="text-xl font-bold text-white tracking-tight">Agregar Nuevo Ingrediente</h2>
            <p class="text-slate-400 text-xs mt-1">Registrá una materia prima para poder usarla en el cálculo de tus recetas.</p>
        </div>

        <form action="{{ url('/ingredients') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Nombre del Ingrediente</label>
                <input type="text" name="name" id="name" required placeholder="Ej: Harina 0000, Azúcar Blanco, Huevos"
                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="unit_measure" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Unidad de Medida</label>
                    <select name="unit_measure" id="unit_measure" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        <option value="">-- Seleccionar unidad --</option>
                        <option value="kg">Kilogramo (kg)</option>
                        <option value="g">Gramo (g)</option>
                        <option value="litro">Litro (litro)</option>
                        <option value="ml">Mililitro (ml)</option>
                        <option value="docena">Docena (docena)</option>
                        <option value="unidad">Unidad (unidad)</option>
                    </select>
                </div>

                <div>
                    <label for="unit_cost" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Costo ($)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500 text-sm font-mono">$</span>
                        <input type="number" name="unit_cost" id="unit_cost" step="0.01" min="0" required placeholder="0,00"
                               class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-7 pr-3 py-2.5 text-sm text-emerald-400 font-semibold font-mono focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/60">
                <a href="{{ url()->previous() }}" class="text-xs font-bold text-slate-400 hover:text-slate-200 px-4 py-2 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-5 py-2.5 rounded-lg transition-colors shadow-md shadow-emerald-500/10">
                    Guardar Ingrediente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
