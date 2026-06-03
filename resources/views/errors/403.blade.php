@extends('layouts.app')

@section('title', 'Acceso Denegado | ProyectoUTN')

@section('content')
<div class="max-w-md mx-auto text-center py-12">
    <div class="border border-slate-800/80 bg-slate-900/40 backdrop-blur rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
        <!-- Glow Effect -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-48 rounded-full bg-rose-500/5 blur-3xl pointer-events-none"></div>

        <!-- Animated SVG Cat Container -->
        <div class="relative w-48 h-48 mx-auto mb-6 flex items-center justify-center">
            <svg class="w-full h-full text-slate-300" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Tail -->
                <path class="cat-tail" d="M150 140 C170 140, 185 110, 175 80 C170 65, 155 60, 155 75 C155 90, 165 95, 160 115 C155 125, 145 130, 135 130" stroke="#6366f1" stroke-width="8" stroke-linecap="round" fill="none"/>
                
                <!-- Body -->
                <ellipse class="cat-body" cx="100" cy="135" rx="55" ry="40" fill="#1e1b4b" stroke="#312e81" stroke-width="4"/>
                
                <!-- Head -->
                <g class="cat-head">
                    <!-- Left Ear -->
                    <path class="cat-ear-left" d="M65 85 L50 45 L85 65 Z" fill="#312e81" stroke="#4338ca" stroke-width="3" stroke-linejoin="round"/>
                    <path d="M68 80 L58 53 L81 67 Z" fill="#fda4af"/> <!-- Inner ear -->
                    
                    <!-- Right Ear -->
                    <path class="cat-ear-right" d="M135 85 L150 45 L115 65 Z" fill="#312e81" stroke="#4338ca" stroke-width="3" stroke-linejoin="round"/>
                    <path d="M132 80 L142 53 L119 67 Z" fill="#fda4af"/> <!-- Inner ear -->
                    
                    <!-- Face Base -->
                    <circle cx="100" cy="95" r="35" fill="#1e1b4b" stroke="#312e81" stroke-width="4"/>
                    
                    <!-- Left Eye -->
                    <g class="cat-eye-left">
                        <ellipse cx="85" cy="92" rx="5" ry="7" fill="#6366f1"/>
                        <circle cx="83" cy="90" r="1.5" fill="white"/> <!-- Glare -->
                        <line class="eye-lid-left" x1="78" y1="92" x2="92" y2="92" stroke="#1e1b4b" stroke-width="14" style="visibility: hidden;"/>
                    </g>
                    
                    <!-- Right Eye -->
                    <g class="cat-eye-right">
                        <ellipse cx="115" cy="92" rx="5" ry="7" fill="#6366f1"/>
                        <circle cx="113" cy="90" r="1.5" fill="white"/> <!-- Glare -->
                        <line class="eye-lid-right" x1="108" y1="92" x2="122" y2="92" stroke="#1e1b4b" stroke-width="14" style="visibility: hidden;"/>
                    </g>

                    <!-- Nose & Snout -->
                    <polygon points="97,102 103,102 100,105" fill="#fda4af"/>
                    <path d="M96 108 C98 111, 100 111, 100 108 C100 111, 102 111, 104 108" stroke="#fda4af" stroke-width="2" stroke-linecap="round" fill="none"/>
                    
                    <!-- Whiskers -->
                    <line x1="60" y1="102" x2="40" y2="100" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="60" y1="107" x2="38" y2="109" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="140" y1="102" x2="160" y2="100" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="140" y1="107" x2="162" y2="109" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                </g>

                <!-- Paws (Front) -->
                <circle cx="80" cy="170" r="10" fill="#312e81" stroke="#4338ca" stroke-width="3"/>
                <circle cx="120" cy="170" r="10" fill="#312e81" stroke="#4338ca" stroke-width="3"/>
            </svg>


        </div>

        <!-- Error Code Badges -->
        <div class="inline-flex items-center space-x-2 bg-rose-500/10 border border-rose-500/20 text-rose-450 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-4">
            <span>Error 403</span>
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
            <span>Acceso Prohibido</span>
        </div>

        <!-- Messaging -->
        <h2 class="text-xl font-bold text-white mb-2">¡Ups! Acceso Restringido</h2>
        <p class="text-slate-400 text-sm leading-relaxed mb-6">
            No tenés permisos para acceder a esta vista. Solo los usuarios con cuenta de Emprendedor pueden gestionar este catálogo.
        </p>

        <!-- Navigation Button -->
        <div class="pt-4 border-t border-slate-800/80">
            <a 
                href="{{ route('dashboard') }}" 
                class="w-full inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-rose-600 to-indigo-600 hover:from-rose-500 hover:to-indigo-500 text-white font-semibold text-sm rounded-xl py-3 shadow-lg shadow-rose-950/20 transition-all duration-300 active:scale-[0.98]"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span>Volver al Panel Principal</span>
            </a>
        </div>
    </div>
</div>
@endsection
