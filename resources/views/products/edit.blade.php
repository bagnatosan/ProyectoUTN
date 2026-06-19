@extends('layouts.app')

@section('title', 'Editar Producto | ProyectoUTN')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl relative overflow-hidden group">
        <!-- Accent Glow -->
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-amber-500/5 blur-2xl pointer-events-none group-hover:bg-amber-500/10 transition-all duration-500"></div>

        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800/80">
            <div>
                <span class="px-2.5 py-1 text-[10px] font-bold tracking-wider rounded-full uppercase bg-amber-500/10 border border-amber-500/20 text-amber-400">
                    Módulo de Catálogo
                </span>
                <h1 class="text-2xl font-bold tracking-tight text-white mt-3">
                    Editar Producto
                </h1>
                <p class="text-slate-400 text-xs mt-1">
                    Modifica los datos del producto, actualiza su imagen o cambia su estado.
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-400 hover:text-white bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Cancelar</span>
            </a>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <!-- NOTA: Se usa method="POST" con la directiva @method('PUT') y enctype para soportar subida de imágenes -->
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Nombre del Producto <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $product->name) }}"
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
                >{{ old('description', $product->description) }}</textarea>
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
                        <option value="" disabled>Selecciona una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        value="{{ old('price', $product->price) }}"
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-100 border border-slate-300 p-4 rounded-xl">
                <!-- Visibilidad (Estado) -->
                <div>
                    <label for="is_active" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                        Visibilidad en el Catálogo
                    </label>
                    <select 
                        name="is_active" 
                        id="is_active" 
                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-indigo-500 cursor-pointer"
                    >
                        <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Activo (Visible para clientes)</option>
                        <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>Inactivo (Oculto en catálogo)</option>
                    </select>
                </div>

                <!-- Resumen de Costos Receta (Informativo / Deshabilitado) -->
                <div class="flex flex-col justify-center text-xs space-y-1">
                    <p class="text-slate-600 font-semibold uppercase tracking-wider">Costo Estimado de Receta:</p>
                    <p class="text-slate-700 font-medium">
                        @if($product->estimated_cost !== null)
                            <span class="text-sm font-bold text-slate-900">${{ number_format($product->estimated_cost, 2) }}</span>
                        @else
                            <span class="text-slate-500 italic">No calculado (sin receta)</span>
                        @endif
                    </p>
                    <p class="text-[10px] text-slate-600 mt-1">
                        Sugerido para venta: <span class="font-semibold text-slate-700">${{ number_format($product->suggested_price ?? 0, 2) }}</span>
                    </p>
                </div>
            </div>

            <!-- Margen personalizado para este producto -->
            <div class="bg-slate-100 border border-slate-300 p-4 rounded-xl">
                <label for="custom_margin" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                    Margen personalizado para este producto
                </label>
                <div class="flex items-center gap-3">
                    <input 
                        type="number" 
                        name="custom_margin" 
                        id="custom_margin" 
                        step="0.1" 
                        min="1" 
                        max="50"
                        value="{{ old('custom_margin', $product->custom_margin) }}"
                        placeholder="Ej: 4"
                        class="w-28 bg-white border @error('custom_margin') border-rose-500 focus:ring-rose-500/30 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/30 @enderror rounded-xl px-3 py-2.5 text-sm text-amber-600 font-mono font-semibold placeholder-slate-400 focus:outline-none focus:ring-1 transition-all duration-300"
                    >
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Dejalo vacío para usar el margen general de tu negocio
                        (actualmente <span class="font-semibold text-slate-700">x{{ $product->businessProfile->profit_margin ?? 3 }}</span>).
                        Completalo solo si querés un margen distinto para este producto puntual.
                    </p>
                </div>
                @error('custom_margin')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Imagen actual y nueva imagen -->
            <div class="space-y-3">
                <label for="image" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Imagen del Producto
                </label>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <!-- Vista previa de imagen actual -->
                    <div class="w-20 h-20 rounded-xl bg-slate-950 border border-slate-850 flex items-center justify-center overflow-hidden shrink-0">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        @endif
                    </div>
                    
                    <div class="flex-grow w-full">
                        <input 
                            type="file" 
                            name="image" 
                            id="image" 
                            accept="image/*"
                            class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-950 file:text-indigo-400 file:border-slate-800 hover:file:bg-slate-900 transition-all duration-350 cursor-pointer"
                        >
                        <p class="text-[10px] text-slate-500 mt-1.5">
                            Formatos soportados: JPG, PNG, WEBP. Máx: 2MB. Selecciona uno nuevo si deseas reemplazar la imagen actual.
                        </p>
                        @error('image')
                            <p class="text-xs text-rose-450 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
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
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-amber-600/10 hover:shadow-amber-500/25 transition-all duration-300 cursor-pointer"
                >
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
