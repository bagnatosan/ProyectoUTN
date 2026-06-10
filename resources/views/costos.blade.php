<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Costos - ProyectoUTN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        {!! $estilosAmigo !!}
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-bg': '#0b0f19',
                        'dark-card': '#13192b',
                        'laravel-green': '#00c853',
                        'dark-input': '#1e253b'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-bg text-white font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-dark-card border-r border-gray-800/80 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-8">
                    <span class="bg-laravel-green text-black font-bold px-2 py-1 rounded">P</span>
                    <h1 class="text-lg font-bold tracking-wide">ProyectoUTN</h1>
                </div>
                <nav class="space-y-2">
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Dashboard</a>
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Productos</a>
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Reservas</a>
                    <a href="#" class="block py-2 px-4 rounded text-laravel-green bg-gray-800/40 font-medium border-l-4 border-laravel-green pl-3">Costos</a>
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Perfil</a>
                </nav>
            </div>
            <button class="text-left text-sm text-gray-500 hover:text-red-400 py-2 px-4 transition">Cerrar Sesión</button>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-800 pb-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white">Módulo de costos</h2>
        <p class="text-gray-400 text-sm mt-1">Calculá el costo real de tus productos y tu margen de ganancia</p>
    </div>
    
    <div class="bg-gray-900/60 border border-gray-800 p-2 rounded-xl flex items-center gap-2 shadow-inner">
        <label for="recipe_switcher" class="text-xs font-semibold text-gray-400 uppercase pl-2 tracking-wider">Receta Activa:</label>
        <select id="recipe_switcher" 
                onchange="window.location.href='/recipes/' + this.value + '/edit'" 
                class="bg-gray-950 border border-gray-800 rounded-lg px-3 py-1.5 text-sm text-green-400 font-medium focus:outline-none focus:border-green-500 cursor-pointer">
            
            @foreach($allProducts ?? \App\Models\Product::all() as $p)
                <option value="{{ $p->id }}" {{ (isset($product) && $product->id == $p->id) ? 'selected' : '' }}>
                    {{ $p->name }}
                </option>
            @endforeach
            
        </select>
    </div>
</div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-dark-card p-6 rounded-xl border border-gray-800/60 shadow-2xl h-fit">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-laravel-green tracking-wide">Materias primas</h3>
                        <button onclick="openCreateModal()" class="bg-laravel-green hover:bg-green-600 text-black font-bold px-4 py-1.5 rounded-lg text-sm transition shadow-lg shadow-green-900/20">+ Nuevo</button>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-800/40">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-900/40 border-b border-gray-800 text-gray-400 font-medium">
                                    <th class="py-3 px-4">INGREDIENTE</th>
                                    <th class="py-3 px-4">UNIDAD</th>
                                    <th class="py-3 px-4">$$ COSTO</th>
                                    <th class="py-3 px-4 text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/40 text-gray-300">
                                @foreach ($ingredients as $ingredient)
                                    <tr class="border-b border-gray-800/60 hover:bg-gray-800/40 transition">
                                        <td class="py-3 px-4 font-medium">{{ $ingredient->name }}</td>
                                        <td class="py-3 px-4 text-gray-400">{{ $ingredient->unit_measure }}</td>
                                        <td class="py-3 px-4 font-semibold text-emerald-400">$ {{ number_format($ingredient->unit_cost, 2, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-center space-x-3">
                                             <button 
                                                   onclick="openEditModal({{ $ingredient->id }}, '{{ $ingredient->name }}', '{{ $ingredient->unit_measure }}', {{ $ingredient->unit_cost }})" 
                                                   class="text-blue-400 hover:text-blue-300 transition font-medium">
                                                ✏️ Editar
                                            </button>
                                            <form action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Seguro querés eliminar {{ $ingredient->name }}?')" class="text-red-400 hover:text-red-300 transition">
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

                <div class="bg-dark-card p-6 rounded-xl border border-gray-800/60 flex flex-col justify-between shadow-2xl h-fit">
                    <div>
                        <h3 class="text-lg font-semibold text-laravel-green tracking-wide mb-1">
                            Receta: {{ $product->name }}
                        </h3>
                        <p class="text-xs text-gray-400 mb-6">{{ $product->description ?? 'Sin descripción' }}</p>
                        
                        <form action="{{ route('recipes.update', $product->id) }}" method="POST" id="recipeForm">
                            @csrf
                            @method('PUT')

                            <div class="space-y-3 mb-6 bg-gray-900/20 p-4 rounded-lg border border-gray-800/40">
                                <div class="grid grid-cols-12 gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-800 pb-2">
                                    <div class="col-span-6">Ingrediente</div>
                                    <div class="col-span-4">Cantidad</div>
                                    <div class="col-span-2 text-center">Acción</div>
                                </div>

                                <div id="recipeIngredientsContainer" class="space-y-2">
                                    @foreach($product->ingredients as $index => $prodIng)
                                        <div class="grid grid-cols-12 gap-2 items-center recipe-row py-1">
                                            <div class="col-span-6 text-sm text-gray-300">
                                                {{ $prodIng->name }} ({{ $prodIng->unit_measure }})
                                                <input type="hidden" name="ingredients[{{ $index }}][id]" value="{{ $prodIng->id }}">
                                            </div>
                                            <div class="col-span-4">
                                                <input type="number" step="0.01" min="0.01" name="ingredients[{{ $index }}][quantity]" value="{{ $prodIng->pivot->quantity }}" required class="w-full bg-dark-input border border-gray-800 rounded px-2 py-1 text-sm text-white focus:outline-none focus:border-laravel-green">
                                            </div>
                                            <div class="col-span-2 text-center">
                                                <button type="button" onclick="this.closest('.recipe-row').remove()" class="text-red-400 hover:text-red-300 text-xs">Quitar</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="border-t border-gray-800/80 pt-3 mt-4 flex gap-2">
                                    <select id="ingredientSelector" class="flex-1 bg-dark-input border border-gray-800 rounded px-2 py-1.5 text-sm text-gray-300 focus:outline-none focus:border-laravel-green">
                                        <option value="">-- Seleccionar ingrediente para añadir --</option>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" data-name="{{ $ing->name }}" data-unit="{{ $ing->unit_measure }}">
                                                {{ $ing->name }} (${{ $ing->unit_cost }}/{{ $ing->unit_measure }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="addIngredientToRecipe()" class="bg-gray-800 hover:bg-gray-700 text-laravel-green font-bold px-3 py-1 rounded text-sm transition">
                                        + Añadir
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-laravel-green text-black font-bold py-2.5 rounded-xl text-sm hover:bg-green-600 transition shadow-lg shadow-green-950/30 mb-4">
                                Guardar Cambios en la Receta
                            </button>
                        </form>
                    </div>

                    <div class="bg-dark-input p-5 rounded-xl space-y-3 border border-gray-800/80 shadow-inner mt-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">COSTO ESTIMADO:</span>
                            <span class="font-semibold text-emerald-400">$ {{ number_format($product->estimated_cost, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">MARGEN COMERCIAL SUGERIDO:</span>
                            <span class="font-medium text-laravel-green bg-green-950/40 px-2 py-0.5 rounded text-xs border border-green-800/30">300% (Multiplicador x3)</span>
                        </div>
                        <div class="flex justify-between text-base border-t border-gray-800/60 pt-3 mt-1">
                            <span class="font-medium text-gray-200">PRECIO SUGERIDO FINAL:</span>
                            <span class="font-bold text-xl text-laravel-green tracking-wide">$ {{ number_format($product->suggested_price, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="createModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-dark-card border border-gray-800 p-6 rounded-xl w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-laravel-green mb-4">Añadir Materia Prima</h3>
            <form action="{{ route('ingredients.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Nombre</label>
                    <input type="text" name="name" required class="w-full bg-dark-input border border-gray-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-laravel-green text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Unidad de Medida (Kg, Unidades, Litros)</label>
                    <input type="text" name="unit_measure" required placeholder="ej: kg" class="w-full bg-dark-input border border-gray-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-laravel-green text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Costo Unitario ($)</label>
                    <input type="number" name="unit_cost" step="0.01" min="0" required class="w-full bg-dark-input border border-gray-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-laravel-green text-sm">
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                    <button type="submit" class="bg-laravel-green text-black font-bold px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-dark-card border border-gray-800 p-6 rounded-xl w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-blue-400 mb-4">Editar Materia Prima</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Nombre</label>
                    <input type="text" id="edit_name" name="name" required class="w-full bg-dark-input border border-gray-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-400 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Unidad de Medida</label>
                    <input type="text" id="edit_unit_measure" name="unit_measure" required class="w-full bg-dark-input border border-gray-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-400 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Costo Unitario ($)</label>
                    <input type="number" id="edit_unit_cost" name="unit_cost" step="0.01" min="0" required class="w-full bg-dark-input border border-gray-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-400 text-sm">
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                    <button type="submit" class="bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('flex');
        }
        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('flex');
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(id, name, measure, cost) {
            document.getElementById('editForm').action = `/ingredients/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_unit_measure').value = measure;
            document.getElementById('edit_unit_cost').value = cost;
            
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
        
        let ingredientIndex = {{ $product->ingredients->count() }};

        function addIngredientToRecipe() {
            const selector = document.getElementById('ingredientSelector');
            const selectedOption = selector.options[selector.selectedIndex];
    
            if (!selectedOption.value) return;

            const id = selectedOption.value;
            const name = selectedOption.getAttribute('data-name');
            const unit = selectedOption.getAttribute('data-unit');

            const existingInputs = document.querySelectorAll(`#recipeIngredientsContainer input[value="${id}"]`);
            if (existingInputs.length > 0) {
                alert('Este ingrediente ya está en la receta.');
                return;
            }

            const container = document.getElementById('recipeIngredientsContainer');
            
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-2 items-center recipe-row py-1';
            row.innerHTML = `
                <div class="col-span-6 text-sm text-gray-300">
                    ${name} (${unit})
                    <input type="hidden" name="ingredients[${ingredientIndex}][id]" value="${id}">
                </div>
                <div class="col-span-4">
                    <input type="number" step="0.01" min="0.01" name="ingredients[${ingredientIndex}][quantity]" placeholder="Cant." required class="w-full bg-dark-input border border-gray-800 rounded px-2 py-1 text-sm text-white focus:outline-none focus:border-laravel-green">
                </div>
                <div class="col-span-2 text-center">
                    <button type="button" onclick="this.closest('.recipe-row').remove()" class="text-red-400 hover:text-red-300 text-xs">Quitar</button>
                </div>
            `;
            
            container.appendChild(row);
            ingredientIndex++;
            selector.value = "";
        }
    </script>
    
</body>
@if (session('success'))
    <div id="toast" class="fixed top-5 right-5 bg-emerald-500 text-black font-bold px-6 py-3 rounded-xl shadow-2xl z-50 transition-opacity duration-500">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => { document.getElementById('toast').style.opacity = '0'; setTimeout(() => document.getElementById('toast').remove(), 500); }, 3000);
    </script>
@endif
</html>