@extends('layouts.app')

@section('title', 'Perfil del Emprendimiento')

@section('content')
<div class="w-full max-w-5xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Perfil del emprendimiento</h1>
        <p class="text-slate-400 text-sm mt-1">Información pública visible para tus clientes</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- COLUMNA IZQUIERDA: Formulario --}}
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-slate-300 mb-4">Datos del negocio</h2>

            <form action="{{ route('business_profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Logo --}}
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 rounded-xl bg-green-600/20 border border-green-600/30 flex items-center justify-center overflow-hidden">
                        @if($profile && $profile->logo)
                            <img src="{{ Storage::url($profile->logo) }}" alt="Logo" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $profile->business_name ?? auth()->user()->name }}</p>
                        <label class="mt-1 cursor-pointer text-xs text-green-400 hover:text-green-300 border border-green-600/30 px-3 py-1 rounded-lg inline-block">
                            Cambiar logo
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg" class="hidden">
                        </label>
                        <p class="text-xs text-slate-500 mt-1">JPG o PNG, máx 2MB</p>
                    </div>
                </div>

                {{-- Errores --}}
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Nombre comercial --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nombre comercial</label>
                    <input type="text" name="business_name"
                        value="{{ old('business_name', $profile->business_name ?? '') }}"
                        class="w-full bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-green-500/50"
                        required>
                </div>

                {{-- Descripción --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Descripción</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-green-500/50">{{ old('description', $profile->description ?? '') }}</textarea>
                </div>

                {{-- Teléfono --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Teléfono / WhatsApp</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $profile->phone ?? '') }}"
                        class="w-full bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-green-500/50">
                </div>

                {{-- Dirección --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Dirección (opcional)</label>
                    <input type="text" name="address"
                        value="{{ old('address', $profile->address ?? '') }}"
                        class="w-full bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-green-500/50">
                </div>

                {{-- Éxito --}}
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-500 text-white font-semibold py-2 rounded-lg transition-colors text-sm">
                    Guardar cambios
                </button>

            </form>
        </div>

        {{-- COLUMNA DERECHA: Vista previa --}}
        <div class="space-y-6">

            {{-- Vista previa pública --}}
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden">
                <h2 class="text-sm font-semibold text-slate-300 p-4 pb-0">Vista previa pública</h2>

                {{-- Banner verde --}}
                <div class="m-4 rounded-xl bg-green-800/40 border border-green-700/30 p-6 text-center">
                    <div class="w-12 h-12 rounded-xl bg-green-600/30 border border-green-600/40 flex items-center justify-center mx-auto mb-3">
                        @if($profile && $profile->logo)
                            <img src="{{ Storage::url($profile->logo) }}" alt="Logo" class="w-full h-full object-cover rounded-xl">
                        @else
                            <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                            </svg>
                        @endif
                    </div>
                    <p class="font-bold text-white text-lg">{{ $profile->business_name ?? 'Tu emprendimiento' }}</p>
                    <p class="text-slate-300 text-sm mt-1">{{ $profile->description ?? 'Descripción de tu negocio' }}</p>
                    @if($profile && $profile->address)
                        <p class="text-slate-400 text-xs mt-1">{{ $profile->address }}</p>
                    @endif
                </div>
            </div>

            {{-- Sección seguridad --}}
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-slate-300 mb-4">Seguridad</h2>

                <form action="{{ route('business_profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Contraseña actual</label>
                        <input type="password" name="current_password"
                            class="w-full bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-green-500/50">
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nueva contraseña</label>
                        <input type="password" name="new_password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full bg-slate-800/60 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-green-500/50">
                    </div>

                    <button type="submit"
                        class="w-full border border-slate-600 hover:border-slate-500 text-slate-300 hover:text-white font-semibold py-2 rounded-lg transition-colors text-sm">
                        Cambiar contraseña
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection