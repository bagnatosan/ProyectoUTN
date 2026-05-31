@extends('layouts.app')

@section('title', 'Panel de Control | ProyectoUTN')

@section('content')
<div class="max-w-2xl mx-auto text-center animate-fade-in py-12">
    <!-- User/Role Badge -->
    <span class="px-3.5 py-1 text-xs font-bold tracking-wider rounded-full uppercase border shadow-sm {{ auth()->user()->role === 'seller' ? 'text-purple-400 bg-purple-500/10 border-purple-500/20' : 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20' }}">
        Sesión Iniciada: {{ auth()->user()->role === 'seller' ? 'Emprendedor' : 'Cliente' }}
    </span>

    <!-- Heading -->
    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mt-6 text-white">
        ¡Bienvenido, 
        <span class="bg-gradient-to-r {{ auth()->user()->role === 'seller' ? 'from-purple-400 to-pink-400' : 'from-indigo-400 to-cyan-400' }} bg-clip-text text-transparent">
            @if(auth()->user()->role === 'seller' && auth()->user()->businessProfile)
                {{ auth()->user()->businessProfile->business_name }}
            @else
                {{ auth()->user()->name }}
            @endif
        </span>!
    </h1>

    <p class="text-slate-400 mt-4 max-w-md mx-auto text-sm md:text-base leading-relaxed">
        Este es tu panel de control. Has ingresado correctamente en la plataforma y tu sesión se encuentra activa.
    </p>

    <!-- Empty View Actions (Dashboard mock cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-12 max-w-lg mx-auto">
        <div class="p-6 rounded-xl border border-slate-900 bg-slate-900/20 backdrop-blur text-left">
            <h3 class="font-bold text-slate-200 text-sm">Tu Perfil</h3>
            <p class="text-slate-500 text-xs mt-1">Configura los datos personales de tu cuenta de acceso.</p>
        </div>
        <div class="p-6 rounded-xl border border-slate-900 bg-slate-900/20 backdrop-blur text-left">
            <h3 class="font-bold text-slate-200 text-sm">
                {{ auth()->user()->role === 'seller' ? 'Mi Negocio' : 'Mis Reservas' }}
            </h3>
            <p class="text-slate-500 text-xs mt-1">
                {{ auth()->user()->role === 'seller' ? 'Administra tu catálogo, productos y horarios.' : 'Visualiza y gestiona tus reservas agendadas.' }}
            </p>
        </div>
    </div>
</div>
@endsection
