@extends('layouts.app')

@section('title', 'Cocinet | Reservas y Emprendimientos Locales')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@section('content')
<div class="relative space-y-24 md:space-y-32" style="padding-bottom:4rem;">
    <div class="home-blob-green" aria-hidden="true"></div>
    <div class="home-blob-peach" aria-hidden="true"></div>

    {{-- Hero --}}
    <section class="relative z-10 text-center pt-4 md:pt-8 rounded-3xl overflow-hidden"
             style="background-image: url('{{ asset('images/banner-home.png') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 rounded-3xl" style="background: linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.45) 60%, rgba(0,0,0,0.6) 100%);"></div>
        <div class="relative z-10 pb-10 pt-10 px-4 md:px-10">
        <span class="inline-block px-3 py-1 text-xs font-semibold tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20 uppercase animate-fade-in-up">
            Plataforma de reservas locales
        </span>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mt-5 max-w-3xl mx-auto leading-tight animate-fade-in-up animate-delay-1"
            style="background: linear-gradient(135deg, #ffffff 0%, #d4f5e2 40%, #52b788 70%, #f5a623 100%);
                   -webkit-background-clip: text;
                   -webkit-text-fill-color: transparent;
                   background-clip: text;
                   letter-spacing:-0.04em;
                   filter: drop-shadow(0 4px 24px rgba(0,0,0,0.4));">
            Cocinet
        </h1>

        <p class="text-white/80 mt-4 max-w-2xl mx-auto text-sm md:text-lg animate-fade-in-up animate-delay-2" style="text-shadow: 0 1px 4px rgba(0,0,0,0.5);">
            Conectamos emprendimientos gastronómicos locales con quienes quieren reservar. Explorá catálogos, elegí horarios y reservá al instante.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-8 animate-fade-in-up animate-delay-3">
            <a href="{{ route('register.client') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-cyan-500 text-white shadow-lg shadow-indigo-600/20 transition-all duration-300 transform hover:scale-[1.02]">
                Soy cliente
            </a>
            <a href="{{ route('map.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold bg-slate-900/40 border border-slate-800 transition-all duration-300 transform hover:scale-[1.02]">
                Ver mapa
            </a>
            <a href="#para-emprendedores"
               class="home-btn-outline-peach inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-[1.02]">
                Soy emprendedor
            </a>
        </div>

        <div class="mt-14 flex justify-center animate-fade-in animate-delay-4">
            <div class="animate-float rounded-2xl border border-slate-800 bg-slate-900/40 p-6 max-w-sm shadow-lg">
                <div class="flex items-center gap-3 text-left">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Reserva confirmada</p>
                        <p class="text-xs text-slate-400 mt-0.5">Turno reservado en 2 clics, sin mensajes</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Trust bar --}}
    <section class="relative z-10 home-scroll-reveal">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['Reservas online 24/7', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['Emprendimientos locales', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['Confirmación inmediata', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Panel para vendedores', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ] as $item)
                <div style="border-radius:0.875rem;background:linear-gradient(135deg,#1a4a33 0%,#0f2e1e 100%);border:1px solid rgba(255,255,255,0.1);padding:1rem;display:flex;align-items:flex-start;gap:0.75rem;box-shadow:0 0 20px rgba(45,106,79,0.25),0 4px 12px rgba(0,0,0,0.15);">
                    <svg style="width:1.25rem;height:1.25rem;color:#f5a623;flex-shrink:0;margin-top:0.125rem;filter:drop-shadow(0 0 6px rgba(245,166,35,0.6));" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item[1] }}" />
                    </svg>
                    <span style="font-size:0.875rem;font-weight:600;color:#ffffff;text-shadow:0 0 8px rgba(255,255,255,0.15);">{{ $item[0] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Galería de emprendimientos --}}
    <section class="relative z-10 home-scroll-reveal">
        <div class="text-center mb-8">
            <h2 class="text-xl md:text-2xl font-extrabold">Emprendimientos que ya están en Cocinet</h2>
            <p class="text-slate-400 mt-2 text-sm">Pastelería, comida casera, catering y mucho más.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl overflow-hidden aspect-[4/3] home-scroll-reveal-delay-1">
                <img src="{{ asset('images/galeria-pasteleria.jpg') }}"
                     alt="Pastelería artesanal"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="rounded-2xl overflow-hidden aspect-[4/3] home-scroll-reveal-delay-2">
                <img src="{{ asset('images/galeria-comida-casera.jpg') }}"
                     alt="Comida casera"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="rounded-2xl overflow-hidden aspect-[4/3] home-scroll-reveal-delay-3">
                <img src="{{ asset('images/galeria-catering.jpg') }}"
                     alt="Catering y eventos"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
        </div>
    </section>

    {{-- Para clientes --}}
    <section id="clientes" class="relative z-10 scroll-mt-24">
        <div class="rounded-3xl p-8 md:p-12 overflow-hidden relative" style="background:radial-gradient(ellipse at 50% 0%, #2d6a4f 0%, #1a4a33 45%, #0f2e1e 100%);border:1px solid rgba(255,255,255,0.08);">
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image:radial-gradient(#ffffff 1px,transparent 1px);background-size:16px 16px;"></div>

            <div class="relative z-10 text-center mb-10 home-scroll-reveal">
                <span style="display:inline-block;padding:0.2rem 0.75rem;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.7);background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:9999px;">
                    Para clientes
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold mt-4" style="color:#ffffff !important;">Tu próxima reserva, a un clic</h2>
                <p style="color:rgba(255,255,255,0.65);margin-top:0.5rem;max-width:36rem;margin-left:auto;margin-right:auto;font-size:0.95rem;">
                    Descubrí emprendimientos de tu zona, mirá disponibilidad y reservá sin llamadas ni mensajes.
                </p>
            </div>

            <div class="relative z-10 grid md:grid-cols-3 gap-6 mb-10">
                @foreach ([
                    ['1', 'Explorá', 'Navegá catálogos de negocios locales y encontrá lo que necesitás.', 'home-scroll-reveal-delay-1'],
                    ['2', 'Elegí', 'Seleccioná producto o turno según la disponibilidad publicada.', 'home-scroll-reveal-delay-2'],
                    ['3', 'Reservá', 'Confirmá tu reserva y recibí la confirmación al instante.', 'home-scroll-reveal-delay-3'],
                ] as $step)
                    <div class="home-step-card home-scroll-reveal {{ $step[3] }} rounded-2xl p-6 transition-all duration-300" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:9999px;background:rgba(245,166,35,0.2);border:1px solid rgba(245,166,35,0.4);font-size:0.875rem;font-weight:700;color:#f5a623;">
                            {{ $step[0] }}
                        </span>
                        <h3 class="text-lg font-bold mt-4" style="color:#ffffff !important;">{{ $step[1] }}</h3>
                        <p style="font-size:0.875rem;color:rgba(255,255,255,0.6);margin-top:0.5rem;">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Foto 50/50 para clientes --}}
            <div class="relative z-10 grid md:grid-cols-2 gap-6 mb-10 items-center home-scroll-reveal">
                <div class="rounded-2xl overflow-hidden shadow-xl aspect-[4/3]">
                <img src="{{ asset('images/clientes-celular.jpg') }}"
                     alt="Persona eligiendo comida desde su celular"
                         class="w-full h-full object-cover">
                </div>
                <div style="color:rgba(255,255,255,0.85);">
                    <h3 class="text-xl font-bold mb-3" style="color:#ffffff !important;">Tu comida favorita, sin llamadas</h3>
                    <p style="font-size:0.95rem;color:rgba(255,255,255,0.65);line-height:1.7;">
                        Entrás a Cocinet, buscás el emprendimiento que más te gusta y reservás tu turno o pedido en segundos. Sin mensajes por WhatsApp, sin esperar respuesta.
                    </p>
                    <ul class="mt-4 space-y-2" style="font-size:0.875rem;color:rgba(255,255,255,0.6);">
                        <li style="display:flex;align-items:center;gap:0.5rem;"><span style="color:#f5a623;">✓</span> Catálogos con fotos y precios</li>
                        <li style="display:flex;align-items:center;gap:0.5rem;"><span style="color:#f5a623;">✓</span> Horarios disponibles en tiempo real</li>
                        <li style="display:flex;align-items:center;gap:0.5rem;"><span style="color:#f5a623;">✓</span> Confirmación inmediata de tu reserva</li>
                    </ul>
                </div>
            </div>

            <div class="relative z-10 text-center home-scroll-reveal pt-8" style="border-top:1px solid rgba(255,255,255,0.12);">
                <p style="font-size:0.875rem;color:rgba(255,255,255,0.55);margin-bottom:1.25rem;">Creá tu cuenta gratis y empezá a explorar emprendimientos locales.</p>
                <div class="home-cta-actions">
                    <a href="{{ route('register.client') }}"
                       style="display:inline-flex;align-items:center;justify-content:center;padding:0.75rem 2rem;border-radius:0.75rem;font-size:0.875rem;font-weight:600;color:#1e3a2f;background:#f5a623;border:none;text-decoration:none;transition:all 0.2s;"
                       onmouseenter="this.style.background='#e8961a';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(245,166,35,0.4)'"
                       onmouseleave="this.style.background='#f5a623';this.style.transform='';this.style.boxShadow=''">
                        Registrarme
                    </a>
                    <a href="{{ route('login') }}"
                       style="display:inline-flex;align-items:center;justify-content:center;padding:0.75rem 1.5rem;border-radius:0.75rem;font-size:0.875rem;font-weight:600;color:#ffffff;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.25);text-decoration:none;transition:all 0.2s;"
                       onmouseenter="this.style.background='rgba(255,255,255,0.2)';this.style.transform='translateY(-2px)'"
                       onmouseleave="this.style.background='rgba(255,255,255,0.1)';this.style.transform=''">
                        Iniciar sesión
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Para emprendedores --}}
    <section id="para-emprendedores" class="relative z-10 scroll-mt-24">
        <div class="rounded-3xl p-8 md:p-12 overflow-hidden relative" style="background:radial-gradient(ellipse at 50% 0%, #2d6a4f 0%, #1a4a33 45%, #0f2e1e 100%);border:1px solid rgba(255,255,255,0.08);">
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image:radial-gradient(#ffffff 1px,transparent 1px);background-size:16px 16px;"></div>

            <div class="relative z-10 text-center mb-10 home-scroll-reveal">
                <span style="display:inline-block;padding:0.2rem 0.75rem;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.7);background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:9999px;">
                    Para emprendedores
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold mt-4" style="color:#ffffff !important;">Potenciá tu negocio local</h2>
                <p style="color:rgba(255,255,255,0.65);margin-top:0.5rem;max-width:42rem;margin-left:auto;margin-right:auto;font-size:0.95rem;">
                    Publicá tu catálogo, gestioná horarios y recibí reservas directas. Tus clientes reservan solos y vos gestionás desde un panel.
                </p>
            </div>

            {{-- Foto de emprendedor cocinando --}}
            <div class="relative z-10 rounded-2xl overflow-hidden mb-10 home-scroll-reveal" style="height:280px;">
                <img src="{{ asset('images/emprendedor-cocina.jpg') }}"
                     alt="Chef preparando pedidos"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0" style="background:linear-gradient(to right, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);"></div>
                <div class="absolute inset-0 flex items-center px-8 md:px-12">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#f5a623;">Para emprendedores</p>
                        <p class="text-2xl md:text-3xl font-extrabold leading-tight max-w-xs" style="color:#ffffff !important;">Tu cocina, tu negocio, tu panel.</p>
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
                @foreach ([
                    ['Perfil de negocio', 'Nombre comercial, descripción y datos de contacto en un solo lugar.', 'home-scroll-reveal-delay-1'],
                    ['Catálogo de productos', 'Publicá productos, precios y categorías para tus clientes.', 'home-scroll-reveal-delay-2'],
                    ['Horarios y disponibilidad', 'Definí cuándo podés atender reservas y turnos.', 'home-scroll-reveal-delay-3'],
                    ['Gestión de reservas', 'Recibí y administrá pedidos desde tu panel.', 'home-scroll-reveal-delay-1'],
                    ['Recetas e insumos', 'Organizá ingredientes y costos de lo que ofrecés.', 'home-scroll-reveal-delay-2'],
                    ['Métricas del negocio', 'Mirá cómo rinde tu emprendimiento en el dashboard.', 'home-scroll-reveal-delay-3'],
                ] as $feature)
                    <div class="home-seller-card home-scroll-reveal {{ $feature[2] }} rounded-xl p-5 transition-all duration-300" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);">
                        <div style="width:2.25rem;height:2.25rem;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem;background:rgba(245,166,35,0.2);border:1px solid rgba(245,166,35,0.35);">
                            <svg style="width:1rem;height:1rem;color:#f5a623;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold" style="color:#ffffff !important;">{{ $feature[0] }}</h3>
                        <p style="font-size:0.8rem;color:rgba(255,255,255,0.6);margin-top:0.375rem;">{{ $feature[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-10" style="margin-top:2rem;">
                @foreach ([
                    ['1', 'Registrate', 'Creá tu cuenta como emprendedor en pocos minutos.', 'home-scroll-reveal-delay-1'],
                    ['2', 'Completá tu perfil', 'Subí tu catálogo, categorías y horarios disponibles.', 'home-scroll-reveal-delay-2'],
                    ['3', 'Recibí reservas', 'Compartí tu negocio y empezá a recibir pedidos.', 'home-scroll-reveal-delay-3'],
                ] as $step)
                    <div class="home-scroll-reveal {{ $step[3] }} text-center md:text-left">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:9999px;background:rgba(245,166,35,0.2);border:1px solid rgba(245,166,35,0.4);font-size:0.875rem;font-weight:700;color:#f5a623;">
                            {{ $step[0] }}
                        </span>
                        <h3 class="text-base font-bold mt-3" style="color:#ffffff !important;">{{ $step[1] }}</h3>
                        <p style="font-size:0.875rem;color:rgba(255,255,255,0.6);margin-top:0.25rem;">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center home-scroll-reveal pt-6" style="border-top:1px solid rgba(255,255,255,0.12);">
                <p style="font-size:0.875rem;color:rgba(255,255,255,0.55);margin-bottom:1.25rem;">¿Listo para publicar tu negocio?</p>
                <div class="home-cta-actions">
                    <a href="{{ route('register.seller') }}"
                       id="btn-select-seller"
                       style="display:inline-flex;align-items:center;justify-content:center;padding:0.75rem 2rem;border-radius:0.75rem;font-size:0.875rem;font-weight:600;color:#1e3a2f;background:#f5a623;border:none;text-decoration:none;transition:all 0.2s;"
                       onmouseenter="this.style.background='#e8961a';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(245,166,35,0.4)'"
                       onmouseleave="this.style.background='#f5a623';this.style.transform='';this.style.boxShadow=''">
                        Registrar mi emprendimiento
                    </a>
                </div>
                <p style="font-size:0.875rem;color:rgba(255,255,255,0.55);margin-top:1rem;">
                    ¿Tenés dudas antes de registrarte?
                    <a href="#contacto-emprendedores" style="font-weight:600;color:#f5a623;text-decoration:none;">Escribinos</a>
                </p>
            </div>
        </div>
    </section>

    {{-- Casos de uso --}}
    <section class="relative z-10 home-scroll-reveal">
        <h2 class="text-xl md:text-2xl font-extrabold text-center mb-8">Hecho para la vida real</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-2">Cliente</p>
                <p class="text-sm text-slate-300 italic">"Lucas reservó un turno para retiro en 2 clics, sin escribirle a nadie por WhatsApp."</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6">
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: #d88448;">Emprendedor</p>
                <p class="text-sm text-slate-300 italic">"María publicó su catálogo de pastelería y recibe reservas para los fines de semana desde un solo panel."</p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="relative z-10 home-scroll-reveal pb-8">
        <h2 class="text-xl md:text-2xl font-extrabold text-center mb-8">Preguntas frecuentes</h2>
        <div class="grid md:grid-cols-2 gap-4 max-w-4xl mx-auto">
            <details class="home-faq rounded-xl border border-slate-800 bg-slate-900/40 p-4 group">
                <summary class="flex items-center justify-between text-sm font-semibold">
                    ¿Es gratis registrarse como cliente?
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>
                <p class="text-sm text-slate-400 mt-3">Sí, crear una cuenta de cliente es gratuito y te permite explorar catálogos y realizar reservas.</p>
            </details>
            <details class="home-faq rounded-xl border border-slate-800 bg-slate-900/40 p-4 group">
                <summary class="flex items-center justify-between text-sm font-semibold">
                    ¿Cómo reservo un producto o turno?
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>
                <p class="text-sm text-slate-400 mt-3">Registrate, elegí un emprendimiento, seleccioná el producto o horario disponible y confirmá tu reserva.</p>
            </details>
            <details class="home-faq rounded-xl border border-slate-800 bg-slate-900/40 p-4 group">
                <summary class="flex items-center justify-between text-sm font-semibold">
                    ¿Qué necesito para registrar mi emprendimiento?
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>
                <p class="text-sm text-slate-400 mt-3">Solo necesitás un email, los datos de tu negocio y tus productos. Podés completar el resto después desde el panel.</p>
            </details>
            <details class="home-faq rounded-xl border border-slate-800 bg-slate-900/40 p-4 group">
                <summary class="flex items-center justify-between text-sm font-semibold">
                    ¿Puedo cambiar mis horarios después?
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>
                <p class="text-sm text-slate-400 mt-3">Sí, desde tu panel podés actualizar disponibilidad, productos y datos de contacto cuando quieras.</p>
            </details>
        </div>
    </section>


    {{-- Footer CTA --}}
    <section class="relative z-10 home-scroll-reveal rounded-3xl overflow-hidden" style="min-height:320px;">
        <img src="{{ asset('images/cta-mesa.jpg') }}"
             alt="Mesa lista para una cena"
             class="absolute inset-0 w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:linear-gradient(135deg, rgba(15,46,30,0.88) 0%, rgba(30,58,47,0.75) 100%);"></div>
        <div class="relative z-10 text-center py-16 px-6">
        <h2 class="text-2xl md:text-3xl font-bold mb-3" style="color:#ffffff !important;">¿Listo para empezar?</h2>
        <p class="text-white/70 text-sm mb-8 max-w-md mx-auto">Unite a los emprendimientos y clientes que ya usan Cocinet para reservar sin complicaciones.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('register.client') }}"
               style="display:inline-flex;align-items:center;justify-content:center;padding:0.75rem 1.75rem;border-radius:0.75rem;font-size:0.875rem;font-weight:600;color:#1e3a2f;background:#ffffff;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 14px rgba(0,0,0,0.25);"
               onmouseenter="this.style.background='#f0f0f0';this.style.transform='translateY(-2px)'"
               onmouseleave="this.style.background='#ffffff';this.style.transform='translateY(0)'">
                Registrarme como cliente
            </a>
            <a href="{{ route('register.seller') }}"
               style="display:inline-flex;align-items:center;justify-content:center;padding:0.75rem 1.75rem;border-radius:0.75rem;font-size:0.875rem;font-weight:600;color:#1e3a2f;background:#f5a623;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 14px rgba(245,166,35,0.4);"
               onmouseenter="this.style.background='#e8961a';this.style.transform='translateY(-2px)'"
               onmouseleave="this.style.background='#f5a623';this.style.transform='translateY(0)'">
                Registrar emprendimiento
            </a>
        </div>
        </div>
    </section>

    {{-- Contacto emprendedores (final de página) --}}
    <section id="contacto-emprendedores" class="relative z-10 scroll-mt-24 home-scroll-reveal pb-4">
        <div class="rounded-3xl overflow-hidden relative" style="padding:4rem 3rem 5rem;background:radial-gradient(ellipse at 50% 0%, #2d6a4f 0%, #1a4a33 45%, #0f2e1e 100%);border:1px solid rgba(255,255,255,0.08);">
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image:radial-gradient(#ffffff 1px,transparent 1px);background-size:16px 16px;"></div>

            <div class="relative z-10 text-center mb-8">
                <span style="display:inline-block;padding:0.2rem 0.75rem;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.7);background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:9999px;">
                    Contacto emprendedores
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold mt-4" style="color:#ffffff !important;">¿Querés sumarte o tenés consultas?</h2>
                <p style="color:rgba(255,255,255,0.65);margin-top:0.5rem;max-width:36rem;margin-left:auto;margin-right:auto;font-size:0.95rem;">
                    Escribinos y te ayudamos a publicar tu emprendimiento en la plataforma.
                </p>
            </div>

            @if (session('contact_success'))
                <div class="max-w-xl mx-auto mb-6 p-4 rounded-xl text-sm" style="background:rgba(45,140,78,0.2);border:1px solid rgba(45,140,78,0.4);color:#86efac;">
                    {{ session('contact_success') }}
                </div>
            @endif

            <div class="max-w-xl mx-auto">
                <form action="{{ route('entrepreneur.contact.store') }}" method="POST" class="space-y-5 @if($errors->has('business_name') || $errors->has('contact_name') || $errors->has('email') || $errors->has('message')) animate-shake @endif">
                    @csrf

                    @if ($errors->has('business_name') || $errors->has('contact_name') || $errors->has('email') || $errors->has('phone') || $errors->has('message'))
                        <div class="p-3 rounded-xl text-sm" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach (['business_name', 'contact_name', 'email', 'phone', 'message'] as $field)
                                    @error($field)
                                        <li>{{ $message }}</li>
                                    @enderror
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="contact_business_name" style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:rgba(255,255,255,0.6);">Nombre del negocio</label>
                            <input type="text" name="business_name" id="contact_business_name" value="{{ old('business_name') }}" required
                                   style="display:block;width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:0.75rem;font-size:0.875rem;color:#ffffff;outline:none;box-sizing:border-box;">
                        </div>
                        <div class="space-y-1.5">
                            <label for="contact_name" style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:rgba(255,255,255,0.6);">Tu nombre</label>
                            <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" required
                                   style="display:block;width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:0.75rem;font-size:0.875rem;color:#ffffff;outline:none;box-sizing:border-box;">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="contact_email" style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:rgba(255,255,255,0.6);">Email</label>
                            <input type="email" name="email" id="contact_email" value="{{ old('email') }}" required
                                   style="display:block;width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:0.75rem;font-size:0.875rem;color:#ffffff;outline:none;box-sizing:border-box;">
                        </div>
                        <div class="space-y-1.5">
                            <label for="contact_phone" style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:rgba(255,255,255,0.6);">Teléfono (opcional)</label>
                            <input type="text" name="phone" id="contact_phone" value="{{ old('phone') }}"
                                   style="display:block;width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:0.75rem;font-size:0.875rem;color:#ffffff;outline:none;box-sizing:border-box;">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="contact_message" style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:rgba(255,255,255,0.6);">Mensaje</label>
                        <textarea name="message" id="contact_message" rows="4" required placeholder="Contanos sobre tu emprendimiento o tu consulta..."
                                  style="display:block;width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:0.75rem;font-size:0.875rem;color:#ffffff;outline:none;resize:none;box-sizing:border-box;">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn-orange-home"
                            onmouseenter="this.style.background='#e8961a';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(245,166,35,0.4)'"
                            onmouseleave="this.style.background='#f5a623';this.style.transform='translateY(0)';this.style.boxShadow='none'">
                        Enviar consulta
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>

@if(session('contact_success') || ($errors->has('business_name') || $errors->has('message')))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var target = document.getElementById('contacto-emprendedores');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function revealInViewport() {
            document.querySelectorAll('.home-scroll-reveal:not(.is-visible)').forEach(function (el) {
                var rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    el.classList.add('is-visible');
                }
            });
        }

        function revealWithin(target) {
            if (!target) return;
            target.classList.add('is-visible');
            target.querySelectorAll('.home-scroll-reveal').forEach(function (el) {
                el.classList.add('is-visible');
            });
        }

        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var targetId = this.getAttribute('href');
                if (targetId.length <= 1) return;
                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    revealWithin(target);
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        revealInViewport();

        var revealElements = document.querySelectorAll('.home-scroll-reveal:not(.is-visible)');
        if ('IntersectionObserver' in window && revealElements.length) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
            revealElements.forEach(function (el) { observer.observe(el); });
        } else {
            revealElements.forEach(function (el) { el.classList.add('is-visible'); });
        }

        if (window.location.hash) {
            var hashTarget = document.querySelector(window.location.hash);
            if (hashTarget) {
                revealWithin(hashTarget);
            }
        }
    });
</script>
@endsection
