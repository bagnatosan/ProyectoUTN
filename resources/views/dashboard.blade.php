@extends('layouts.app')

@section('title', auth()->user()->role === 'client' ? 'Catálogos Disponibles' : 'Panel de Control')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@push('page_bg')
<div style="position:fixed;inset:0;z-index:0;pointer-events:none;background:#1e3a2f;"></div>
@endpush

@section('content')
@if(auth()->user()->role === 'client')
<div class="animate-fade-in">

    {{-- ===== BANNER ===== --}}
    <div class="page-banner">
        <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
        <div class="page-banner__overlay"></div>
        <div class="page-banner__content">
            <h1 class="page-banner__title">Emprendimientos Locales</h1>
            <p class="page-banner__subtitle">Explorá las tiendas disponibles y reservá en simples pasos.</p>
        </div>
    </div>

    {{-- Próximas Reservas (debajo del banner) --}}
    @if($upcomingReservations->isNotEmpty())
    <div style="margin-bottom:1.5rem;padding:1rem 1.25rem;border-radius:0.875rem;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.95);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <span style="font-size:0.75rem;font-weight:700;color:#6a6966;text-transform:uppercase;letter-spacing:0.08em;">Mis próximas reservas</span>
            <a href="{{ route('reservations.index') }}" style="font-size:0.75rem;font-weight:600;color:#2d6a4f;">Ver todas →</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:0.5rem;">
            @foreach($upcomingReservations as $res)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.5rem 0.75rem;border-radius:0.625rem;background:#f9f7f2;border:1px solid #e8e0d0;">
                <div style="display:flex;align-items:center;gap:0.625rem;min-width:0;overflow:hidden;">
                    <span style="font-size:0.75rem;font-weight:700;color:#2d6a4f;white-space:nowrap;">{{ \Carbon\Carbon::parse($res->reservation_time)->format('H:i') }}</span>
                    <div style="min-width:0;overflow:hidden;">
                        <p style="font-size:0.8rem;font-weight:600;color:#1a1918;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $res->product?->name ?? 'Producto' }}</p>
                        <p style="font-size:0.7rem;color:#6a6966;">{{ $res->reservation_date->format('d/m/Y') }} · {{ $res->product?->businessProfile?->business_name ?? 'Emprendedor' }}</p>
                    </div>
                </div>
                <span style="font-size:0.65rem;font-weight:700;text-transform:uppercase;padding:0.2rem 0.5rem;border-radius:9999px;white-space:nowrap;
                    {{ $res->status === 'pending' ? 'background:rgba(245,166,35,0.12);color:#b45309;border:1px solid rgba(245,166,35,0.35);' : 'background:rgba(45,106,79,0.1);color:#2d6a4f;border:1px solid rgba(45,106,79,0.25);' }}">
                    {{ $res->status === 'pending' ? 'Pendiente' : 'Confirmada' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== CONTENT (negocios) ===== --}}
    <div class="space-y-6">

        {{-- Buscador --}}
        <div class="relative max-w-md mx-auto">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color:#9a9390;">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" id="business-search" placeholder="Buscar emprendimientos..." style="width:100%;padding:0.6rem 1rem 0.6rem 2.5rem;border-radius:0.75rem;border:1.5px solid rgba(255,255,255,0.3);background:rgba(255,255,255,0.15);color:#ffffff;font-size:0.875rem;outline:none;transition:border-color 0.2s;" placeholder-style="color:rgba(255,255,255,0.6);" onfocus="this.style.borderColor='rgba(255,255,255,0.6)'" onblur="this.style.borderColor='rgba(255,255,255,0.3)'">
        </div>

        {{-- Grilla de Negocios --}}
        @if($businesses->isEmpty())
            <div class="relative overflow-hidden" style="border-radius:1rem;min-height:200px;">
                <img src="{{ asset('images/cta-mesa.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0" style="background:rgba(245,239,230,0.88);"></div>
                <div class="relative flex flex-col items-center justify-center py-16 px-6 text-center">
                    <svg style="width:3rem;height:3rem;margin:0 auto 1rem;color:#b8b0a0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p style="font-size:0.95rem;color:#6a6966;font-weight:500;">No hay emprendimientos registrados en este momento.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="businesses-grid">
                @foreach($businesses as $business)
                    <div class="business-card group relative flex flex-col justify-between overflow-hidden transition-all duration-300"
                         style="border-radius:1rem;border:1.5px solid #e8e0d0;background:#ffffff;box-shadow:0 1px 4px rgba(0,0,0,0.06);cursor:pointer;"
                         onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.10)';this.style.borderColor='rgba(45,140,78,0.35)';"
                         onmouseleave="this.style.boxShadow='0 1px 4px rgba(0,0,0,0.06)';this.style.borderColor='#e8e0d0';"
                         data-name="{{ strtolower($business->business_name) }}">

                        {{-- Cover image --}}
                        @php
                            $fallbacks = ['galeria-pasteleria.jpg','galeria-comida-casera.jpg','galeria-catering.jpg','emprendedor-cocina.jpg'];
                            $coverSrc = $business->cover_image
                                ? storage_url($business->cover_image)
                                : asset('images/' . $fallbacks[$loop->index % count($fallbacks)]);
                        @endphp
                        <div class="relative overflow-hidden" style="height:110px;">
                            <img src="{{ $coverSrc }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0" style="background:linear-gradient(to bottom, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.25) 100%);"></div>
                        </div>

                        <div class="flex items-start gap-4 mb-4" style="padding:1.25rem 1.25rem 0;">
                            {{-- Logo --}}
                            @if($business->logo)
                                <img src="{{ filter_var($business->logo, FILTER_VALIDATE_URL) ? $business->logo : storage_url($business->logo) }}" alt="Logo {{ $business->business_name }}" style="width:3.5rem;height:3.5rem;border-radius:0.75rem;object-fit:cover;border:1.5px solid #e8e0d0;flex-shrink:0;">
                            @else
                                <div style="width:3.5rem;height:3.5rem;border-radius:0.75rem;background:linear-gradient(135deg,#2d6a4f,#1e3a2f);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.25rem;color:#ffffff;flex-shrink:0;">
                                    {{ strtoupper(substr($business->business_name, 0, 1)) }}
                                </div>
                            @endif

                            <div style="flex:1;min-width:0;">
                                <h3 style="font-size:1rem;font-weight:700;color:#1a1918;line-height:1.3;" class="group-hover:text-emerald-700 transition-colors">
                                    {{ $business->business_name }}
                                </h3>
                                <p style="font-size:0.75rem;color:#6a6966;margin-top:0.2rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ $business->description ?? 'Sin descripción disponible.' }}
                                </p>
                            </div>
                        </div>

                        <div style="padding:0.875rem 1.25rem 1.25rem;border-top:1px solid #f0ebe2;display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;flex-direction:column;gap:0.25rem;font-size:0.7rem;color:#9a9390;">
                                @if($business->address)
                                    <div style="display:flex;align-items:center;gap:0.3rem;">
                                        <svg style="width:0.875rem;height:0.875rem;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        <span style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $business->address }}</span>
                                    </div>
                                @endif
                                @if($business->phone)
                                    <div style="display:flex;align-items:center;gap:0.3rem;">
                                        <svg style="width:0.875rem;height:0.875rem;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.502-5.118-3.796-6.62-6.62l1.293-.97c.362-.271.527-.834.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                        </svg>
                                        <span>{{ $business->phone }}</span>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('catalog.show', $business->id) }}" style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.4rem 0.875rem;border-radius:0.625rem;font-size:0.75rem;font-weight:600;color:#ffffff;background:#1e3a2f;border:none;text-decoration:none;transition:background 0.2s;"
                               onmouseenter="this.style.background='#2d6a4f'" onmouseleave="this.style.background='#1e3a2f'">
                                Ver Catálogo
                                <svg style="width:0.75rem;height:0.75rem;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="no-results-message" class="hidden" style="border:1.5px dashed rgba(255,255,255,0.3);border-radius:1rem;padding:4rem;text-align:center;background:rgba(0,0,0,0.3);">
                <svg style="width:3rem;height:3rem;margin:0 auto 1rem;color:rgba(255,255,255,0.4);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p style="font-size:0.95rem;color:rgba(255,255,255,0.75);font-weight:500;">No encontramos ningún emprendimiento con ese nombre.</p>
            </div>
        @endif
    </div>
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
<div class="w-full animate-fade-in text-center" style="background:rgba(255,255,255,0.92);border-radius:1.25rem;padding:2rem 2.5rem 2.5rem;box-shadow:0 8px 32px rgba(0,0,0,0.18);margin-top:1.5rem;">

    <!-- User/Role Badge -->
    <!-- @if(auth()->user()->role === 'admin')
        <span class="px-3.5 py-1 text-xs font-bold tracking-wider rounded-full uppercase border shadow-sm text-purple-400 bg-purple-500/10 border-purple-500/20">
            Sesión Iniciada: Administrador
        </span>
    @elseif(auth()->user()->role === 'seller')
        <span class="px-3.5 py-1 text-xs font-bold tracking-wider rounded-full uppercase border shadow-sm text-purple-400 bg-purple-500/10 border-purple-500/20">
            Sesión Iniciada: Emprendedor
        </span>
    @endif -->

    <!-- Heading -->
    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mt-6" style="color:#1a1918 !important;">
        ¡Bienvenido,
        <span style="color:#f5a623;">
            @if(auth()->user()->role === 'seller' && auth()->user()->businessProfile)
                {{ auth()->user()->businessProfile->business_name }}
            @else
                {{ auth()->user()->name }}
            @endif
        </span>!
    </h1>

    <p class="text-slate-500 mt-4 max-w-md mx-auto text-sm md:text-base leading-relaxed">
        Este es tu panel de control. Has ingresado correctamente en la plataforma y tu sesión se encuentra activa.
    </p>

    @if(auth()->user()->role === 'seller')
        @php
            $overdueCount = $stats['overdue'] ?? 0;
            $todayCount = $stats['today'] ?? 0;
            $pendingCount = $stats['pending'] ?? 0;
            $confirmedCount = $stats['confirmed'] ?? 0;
        @endphp

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-8 max-w-2xl mx-auto">
            <div class="p-4 rounded-xl border border-amber-500/20 bg-amber-500/5 text-left">
                <p class="text-2xl font-bold text-amber-400">{{ $pendingCount }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Pendientes</p>
            </div>
            <div class="p-4 rounded-xl border border-sky-500/20 bg-sky-500/5 text-left">
                <p class="text-2xl font-bold text-sky-400">{{ $confirmedCount }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Confirmadas</p>
            </div>
            <div class="p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5 text-left">
                <p class="text-2xl font-bold text-emerald-400">{{ $todayCount }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Hoy</p>
            </div>
            <div class="p-4 rounded-xl border border-rose-500/20 bg-rose-500/5 text-left">
                <p class="text-2xl font-bold text-rose-400">{{ $overdueCount }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Vencidas</p>
            </div>
        </div>
    @endif

    @if(auth()->user()->role === 'seller' && auth()->user()->businessProfile)
        {{-- Upcoming reservations --}}
        <div class="mt-10 max-w-3xl mx-auto text-left space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Próximas Reservas
                </h2>
                <a href="{{ route('reservations.manage') }}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Ver todas →</a>
            </div>

            {{-- Today --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                    Hoy — {{ now()->format('d/m/Y') }}
                    <span class="text-xs text-slate-500 font-normal">({{ $reservationsToday->count() }})</span>
                </h3>
                @if($reservationsToday->isEmpty())
                    <p class="text-sm text-slate-600 py-4 text-center border border-dashed border-slate-800 rounded-xl">Sin reservas para hoy.</p>
                @else
                    <div class="space-y-2">
                        @foreach($reservationsToday as $res)
                            <a href="{{ route('reservations.detail', $res) }}" class="block p-3 rounded-xl border border-slate-800 bg-slate-900/20 hover:bg-slate-900/40 hover:border-slate-700 transition-all">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="text-xs font-bold text-indigo-400 tabular-nums shrink-0">{{ \Carbon\Carbon::parse($res->reservation_time)->format('H:i') }}</span>
                                        <span class="text-sm font-medium text-white truncate">{{ $res->client_name }}</span>
                                        <span class="text-xs text-slate-500 truncate hidden sm:inline">{{ $res->product?->name }}</span>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full
                                        @if($res->status === 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                        @elseif($res->status === 'confirmed') bg-sky-500/10 text-sky-400 border border-sky-500/20
                                        @elseif($res->status === 'completed') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @else bg-rose-500/10 text-rose-400 border border-rose-500/20
                                        @endif">
                                        {{ $res->status === 'pending' ? 'Pendiente' : ($res->status === 'confirmed' ? 'Confirmada' : ($res->status === 'completed' ? 'Completada' : 'Cancelada')) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tomorrow --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-400 inline-block"></span>
                    Mañana — {{ now()->addDay()->format('d/m/Y') }}
                    <span class="text-xs text-slate-500 font-normal">({{ $reservationsTomorrow->count() }})</span>
                </h3>
                @if($reservationsTomorrow->isEmpty())
                    <p class="text-sm text-slate-600 py-4 text-center border border-dashed border-slate-800 rounded-xl">Sin reservas para mañana.</p>
                @else
                    <div class="space-y-2">
                        @foreach($reservationsTomorrow as $res)
                            <a href="{{ route('reservations.detail', $res) }}" class="block p-3 rounded-xl border border-slate-800 bg-slate-900/20 hover:bg-slate-900/40 hover:border-slate-700 transition-all">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="text-xs font-bold text-indigo-400 tabular-nums shrink-0">{{ \Carbon\Carbon::parse($res->reservation_time)->format('H:i') }}</span>
                                        <span class="text-sm font-medium text-white truncate">{{ $res->client_name }}</span>
                                        <span class="text-xs text-slate-500 truncate hidden sm:inline">{{ $res->product?->name }}</span>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full
                                        @if($res->status === 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                        @elseif($res->status === 'confirmed') bg-sky-500/10 text-sky-400 border border-sky-500/20
                                        @elseif($res->status === 'completed') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @else bg-rose-500/10 text-rose-400 border border-rose-500/20
                                        @endif">
                                        {{ $res->status === 'pending' ? 'Pendiente' : ($res->status === 'confirmed' ? 'Confirmada' : ($res->status === 'completed' ? 'Completada' : 'Cancelada')) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('reservations.manage') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600/20 border border-indigo-500/30 hover:bg-indigo-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Gestionar Pedidos
            </a>
        </div>
    @endif

    @if(auth()->user()->role === 'admin')
        {{-- Admin section --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-10 max-w-lg mx-auto">
            <a href="{{ route('admin.dashboard') }}" class="p-6 rounded-xl border border-purple-900 bg-purple-500/10 backdrop-blur text-left hover:bg-purple-500/20 transition-colors">
                <h3 class="font-bold text-purple-300 text-sm">Panel de Administración</h3>
                <p class="text-slate-500 text-xs mt-1">Gestión global de usuarios, productos y estadísticas.</p>
            </a>
            <div class="p-6 rounded-xl border border-slate-900 bg-slate-900/20 backdrop-blur text-left">
                <h3 class="font-bold text-slate-200 text-sm">Tu Perfil</h3>
                <p class="text-slate-500 text-xs mt-1">Configura los datos de tu cuenta de administrador.</p>
            </div>
        </div>
    @endif

</div>
@endif
@endsection
