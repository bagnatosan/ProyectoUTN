@extends('layouts.app')

@section('title', 'Catálogo de ' . $business->business_name . ' | ProyectoUTN')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Perfil del Negocio Card -->
    <div class="relative overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/40 p-6 sm:p-8 backdrop-blur shadow-xl">
        <!-- Background decorative glow -->
        <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-500/10 blur-2xl"></div>
        
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10">
            <!-- Logo -->
            <div class="shrink-0">
                @if($business->logo)
                    <img src="{{ asset('storage/' . $business->logo) }}" alt="Logo {{ $business->business_name }}" class="w-24 h-24 rounded-2xl object-cover border border-slate-700 shadow-md">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center font-bold text-3xl text-white border border-emerald-500/30 shadow-md">
                        {{ strtoupper(substr($business->business_name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-grow text-center sm:text-left space-y-3">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">{{ $business->business_name }}</h1>
                    @if($business->description)
                        <p class="text-slate-400 mt-2 text-sm sm:text-base leading-relaxed">{{ $business->description }}</p>
                    @else
                        <p class="text-slate-500 mt-2 text-sm italic">Sin descripción disponible.</p>
                    @endif
                </div>

                <div class="flex flex-wrap justify-center sm:justify-start gap-4 text-xs text-slate-300">
                    @if($business->address)
                        <span class="flex items-center gap-1.5 bg-slate-950/60 border border-slate-800 rounded-full px-3 py-1">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            {{ $business->address }}
                        </span>
                    @endif
                    @if($business->phone)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $business->phone) }}" target="_blank" class="flex items-center gap-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-full px-3 py-1 text-emerald-400 font-medium transition-colors">
                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.18 1.449 4.725 1.45 5.556 0 10.074-4.522 10.077-10.077.001-2.691-1.042-5.222-2.937-7.12C16.518 1.51 13.98 1.465 11.298 1.465c-5.555 0-10.074 4.52-10.077 10.077-.001 1.765.463 3.489 1.345 5.008l-.985 3.593 3.682-.966c1.554.847 3.193 1.29 4.794 1.29zm10.978-7.525c-.302-.151-1.785-.882-2.057-.982-.272-.1-.47-.15-.668.151-.198.3-.765.982-.94 1.181-.173.2-.347.225-.648.075-.302-.15-1.272-.469-2.423-1.496-.895-.798-1.5-1.784-1.675-2.086-.175-.302-.018-.465.132-.614.135-.134.302-.351.453-.526.151-.175.202-.3.302-.5.101-.2.05-.376-.025-.526-.075-.15-.668-1.609-.915-2.203-.241-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.785-.73 2.033-1.433.248-.704.248-1.311.173-1.436-.075-.125-.272-.2-.574-.35z"/>
                            </svg>
                            {{ $business->phone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Buscador y Filtros -->
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
            <!-- Buscador -->
            <div class="relative flex-grow max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" id="search-input" placeholder="Buscar productos por nombre..." class="w-full pl-10 pr-4 py-2.5 bg-slate-900/60 border border-slate-800 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 rounded-xl text-slate-200 placeholder-slate-500 text-sm focus:outline-none transition-all">
            </div>

            <!-- Info de productos mostrados -->
            <div class="text-xs text-slate-400 self-center" id="results-count">
                Mostrando {{ $products->count() }} productos
            </div>
        </div>

        <!-- Filtros por Categoría (Pills) -->
        @if($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 py-1" id="category-filters">
                <button data-category-id="all" class="category-pill px-4 py-1.5 rounded-full text-xs font-semibold border transition-all duration-200 cursor-pointer bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-500/10">
                    Todos
                </button>
                @foreach($categories as $category)
                    <button data-category-id="{{ $category->id }}" class="category-pill px-4 py-1.5 rounded-full text-xs font-semibold border border-slate-800 bg-slate-900/60 text-slate-400 hover:text-slate-200 hover:border-slate-700 transition-all duration-200 cursor-pointer">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Listado de Productos -->
    @if($products->isEmpty())
        <div class="border border-dashed border-slate-800 rounded-2xl p-16 text-center text-slate-500 bg-slate-900/20">
            <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="font-medium text-slate-400 text-base">Este emprendimiento no tiene productos activos todavía.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" id="products-grid">
            @foreach($products as $product)
                <div class="product-card group relative flex flex-col overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-900/20 hover:bg-slate-900/40 hover:border-slate-700/80 transition-all duration-300 shadow-lg hover:shadow-2xl" 
                     data-name="{{ strtolower($product->name) }}" 
                     data-category-id="{{ $product->category_id }}">
                    
                    <!-- Imagen o Placeholder -->
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-950 border-b border-slate-800/60">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center bg-gradient-to-br from-slate-900 to-slate-950 text-slate-650 transition-colors group-hover:text-emerald-500/60">
                                <svg class="w-12 h-12 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697-.056-4.024-.166C6.845 7.996 6 7.014 6 5.869V4.502c0-.475.29-.9.73-1.077 1.64-.66 3.407-1.006 5.27-1.006s3.63.346 5.27 1.006c.44.177.73.602.73 1.077v1.367c0 1.145-.845 2.127-1.976 2.215a44.62 44.62 0 01-4.024.166zM6 18.75h12M6 18.75a2.25 2.25 0 01-2.25-2.25V9.75H20.25v6.75A2.25 2.25 0 0118 18.75M6 18.75v1.5a1.5 1.5 0 001.5 1.5h9a1.5 1.5 0 001.5-1.5v-1.5" />
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Badge de Categoría en esquina -->
                        @if($product->category)
                            <span class="absolute right-3 top-3 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded-md bg-slate-950/80 border border-slate-800/80 text-indigo-450 backdrop-blur-sm">
                                {{ $product->category->name }}
                            </span>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex flex-grow flex-col p-5 space-y-4">
                        <div class="flex-grow space-y-1.5">
                            <h3 class="text-lg font-bold text-white tracking-tight leading-snug group-hover:text-emerald-400 transition-colors">
                                {{ $product->name }}
                            </h3>
                            @if($product->description)
                                <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            @else
                                <p class="text-xs text-slate-500 italic leading-relaxed">
                                    Sin descripción disponible.
                                </p>
                            @endif
                        </div>

                        <!-- Precio y Botón -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-900">
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Precio</span>
                                <span class="text-lg font-black text-emerald-400">${{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                            
                            <a href="{{ route('reservations.create', ['product_id' => $product->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-tr from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 border border-emerald-500/20 transition-all cursor-pointer">
                                <span>Reservar</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mensaje de no resultados (oculto por defecto) -->
        <div id="no-results-message" class="hidden border border-dashed border-slate-800 rounded-2xl p-16 text-center text-slate-500 bg-slate-900/20">
            <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-medium text-slate-400 text-base">No encontramos ningún producto que coincida con tu búsqueda o filtro.</p>
            <button id="clear-filters-btn" class="mt-4 px-4 py-2 text-xs font-semibold rounded-xl border border-slate-850 bg-slate-900/50 text-slate-400 hover:text-slate-200 hover:border-slate-700 transition-all cursor-pointer">
                Restablecer filtros
            </button>
        </div>
    @endif
</div>

<!-- JS Vanilla para Filtrado Interactivo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const categoryPills = document.querySelectorAll('.category-pill');
    const productCards = document.querySelectorAll('.product-card');
    const resultsCount = document.getElementById('results-count');
    const noResultsMessage = document.getElementById('no-results-message');
    const productsGrid = document.getElementById('products-grid');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');

    if (!productsGrid) return; // Si no hay productos, no inicializar

    let currentSearch = '';
    let currentCategory = 'all';

    function filterProducts() {
        let visibleCount = 0;

        productCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const categoryId = card.getAttribute('data-category-id');

            const matchesSearch = name.includes(currentSearch);
            const matchesCategory = currentCategory === 'all' || categoryId === currentCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Actualizar contador
        resultsCount.textContent = `Mostrando ${visibleCount} de ${productCards.length} productos`;

        // Mostrar/ocultar mensaje de no resultados
        if (visibleCount === 0) {
            productsGrid.style.display = 'none';
            noResultsMessage.classList.remove('hidden');
        } else {
            productsGrid.style.display = '';
            noResultsMessage.classList.add('hidden');
        }
    }

    // Evento del Buscador
    searchInput.addEventListener('input', function(e) {
        currentSearch = e.target.value.toLowerCase().trim();
        filterProducts();
    });

    // Evento de las Categorías (Pills)
    categoryPills.forEach(pill => {
        pill.addEventListener('click', function() {
            // Remover estilos activos de todas las pills
            categoryPills.forEach(p => {
                p.classList.remove('bg-emerald-500', 'border-emerald-500', 'text-white', 'shadow-lg', 'shadow-emerald-500/10');
                p.classList.add('bg-slate-900/60', 'border-slate-800', 'text-slate-400');
            });

            // Agregar estilos activos a la pill clickeada
            this.classList.remove('bg-slate-900/60', 'border-slate-800', 'text-slate-400');
            this.classList.add('bg-emerald-500', 'border-emerald-500', 'text-white', 'shadow-lg', 'shadow-emerald-500/10');

            currentCategory = this.getAttribute('data-category-id');
            filterProducts();
        });
    });

    // Botón de Limpiar filtros
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            
            // Simular click en la pill "Todos"
            const allPill = document.querySelector('.category-pill[data-category-id="all"]');
            if (allPill) allPill.click();
        });
    }
});
</script>
@endsection
