@extends('layouts.app')

@section('title', 'Mis Productos | ProyectoUTN')

@section('content')
@php
    $businessProfile = auth()->user()->businessProfile;
    
    $productsList = $businessProfile 
        ? \App\Models\Product::where('business_profile_id', $businessProfile->id)->with('category')->get() 
        : collect();
    
    $totalProducts = $productsList->count();
    $activeProducts = $productsList->where('is_active', true)->count();
    $inactiveProducts = $productsList->where('is_active', false)->count();
@endphp

<div class="py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-6 border-b border-slate-800/60">
        <div>
            <span class="px-2.5 py-1 text-[10px] font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                Módulo de Catálogo
            </span>
            <h1 class="text-3xl font-bold tracking-tight text-white mt-3 font-sans">
                Mis Productos
            </h1>
            <p class="text-slate-400 mt-1.5 text-sm">
                Administra el catálogo de productos de tu negocio, sus precios y recetas.
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-2 text-xs text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800/80 hover:border-slate-700 rounded-xl px-4 py-2.5 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Volver al Panel</span>
            </a>
            <a href="{{ route('products.create') }}" class="inline-flex items-center space-x-2 text-xs bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-xl px-4 py-2.5 shadow-lg shadow-indigo-600/20 hover:shadow-indigo-500/30 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Nuevo Producto</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <!-- Stat 1 -->
        <div class="p-5 rounded-2xl border border-slate-800/80 bg-slate-900/20 backdrop-blur flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Productos</p>
                <p class="text-2xl font-bold text-white mt-0.5">{{ $totalProducts }}</p>
            </div>
        </div>
        <!-- Stat 2 -->
        <div class="p-5 rounded-2xl border border-slate-800/80 bg-slate-900/20 backdrop-blur flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Activos en Catálogo</p>
                <p class="text-2xl font-bold text-emerald-400 mt-0.5">{{ $activeProducts }}</p>
            </div>
        </div>
        <!-- Stat 3 -->
        <div class="p-5 rounded-2xl border border-slate-800/80 bg-slate-900/20 backdrop-blur flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Inactivos / Ocultos</p>
                <p class="text-2xl font-bold text-rose-400 mt-0.5">{{ $inactiveProducts }}</p>
            </div>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="border border-slate-800/80 bg-slate-900/40 backdrop-blur rounded-2xl shadow-xl shadow-indigo-950/10 overflow-hidden">
        @if($productsList->isEmpty())
        <div class="py-20 text-center border-dashed border-slate-800 rounded-2xl">
            <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            <h3 class="text-lg font-bold text-white">No tienes productos registrados</h3>
            <p class="text-sm text-slate-400 mt-2 max-w-sm mx-auto">
                Comienza agregando productos a tu inventario para que tus clientes puedan verlos y reservarlos.
            </p>
            <div class="mt-6">
                <a href="{{ route('products.create') }}" class="inline-flex items-center space-x-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl px-4 py-2.5 transition-colors duration-200">
                    <span>Crear mi primer producto</span>
                </a>
            </div>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/40">
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Producto</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Categoría</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Precio Venta</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Costo Estimado</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Precio Sugerido</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Estado</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($productsList as $product)
                    <tr class="hover:bg-slate-900/20 transition-colors duration-200">
                        <!-- Producto info (Image + Name) -->
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <!-- Placeholder icon -->
                                        <svg class="w-6 h-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white text-sm leading-snug">{{ $product->name }}</h4>
                                    @if($product->description)
                                        <p class="text-xs text-slate-500 line-clamp-1 mt-0.5 max-w-xs">{{ $product->description }}</p>
                                    @else
                                        <p class="text-xs text-slate-650 italic mt-0.5">Sin descripción</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Categoría badge -->
                        <td class="p-4">
                            @if($product->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-900 text-indigo-400 border border-slate-800/80">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-600 italic">Ninguna</span>
                            @endif
                        </td>

                        <!-- Precio Venta -->
                        <td class="p-4">
                            <span class="font-semibold text-white text-sm">
                                ${{ number_format($product->price, 2) }}
                            </span>
                        </td>

                        <!-- Costo Estimado -->
                        <td class="p-4">
                            @if($product->estimated_cost !== null)
                                <span class="text-slate-300 text-sm">
                                    ${{ number_format($product->estimated_cost, 2) }}
                                </span>
                            @else
                                <span class="text-xs text-slate-500 italic">No calculado</span>
                            @endif
                        </td>

                        <!-- Precio Sugerido -->
                        <td class="p-4">
                            @if($product->suggested_price !== null)
                                <span class="text-slate-400 text-sm">
                                    ${{ number_format($product->suggested_price, 2) }}
                                </span>
                            @else
                                <span class="text-xs text-slate-500 italic">N/A</span>
                            @endif
                        </td>

                        <!-- Estado (Toggle Form / Badge) -->
                        <td class="p-4">
                            <form action="{{ route('products.change-statement', $product->id) }}" method="POST" class="form-toggle inline-block align-middle">
                                @csrf
                                @method('PATCH')
                                <button 
                                    type="submit" 
                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-all duration-300 ease-out focus:outline-none transform hover:scale-105 active:scale-95 {{ $product->is_active ? 'bg-indigo-600' : 'bg-slate-800' }}"
                                    title="Alternar estado (Activo/Inactivo)"
                                >
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] {{ $product->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                            <span class="ml-2 text-xs font-semibold {{ $product->is_active ? 'text-emerald-400' : 'text-slate-500' }}">
                                {{ $product->is_active ? 'Activo' : 'Oculto' }}
                            </span>
                        </td>

                        <!-- Acciones (Edit / Delete / Recipe) -->
                        <td class="p-4 text-right">
                            <div class="inline-flex items-center space-x-1">
                                <!-- Receta Button (Programador 3 Integration) -->
                                <a 
                                    href="{{ route('recipes.edit', $product->id) }}" 
                                    class="p-2 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 border border-transparent hover:border-indigo-500/20 transition-all duration-300"
                                    title="Constructor de Receta (Ingredientes)"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </a>

                                <!-- Edit Button -->
                                <a 
                                    href="{{ route('products.edit', $product->id) }}" 
                                    class="p-2 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition-all duration-300"
                                    title="Editar producto"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </a>

                                <!-- Delete Button -->
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="p-2 rounded-lg text-slate-400 hover:text-rose-450 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all duration-300 cursor-pointer"
                                        title="Eliminar producto"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar el producto {{ $product->name }}? Se aplicará borrado lógico.')"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
<script src="/js/products.js"></script>
@endsection
