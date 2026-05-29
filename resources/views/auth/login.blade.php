@extends('layouts.app')

@section('title', 'Iniciar Sesión | ProyectoUTN')

@section('content')
<div class="max-w-md mx-auto">
    <!-- Back to Role Selection -->
    <a href="{{ route('register.select') }}" class="inline-flex items-center space-x-2 text-sm text-slate-400 hover:text-indigo-400 mb-6 transition-colors duration-200">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver a la selección de registro</span>
    </a>

    <!-- Login Card -->
    <div class="relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 shadow-2xl backdrop-blur-sm">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-t-2xl"></div>

        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-white">Ingresar a tu Cuenta</h1>
            <p class="text-xs text-slate-400 mt-1 font-medium">Introduce tus datos de acceso para ingresar al panel.</p>
        </div>

        <form action="{{ route('login.store') }}" method="POST" class="space-y-6" id="login-form">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Correo Electrónico
                </label>
                <div class="relative">
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
                           placeholder="usuario@correo.com"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('email') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('email')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Contraseña
                    </label>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('password') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('password')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    id="btn-submit-login"
                    class="w-full py-3 px-4 rounded-xl text-center text-sm font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white shadow-lg shadow-indigo-600/20 hover:shadow-indigo-500/30 transition-all duration-300 transform active:scale-[0.98]">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-500">
            ¿No tienes una cuenta aún? 
            <a href="{{ route('register.select') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors duration-200 ml-1">
                Regístrate aquí
            </a>
        </div>
    </div>
</div>
@endsection
