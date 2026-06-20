@extends('layouts.app')

@section('title', 'Nuevo Producto | ProyectoUTN')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl relative overflow-hidden group">
        <!-- Accent Glow -->
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-indigo-500/5 blur-2xl pointer-events-none group-hover:bg-indigo-500/10 transition-all duration-500"></div>

        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800/80">
            <div>
                <!--<span class="px-2.5 py-1 text-[10px] font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                    Módulo de Catálogo
                </span>-->
                <h1 class="text-2xl font-bold tracking-tight text-white mt-3">
                    Agregar Nuevo Producto
                </h1>
                <p class="text-slate-400 text-xs mt-1">
                    Ingresa los datos para registrar un nuevo producto en tu catálogo de ventas.
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-400 hover:text-white bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Cancelar</span>
            </a>
        </div>

        <!-- FORMULARIO DE CREACIÓN -->
        <!-- NOTA: Se usa el método POST tradicional que apunta a la ruta 'products.store' -->
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Nombre del Producto <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}"
                    required
                    placeholder="Ej: Medialuna de manteca, Café con leche..."
                    class="w-full bg-slate-950/80 border @error('name') border-rose-500 focus:ring-rose-500/30 @else border-slate-800/80 focus:border-indigo-500 focus:ring-indigo-500/30 @enderror rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 transition-all duration-300"
                >
                @error('name')
                    <p class="text-xs text-rose-450 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Descripción del Producto
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    placeholder="Describe los ingredientes, alérgenos o tamaño del producto..."
                    class="w-full bg-slate-950/80 border @error('description') border-rose-500 focus:ring-rose-500/30 @else border-slate-800/80 focus:border-indigo-500 focus:ring-indigo-500/30 @enderror rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 transition-all duration-300"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-450 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fila: Categoría y Precio -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Categoría -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                        Categoría <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="category_id" 
                        id="category_id" 
                        required
                        class="w-full bg-slate-950/80 border @error('category_id') border-rose-500 focus:ring-rose-500/30 @else border-slate-800/80 focus:border-indigo-500 focus:ring-indigo-500/30 @enderror rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 transition-all duration-300 cursor-pointer"
                    >
                        <option value="" selected disabled>Selecciona una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-rose-450 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Precio -->
                <div>
                    <label for="price" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                        Precio de Venta ($) <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="price" 
                        id="price" 
                        step="0.01" 
                        min="0"
                        value="{{ old('price') }}"
                        required
                        placeholder="0.00"
                        class="w-full bg-slate-950/80 border @error('price') border-rose-500 focus:ring-rose-500/30 @else border-slate-800/80 focus:border-indigo-500 focus:ring-indigo-500/30 @enderror rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 transition-all duration-300"
                    >
                    @error('price')
                        <p class="text-xs text-rose-450 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Fila: Estado Activo y Costos Informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-950/30 border border-slate-850/60 p-4 rounded-xl">
                <!-- Visibilidad (Estado) -->
                <div>
                    <label for="is_active" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                        Visibilidad en el Catálogo
                    </label>
                    <select 
                        name="is_active" 
                        id="is_active" 
                        class="w-full bg-slate-950 border border-slate-800/80 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500 cursor-pointer"
                    >
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Activo (Visible inmediatamente para los clientes)</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactivo (Oculto en catálogo, ideal para borradores)</option>
                    </select>
                </div>

                <!-- Resumen de Costos Receta (Informativo / Deshabilitado) -->
                <div class="flex flex-col justify-center text-xs space-y-1">
                    <p class="text-slate-500 font-semibold uppercase tracking-wider">Costo Estimado de Receta:</p>
                    <p class="text-slate-300 font-medium">
                        <span class="text-slate-500 italic">No calculado (sin receta)</span>
                    </p>
                    <p class="text-[10px] text-slate-500 mt-1">
                        Sugerido para venta: <span class="font-semibold text-slate-400">$0.00</span>
                    </p>
                </div>
            </div>

            <!-- Imagen del Producto -->
            <div class="space-y-3">
                <label for="image" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Imagen del Producto
                </label>
                <div class="w-full">
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        accept="image/*"
                        class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-950 file:text-indigo-400 file:border-slate-800 hover:file:bg-slate-900 transition-all duration-350 cursor-pointer"
                    >
                    <p class="text-[10px] text-slate-500 mt-1.5">
                        Formatos soportados: JPG, PNG, WEBP. Máx: 2MB. (Opcional)
                    </p>
                    @error('image')
                        <p class="text-xs text-rose-450 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-800/60">
                <a 
                    href="{{ route('products.index') }}" 
                    class="px-5 py-2.5 bg-slate-950 hover:bg-slate-900 border border-slate-850 text-slate-300 font-semibold text-sm rounded-xl transition-all duration-200"
                >
                    Volver
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-500/25 transition-all duration-300 cursor-pointer"
                >
                    Crear Producto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
