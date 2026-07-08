@extends('layouts.app')

@section('title', 'Editar Ingrediente')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@section('content')
<div class="page-banner" style="margin-bottom:1.5rem;">
    <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__content">
        <h1 class="page-banner__title">Editar Ingrediente</h1>
        <p class="page-banner__subtitle">Modificá los datos de un ingrediente.</p>
    </div>
</div>
<div class="w-full max-w-2xl mx-auto px-4 py-6">

    <div class="mb-4">
        <a href="{{ route('ingredients.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1">
            ← Volver a Mis Ingredientes
        </a>
    </div>

    <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-6 backdrop-blur-sm shadow-xl">
        <div class="border-b border-slate-800 pb-4 mb-6">
            <h2 class="text-xl font-bold text-white tracking-tight">Editar Ingrediente: {{ $ingredient->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Modificá los valores de la materia prima. Los cambios se actualizarán en todas las recetas.</p>
        </div>

        <form action="{{ route('ingredients.update', $ingredient->id) }}" method="POST" class="space-y-5" id="ingredient-form">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Nombre del Ingrediente</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $ingredient->name) }}"
                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="unit_measure" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Unidad de Medida</label>
                    <select name="unit_measure" id="unit_measure" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        @php $currentUnit = old('unit_measure', $ingredient->unit_measure); @endphp
                        <option value="kg" {{ $currentUnit == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                        <option value="g" {{ $currentUnit == 'g' ? 'selected' : '' }}>Gramo (g)</option>
                        <option value="litro" {{ $currentUnit == 'litro' ? 'selected' : '' }}>Litro (litro)</option>
                        <option value="ml" {{ $currentUnit == 'ml' ? 'selected' : '' }}>Mililitro (ml)</option>
                        <option value="docena" {{ $currentUnit == 'docena' ? 'selected' : '' }}>Docena (docena)</option>
                        <option value="unidad" {{ $currentUnit == 'unidad' ? 'selected' : '' }}>Unidad (unidad)</option>
                    </select>
                </div>

                <div>
                    <label for="unit_cost" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Costo ($)</label>
                    <div class="relative input-icon-group">
                        <span class="absolute left-3 top-2.5 text-slate-500 text-sm font-mono">$</span>
                        <input type="text" name="unit_cost" id="unit_cost" required
                               inputmode="numeric"
                               value="{{ old('unit_cost', $ingredient->unit_cost) }}"
                               class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-7 pr-3 py-2.5 text-sm text-emerald-400 font-semibold font-mono focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                </div>
            </div>

            <div>
                <label for="supplier_notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Notas del Proveedor</label>
                <textarea name="supplier_notes" id="supplier_notes" rows="4"
                          placeholder="Ej: Proveedor: Juan López · Tel: 11-1234-5678 · Dirección: Av. Corrientes 1234 · Entrega los lunes."
                          class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors resize-y">{{ old('supplier_notes', $ingredient->supplier_notes) }}</textarea>
            </div>

            <div class="border-t border-slate-800/60 pt-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Stock</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stock" class="block text-xs text-slate-500 mb-2">Cantidad en depósito</label>
                        <div class="relative">
                            <input type="text" name="stock" id="stock" inputmode="numeric"
                                   value="{{ old('stock', $ingredient->stock !== null ? number_format((float)$ingredient->stock, 2, ',', '.') : '') }}"
                                   placeholder="Ej: 5,00"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors">
                            <span class="absolute right-3 top-2.5 text-slate-500 text-xs font-mono">{{ $ingredient->unit_measure }}</span>
                        </div>
                    </div>
                    <div>
                        <label for="stock_minimo" class="block text-xs text-slate-500 mb-2">Stock mínimo (alerta)</label>
                        <div class="relative">
                            <input type="text" name="stock_minimo" id="stock_minimo" inputmode="numeric"
                                   value="{{ old('stock_minimo', $ingredient->stock_minimo !== null ? number_format((float)$ingredient->stock_minimo, 2, ',', '.') : '') }}"
                                   placeholder="Ej: 1,00"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors">
                            <span class="absolute right-3 top-2.5 text-slate-500 text-xs font-mono">{{ $ingredient->unit_measure }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/60">
                <a href="{{ route('ingredients.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-200 px-4 py-2 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-5 py-2.5 rounded-lg transition-colors shadow-md shadow-emerald-500/10">
                    Actualizar Ingrediente
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('ingredient-form').addEventListener('submit', function () {
    ['unit_cost', 'stock', 'stock_minimo'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el && el.value) el.value = el.value.replace(/\./g, '').replace(',', '.');
    });
});
</script>
@endsection