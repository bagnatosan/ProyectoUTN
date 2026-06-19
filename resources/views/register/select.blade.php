@extends('layouts.app')

@section('title', 'ProyectoUTN | Reservas y Emprendimientos Locales')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl')

@section('content')
<div class="relative space-y-24 md:space-y-32">
    <div class="home-blob-green" aria-hidden="true"></div>
    <div class="home-blob-peach" aria-hidden="true"></div>

    {{-- Hero --}}
    <section class="relative z-10 text-center pt-4 md:pt-8">
        <span class="inline-block px-3 py-1 text-xs font-semibold tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20 uppercase animate-fade-in-up">
            Plataforma de reservas locales
        </span>

        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mt-5 max-w-3xl mx-auto leading-tight animate-fade-in-up animate-delay-1">
            Conectamos emprendimientos locales con quienes quieren reservar
        </h1>

        <p class="text-slate-400 mt-4 max-w-2xl mx-auto text-sm md:text-lg animate-fade-in-up animate-delay-2">
            Explorá catálogos, elegí horarios y recibí confirmación al instante. Si tenés un negocio, publicá tu perfil y empezá a recibir reservas directas.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-8 animate-fade-in-up animate-delay-3">
            <a href="#registro-cliente"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-cyan-500 text-white shadow-lg shadow-indigo-600/20 transition-all duration-300 transform hover:scale-[1.02]">
                Soy cliente
            </a>
            <a href="#para-emprendedores"
               class="home-btn-outline-peach inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-[1.02]">
                Tengo un emprendimiento
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
                <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item[1] }}" />
                    </svg>
                    <span class="text-sm font-medium text-slate-300">{{ $item[0] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Para clientes --}}
    <section id="registro-cliente" class="relative z-10 scroll-mt-24">
        <div class="text-center mb-10 home-scroll-reveal">
            <span class="px-3 py-1 text-xs font-semibold tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20 uppercase">
                Para clientes
            </span>
            <h2 class="text-2xl md:text-3xl font-extrabold mt-4">Tu próxima reserva, a un clic</h2>
            <p class="text-slate-400 mt-2 max-w-xl mx-auto text-sm md:text-base">
                Descubrí emprendimientos de tu zona, mirá disponibilidad y reservá sin llamadas ni mensajes.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-12">
            @foreach ([
                ['1', 'Explorá', 'Navegá catálogos de negocios locales y encontrá lo que necesitás.', 'home-scroll-reveal-delay-1'],
                ['2', 'Elegí', 'Seleccioná producto o turno según la disponibilidad publicada.', 'home-scroll-reveal-delay-2'],
                ['3', 'Reservá', 'Confirmá tu reserva y recibí la confirmación al instante.', 'home-scroll-reveal-delay-3'],
            ] as $step)
                <div class="home-step-card home-scroll-reveal {{ $step[3] }} rounded-2xl border border-slate-800 bg-slate-900/40 p-6 transition-all duration-300">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-sm font-bold text-indigo-400">
                        {{ $step[0] }}
                    </span>
                    <h3 class="text-lg font-bold mt-4">{{ $step[1] }}</h3>
                    <p class="text-sm text-slate-400 mt-2">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>

        <div class="max-w-md mx-auto home-scroll-reveal @if($errors->any()) animate-shake @endif" id="home-client-form">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 shadow-2xl">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-t-2xl"></div>

                <div class="mb-8 text-center">
                    <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20 uppercase">
                        Cuenta Cliente
                    </span>
                    <h3 class="text-2xl font-extrabold mt-2">Creá tu cuenta gratis</h3>
                    <p class="text-xs text-slate-400 mt-1">En menos de un minuto tenés acceso a catálogos y reservas.</p>
                </div>

                <form action="{{ route('register.client.store') }}" method="POST" class="space-y-5" id="client-registration-form">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Nombre completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Ej. Juan Pérez"
                               class="block w-full px-4 py-3 bg-slate-950/60 border @error('name') border-rose-500 @else border-slate-800 @enderror rounded-xl text-sm">
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Correo electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" placeholder="ejemplo@correo.com"
                               class="block w-full px-4 py-3 bg-slate-950/60 border @error('email') border-rose-500 @else border-slate-800 @enderror rounded-xl text-sm">
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Contraseña</label>
                        <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="••••••••"
                               class="block w-full px-4 py-3 bg-slate-950/60 border @error('password') border-rose-500 @else border-slate-800 @enderror rounded-xl text-sm">
                        @error('password')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" placeholder="••••••••"
                               class="block w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm">
                    </div>

                    <button type="submit" id="btn-submit-client-registration"
                            class="w-full py-3 px-4 rounded-xl text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-500 transition-all duration-300 transform active:scale-[0.98]">
                        Crear mi cuenta
                    </button>
                </form>

                <p class="text-center text-xs text-slate-400 mt-5">
                    ¿Ya tenés cuenta?
                    <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Iniciá sesión</a>
                </p>
            </div>
        </div>
    </section>

    {{-- Para emprendedores --}}
    <section id="para-emprendedores" class="relative z-10 scroll-mt-24">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/40 p-8 md:p-12 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-30 pointer-events-none" style="background: radial-gradient(circle, rgba(233,157,98,0.25) 0%, transparent 70%);"></div>

            <div class="relative z-10 text-center mb-10 home-scroll-reveal">
                <span class="px-3 py-1 text-xs font-semibold tracking-wider rounded-full border uppercase" style="color: #d88448; background: #fff7f0; border-color: #e99d62;">
                    Para emprendedores
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold mt-4">Potenciá tu negocio local</h2>
                <p class="text-slate-400 mt-2 max-w-2xl mx-auto text-sm md:text-base">
                    Publicá tu catálogo, gestioná horarios y recibí reservas directas. Dejá de coordinar todo por mensajes: tus clientes reservan solos y vos gestionás desde un panel.
                </p>
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
                    <div class="home-seller-card home-scroll-reveal {{ $feature[2] }} rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition-all duration-300">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-3" style="background: #fff7f0; border: 1px solid #e99d62;">
                            <svg class="w-4 h-4" style="color: #d88448;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold">{{ $feature[0] }}</h3>
                        <p class="text-sm text-slate-400 mt-1.5">{{ $feature[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-10">
                @foreach ([
                    ['1', 'Registrate', 'Creá tu cuenta como emprendedor en pocos minutos.', 'home-scroll-reveal-delay-1'],
                    ['2', 'Completá tu perfil', 'Subí tu catálogo, categorías y horarios disponibles.', 'home-scroll-reveal-delay-2'],
                    ['3', 'Recibí reservas', 'Compartí tu negocio y empezá a recibir pedidos.', 'home-scroll-reveal-delay-3'],
                ] as $step)
                    <div class="home-scroll-reveal {{ $step[3] }} text-center md:text-left">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold" style="background: #fff7f0; border: 1px solid #e99d62; color: #d88448;">
                            {{ $step[0] }}
                        </span>
                        <h3 class="text-base font-bold mt-3">{{ $step[1] }}</h3>
                        <p class="text-sm text-slate-400 mt-1">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center home-scroll-reveal">
                <a href="{{ route('register.seller') }}"
                   id="btn-select-seller"
                   class="home-btn-peach inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-[1.02]">
                    Registrar mi emprendimiento
                </a>
                <p class="text-xs text-slate-400 mt-3">Configuración inicial en pocos minutos</p>
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
    <section class="relative z-10 text-center pb-4 home-scroll-reveal">
        <h2 class="text-lg font-bold mb-4">¿Listo para empezar?</h2>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="#registro-cliente" class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-500 transition-all duration-300">
                Crear cuenta cliente
            </a>
            <a href="{{ route('register.seller') }}" class="home-btn-peach inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300">
                Registrar emprendimiento
            </a>
        </div>
    </section>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('home-client-form');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var targetId = this.getAttribute('href');
                if (targetId.length <= 1) return;
                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        var revealElements = document.querySelectorAll('.home-scroll-reveal');
        if ('IntersectionObserver' in window && revealElements.length) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            revealElements.forEach(function (el) { observer.observe(el); });
        } else {
            revealElements.forEach(function (el) { el.classList.add('is-visible'); });
        }
    });
</script>
@endsection
