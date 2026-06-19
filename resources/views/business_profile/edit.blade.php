@extends('layouts.app')

@section('title', 'Perfil del Emprendimiento')

@section('content')
<div class="w-full max-w-5xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Perfil del emprendimiento</h1>
        <p class="text-slate-500 text-sm mt-1">Información pública visible para tus clientes</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- COLUMNA IZQUIERDA: Formulario --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Datos del negocio</h2>

            <form action="{{ route('business_profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Logo --}}
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center overflow-hidden">
                        @if($profile && $profile->logo)
                            <img src="{{ Storage::url($profile->logo) }}" alt="Logo" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $profile->business_name ?? auth()->user()->name }}</p>
                        <label class="mt-1 cursor-pointer text-xs text-emerald-600 hover:text-emerald-700 border border-emerald-300 px-3 py-1 rounded-lg inline-block">
                            Cambiar logo
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg" class="hidden">
                        </label>
                        <p class="text-xs text-slate-400 mt-1">JPG o PNG, máx 2MB</p>
                    </div>
                </div>

                {{-- Errores --}}
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 text-sm">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Nombre comercial --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nombre comercial</label>
                    <input type="text" name="business_name"
                        value="{{ old('business_name', $profile->business_name ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                        required>
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Descripción</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500">{{ old('description', $profile->description ?? '') }}</textarea>
                </div>

                {{-- Teléfono --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Teléfono / WhatsApp</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $profile->phone ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500">
                </div>

                {{-- Dirección --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Dirección (opcional)</label>
                    <input type="text" name="address"
                        value="{{ old('address', $profile->address ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500">
                </div>

                {{-- Margen de ganancia general --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                        Margen de ganancia general
                    </label>
                    <div class="flex items-start gap-3">
                        <input type="number" name="profit_margin" step="0.1" min="1" max="50"
                            value="{{ old('profit_margin', $profile->profit_margin ?? 3) }}"
                            class="w-24 bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-emerald-600 font-mono font-semibold focus:outline-none focus:border-emerald-500">
                        <span class="text-xs text-slate-500 leading-relaxed">
                            Multiplicador sobre el costo (ej: 3 = 300%). Se usa como margen por defecto en todos tus productos, salvo que definas uno personalizado en un producto puntual.
                        </span>
                    </div>
                </div>

                {{-- Éxito --}}
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 rounded-lg transition-colors text-sm">
                    Guardar cambios
                </button>

            </form>
        </div>

        {{-- COLUMNA DERECHA: Vista previa --}}
        <div class="space-y-6">

            {{-- Vista previa pública --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700 p-4 pb-0">Vista previa pública</h2>

                {{-- Banner verde --}}
                <div class="m-4 rounded-xl bg-emerald-700 p-6 text-center">
                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center mx-auto mb-3 overflow-hidden">
                        @if($profile && $profile->logo)
                            <img src="{{ Storage::url($profile->logo) }}" alt="Logo" class="w-full h-full object-cover">
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                            </svg>
                        @endif
                    </div>
                    <p class="font-bold text-white text-lg">{{ $profile->business_name ?? 'Tu emprendimiento' }}</p>
                    <p class="text-emerald-50 text-sm mt-1">{{ $profile->description ?? 'Descripción de tu negocio' }}</p>
                    @if($profile && $profile->address)
                        <p class="text-emerald-100 text-xs mt-1">{{ $profile->address }}</p>
                    @endif
                </div>
            </div>

            {{-- Sección seguridad --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Seguridad</h2>

                <form action="{{ route('business_profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Contraseña actual</label>
                        <input type="password" name="current_password"
                            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nueva contraseña</label>
                        <input type="password" name="new_password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500">
                    </div>

                    <button type="submit"
                        class="w-full border border-slate-300 hover:border-slate-400 text-slate-600 hover:text-slate-800 font-semibold py-2 rounded-lg transition-colors text-sm">
                        Cambiar contraseña
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
