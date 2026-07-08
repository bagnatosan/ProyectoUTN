@extends('layouts.app')

@section('title', 'Módulo de Costos')

@section('content')
<div class="w-full max-w-none px-2 sm:px-6 py-2">

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">"¿Cuánto te cuesta producir?"</h2>
            <p class="text-slate-400 text-xs sm:text-sm mt-0.5">Calculá el costo real de tus productos y tu margen de ganancia</p>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 p-2 rounded-xl flex items-center gap-2 shadow-inner self-start md:self-auto">
            <label for="recipe_switcher" class="text-[10px] font-bold text-slate-400 uppercase pl-1 tracking-wider">Receta Activa:</label>
            <select id="recipe_switcher"
                    onchange="window.location.href='{{ url('/recipes') }}/' + this.value + '/edit'"
                    class="bg-slate-950 border border-slate-800 rounded-lg px-2 py-1 text-xs sm:text-sm text-emerald-400 font-medium focus:outline-none focus:border-emerald-500 cursor-pointer">
                @foreach($allProducts as $p)
                    <option value="{{ $p->id }}" {{ $product->id == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">

        <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 sm:p-5 backdrop-blur-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-emerald-400 tracking-wider uppercase">Receta: {{ $product->name }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Gestión de insumos e importes proporcionales</p>
                </div>
                <a href="{{ route('ingredients.index') }}"
                   class="text-[11px] text-indigo-300 hover:text-indigo-200 underline whitespace-nowrap">
                    Ver mis materias primas →
                </a>
            </div>

            <form action="{{ route('recipes.add-ingredient', $product->id) }}" method="POST"
                  class="mb-5 flex flex-col gap-2.5 bg-slate-950/50 p-3 rounded-xl border border-slate-800/60">
                @csrf

                <div class="flex items-center justify-between gap-2">
                    <select id="select_ingredient" name="ingredient_id" required
                            class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-2 text-xs sm:text-sm text-slate-300 focus:outline-none focus:border-emerald-500 cursor-pointer"
                            onchange="onIngredientChange()">
                        <option value="">-- Añadir ingrediente --</option>
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit_measure }}">
                                {{ $ingredient->name }} ({{ $ingredient->unit_measure }})
                            </option>
                        @endforeach
                    </select>

                    <button type="button" onclick="openNewIngredientModal()"
                            class="shrink-0 bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-bold px-3 py-2 rounded-lg transition-colors whitespace-nowrap">
                        + Nuevo
                    </button>
                </div>

                <div id="quantity_container" class="hidden flex gap-2 w-full pt-0.5">
                    <input type="number" name="quantity" id="quantity_input" step="any" min="0.001" placeholder="Cantidad"
                           class="w-1/3 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs sm:text-sm text-center text-emerald-400 font-mono focus:outline-none focus:border-emerald-500">

                    <select name="quantity_unit" id="quantity_unit_select"
                            class="w-1/3 bg-slate-950 border border-slate-800 rounded-lg px-2 py-2 text-xs sm:text-sm text-slate-300 focus:outline-none focus:border-emerald-500">
                    </select>

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
                            <th class="p-2.5">Costo ref.</th>
                            <th class="p-2.5 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 font-mono">
                        @forelse($product->ingredients as $ing)
                            @php
                                $unitMeasure = strtolower($ing->unit_measure);
                                $unitCost = (float) $ing->unit_cost;
                                if (in_array($unitMeasure, ['g', 'gr'])) {
                                    $refCost = $unitCost * 1000;
                                    $refUnit = 'kg';
                                } elseif (in_array($unitMeasure, ['kg'])) {
                                    $refCost = $unitCost;
                                    $refUnit = 'kg';
                                } elseif (in_array($unitMeasure, ['ml'])) {
                                    $refCost = $unitCost * 1000;
                                    $refUnit = 'L';
                                } elseif (in_array($unitMeasure, ['l', 'lt', 'lts'])) {
                                    $refCost = $unitCost;
                                    $refUnit = 'L';
                                } elseif (in_array($unitMeasure, ['docena', 'docenas'])) {
                                    $refCost = $unitCost;
                                    $refUnit = 'docena';
                                } else {
                                    $refCost = $unitCost;
                                    $refUnit = 'u';
                                }
                            @endphp
                            <tr class="hover:bg-slate-900/10">
                                <td class="p-2.5 text-slate-200 font-sans font-medium">{{ $ing->name }}</td>
                                <td class="p-2.5 text-slate-400 text-xs">
                                    {{ $ing->pivot->quantity }} {{ $ing->pivot->quantity_unit ?? $ing->unit_measure }}
                                </td>
                                <td class="p-2.5 text-slate-500 text-xs">
                                    ${{ number_format($refCost, 2) }}<span class="text-slate-600">/{{ $refUnit }}</span>
                                </td>
                                <td class="p-2.5 text-right">
                                    <form action="{{ route('recipes.remove-ingredient', [$product->id, $ing->id]) }}" method="POST"
                                          onsubmit="return confirm('¿Querés quitar este ingrediente de la receta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-300 hover:underline text-[11px] font-sans bg-transparent border-0 cursor-pointer">
                                            Quitar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-slate-500 font-sans text-xs italic">
                                    No hay ingredientes asignados a esta receta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-2.5 pt-4 border-t border-slate-800/80 text-xs tracking-wide">

                @php
                    $margin = $product->custom_margin ?? $product->businessProfile->profit_margin ?? 3;
                    $isCustomMargin = !is_null($product->custom_margin);
                @endphp

                <div class="flex justify-between items-center text-slate-400">
                    <span class="font-medium text-[11px]">COSTO ESTIMADO:</span>
                    <span class="font-bold font-mono text-slate-200">
                        $ {{ number_format($product->estimated_cost ?? 0, 2, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between items-center text-slate-400">
                    <span class="font-medium text-[11px]">
                        MARGEN APLICADO {{ $isCustomMargin ? '(personalizado)' : '(general del negocio)' }}:
                    </span>
                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold font-mono">
                        {{ number_format($margin * 100, 0) }}% (x{{ rtrim(rtrim(number_format($margin, 2), '0'), '.') }})
                    </span>
                </div>

                <div class="flex justify-between items-center pt-2.5 border-t border-slate-800/40">
                    <span class="font-bold text-slate-300 text-xs sm:text-sm">PRECIO SUGERIDO FINAL:</span>
                    <span class="font-black text-emerald-400 text-base sm:text-xl font-mono tracking-tight">
                        $ {{ number_format($product->suggested_price ?? 0, 2, ',', '.') }}
                    </span>
                </div>

                <p class="text-[10px] text-slate-500 pt-1">
                    Podés ajustar el margen general en tu <a href="{{ route('business_profile.edit') }}" class="text-indigo-300 underline">perfil de negocio</a>,
                    o un margen propio para este producto desde <a href="{{ route('products.edit', $product->id) }}" class="text-indigo-300 underline">editar producto</a>.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal rápido para crear un ingrediente nuevo sin salir de la pantalla -->
<div id="new_ingredient_modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-5 w-full max-w-sm shadow-2xl">
        <h3 class="text-sm font-bold text-slate-200 mb-3">Nuevo ingrediente</h3>
        <form action="{{ route('ingredients.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ route('recipes.edit', $product->id) }}">

            <div>
                <label class="text-[10px] text-slate-400 uppercase font-bold">Nombre</label>
                <input type="text" name="name" required
                       class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 placeholder-slate-500">
            </div>

            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="text-[10px] text-slate-400 uppercase font-bold">Unidad de compra</label>
                    <select name="unit_measure" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-2 text-sm text-slate-300 focus:outline-none focus:border-emerald-500 cursor-pointer">
                        <option value="kg">Kilogramo (kg)</option>
                        <option value="g">Gramo (g)</option>
                        <option value="litro">Litro (litro)</option>
                        <option value="ml">Mililitro (ml)</option>
                        <option value="docena">Docena (docena)</option>
                        <option value="unidad">Unidad (unidad)</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-[10px] text-slate-400 uppercase font-bold">Costo ($)</label>
                    <input type="number" name="unit_cost" step="0.01" min="0" required
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-emerald-400 font-mono font-semibold focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeNewIngredientModal()"
                        class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold py-2 rounded-lg border border-slate-700 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2 rounded-lg transition-colors cursor-pointer">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const unitLabels = {
    kg: 'Kilogramo (kg)',
    g: 'Gramo (g)',
    litro: 'Litro (litro)',
    ml: 'Mililitro (ml)',
    cucharada: 'Cucharada',
    cucharadita: 'Cucharadita',
    docena: 'Docena (docena)',
    unidad: 'Unidad (unidad)',
};

function onIngredientChange() {
    const select = document.getElementById('select_ingredient');
    const container = document.getElementById('quantity_container');
    const ingredientId = select.value;
    const unitSelect = document.getElementById('quantity_unit_select');
    const submitButton = container.querySelector('button[type="submit"]');

    if (!ingredientId) {
        container.classList.add('hidden');
        return;
    }

    // Ocultamos el contenedor y deshabilitamos el submit hasta tener las unidades correctas
    container.classList.remove('hidden');
    unitSelect.innerHTML = '<option value="">Cargando...</option>';
    submitButton.disabled = true;

    fetch(`/ingredients/${ingredientId}/valid-units`)
        .then(res => res.json())
        .then(data => {
            unitSelect.innerHTML = '';
            data.units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit;
                option.textContent = unitLabels[unit] ?? unit;
                unitSelect.appendChild(option);
            });
            // Forzamos que quede seleccionada la PRIMERA opción de la lista nueva,
            // así nunca queda pegada una selección de una consulta anterior
            unitSelect.selectedIndex = 0;
            submitButton.disabled = false;
        })
        .catch(() => {
            const selectedOption = select.options[select.selectedIndex];
            const fallbackUnit = selectedOption.dataset.unit;
            unitSelect.innerHTML = `<option value="${fallbackUnit}">${unitLabels[fallbackUnit] ?? fallbackUnit}</option>`;
            unitSelect.selectedIndex = 0;
            submitButton.disabled = false;
        });
}
function openNewIngredientModal() {
    document.getElementById('new_ingredient_modal').classList.remove('hidden');
}

function closeNewIngredientModal() {
    document.getElementById('new_ingredient_modal').classList.add('hidden');
}
</script>
@endsection