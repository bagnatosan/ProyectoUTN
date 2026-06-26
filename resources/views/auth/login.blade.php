@extends('layouts.app')

@section('title', 'Iniciar sesión | ProyectoUTN')

@section('content')
<div class="max-w-md mx-auto animate-fade-in">
    <a href="{{ route('register.select') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-400 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver al inicio</span>
    </a>

    <div class="relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 shadow-2xl backdrop-blur-sm">
        <div class="absolute inset-x-0 top-0 h-1 auth-accent-bar-client rounded-t-2xl"></div>

        <div class="mb-8">
            <!-- <span class="auth-role-badge auth-role-badge-client">Acceso</span> -->
            <h1 class="text-2xl font-extrabold mt-2">Iniciar sesión</h1>
            <p class="text-xs text-slate-400 mt-1">
                Ingresá con tu cuenta. Te llevamos al panel según tu rol: cliente, emprendedor o administrador.
            </p>
        </div>

        <form action="{{ route('login.store') }}" method="POST" class="space-y-5" id="login-form">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Correo electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" placeholder="ejemplo@correo.com"
                       class="block w-full px-4 py-3 bg-slate-950/60 border @error('email') border-rose-500 @else border-slate-800 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('email')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Contraseña</label>
                <input type="password" name="password" id="password" required autocomplete="current-password" placeholder="••••••••"
                       class="block w-full px-4 py-3 bg-slate-950/60 border @error('password') border-rose-500 @else border-slate-800 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('password')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" id="btn-submit-login" class="auth-role-btn auth-role-btn-client w-full transition-all duration-300 transform active:scale-[0.98]">
                Ingresar
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-800 text-center text-xs text-slate-400">
            ¿No tenés cuenta?
            <a href="{{ route('register.hub') }}" class="text-indigo-400 font-semibold hover:underline">Elegí cómo registrarte</a>
        </div>
    </div>
</div>
@endsection
