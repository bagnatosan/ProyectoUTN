@extends('layouts.app')

@section('title', 'Registro de Cliente | ProyectoUTN')

@push('page_bg')
<div style="position:fixed;inset:0;z-index:0;pointer-events:none;">
    <img src="{{ asset('images/banner-home.png') }}" alt="" style="width:100%;height:100%;object-fit:cover;object-position:center;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.55);"></div>
</div>
@endpush

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in">
    <a href="{{ route('register.hub') }}" class="inline-flex items-center gap-2 text-sm mb-6 transition-colors" style="color:#ffffff;">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver a opciones de registro</span>
    </a>

    <!-- Registration Card -->
    <div class="relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 shadow-2xl backdrop-blur-sm">
        <div class="absolute inset-x-0 top-0 h-1 auth-accent-bar-client rounded-t-2xl"></div>

        <div class="mb-8">
            <span class="auth-role-badge auth-role-badge-client">Cuenta Cliente</span>
            <h1 class="text-2xl font-extrabold mt-2">Registro de cliente</h1>
            <p class="text-xs text-slate-400 mt-1">Completá tus datos para explorar emprendimientos y reservar.</p>
        </div>

        <form action="{{ route('register.client.store') }}" method="POST" class="space-y-6" id="client-registration-form">
            @csrf

            <!-- Full Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Nombre Completo
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name"
                           placeholder="Ej. Juan Pérez"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('name') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('name')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Correo Electrónico
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="ejemplo@correo.com"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('email') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('email')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- ── DIRECCIÓN ─────────────────────────────────── -->
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dirección</p>

                {{-- Calle + Número --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2 space-y-1.5">
                        <label for="street" class="block text-xs text-slate-500">Calle</label>
                        <input type="text" name="street" id="street" value="{{ old('street') }}" required
                               placeholder="Ej. Av. Rivadavia"
                               class="block w-full px-4 py-3 bg-slate-950/60 border @error('street') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                        @error('street')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="street_number" class="block text-xs text-slate-500">Número</label>
                        <input type="text" name="street_number" id="street_number" value="{{ old('street_number') }}" required
                               placeholder="742"
                               class="block w-full px-4 py-3 bg-slate-950/60 border @error('street_number') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                        @error('street_number')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Piso + Departamento --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="floor" class="block text-xs text-slate-500">
                            Piso <span class="text-slate-600">(Opcional)</span>
                        </label>
                        <input type="text" name="floor" id="floor" value="{{ old('floor') }}"
                               placeholder="Ej. 3"
                               class="block w-full px-4 py-3 bg-slate-950/60 border border-slate-800 focus:ring-indigo-500 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                    </div>
                    <div class="space-y-1.5">
                        <label for="apartment" class="block text-xs text-slate-500">
                            Departamento <span class="text-slate-600">(Opcional)</span>
                        </label>
                        <input type="text" name="apartment" id="apartment" value="{{ old('apartment') }}"
                               placeholder="Ej. A"
                               class="block w-full px-4 py-3 bg-slate-950/60 border border-slate-800 focus:ring-indigo-500 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                {{-- Provincia --}}
                <div class="space-y-1.5">
                    <label for="province" class="block text-xs text-slate-500">Provincia</label>
                    <div class="relative">
                        <select name="province" id="province" required
                                class="block w-full px-4 py-3 bg-slate-950/60 border @error('province') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer">
                            <option value="">Cargando provincias...</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                    @error('province')
                        <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Localidad --}}
                <div class="space-y-1.5">
                    <label for="locality" class="block text-xs text-slate-500">Localidad</label>
                    <div class="relative">
                        <select name="locality" id="locality" required disabled
                                class="block w-full px-4 py-3 bg-slate-950/60 border @error('locality') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                            <option value="">Primero seleccioná una provincia</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                    @error('locality')
                        <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Código Postal --}}
                <div class="space-y-1.5">
                    <label for="postal_code" class="block text-xs text-slate-500">Código Postal</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required
                           placeholder="Ej. 1425" maxlength="8"
                           class="block w-full px-4 py-3 bg-slate-950/60 border @error('postal_code') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                    @error('postal_code')
                        <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-[11px] text-slate-500">La usamos para mostrarte emprendimientos cercanos y calcular envíos.</p>
            </div>
            {{-- ── FIN DIRECCIÓN ──────────────────────────────── --}}

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Contraseña
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                           placeholder="••••••••"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border @error('password') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-indigo-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
                @error('password')
                    <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Confirmar Contraseña
                </label>
                <div class="relative input-icon-group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                           placeholder="••••••••"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-950/60 border border-slate-800 focus:ring-indigo-500 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    id="btn-submit-client-registration"
                    class="w-full py-3 px-4 rounded-xl text-center text-sm font-semibold auth-role-btn auth-role-btn-client transition-all duration-300 transform active:scale-[0.98]">
                Crear mi cuenta
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-5">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Iniciá sesión</a>
        </p>
    </div>
</div>

<script>
(function () {
    const OLD_PROVINCE = @json(old('province', ''));
    const OLD_LOCALITY = @json(old('locality', ''));

    const provinceSelect = document.getElementById('province');
    const localitySelect = document.getElementById('locality');

    // 1. Cargar provincias al iniciar
    fetch('https://apis.datos.gob.ar/georef/api/provincias?orden=nombre&campos=nombre&max=100')
        .then(r => r.json())
        .then(data => {
            provinceSelect.innerHTML = '<option value="">Seleccioná una provincia</option>';
            data.provincias.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.nombre;
                opt.textContent = p.nombre;
                if (p.nombre === OLD_PROVINCE) opt.selected = true;
                provinceSelect.appendChild(opt);
            });
            // Si había un valor previo (error de validación), cargar sus localidades
            if (OLD_PROVINCE) loadLocalities(OLD_PROVINCE, OLD_LOCALITY);
        })
        .catch(() => {
            provinceSelect.innerHTML = '<option value="">Error al cargar — intentá de nuevo</option>';
        });

    // 2. Cuando cambia la provincia, cargar localidades
    provinceSelect.addEventListener('change', function () {
        if (this.value) {
            loadLocalities(this.value, null);
        } else {
            localitySelect.innerHTML = '<option value="">Primero seleccioná una provincia</option>';
            localitySelect.disabled = true;
        }
    });

    function loadLocalities(province, selectedLocality) {
        localitySelect.innerHTML = '<option value="">Cargando localidades...</option>';
        localitySelect.disabled = true;

        fetch(`https://apis.datos.gob.ar/georef/api/localidades?provincia=${encodeURIComponent(province)}&orden=nombre&campos=nombre&max=1000`)
            .then(r => r.json())
            .then(data => {
                localitySelect.innerHTML = '<option value="">Seleccioná una localidad</option>';
                const seen = new Set();
                data.localidades.forEach(l => {
                    if (!seen.has(l.nombre)) {
                        seen.add(l.nombre);
                        const opt = document.createElement('option');
                        opt.value = l.nombre;
                        opt.textContent = l.nombre;
                        if (l.nombre === selectedLocality) opt.selected = true;
                        localitySelect.appendChild(opt);
                    }
                });
                localitySelect.disabled = false;
            })
            .catch(() => {
                localitySelect.innerHTML = '<option value="">Error al cargar localidades</option>';
            });
    }
})();
</script>
@endsection
