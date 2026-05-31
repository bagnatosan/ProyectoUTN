@extends('layouts.app')

@section('title', 'Selecciona tu Rol | ProyectoUTN')

@section('content')
<div class="text-center mb-12 animate-fade-in">
    <span class="px-3 py-1 text-xs font-semibold tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20 uppercase">
        Paso 1: Identificación
    </span>
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mt-4 text-white">
        ¿Cómo deseas unirte a nosotros?
    </h1>
    <p class="text-slate-400 mt-2 max-w-lg mx-auto text-sm md:text-base">
        Selecciona si deseas registrarte para reservar servicios o si eres un vendedor buscando publicar y gestionar su emprendimiento.
    </p>
</div>

<div class="grid md:grid-cols-2 gap-8 items-stretch max-w-3xl mx-auto">
    <!-- Client Card Selection -->
    <div class="group relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 flex flex-col justify-between hover:border-indigo-500/50 hover:bg-slate-900/60 hover:shadow-2xl hover:shadow-indigo-500/5 transition-all duration-300 transform hover:-translate-y-1">
        <!-- Top border glow on hover -->
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <div>
            <!-- Icon -->
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>

            <!-- Title -->
            <h2 class="text-xl font-bold text-white group-hover:text-indigo-300 transition-colors duration-300">
                Soy Cliente
            </h2>
            <p class="text-slate-400 text-sm mt-2">
                Busco explorar emprendimientos locales y reservar turnos o comprar productos de forma rápida y segura.
            </p>

            <!-- Features -->
            <ul class="mt-6 space-y-3 text-sm text-slate-300">
                <li class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Explora catálogos y productos</span>
                </li>
                <li class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Realiza múltiples reservas online</span>
                </li>
                <li class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Recibe notificaciones y recordatorios</span>
                </li>
            </ul>
        </div>

        <div class="mt-8">
            <a href="{{ route('register.client') }}" 
               id="btn-select-client"
               class="block w-full py-3 px-4 rounded-xl text-center text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-cyan-500 text-white shadow-lg shadow-indigo-600/20 hover:shadow-indigo-500/30 transition-all duration-300">
                Registrarme como Cliente
            </a>
        </div>
    </div>

    <!-- Seller/Entrepreneur Card Selection -->
    <div class="group relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 flex flex-col justify-between hover:border-purple-500/50 hover:bg-slate-900/60 hover:shadow-2xl hover:shadow-purple-500/5 transition-all duration-300 transform hover:-translate-y-1">
        <!-- Top border glow on hover -->
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <div>
            <!-- Icon -->
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>

            <!-- Title -->
            <h2 class="text-xl font-bold text-white group-hover:text-purple-300 transition-colors duration-300">
                Soy Emprendedor
            </h2>
            <p class="text-slate-400 text-sm mt-2">
                Quiero registrar mi negocio, publicar mi catálogo de productos, gestionar mis horarios y recibir reservas directas de mis clientes.
            </p>

            <!-- Features -->
            <ul class="mt-6 space-y-3 text-sm text-slate-300">
                <li class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Crea tu perfil de negocio comercial</span>
                </li>
                <li class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Gestiona horarios y productos</span>
                </li>
                <li class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Organiza insumos e ingredientes</span>
                </li>
            </ul>
        </div>

        <div class="mt-8">
            <a href="{{ route('register.seller') }}" 
               id="btn-select-seller"
               class="block w-full py-3 px-4 rounded-xl text-center text-sm font-semibold bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-pink-500 text-white shadow-lg shadow-purple-600/20 hover:shadow-purple-500/30 transition-all duration-300">
                Registrarme como Emprendedor
            </a>
        </div>
    </div>
</div>
@endsection
