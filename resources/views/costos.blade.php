@extends('layouts.app')

@section('title', 'Módulo de Costos - ProyectoUTN')

@section('content')
<div class="w-full max-w-none px-2 sm:px-6 py-2">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Módulo de costos</h2>
            <p class="text-slate-400 text-xs sm:text-sm mt-0.5">Calculá el costo real de tus productos y tu margen de ganancia</p>
        </div>
        
        <div class="bg-slate-900/60 border border-slate-800 p-2 rounded-xl flex items-center gap-2 shadow-inner self-start md:self-auto">
            <label for="recipe_switcher" class="text-[10px] font-bold text-slate-400 uppercase pl-1 tracking-wider">Receta Activa:</label>
            <select id="recipe_switcher" 
                    onchange="window.location.href='{{ url('/recipes') }}/' + this.value + '/edit'" 
                    class="bg-slate-950 border border-slate-800 rounded-lg px-2 py-1 text-xs sm:text-sm text-emerald-400 font-medium focus:outline-none focus:border-emerald-500 cursor-pointer">
                @foreach($allProducts ?? \App\Models\Product::all() as $p)
                    <option value="{{ $p->id }}" {{ (isset($product) && $product->id == $p->id) ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <div class="lg:col-span-7 bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 sm:p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-emerald-400 tracking-wider uppercase">Materias primas</h3>
                
                <a href="{{ url('/ingredients/create') }}?product_id={{ $product->id ?? 1 }}" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center shadow-md shadow-emerald-500/5">
                    + Nuevo Ingrediente
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-800/50 bg-slate-950/20">
                <table class="w-full text-left text-xs sm:text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-900/50 text-[10px] uppercase text-slate-400 tracking-wider">
                            <th class="p-2 sm:p-3">Ingrediente</th>
                            <th class="p-2 sm:p-3">Unidad</th>
                            <th class="p-2 sm:p-3">Costo</th>
                            <th class="p-2 sm:p-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @foreach($ingredients ?? \App\Models\Ingredient::all() as $ingredient)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="p-2 sm:p-3 font-medium text-slate-200">{{ $ingredient->name }}</td>
                                
                                <td class="p-2 sm:p-3 text-slate-400 font-mono text-xs">
                                    {{ $ingredient->unit_measure ?? $ingredient->unit ?? 'u' }}
                                </td>
                                
                                <td class="p-2 sm:p-3 text-emerald-400 font-semibold font-mono whitespace-nowrap">
                                    $ {{ number_format($ingredient->unit_cost ?? $ingredient->cost ?? 0, 2, ',', '.') }}
                                </td>
                                
                                <td class="p-2 sm:p-3 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ url('/ingredients/' . ($ingredient->id ?? 1) . '/edit') }}" class="inline-flex items-center text-[10px] sm:text-[11px] bg-slate-800 hover:bg-slate-700 text-indigo-300 px-2 py-1 rounded transition-colors">
                                        ✏️ Editar
                                    </a>
                                    
                                    <form action="{{ url('/ingredients/' . $ingredient->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro querés eliminar este ingrediente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] sm:text-[11px] bg-slate-800/80 hover:bg-rose-950 hover:text-rose-400 text-slate-400 px-2 py-1 rounded transition-colors">
                                            ❌
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-5 bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 sm:p-5 backdrop-blur-sm">
            <div class="mb-4">
                <h3 class="text-xs font-bold text-emerald-400 tracking-wider uppercase">Receta: {{ $product->name ?? 'Torta de Chocolate' }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Gestión de insumos e importes proporcionales</p>
            </div>

           <form action="{{ url('/recipes/' . $product->id . '/add-ingredient') }}" method="POST" class="mb-5 flex flex-col gap-2.5 bg-slate-950/50 p-3 rounded-xl border border-slate-800/60">
                @csrf
                
                <div class="w-full">
                    <select id="select_ingredient" name="ingredient_id" required 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-2 text-xs sm:text-sm text-slate-300 focus:outline-none focus:border-emerald-500 cursor-pointer"
                            onchange="toggleQuantityInput()">
                        <option value="">-- Añadir ingrediente --</option>
                        @foreach($ingredients ?? \App\Models\Ingredient::all() as $ingredient)
                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit_measure ?? $ingredient->unit ?? 'u' }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="quantity_container" class="hidden flex gap-2 w-full pt-0.5">
                    <input type="number" name="quantity" step="any" min="0.01" placeholder="Cantidad" required 
                        class="w-1/3 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs sm:text-sm text-center text-emerald-400 font-mono focus:outline-none focus:border-emerald-500">
                    
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-4 py-2 rounded-lg transition-colors duration-200 shadow-md shadow-emerald-500/5">
                        + Añadir
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto mb-5 rounded-lg border border-slate-800/50 bg-slate-950/20">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-900/50 text-[10px] uppercase text-slate-500 tracking-wider">
                            <th class="p-2.5">Ingrediente</th>
                            <th class="p-2.5">Cantidad</th>
                            <th class="p-2.5 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 font-mono">
    @if(isset($product) && isset($product->ingredients) && $product->ingredients->count() > 0)
        @foreach($product->ingredients as $ing)
            <tr class="hover:bg-slate-900/10">
                <td class="p-2.5 text-slate-200 font-sans font-medium">{{ $ing->name }}</td>
                <td class="p-2.5 text-slate-400 text-xs">{{ $ing->pivot->quantity }} {{ $ing->unit }}</td>
                <td class="p-2.5 text-right">
                    <form action="{{ url('/recipes/' . ($product->id ?? 2) . '/remove-ingredient/' . $ing->id) }}" method="POST" onsubmit="return confirm('¿Querés quitar este ingrediente de la receta?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-400 hover:text-rose-300 hover:underline text-[11px] font-sans bg-transparent border-0 cursor-pointer">
                            Quitar
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="3" class="p-4 text-center text-slate-500 font-sans text-xs italic">
                No hay ingredientes asignados a esta receta.
            </td>
        </tr>
    @endif
</tbody>
                </table>
            </div>

            <a href="{{ route('products.index') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs py-2.5 rounded-lg transition-all duration-200 mb-6 shadow-md cursor-pointer">
                Guardar Cambios en la Receta
            </a>

            <div class="space-y-2.5 pt-4 border-t border-slate-800/80 text-xs tracking-wide">
    
    @php
        // 1. Calculamos el costo total sumando (cantidad usada * costo unitario del ingrediente)
        $costoEstimado = 0;
        if(isset($product) && isset($product->ingredients)) {
            foreach($product->ingredients as $ing) {
                // Buscamos el costo en cualquier variante de nombre que hayan usado tus compañeros
                $costoUnitario = $ing->unit_cost ?? $ing->cost ?? 0;
                $cantidadUsada = $ing->pivot->quantity ?? 0;
                $costoEstimado += ($costoUnitario * $cantidadUsada);
            }
        }
        
        // 2. Definimos el margen (multiplicador x3 o 300%)
        $margenSugerido = 3; 
        $precioSugeridoFinal = $costoEstimado * $margenSugerido;
    @endphp

    <div class="flex justify-between items-center text-slate-400">
        <span class="font-medium text-[11px]">COSTO ESTIMADO:</span>
        <span class="font-bold font-mono text-slate-200">
            $ {{ number_format($costoEstimado, 2, ',', '.') }}
        </span>
    </div>

    <div class="flex justify-between items-center text-slate-400">
        <span class="font-medium text-[11px]">MARGEN SUGERIDO:</span>
        <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold font-mono">300% (x3)</span>
    </div>

    <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/40">
        <span class="font-bold text-slate-300 text-xs sm:text-sm">PRECIO SUGERIDO FINAL:</span>
        <span class="font-black text-emerald-400 text-base sm:text-xl font-mono tracking-tight">
            $ {{ number_format($precioSugeridoFinal, 2, ',', '.') }}
        </span>
    </div>
</div>

        </div>
    </div>
</div>

<script>
function toggleQuantityInput() {
    const select = document.getElementById('select_ingredient');
    const container = document.getElementById('quantity_container');
    
    if (select.value !== "") {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}
</script>
@endsection