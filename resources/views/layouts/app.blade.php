<!DOCTYPE html>
<html lang="es" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Plataforma de Reservas y Emprendimientos')</title>
    <meta name="description" content="Regístrate como cliente para realizar reservas o como emprendedor para potenciar tu negocio.">
    <!-- Vite Assets (CSS only) -->
    @vite(['resources/css/app.css'])
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased overflow-x-hidden relative flex flex-col" style="min-height: 100%; display: flex; flex-direction: column; margin: 0;">
    
    <!-- Background Glow Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-900/20 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-900/20 blur-[120px]"></div>
    </div>

    <header class="sticky top-0 z-50 w-full border-b border-slate-800 bg-slate-950/75 backdrop-blur-md">
        <div class="container mx-auto px-4 min-h-16 py-2 flex items-center justify-between gap-3">
            
            <a href="{{ route('register.select') }}" class="flex items-center space-x-2 group">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-450" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 22C2 22 6 18 12 18M12 18C18 18 22 22 22 22M12 18V2M12 6C9.5 8.5 7 11 7 14M12 10C14.5 12.5 17 15 17 18"/>
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-white">
                    ProyectoUTN
                </span>
            </a>

            <div class="flex items-center gap-2 min-w-0 flex-1 justify-end">
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

                @auth                    @if(auth()->user()->role === 'admin')
                        <!-- ADMIN autenticado -->
                        <a href="{{ route('admin.dashboard') }}" 
                        class="px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('admin.*') ? 'bg-purple-600/20 text-purple-400 border border-purple-600/30' : '' }}">
                            Admin Panel
                        </a>
                    @endif



                    @if(auth()->user()->role === 'seller')
                        <span class="text-slate-600 px-1 shrink-0">|</span>

                        <a href="{{ route('dashboard') }}" 
                        class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('dashboard') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('products.index') }}" 
                        class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('products.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Productos
                        </a>

                        <a href="{{ route('categories.index') }}" 
                        class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('categories.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Categorías
                        </a>

                        <a href="{{ route('reservations.create') }}" 
                        class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('reservations.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Reservas
                        </a>

                        <a href="{{ route('availability.edit') }}" 
                        class="shrink-0 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all {{ request()->routeIs('availability.*') ? 'bg-green-600/20 text-green-400 border border-green-600/30' : '' }}">
                            Horarios
                        </a>
                    @endif

            </nav>

                    <!-- User Dropdown Menu -->
                    <div class="relative shrink-0" id="user-menu-container">
                        <!-- User Profile Trigger Button -->
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

                        <div class="absolute right-0 mt-2 w-52 origin-top-right rounded-xl border border-slate-800 bg-slate-950 p-2 shadow-2xl backdrop-blur-md z-[60] user-dropdown-panel" 
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
                                    <a href="{{ route('business_profile.edit') }}" class="nav-dropdown-link {{ request()->routeIs('business_profile.*') ? 'nav-dropdown-link-active' : '' }}">Perfil</a>
                                </div>
                            @endif

                            @if(auth()->user()->role === 'client')
                                <div class="py-1 mb-1 border-b border-slate-900">
                                    <a href="{{ route('reservations.client_history') }}" class="nav-dropdown-link {{ request()->routeIs('reservations.client_history') ? 'nav-dropdown-link-active' : '' }}">Mis reservas</a>
                                </div>
                            @endif

                            <form action="{{ route('logout') }}" method="POST" class="block pt-1">
                                @csrf
                                <button type="submit" 
                                        id="btn-logout"
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
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow flex @yield('main_align', 'items-center justify-center') py-12 px-4 sm:px-6 lg:px-8 relative z-10" style="flex-grow: 1;">
        <div class="w-full @yield('content_width', 'max-w-4xl')">
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

            @yield('content')
        </div>
    </main>

    <footer class="border-t border-slate-900 bg-slate-950 py-6 text-center text-xs text-slate-500 relative z-10">
        <p>&copy; {{ date('Y') }} ProyectoUTN. Todos los derechos reservados.</p>
    </footer>

    @stack('scripts')
</body>
</html>
