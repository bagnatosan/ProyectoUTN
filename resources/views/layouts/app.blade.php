<!DOCTYPE html>
<html lang="es" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cocinet')</title>
    <meta name="description" content="Cocinet — Conectá con emprendimientos locales, reservá y pagá fácil.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css'])
    @stack('styles')
    <style>
        #mobile-menu { display: none; }
    </style>
</head>
<body class="text-slate-100 font-sans antialiased overflow-x-hidden relative flex flex-col" style="background-color:#1e3a2f; min-height:100%; display:flex; flex-direction:column; margin:0;">

    @stack('page_bg')

    <header class="sticky top-0 z-50 w-full border-b border-slate-800 bg-slate-950/75 backdrop-blur-md">
        <div class="container mx-auto px-4 min-h-16 py-2 flex items-center justify-between gap-3">

            <a href="{{ route('register.select') }}" class="flex items-center group" style="text-decoration:none;">
                <img src="{{ asset('cocinet_logo_v2.png') }}" alt="Cocinet" style="height: 70px;width:auto;mix-blend-mode:screen;">
            </a>

            {{-- Desktop nav --}}
            <div id="desktop-nav" class="flex items-center gap-2 min-w-0 flex-1 justify-end">
                <nav class="flex items-center space-x-1 text-sm font-medium overflow-x-auto min-w-0">

                    <a href="{{ route('map.index') }}"
                       class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('map.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                        Mapa
                    </a>

                    @auth
                        @if(auth()->user()->role === 'client')
                            <a href="{{ route('dashboard') }}"
                               class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('dashboard') || request()->routeIs('catalog.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                                Catálogos
                            </a>
                            <a href="{{ route('reservations.index') }}"
                               class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('reservations.index') || request()->routeIs('reservations.edit') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                                Mis Reservas
                            </a>
                        @endif
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                        class="px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('login') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Ingresar
                        </a>
                        <a href="{{ route('register.hub') }}"
                        class="px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('register.*') && !request()->routeIs('register.select') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Registrarse
                        </a>
                    @endguest

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('admin.*') ? 'bg-purple-600/20 text-purple-400 border border-purple-600/30' : '' }}">
                                Admin Panel
                            </a>
                        @endif

                        @if(auth()->user()->role === 'seller')
                            @php
                                $pendingReservationsCount = auth()->user()->businessProfile
                                    ? \App\Models\Reservation::forBusiness(auth()->user()->businessProfile->id)->pending()->count()
                                    : 0;
                            @endphp

                            <span class="text-slate-600 px-1 shrink-0">|</span>

                            <a href="{{ route('dashboard') }}"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('dashboard') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                                Dashboard
                            </a>

                            <a href="{{ route('reservations.manage') }}"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all flex items-center gap-1.5 {{ request()->routeIs('reservations.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                                <span>Pedidos</span>
                                @if($pendingReservationsCount > 0)
                                    <span class="px-1.5 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-bold shadow-sm shadow-rose-600/20">
                                        {{ $pendingReservationsCount }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('availability.index') }}"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('availability.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                                Horarios
                            </a>
                        @endif
                    @endauth

                </nav>

                @auth
                    @if(auth()->user()->role === 'seller')
                        <div class="relative shrink-0" id="catalogo-menu-container">
                            <button type="button"
                                    class="px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all flex items-center gap-1 {{ request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('ingredients.*') || request()->routeIs('recipes.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}"
                                    id="catalogo-trigger-btn">
                                <span>Catálogo</span>
                                <svg class="w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" id="catalogo-arrow">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-44 origin-top-left rounded-xl border border-slate-800 bg-slate-950 p-1.5 shadow-2xl backdrop-blur-md transition-all duration-200 transform opacity-0 scale-95 pointer-events-none z-[60]"
                                 id="catalogo-dropdown-menu">
                                <a href="{{ route('categories.index') }}"
                                   class="block text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'text-green-400' : '' }}">
                                    Categorías
                                </a>
                                <a href="{{ route('products.index') }}"
                                   class="block text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'text-green-400' : '' }}">
                                    Productos
                                </a>
                                <a href="{{ route('ingredients.index') }}"
                                   class="block text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('ingredients.*') ? 'text-green-400' : '' }}">
                                    Ingredientes
                                </a>
                            </div>
                        </div>
                    @endif
                @endauth

                @auth
                    <div class="relative shrink-0" id="notif-bell-container">
                        <a href="{{ route('notifications.index') }}"
                           class="flex items-center justify-center w-9 h-9 rounded-lg hover:bg-white/10 transition-all relative" style="color:rgba(255,255,255,0.75);"
                           aria-label="Notificaciones">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 flex items-center justify-center bg-rose-500 text-white text-[10px] font-bold rounded-full shadow-lg shadow-rose-500/30" id="notif-badge">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <div class="relative shrink-0" id="user-menu-container">
                        <button type="button"
                                class="flex items-center space-x-2 text-sm text-slate-300 bg-slate-900/60 hover:bg-slate-900/80 border border-slate-800 rounded-lg px-3 py-1.5 cursor-pointer transition-all duration-200 focus:outline-none"
                                id="nav-user-display-btn">
                            <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="font-medium text-xs sm:text-sm">
                                @if(auth()->user()->role === 'seller' && auth()->user()->businessProfile)
                                    {{ auth()->user()->businessProfile->business_name }}
                                @else
                                    {{ auth()->user()->name }}
                                @endif
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" id="nav-user-arrow">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div class="absolute right-0 mt-2 w-52 origin-top-right rounded-xl border border-slate-800 bg-slate-950 p-2 shadow-2xl backdrop-blur-md transition-all duration-200 transform opacity-0 scale-95 pointer-events-none z-[60] user-dropdown-panel"
                             id="user-dropdown-menu">
                            <div class="px-3 py-2 border-b border-slate-900 mb-1">
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Rol de acceso</p>
                                <p class="text-xs font-medium capitalize {{ auth()->user()->role === 'admin' ? 'text-purple-400' : 'text-indigo-400' }}">
                                    @if(auth()->user()->role === 'admin') Administrador
                                    @elseif(auth()->user()->role === 'seller') Emprendedor
                                    @else Cliente
                                    @endif
                                </p>
                            </div>
                            @if(auth()->user()->role === 'seller')
                                <div class="py-1 mb-1 border-b border-slate-900">
                                    <a href="{{ route('business_profile.edit') }}" class="nav-dropdown-link {{ request()->routeIs('business_profile.*') ? 'nav-dropdown-link-active' : '' }}">Configurar Perfil</a>
                                    @if(auth()->user()->businessProfile)
                                        <a href="{{ route('catalog.show', auth()->user()->businessProfile->id) }}" class="nav-dropdown-link" target="_blank">Ver mi catálogo público</a>
                                    @endif
                                </div>
                            @endif
                            @if(auth()->user()->role === 'client')
                                <div class="py-1 mb-1 border-b border-slate-900">
                                    <a href="{{ route('client_profile.edit') }}" class="nav-dropdown-link {{ request()->routeIs('client_profile.*') ? 'nav-dropdown-link-active' : '' }}">Mi Perfil</a>
                                    <a href="{{ route('reservations.index') }}" class="nav-dropdown-link {{ request()->routeIs('reservations.index') ? 'nav-dropdown-link-active' : '' }}">Mis reservas</a>
                                </div>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="block pt-1">
                                @csrf
                                <button type="submit" id="btn-logout"
                                        class="nav-dropdown-logout w-full flex items-center space-x-2 text-left text-xs font-semibold p-2.5 rounded-lg transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    <span>Salir</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            {{-- Mobile right: bell + hamburger --}}
            <div id="mobile-nav-toggle" class="items-center gap-2">
                @auth
                    <a href="{{ route('notifications.index') }}"
                       class="flex items-center justify-center w-9 h-9 rounded-lg hover:bg-white/10 transition-all relative" style="color:rgba(255,255,255,0.75);">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        @php $unreadCount = $unreadCount ?? auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 flex items-center justify-center bg-rose-500 text-white text-[10px] font-bold rounded-full">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                @endauth

                <button type="button" id="mobile-menu-btn"
                        style="display:flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.5rem;background:transparent;border:none;cursor:pointer;color:#ffffff;"
                        aria-label="Menú">
                    <svg id="hamburger-icon" style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg id="close-icon" style="width:1.25rem;height:1.25rem;display:none;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu panel --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 bg-slate-950/95 backdrop-blur-md">
            <div class="px-4 py-4 space-y-1">

                <a href="{{ route('map.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('map.*') ? 'bg-green-600/20 text-green-400' : '' }}">
                    Mapa
                </a>

                @auth
                    @if(auth()->user()->role === 'client')
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('dashboard') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Catálogos
                        </a>
                        <a href="{{ route('reservations.index') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('reservations.index') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Mis Reservas
                        </a>
                        <div class="border-t border-slate-800 my-2"></div>
                        <a href="{{ route('client_profile.edit') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                            Mi Perfil
                        </a>
                    @endif

                    @if(auth()->user()->role === 'seller')
                        @php
                            $pendingReservationsCount = $pendingReservationsCount ?? (auth()->user()->businessProfile
                                ? \App\Models\Reservation::forBusiness(auth()->user()->businessProfile->id)->pending()->count()
                                : 0);
                        @endphp
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('dashboard') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('reservations.manage') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('reservations.*') ? 'bg-green-600/20 text-green-400' : '' }}">
                            <span>Pedidos</span>
                            @if($pendingReservationsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-bold">{{ $pendingReservationsCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('availability.index') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('availability.*') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Horarios
                        </a>
                        <div class="border-t border-slate-800 my-2"></div>
                        <p class="px-3 pt-1 pb-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Catálogo</p>
                        <a href="{{ route('categories.index') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('categories.*') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Categorías
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('products.*') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Productos
                        </a>
                        <a href="{{ route('ingredients.index') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('ingredients.*') ? 'bg-green-600/20 text-green-400' : '' }}">
                            Ingredientes
                        </a>
                        <div class="border-t border-slate-800 my-2"></div>
                        <a href="{{ route('business_profile.edit') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                            Configurar Perfil
                        </a>
                        @if(auth()->user()->businessProfile)
                            <a href="{{ route('catalog.show', auth()->user()->businessProfile->id) }}"
                               class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all"
                               target="_blank">
                                Ver mi catálogo público
                            </a>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('admin.*') ? 'bg-purple-600/20 text-purple-400' : '' }}">
                            Admin Panel
                        </a>
                    @endif

                    <div class="border-t border-slate-800 my-2"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm text-rose-400 hover:text-rose-300 hover:bg-slate-800 transition-all text-left">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Salir
                        </button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                        Ingresar
                    </a>
                    <a href="{{ route('register.hub') }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                        Registrarse
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <main class="flex-grow flex @yield('main_align', 'items-center justify-center') py-12 px-4 sm:px-6 lg:px-8 relative z-10" style="flex-grow: 1; padding-bottom: 7rem;">
        <div class="w-full main-content-wrapper @yield('content_width', 'max-w-4xl')">

            <div class="w-full">
                @if (session('success'))
                    <div class="mb-8 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300 flex items-start space-x-3 shadow-lg shadow-emerald-500/5 animate-fade-in" id="alert-success">
                        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-emerald-200">¡Acción exitosa!</p>
                            <p class="text-sm mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-8 p-4 rounded-xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-300 flex items-start space-x-3 shadow-lg animate-fade-in" id="alert-info">
                        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <div>
                            <p class="font-medium text-indigo-200">Sesión activa</p>
                            <p class="text-sm mt-0.5">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-8 p-4 rounded-xl border border-rose-500/20 bg-rose-500/10 text-rose-300 shadow-lg shadow-rose-500/5 animate-fade-in" id="alert-error">
                        <div class="flex items-start space-x-3 mb-2">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="font-medium text-rose-200">Por favor, corrige los siguientes errores:</p>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1 pl-1 text-rose-300/90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @yield('content')
        </div>
    </main>
        

    <footer style="background-color:#1e3a2f;border-top:1px solid rgba(255,255,255,0.08);padding:2rem 1.5rem;text-align:center;position:relative;z-index:10;">
        <div style="max-width:480px;margin:0 auto;">
            <p style="font-size:1.1rem;font-weight:700;color:#ffffff;letter-spacing:0.02em;">Cocinet</p>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.45);margin-top:0.375rem;">Conectamos emprendimientos gastronómicos locales con quienes quieren reservar.</p>
            <p style="font-size:0.7rem;color:rgba(255,255,255,0.3);margin-top:1rem;">&copy; {{ date('Y') }} Cocinet. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

    const allDropdowns = [];

    function setupDropdownIsOpen(triggerId, menuId, arrowId) {
        const trigger = document.getElementById(triggerId);
        const menu = document.getElementById(menuId);
        const arrow = arrowId ? document.getElementById(arrowId) : null;

        if (!trigger || !menu) return;

        function close() {
            menu.classList.remove('is-open');
            if (arrow) arrow.classList.remove('is-open');
        }

        function isOpen() {
            return menu.classList.contains('is-open');
        }

        function open() {
            menu.classList.add('is-open');
            if (arrow) arrow.classList.add('is-open');
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const wasOpen = isOpen();
            allDropdowns.forEach(d => d.close());
            if (!wasOpen) open();
        });

        allDropdowns.push({ close });
    }

    function setupDropdownTailwind(triggerId, menuId, arrowId) {
            const trigger = document.getElementById(triggerId);
            const menu = document.getElementById(menuId);
            const arrow = arrowId ? document.getElementById(arrowId) : null;

            if (!trigger || !menu) return;

            function close() {
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                if (arrow) arrow.classList.remove('rotate-180');
            }

            function isOpen() {
                return !menu.classList.contains('pointer-events-none');
            }

            function open() {
                menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                if (arrow) arrow.classList.add('rotate-180');
            }

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                const wasOpen = isOpen();
                allDropdowns.forEach(d => d.close());
                if (!wasOpen) open();
            });

            allDropdowns.push({ close });
        }

        setupDropdownTailwind('catalogo-trigger-btn', 'catalogo-dropdown-menu', 'catalogo-arrow');
        setupDropdownIsOpen('nav-user-display-btn', 'user-dropdown-menu', 'nav-user-arrow');

        // Responsive nav
        const desktopNav = document.getElementById('desktop-nav');
        const mobileToggle = document.getElementById('mobile-nav-toggle');

        function applyResponsive() {
            if (window.innerWidth < 768) {
                if (desktopNav) desktopNav.style.display = 'none';
                if (mobileToggle) mobileToggle.style.display = 'flex';
            } else {
                if (desktopNav) desktopNav.style.display = '';
                if (mobileToggle) mobileToggle.style.display = 'none';
                if (mobileMenu) mobileMenu.style.display = 'none';
            }
        }
        applyResponsive();
        window.addEventListener('resize', applyResponsive);
        window.addEventListener('load', applyResponsive);

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = mobileMenu.style.display === 'block';
                if (isOpen) {
                    mobileMenu.style.display = 'none';
                    hamburgerIcon.style.display = '';
                    closeIcon.style.display = 'none';
                } else {
                    mobileMenu.style.display = 'block';
                    hamburgerIcon.style.display = 'none';
                    closeIcon.style.display = '';
                    allDropdowns.forEach(d => d.close());
                }
            });
        }

        document.addEventListener('click', function () {
            allDropdowns.forEach(d => d.close());
        });

    });
    </script>
    @stack('scripts')
</body>
</html>
