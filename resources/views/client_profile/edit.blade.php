@extends('layouts.app')

@section('title', 'Mi Perfil | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'w-full mx-auto')

@section('content')
<div class="w-full animate-fade-in">

    {{-- Banner --}}
    <div class="page-banner">
        <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
        <div class="page-banner__overlay"></div>
        <div class="page-banner__content">
            <h1 class="page-banner__title">Mi perfil</h1>
            <p class="page-banner__subtitle">Editá tus datos y tu dirección para encontrar emprendimientos cerca tuyo.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto mt-8">

    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-400 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver al panel</span>
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <div class="profile-card relative">
            <div class="absolute inset-x-0 top-0 auth-accent-bar-client rounded-t-2xl"></div>

            <div class="profile-card-header">
                <h2 class="profile-card-title">Datos personales</h2>
                <p class="profile-card-subtitle">Tu nombre y dirección de referencia.</p>
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

            <form action="{{ route('client_profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label for="name" class="profile-label">Nombre completo</label>
                    <input type="text" name="name" id="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        class="profile-input @error('name') profile-input-error @enderror"
                        placeholder="Ej. Juan Pérez"
                        required>
                    @error('name')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field">
                    <label class="profile-label">Correo electrónico</label>
                    <input type="email" value="{{ auth()->user()->email }}" class="profile-input" disabled>
                    <p class="profile-field-hint">El correo no se puede modificar porque es tu identificador de acceso.</p>
                </div>

                <div class="profile-section-divider">
                    <span class="profile-section-label">Ubicación</span>
                </div>

                <div class="profile-field">
                    <label for="client-address" class="profile-label">Dirección</label>
                    <input type="text" name="address" id="client-address"
                        value="{{ old('address', $profile->address ?? '') }}"
                        placeholder="Ej. Av. Corrientes 1234, CABA"
                        class="profile-input @error('address') profile-input-error @enderror"
                        required>
                    <p class="profile-field-hint">La usamos para mostrarte emprendimientos cercanos y calcular envíos.</p>
                    @error('address')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-map-block">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tu ubicación en el mapa</p>
                        <button type="button" id="btn-geocode-address" class="profile-btn-outline">
                            Ubicar dirección
                        </button>
                    </div>
                    <div id="profile-location-map" class="map-container map-container-profile rounded-xl overflow-hidden border border-slate-800"></div>
                    <input type="hidden" name="latitude" id="client-latitude" value="{{ old('latitude', $profile->latitude ?? '') }}">
                    <input type="hidden" name="longitude" id="client-longitude" value="{{ old('longitude', $profile->longitude ?? '') }}">
                    <p id="location-feedback" class="profile-field-hint mt-2">
                        Hacé click en "Ubicar dirección" o arrastrá el marcador para ajustar tu ubicación.
                    </p>
                </div>

                <button type="submit" class="profile-btn-outline w-full">
                    Guardar cambios
                </button>
            </form>
        </div>

        <div class="profile-card">
            <div class="profile-card-header !pb-3">
                <h2 class="profile-card-title">Seguridad</h2>
                <p class="profile-card-subtitle">Actualizá tu contraseña de acceso.</p>
            </div>

            <form action="{{ route('client_profile.password') }}" method="POST" class="space-y-4">
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

    </div>{{-- /grid --}}
    </div>{{-- /max-w-4xl --}}
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
    var latInput = document.getElementById('client-latitude');
    var lngInput = document.getElementById('client-longitude');
    var addressInput = document.getElementById('client-address');
    var feedback = document.getElementById('location-feedback');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
