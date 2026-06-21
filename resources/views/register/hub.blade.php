@extends('layouts.app')

@section('title', 'Crear cuenta | ProyectoUTN')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="text-center mb-10">
        <a href="{{ route('register.select') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-400 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al inicio
        </a>
        <h1 class="text-2xl md:text-3xl font-extrabold">¿Cómo querés unirte?</h1>
        <p class="text-slate-400 mt-2 text-sm md:text-base">Elegí el tipo de cuenta que mejor se adapte a vos.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Cliente --}}
        <div class="auth-role-card auth-role-card-client">
            <div class="auth-role-icon auth-role-icon-client">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="auth-role-badge auth-role-badge-client">Cliente</span>
            <h2 class="text-xl font-bold mt-4">Quiero reservar</h2>
            <p class="text-sm text-slate-400 mt-2">Explorá emprendimientos locales, mirá catálogos y reservá turnos o productos.</p>
            <ul class="mt-5 space-y-2 text-sm text-slate-300">
                <li class="flex items-center gap-2"><span class="text-indigo-400">✓</span> Cuenta gratuita</li>
                <li class="flex items-center gap-2"><span class="text-indigo-400">✓</span> Reservas online</li>
                <li class="flex items-center gap-2"><span class="text-indigo-400">✓</span> Historial de pedidos</li>
            </ul>
            <a href="{{ route('register.client') }}" class="auth-role-btn auth-role-btn-client mt-8">
                Registrarme como cliente
            </a>
            <p class="text-xs text-slate-400 mt-4 text-center">
                ¿Ya tenés cuenta?
                <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Iniciá sesión</a>
            </p>
        </div>

        {{-- Emprendedor --}}
        <div class="auth-role-card auth-role-card-seller">
            <div class="auth-role-icon auth-role-icon-seller">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="auth-role-badge auth-role-badge-seller">Emprendedor</span>
            <h2 class="text-xl font-bold mt-4">Tengo un negocio</h2>
            <p class="text-sm text-slate-400 mt-2">Publicá tu catálogo, gestioná horarios y recibí reservas desde un panel.</p>
            <ul class="mt-5 space-y-2 text-sm text-slate-300">
                <li class="flex items-center gap-2"><span style="color:#d88448">✓</span> Perfil comercial</li>
                <li class="flex items-center gap-2"><span style="color:#d88448">✓</span> Catálogo y reservas</li>
                <li class="flex items-center gap-2"><span style="color:#d88448">✓</span> Visible en el mapa</li>
            </ul>
            <a href="{{ route('register.seller') }}" class="auth-role-btn auth-role-btn-seller mt-8">
                Registrarme como emprendedor
            </a>
            <p class="text-xs text-slate-400 mt-4 text-center">
                ¿Ya tenés cuenta?
                <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color:#d88448">Iniciá sesión</a>
            </p>
        </div>
    </div>

    <p class="text-center text-xs text-slate-500 mt-8">
        ¿Tenés dudas sobre registrar tu emprendimiento?
        <a href="{{ route('register.select') }}#contacto-emprendedores" class="text-indigo-400 hover:underline">Escribinos</a>
    </p>
</div>
@endsection
