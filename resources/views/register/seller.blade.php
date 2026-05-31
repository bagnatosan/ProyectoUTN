@extends('layouts.app')

@section('title', 'Registro de Emprendedor | ProyectoUTN')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in">
    <!-- Back to Selection Link -->
    <a href="{{ route('register.select') }}" class="inline-flex items-center space-x-2 text-sm text-slate-400 hover:text-purple-400 mb-6 transition-colors duration-200">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver a la selección de rol</span>
    </a>

    <!-- Registration Card -->
    <div class="relative rounded-2xl border border-slate-800 bg-slate-900/40 p-8 shadow-2xl backdrop-blur-sm">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-t-2xl"></div>

        <div class="mb-8">
            <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-purple-400 bg-purple-500/10 rounded-full border border-purple-500/20 uppercase">
                Cuenta Emprendedor
            </span>
            <h1 class="text-2xl font-extrabold text-white mt-2">Registra tu Emprendimiento</h1>
            <p class="text-xs text-slate-400 mt-1">Completa los datos de tu cuenta personal y el perfil comercial de tu negocio.</p>
        </div>

        <form action="{{ route('register.seller.store') }}" method="POST" class="space-y-8" id="seller-registration-form">
            @csrf

            <!-- Section 1: Personal Details -->
            <div class="space-y-6">
                <div class="flex items-center space-x-2 pb-2 border-b border-slate-850">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-purple-500/20 text-purple-400 text-xs font-bold">1</span>
                    <h2 class="text-sm font-bold tracking-wider uppercase text-slate-300">Datos de Usuario</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Nombre Completo
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autocomplete="name" 
                                   placeholder="Ej. María López"
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('name') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
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
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   placeholder="negocio@correo.com"
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('email') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                        </div>
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Contraseña
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   autocomplete="new-password" 
                                   placeholder="••••••••"
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('password') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
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
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required 
                                   autocomplete="new-password" 
                                   placeholder="••••••••"
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 focus:ring-purple-500 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Business Profile -->
            <div class="space-y-6">
                <div class="flex items-center space-x-2 pb-2 border-b border-slate-850">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-purple-500/20 text-purple-400 text-xs font-bold">2</span>
                    <h2 class="text-sm font-bold tracking-wider uppercase text-slate-300">Perfil del Emprendimiento</h2>
                </div>

                <!-- Business Name -->
                <div class="space-y-1.5">
                    <label for="business_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Nombre Comercial
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="business_name" 
                               id="business_name" 
                               value="{{ old('business_name') }}" 
                               required 
                               placeholder="Ej. Arte y Sabor Pastelería"
                               class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('business_name') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                    </div>
                    @error('business_name')
                        <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Teléfono de Contacto
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <input type="text" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone') }}" 
                                   required 
                                   placeholder="Ej. +54 9 1234 5678"
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('phone') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                        </div>
                        @error('phone')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo URL -->
                    <div class="space-y-1.5">
                        <label for="logo" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            URL del Logo
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input type="url" 
                                   name="logo" 
                                   id="logo" 
                                   value="{{ old('logo') }}" 
                                   required 
                                   placeholder="https://ejemplo.com/logo.png"
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('logo') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                        </div>
                        @error('logo')
                            <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-1.5">
                    <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Dirección (Opcional)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="address" 
                               id="address" 
                               value="{{ old('address') }}" 
                               placeholder="Ej. Av. Siempreviva 742"
                               class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border @error('address') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                    </div>
                    @error('address')
                        <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Descripción del Negocio
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              required 
                              placeholder="Cuéntanos a qué se dedica tu emprendimiento..."
                              class="block w-full px-4 py-2.5 bg-slate-950/60 border @error('description') border-rose-500 focus:ring-rose-500 @else border-slate-800 focus:ring-purple-500 @enderror rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    id="btn-submit-seller-registration"
                    class="w-full py-3 px-4 rounded-xl text-center text-sm font-semibold bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-pink-500 text-white shadow-lg shadow-purple-600/20 hover:shadow-purple-500/30 transition-all duration-300 transform active:scale-[0.98]">
                Registrar Emprendedor y Negocio
            </button>
        </form>
    </div>
</div>
@endsection
