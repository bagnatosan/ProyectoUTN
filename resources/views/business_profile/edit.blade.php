@extends('layouts.app')

@section('title', 'Perfil del Emprendimiento | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@php
    $logoUrl = null;
    if ($profile && $profile->logo) {
        $logoUrl = filter_var($profile->logo, FILTER_VALIDATE_URL)
            ? $profile->logo
            : Storage::url($profile->logo);
    }
@endphp

@section('content')
<div class="w-full animate-fade-in">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-400 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver al panel</span>
    </a>

    <div class="mb-8">
        <!-- <span class="auth-role-badge auth-role-badge-seller">Mi negocio</span> -->
        <h1 class="text-2xl md:text-3xl font-extrabold mt-2">Perfil del emprendimiento</h1>
        <p class="text-sm text-slate-400 mt-1">Editá la información pública que ven tus clientes en el catálogo y el mapa.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Formulario --}}
        <div class="lg:col-span-3">
            <div class="profile-card relative">
                <div class="absolute inset-x-0 top-0 auth-accent-bar-seller rounded-t-2xl"></div>

                <div class="profile-card-header">
                    <h2 class="profile-card-title">Datos del negocio</h2>
                    <p class="profile-card-subtitle">Logo, contacto y ubicación en el mapa.</p>
                </div>

                @if($errors->any())
                    <div class="profile-alert profile-alert-error">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="profile-alert profile-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('business_profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Logo --}}
                    <div class="profile-logo-block">
                        <div id="profile-logo-preview" class="profile-logo-preview">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo del negocio" id="profile-logo-img">
                            @else
                                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="profile-logo-placeholder">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-300">{{ $profile->business_name ?? auth()->user()->name }}</p>
                            <label class="profile-logo-upload-btn mt-2">
                                Cambiar logo
                                <input type="file" name="logo" id="profile-logo-input" accept="image/jpeg,image/png,image/jpg" class="hidden">
                            </label>
                            <p class="text-xs text-slate-500 mt-1.5">JPG o PNG, máximo 2 MB.</p>
                        </div>
                    </div>

                    <div class="profile-field">
                        <label for="business_name" class="profile-label">Nombre comercial</label>
                        <input type="text" name="business_name" id="business_name"
                            value="{{ old('business_name', $profile->business_name ?? '') }}"
                            class="profile-input @error('business_name') profile-input-error @enderror"
                            placeholder="Ej. Arte y Sabor Pastelería"
                            required>
                        @error('business_name')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="profile-field">
                        <label for="description" class="profile-label">Descripción</label>
                        <textarea name="description" id="description" rows="4"
                            class="profile-input profile-textarea @error('description') profile-input-error @enderror"
                            placeholder="Contá qué ofrecés y qué te hace especial…">{{ old('description', $profile->description ?? '') }}</textarea>
                        @error('description')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="profile-field">
                        <label for="phone" class="profile-label">Teléfono / WhatsApp</label>
                        <input type="text" name="phone" id="phone"
                            value="{{ old('phone', $profile->phone ?? '') }}"
                            class="profile-input @error('phone') profile-input-error @enderror"
                            placeholder="Ej. +54 9 11 1234 5678">
                        @error('phone')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Datos de cobro --}}
                    <div class="profile-section-divider">
                        <span class="profile-section-label">Datos de cobro</span>
                    </div>

                    <p class="profile-field-hint -mt-2">Estos datos se muestran al cliente cuando hace una reserva para que pueda realizar la transferencia.</p>

                    <div class="profile-field">
                        <label for="bank_account_holder" class="profile-label">Titular de la cuenta</label>
                        <input type="text" name="bank_account_holder" id="bank_account_holder"
                            value="{{ old('bank_account_holder', $profile->bank_account_holder ?? '') }}"
                            class="profile-input @error('bank_account_holder') profile-input-error @enderror"
                            placeholder="Ej. María González">
                        @error('bank_account_holder')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="profile-field">
                            <label for="bank_cbu" class="profile-label">CBU</label>
                            <input type="text" name="bank_cbu" id="bank_cbu"
                                value="{{ old('bank_cbu', $profile->bank_cbu ?? '') }}"
                                class="profile-input @error('bank_cbu') profile-input-error @enderror"
                                placeholder="22 dígitos">
                            @error('bank_cbu')
                                <p class="profile-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="profile-field">
                            <label for="bank_alias" class="profile-label">Alias</label>
                            <input type="text" name="bank_alias" id="bank_alias"
                                value="{{ old('bank_alias', $profile->bank_alias ?? '') }}"
                                class="profile-input @error('bank_alias') profile-input-error @enderror"
                                placeholder="Ej. MI.ALIAS.MP">
                            @error('bank_alias')
                                <p class="profile-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="profile-field">
                        <label for="bank_name" class="profile-label">Banco / Billetera</label>
                        <input type="text" name="bank_name" id="bank_name"
                            value="{{ old('bank_name', $profile->bank_name ?? '') }}"
                            class="profile-input @error('bank_name') profile-input-error @enderror"
                            placeholder="Ej. Mercado Pago, Banco Galicia">
                        @error('bank_name')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="profile-section-divider">
                        <span class="profile-section-label">Costos</span>
                    </div>

                    <div class="profile-field">
                        <label for="profit_margin" class="profile-label">Margen de ganancia general</label>
                        <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                            <input type="number" name="profit_margin" id="profit_margin" step="0.1" min="1" max="50"
                                value="{{ old('profit_margin', $profile->profit_margin ?? 3) }}"
                                class="profile-input w-full sm:w-28 @error('profit_margin') profile-input-error @enderror">
                            <p class="profile-field-hint sm:pt-2">
                                Multiplicador sobre el costo (ej: 3 = 300%). Se usa como margen por defecto en todos tus productos, salvo que definas uno personalizado en un producto puntual.
                            </p>
                        </div>
                        @error('profit_margin')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="profile-section-divider">
                        <span class="profile-section-label">Ubicación</span>
                    </div>

                    <div class="profile-field">
                        <label for="business-address" class="profile-label">Dirección <span class="profile-label-optional">(opcional)</span></label>
                        <input type="text" name="address" id="business-address"
                            value="{{ old('address', $profile->address ?? '') }}"
                            placeholder="Ej. Av. Corrientes 1234, CABA"
                            class="profile-input @error('address') profile-input-error @enderror">
                        <p class="profile-field-hint">Completá la dirección para aparecer en el mapa público de emprendimientos.</p>
                        @error('address')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="profile-map-block">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ubicación en el mapa</p>
                            <button type="button" id="btn-geocode-address" class="profile-btn-outline">
                                Ubicar dirección
                            </button>
                        </div>
                        <div id="profile-location-map" class="map-container map-container-profile rounded-xl overflow-hidden border border-slate-800"></div>
                        <input type="hidden" name="latitude" id="business-latitude" value="{{ old('latitude', $profile->latitude ?? '') }}">
                        <input type="hidden" name="longitude" id="business-longitude" value="{{ old('longitude', $profile->longitude ?? '') }}">
                        <p id="location-feedback" class="profile-field-hint mt-2">Arrastrá el marcador para ajustar la ubicación exacta.</p>
                    </div>

                    <button type="submit" class="auth-role-btn auth-role-btn-seller w-full">
                        Guardar cambios
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="profile-card">
                <div class="profile-card-header !pb-3">
                    <h2 class="profile-card-title">Vista previa pública</h2>
                    <p class="profile-card-subtitle">Así ven tu negocio los clientes.</p>
                </div>

                <div class="profile-preview">
                    <div class="profile-preview-logo">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="" id="preview-logo-img">
                        @else
                            <svg class="w-7 h-7" style="color:#2d6a4f" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="preview-logo-placeholder">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        @endif
                    </div>
                    <p class="profile-preview-name">{{ $profile->business_name ?? 'Tu emprendimiento' }}</p>
                    <p class="profile-preview-desc">{{ $profile->description ?? 'Descripción de tu negocio' }}</p>
                    @if($profile && $profile->address)
                        <p class="profile-preview-address">{{ $profile->address }}</p>
                    @endif
                    <div class="profile-preview-footer">
                        @if($profile && $profile->hasCoordinates())
                            <a href="{{ route('map.index') }}" class="profile-preview-link">
                                Ver en el mapa público
                            </a>
                        @else
                            <span class="profile-preview-muted">Agregá una dirección para aparecer en el mapa.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-card-header !pb-3">
                    <h2 class="profile-card-title">Seguridad</h2>
                    <p class="profile-card-subtitle">Actualizá tu contraseña de acceso.</p>
                </div>

                <form action="{{ route('business_profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="profile-field">
                        <label for="current_password" class="profile-label">Contraseña actual</label>
                        <input type="password" name="current_password" id="current_password"
                            class="profile-input @error('current_password') profile-input-error @enderror"
                            autocomplete="current-password">
                        @error('current_password')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="profile-field">
                        <label for="new_password" class="profile-label">Nueva contraseña</label>
                        <input type="password" name="new_password" id="new_password"
                            placeholder="Mínimo 8 caracteres"
                            class="profile-input @error('new_password') profile-input-error @enderror"
                            autocomplete="new-password">
                        @error('new_password')
                            <p class="profile-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="profile-btn-outline w-full">
                        Cambiar contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var defaultCenter = [-34.6037, -58.3816];
    var latInput = document.getElementById('business-latitude');
    var lngInput = document.getElementById('business-longitude');
    var addressInput = document.getElementById('business-address');
    var feedback = document.getElementById('location-feedback');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var logoInput = document.getElementById('profile-logo-input');

    if (logoInput) {
        logoInput.addEventListener('change', function () {
            var file = logoInput.files && logoInput.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (event) {
                var previewWrap = document.getElementById('profile-logo-preview');
                var previewSidebar = document.querySelector('.profile-preview-logo');
                var url = event.target.result;

                previewWrap.innerHTML = '<img src="' + url + '" alt="Logo del negocio" id="profile-logo-img" class="w-full h-full object-cover">';
                previewSidebar.innerHTML = '<img src="' + url + '" alt="" id="preview-logo-img" class="w-full h-full object-cover rounded-xl">';
            };
            reader.readAsDataURL(file);
        });
    }

    var initialLat = parseFloat(latInput.value);
    var initialLng = parseFloat(lngInput.value);
    var hasInitial = !isNaN(initialLat) && !isNaN(initialLng);
    var center = hasInitial ? [initialLat, initialLng] : defaultCenter;
    var zoom = hasInitial ? 15 : 12;

    var map = L.map('profile-location-map', { zoomControl: true }).setView(center, zoom);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap &copy; CARTO'
    }).addTo(map);

    var markerIcon = L.divIcon({
        className: 'map-marker-wrap',
        html: '<div class="map-marker-pin" aria-hidden="true"><span class="map-marker-dot"></span></div>',
        iconSize: [32, 40],
        iconAnchor: [16, 38],
        popupAnchor: [0, -34]
    });

    var marker = L.marker(center, { draggable: true, icon: markerIcon }).addTo(map);

    marker.on('dragend', function () {
        var position = marker.getLatLng();
        latInput.value = position.lat.toFixed(8);
        lngInput.value = position.lng.toFixed(8);
        feedback.textContent = 'Ubicación ajustada manualmente.';
        feedback.classList.remove('profile-field-error');
    });

    function setMarkerPosition(lat, lng, message) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        feedback.textContent = message;
        feedback.classList.remove('profile-field-error');
    }

    document.getElementById('btn-geocode-address').addEventListener('click', function () {
        var address = addressInput.value.trim();

        if (!address) {
            feedback.textContent = 'Ingresá una dirección antes de ubicar.';
            feedback.classList.add('profile-field-error');
            return;
        }

        feedback.textContent = 'Buscando dirección…';
        feedback.classList.remove('profile-field-error');

        fetch('{{ route('map.geocode') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ address: address })
        })
        .then(function (response) {
            if (!response.ok) throw new Error('not found');
            return response.json();
        })
        .then(function (data) {
            setMarkerPosition(data.latitude, data.longitude, 'Dirección ubicada. Podés ajustar el marcador si hace falta.');
        })
        .catch(function () {
            feedback.textContent = 'No se encontró esa dirección. Probá con más detalle o mové el marcador manualmente.';
            feedback.classList.add('profile-field-error');
        });
    });

    setTimeout(function () {
        map.invalidateSize();
    }, 150);
});
</script>
@endpush
