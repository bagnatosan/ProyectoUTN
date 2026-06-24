@extends('layouts.app')

@section('title', 'Registro de Cliente | ProyectoUTN')

@section('content')
<div class="max-w-md mx-auto">
    <a href="{{ route('register.hub') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-400 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver a opciones de registro</span>
    </a>

    <!-- Registration Card -->
    <div class="relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 shadow-2xl backdrop-blur-sm">
        <div class="absolute inset-x-0 top-0 h-1 auth-accent-bar-client rounded-t-2xl"></div>

        <div class="mb-8">
            <span class="auth-role-badge auth-role-badge-client">Cuenta Cliente</span>
            <h1 class="text-2xl font-extrabold mt-2">Registro de cliente</h1>
            <p class="text-xs text-slate-400 mt-1">Completá tus datos para explorar emprendimientos y reservar.</p>
        </div>

        <form action="{{ route('register.client.store') }}" method="POST" class="space-y-6" id="client-registration-form">
            @csrf

            <!-- Full Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Nombre Completo
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           required 
                           autocomplete="name" 
                           placeholder="Ej. Juan Pérez"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('name') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('name')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Correo Electrónico
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           placeholder="ejemplo@correo.com"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('email') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('email')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Contraseña
                    </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autocomplete="new-password" 
                           placeholder="••••••••"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('password') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('password')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Confirmar Contraseña
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required 
                           autocomplete="new-password" 
                           placeholder="••••••••"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border border-slate-800 focus:ring-indigo-500 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    id="btn-submit-client-registration"
                    class="w-full py-3 px-4 rounded-xl text-center text-sm font-semibold auth-role-btn auth-role-btn-client transition-all duration-300 transform active:scale-[0.98]">
                Crear mi cuenta
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-5">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Iniciá sesión</a>
        </p>
    </div>
</div>
@endsection
