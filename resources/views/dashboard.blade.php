@extends('layouts.app')

@section('title', auth()->user()->role === 'client' ? 'Catálogos Disponibles' : 'Panel de Control')

@section('content')
@if(auth()->user()->role === 'client')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto space-y-4">
        <span class="px-3.5 py-1 text-xs font-bold tracking-wider rounded-full uppercase border shadow-sm text-indigo-400 bg-indigo-500/10 border-indigo-500/20">
            Catálogos Disponibles
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-white mt-4">
            Emprendimientos Locales
        </h1>
        <p class="text-slate-400 text-sm md:text-base leading-relaxed">
            Explora las tiendas disponibles, busca tus delicias favoritas y agenda tu reserva en simples pasos.
        </p>
    </div>

    <!-- Buscador de negocios -->
    <div class="relative max-w-md mx-auto">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
        <input type="text" id="business-search" placeholder="Buscar tiendas por nombre..." class="w-full pl-10 pr-4 py-2.5 bg-slate-900/60 border border-slate-800 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 rounded-xl text-slate-200 placeholder-slate-500 text-sm focus:outline-none transition-all">
    </div>

    <!-- Grilla de Negocios -->
    @if($businesses->isEmpty())
        <div class="border border-dashed border-slate-800 rounded-2xl p-16 text-center text-slate-500 bg-slate-900/20">
            <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="font-medium text-slate-400 text-base">No hay emprendimientos registrados en este momento.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="businesses-grid">
            @foreach($businesses as $business)
                <div class="business-card group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-900/20 hover:bg-slate-900/40 hover:border-slate-700/80 transition-all duration-300 shadow-lg hover:shadow-2xl p-6" 
                     data-name="{{ strtolower($business->business_name) }}">
                    <!-- background glow decorative -->
                    <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-indigo-500/5 blur-xl group-hover:bg-emerald-500/10 transition-colors"></div>
                    
                    <div class="flex items-start gap-4 relative z-10 mb-4">
                        <!-- Logo -->
                        <div class="shrink-0">
                            @if($business->logo && (filter_var($business->logo, FILTER_VALIDATE_URL) || file_exists(public_path('storage/' . $business->logo))))
                                <img src="{{ filter_var($business->logo, FILTER_VALIDATE_URL) ? $business->logo : asset('storage/' . $business->logo) }}" alt="Logo {{ $business->business_name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-700 shadow-md">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center font-bold text-xl text-white border border-indigo-500/30 shadow-md">
                                    {{ strtoupper(substr($business->business_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Title and description -->
                        <div class="space-y-1">
                            <h3 class="text-xl font-bold text-white tracking-tight leading-snug group-hover:text-emerald-400 transition-colors">
                                {{ $business->business_name }}
                            </h3>
                            <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                {{ $business->description ?? 'Sin descripción disponible.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Contact/Address and action -->
                    <div class="pt-4 border-t border-slate-900 flex items-center justify-between mt-auto">
                        <div class="space-y-1 text-[10px] text-slate-400">
                            @if($business->address)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span class="truncate max-w-[150px]">{{ $business->address }}</span>
                                </div>
                            @endif
                            @if($business->phone)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.502-5.118-3.796-6.62-6.62l1.293-.97c.362-.271.527-.834.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                    <span>{{ $business->phone }}</span>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('catalog.show', $business->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-slate-900 border border-slate-800 hover:border-emerald-500/50 hover:bg-slate-900/60 shadow-md transition-all cursor-pointer">
                            <span>Ver Catálogo</span>
                            <svg class="w-3.5 h-3.5 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mensaje de no resultados (oculto por defecto) -->
        <div id="no-results-message" class="hidden border border-dashed border-slate-800 rounded-2xl p-16 text-center text-slate-500 bg-slate-900/20">
            <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-medium text-slate-400 text-base">No encontramos ningún emprendimiento que coincida con tu búsqueda.</p>
        </div>
    @endif
</div>

<!-- JS Vanilla para Filtrado en tiempo real de negocios -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('business-search');
    const businessCards = document.querySelectorAll('.business-card');
    const businessesGrid = document.getElementById('businesses-grid');
    const noResultsMessage = document.getElementById('no-results-message');

    if (!businessesGrid || !searchInput) return;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        let visibleCount = 0;

        businessCards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(query)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            businessesGrid.style.display = 'none';
            noResultsMessage.classList.remove('hidden');
        } else {
            businessesGrid.style.display = '';
            noResultsMessage.classList.add('hidden');
        }
    });
});
</script>
@else
<div class="max-w-2xl mx-auto text-center animate-fade-in py-12">

    <!-- User/Role Badge -->
    @if(auth()->user()->role === 'admin')
        <span class="px-3.5 py-1 text-xs font-bold tracking-wider rounded-full uppercase border shadow-sm text-purple-400 bg-purple-500/10 border-purple-500/20">
            Sesión Iniciada: Administrador
        </span>
    @elseif(auth()->user()->role === 'seller')
        <span class="px-3.5 py-1 text-xs font-bold tracking-wider rounded-full uppercase border shadow-sm text-purple-400 bg-purple-500/10 border-purple-500/20">
            Sesión Iniciada: Emprendedor
        </span>
    @endif

    <!-- Heading -->
    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mt-6 text-white">
        ¡Bienvenido,
        <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
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

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-12 max-w-lg mx-auto">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="p-6 rounded-xl border border-purple-900 bg-purple-500/10 backdrop-blur text-left hover:bg-purple-500/20 transition-colors">
                <h3 class="font-bold text-purple-300 text-sm">Panel de Administración</h3>
                <p class="text-slate-500 text-xs mt-1">Gestión global de usuarios, productos y estadísticas.</p>
            </a>
            <div class="p-6 rounded-xl border border-slate-900 bg-slate-900/20 backdrop-blur text-left">
                <h3 class="font-bold text-slate-200 text-sm">Tu Perfil</h3>
                <p class="text-slate-500 text-xs mt-1">Configura los datos de tu cuenta de administrador.</p>
            </div>
        @else
            <div class="p-6 rounded-xl border border-slate-900 bg-slate-900/20 backdrop-blur text-left">
                <h3 class="font-bold text-slate-200 text-sm">Tu Perfil</h3>
                <p class="text-slate-500 text-xs mt-1">Configura los datos personales de tu cuenta de acceso.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-900 bg-slate-900/20 backdrop-blur text-left">
                <h3 class="font-bold text-slate-200 text-sm">Mi Negocio</h3>
                <p class="text-slate-500 text-xs mt-1">Administra tu catálogo, productos y horarios.</p>
            </div>
        @endif
    </div>

</div>
@endif
@endsection
