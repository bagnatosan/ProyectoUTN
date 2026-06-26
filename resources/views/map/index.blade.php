@extends('layouts.app')

@section('title', 'Mapa de Emprendimientos | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
<div class="space-y-8 home-scroll-reveal is-visible">
    <div class="text-center md:text-left">
        <span class="px-3 py-1 text-xs font-semibold tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20 uppercase">
            Explorá cerca tuyo
        </span>
        <h1 class="text-2xl md:text-3xl font-extrabold mt-4">Emprendimientos locales en el mapa</h1>
        <p class="text-slate-400 mt-2 text-sm md:text-base max-w-2xl">
            Encontrá negocios registrados en la plataforma, mirá dónde están y accedé a su catálogo para reservar.
        </p>
    </div>

    <div id="map-shell" class="map-page-shell hidden">
        <div class="map-page-layout">
            <aside class="map-sidebar">
                <div class="map-sidebar-header">
                    <p class="map-sidebar-title">Emprendimientos</p>
                    <span id="map-count-badge" class="map-count-badge">{{ $businessCount }}</span>
                </div>

                <label class="map-search-wrap">
                    <svg class="map-search-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                    </svg>
                    <input type="search" id="map-search" class="map-search-input" placeholder="Buscar por nombre o dirección…" autocomplete="off">
                </label>

                <ul id="map-business-list" class="map-business-list" aria-label="Lista de emprendimientos"></ul>
            </aside>

            <div class="map-panel">
                <div id="map" class="map-container map-container-page" aria-label="Mapa de emprendimientos locales"></div>
            </div>
        </div>
    </div>

    <div id="map-empty-state" class="hidden map-empty-state">
        <div class="map-empty-icon" aria-hidden="true">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
        </div>
        <p class="map-empty-title">Todavía no hay emprendimientos con ubicación publicada.</p>
        <p class="map-empty-text">Si tenés un negocio, completá tu dirección en el perfil para aparecer acá.</p>
    </div>

    <p class="map-attribution">
        © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>
        · <a href="https://carto.com/attributions" target="_blank" rel="noopener noreferrer">CARTO</a>
    </p>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var userLocation = @json($userLocation);
    var defaultCenter = userLocation ? [userLocation.lat, userLocation.lng] : [-34.6037, -58.3816];
    var mapShell = document.getElementById('map-shell');
    var emptyState = document.getElementById('map-empty-state');
    var businessList = document.getElementById('map-business-list');
    var searchInput = document.getElementById('map-search');
    var countBadge = document.getElementById('map-count-badge');
    var map = null;
    var markerLayer = null;
    var markerEntries = [];

    var markerIcon = L.divIcon({
        className: 'map-marker-wrap',
        html: '<div class="map-marker-pin" aria-hidden="true"><span class="map-marker-dot"></span></div>',
        iconSize: [32, 40],
        iconAnchor: [16, 38],
        popupAnchor: [0, -34]
    });

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function buildPopupContent(business) {
        var description = business.description
            ? business.description.substring(0, 120) + (business.description.length > 120 ? '…' : '')
            : '';

        return '<div class="map-popup">'
            + '<p class="map-popup-name">' + escapeHtml(business.name) + '</p>'
            + (business.address ? '<p class="map-popup-address">' + escapeHtml(business.address) + '</p>' : '')
            + (description ? '<p class="map-popup-desc">' + escapeHtml(description) + '</p>' : '')
            + '<a href="' + escapeHtml(business.catalog_url) + '" class="map-popup-link">Ver catálogo</a>'
            + '</div>';
    }

    function initMap() {
        map = L.map('map', {
            zoomControl: false,
            scrollWheelZoom: true
        }).setView(defaultCenter, 12);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        }).addTo(map);

        markerLayer = L.layerGroup().addTo(map);

        if (userLocation) {
            var userIcon = L.divIcon({
                className: 'map-user-marker-wrap',
                html: '<div class="map-user-marker-pin" aria-hidden="true"></div>',
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            });

            L.marker([userLocation.lat, userLocation.lng], { icon: userIcon, zIndexOffset: 1000 })
                .bindPopup('<div class="map-popup"><p class="map-popup-name">Tu ubicación</p></div>')
                .addTo(map);
        }
    }

    function renderBusinessList(filter) {
        var query = (filter || '').trim().toLowerCase();
        var visibleCount = 0;
        businessList.innerHTML = '';

        markerEntries.forEach(function (entry) {
            var haystack = (entry.business.name + ' ' + (entry.business.address || '')).toLowerCase();
            var matches = !query || haystack.indexOf(query) !== -1;

            entry.listItem.classList.toggle('hidden', !matches);
            entry.marker.setOpacity(matches ? 1 : 0);
            entry.marker._icon && entry.marker._icon.classList.toggle('map-marker-hidden', !matches);

            if (matches) {
                visibleCount++;
                businessList.appendChild(entry.listItem);
            }
        });

        countBadge.textContent = visibleCount;

        if (visibleCount === 0 && markerEntries.length > 0) {
            businessList.innerHTML = '<li class="map-list-empty">No hay resultados para esa búsqueda.</li>';
        }
    }

    function focusBusiness(entry) {
        document.querySelectorAll('.map-business-item.is-active').forEach(function (el) {
            el.classList.remove('is-active');
        });

        entry.listItem.classList.add('is-active');
        map.flyTo([entry.business.lat, entry.business.lng], 15, { duration: 0.8 });
        entry.marker.openPopup();
    }

    function createListItem(business, entry) {
        var item = document.createElement('li');
        item.className = 'map-business-item';
        item.innerHTML = '<button type="button" class="map-business-btn">'
            + '<span class="map-business-name">' + escapeHtml(business.name) + '</span>'
            + (business.address ? '<span class="map-business-address">' + escapeHtml(business.address) + '</span>' : '')
            + '</button>';

        item.querySelector('button').addEventListener('click', function () {
            focusBusiness(entry);
        });

        return item;
    }

    function showEmptyState(message) {
        mapShell.classList.add('hidden');
        emptyState.classList.remove('hidden');
        if (message) {
            emptyState.querySelector('.map-empty-title').textContent = message;
        }
    }

    fetch('{{ route('map.markers') }}')
        .then(function (response) { return response.json(); })
        .then(function (businesses) {
            if (!businesses.length) {
                showEmptyState();
                return;
            }

            initMap();
            mapShell.classList.remove('hidden');
            emptyState.classList.add('hidden');

            var bounds = [];

            businesses.forEach(function (business) {
                var marker = L.marker([business.lat, business.lng], { icon: markerIcon })
                    .bindPopup(buildPopupContent(business));

                var entry = {
                    business: business,
                    marker: marker,
                    listItem: null
                };

                entry.listItem = createListItem(business, entry);
                markerEntries.push(entry);
                markerLayer.addLayer(marker);
                marker.on('click', function () {
                    document.querySelectorAll('.map-business-item.is-active').forEach(function (el) {
                        el.classList.remove('is-active');
                    });
                    entry.listItem.classList.add('is-active');
                });
                bounds.push([business.lat, business.lng]);
            });

            renderBusinessList('');

            if (!userLocation) {
                if (bounds.length === 1) {
                    map.setView(bounds[0], 14);
                } else if (bounds.length > 1) {
                    map.fitBounds(bounds, { padding: [48, 48] });
                }
            }

            searchInput.addEventListener('input', function () {
                renderBusinessList(searchInput.value);
            });

            setTimeout(function () {
                map.invalidateSize();
            }, 100);
        })
        .catch(function () {
            showEmptyState('No se pudieron cargar los emprendimientos del mapa.');
        });
});
</script>
@endpush
