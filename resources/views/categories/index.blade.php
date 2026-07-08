@extends('layouts.app')

@section('title', 'Gestionar Categorías | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@section('content')
@php
    $categoriesList = $categories->loadCount('products');
@endphp

<div class="page-banner" style="margin-bottom:1.5rem;">
    <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__content">
        <h1 class="page-banner__title">Categorías</h1>
        <p class="page-banner__subtitle">Organizá tus productos por categoría.</p>
    </div>
</div>
<div class="py-6">

    {{-- Top bar: volver --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-2 text-xs text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800/80 hover:border-slate-700 rounded-xl px-4 py-2.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Volver al Panel</span>
        </a>
    </div>

    {{-- Buscador centrado --}}
    @if(!$categoriesList->isEmpty())
    <div class="mb-6 flex justify-center">
        <div class="relative w-1/2">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="search" id="category-search" placeholder="Buscar categoría..." autocomplete="off"
                   class="w-full bg-slate-900/40 border border-slate-800/80 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 transition-all duration-200">
        </div>
    </div>
    @endif

    {{-- Main Grid: columnas iguales --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Left: Crear categoría --}}
        <div id="create-category-container" class="border border-slate-800/80 bg-slate-900/40 backdrop-blur rounded-2xl p-6 shadow-xl shadow-indigo-950/10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-indigo-500/5 blur-2xl pointer-events-none group-hover:bg-indigo-500/10 transition-all duration-500"></div>

            <h2 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>Nueva Categoría</span>
            </h2>

            <form action="{{ route('categories.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="category-name-input" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                        Nombre de la categoría
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="category-name-input"
                        required
                        minlength="3"
                        maxlength="50"
                        placeholder="Ej: Bebidas Calientes, Pastas..."
                        class="w-full bg-slate-950/80 border border-slate-800/80 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 transition-all duration-300"
                        autocomplete="off"
                    >
                    <p class="text-[11px] text-slate-500 mt-2">El nombre debe ser único y descriptivo. Mínimo 3 caracteres.</p>
                </div>

                <button
                    type="submit"
                    class="w-full relative group/btn flex items-center justify-center space-x-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold text-sm rounded-xl py-3 shadow-lg shadow-indigo-600/20 hover:shadow-indigo-500/30 transition-all duration-300 cursor-pointer overflow-hidden active:scale-[0.98]"
                >
                    <svg class="w-4 h-4 shrink-0 transition-transform group-hover/btn:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Crear Categoría</span>
                </button>
            </form>
        </div>

        {{-- Right: Lista de categorías --}}
        <div class="border border-slate-800/80 bg-slate-900/40 backdrop-blur rounded-2xl p-6 shadow-xl shadow-indigo-950/10">
            <h2 class="text-lg font-semibold text-white mb-6 flex items-center space-x-2">
                <span>Categorías Existentes</span>
                <span class="text-xs font-normal text-slate-400">({{ $categoriesList->count() }})</span>
            </h2>

            @if($categoriesList->isEmpty())
            <div class="py-12 text-center border border-dashed border-slate-800 rounded-xl">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.241h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.241h3.86m-18 0h18M2.25 13.5l1.626-5.693A2.25 2.25 0 015.982 6.25h12.036a2.25 2.25 0 012.193 1.557l1.626 5.693m-18 0v2.25C2.25 16.893 3.607 18 5.285 18h13.43c1.678 0 3.035-1.107 3.035-2.25V13.5" />
                </svg>
                <p class="text-sm text-slate-400 font-medium">No hay categorías registradas</p>
                <p class="text-xs text-slate-500 mt-1">Ingresa el nombre en el formulario para crear una.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($categoriesList as $category)
                <div class="category-row flex items-center justify-between p-4 rounded-xl border border-slate-800/80 bg-slate-900/20 hover:bg-slate-900/60 hover:border-slate-700/80 hover:shadow-md hover:shadow-indigo-950/5 transition-all duration-300" data-name="{{ strtolower($category->name) }}">
                    <div class="flex items-center space-x-3 flex-grow">
                        <div class="w-10 h-10 rounded-xl bg-slate-950/80 border border-slate-800/80 flex items-center justify-center text-indigo-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.16 3.659A2.25 2.25 0 009.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                        </div>

                        <div class="category-view-container">
                            <p class="font-semibold text-white text-sm">{{ $category->name }}</p>
                            <p class="text-[11px] text-slate-500">
                                Creada {{ is_string($category->created_at) ? $category->created_at : $category->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="category-edit-form hidden items-center space-x-2 flex-grow max-w-sm" id="form-edit">
                            @csrf
                            @method('PUT')
                            <input
                                type="text"
                                name="name"
                                value="{{ $category->name }}"
                                required
                                minlength="3"
                                maxlength="50"
                                class="bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-full"
                            >
                            <button type="submit" class="p-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition-colors cursor-pointer shrink-0" title="Guardar cambios">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button type="button" class="cancel-edit-btn p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors cursor-pointer shrink-0" title="Cancelar">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="category-actions-container flex items-center space-x-2 shrink-0">
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/10 mr-2">
                            {{ $category->products_count ?? 0 }} {{ ($category->products_count ?? 0) === 1 ? 'producto' : 'productos' }}
                        </span>

                        <button
                            type="button"
                            class="edit-category-btn p-2 rounded-lg text-slate-500 hover:text-indigo-400 hover:bg-indigo-500/10 border border-transparent hover:border-indigo-500/20 transition-all duration-300 cursor-pointer"
                            data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}"
                            title="Editar categoría"
                            id="buttonEdit"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>

                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="p-2 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all duration-300 cursor-pointer"
                                title="Eliminar categoría"
                                data-confirm="¿Eliminar la categoría «{{ $category->name }}»?"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
<script src="/js/categories.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('category-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        document.querySelectorAll('.category-row').forEach(function (row) {
            const name = row.dataset.name || '';
            row.style.display = !query || name.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endsection


