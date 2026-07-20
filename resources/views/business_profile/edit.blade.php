@extends('layouts.app')

@section('title', 'Perfil del Emprendimiento | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'w-full mx-auto')

@php
    $logoUrl = null;
    if ($profile && $profile->logo) {
        $logoUrl = filter_var($profile->logo, FILTER_VALIDATE_URL)
            ? $profile->logo
            : storage_url($profile->logo);
    }
@endphp

@section('content')
<div class="w-full animate-fade-in">

    @php
        $coverUrl = $profile && $profile->cover_image ? storage_url($profile->cover_image) : null;
    @endphp

    <div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-400 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Volver al panel</span>
        </a>

        @if($profile && $profile->id)
        <a href="{{ route('catalog.show', $profile->id) }}" target="_blank"
           class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 rounded-xl px-4 py-2 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
            </svg>
            Ver mi catálogo público
        </a>
        @endif
    </div>

    {{-- Alertas globales (fuera de ambos forms) --}}
    @if($errors->any())
        <div class="profile-alert profile-alert-error mb-4">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="profile-alert profile-alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- GRID WRAPPER (fuera de cualquier form) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══════════════════════════════════════════
             COLUMNA IZQUIERDA — FORM PRINCIPAL
             ══════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
        <form action="{{ route('business_profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- ===== PROFILE HEADER ===== --}}
            <div style="position:relative;height:220px;border-radius:1rem;overflow:hidden;margin-bottom:0.5rem;">

                @if($coverUrl)
                    <img src="{{ $coverUrl }}" id="cover-preview-img" alt="Foto de portada" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                @else
                    <img id="cover-preview-img" alt="Foto de portada" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;">
                    <div id="cover-placeholder" style="position:absolute;inset:0;background:linear-gradient(135deg,#1e3a2f 0%,#2d6a4f 60%,#3a8a62 100%);"></div>
                @endif

                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.55) 0%,rgba(0,0,0,0.05) 60%);"></div>

                <input type="hidden" name="remove_cover_image" id="remove-cover-input" value="0">
                <div style="position:absolute;top:1rem;right:1rem;display:flex;gap:0.5rem;align-items:center;">
                    @if($coverUrl)
                    <button type="button" id="btn-remove-cover" onclick="removeCover()" style="cursor:pointer;display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 0.875rem;background:rgba(180,30,30,0.55);border:1px solid rgba(255,255,255,0.3);border-radius:0.625rem;color:#fff;font-size:0.72rem;font-weight:600;backdrop-filter:blur(6px);transition:background 0.2s;" onmouseover="this.style.background='rgba(180,30,30,0.8)'" onmouseout="this.style.background='rgba(180,30,30,0.55)'">
                        <svg style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Eliminar portada
                    </button>
                    @endif
                    <label style="cursor:pointer;display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 0.875rem;background:rgba(0,0,0,0.45);border:1px solid rgba(255,255,255,0.3);border-radius:0.625rem;color:#fff;font-size:0.72rem;font-weight:600;backdrop-filter:blur(6px);transition:background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.65)'" onmouseout="this.style.background='rgba(0,0,0,0.45)'">
                        <svg style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
                        {{ $coverUrl ? 'Cambiar portada' : 'Subir portada' }}
                        <input type="file" name="cover_image" id="cover-image-input" accept="image/jpeg,image/png,image/jpg" class="hidden">
                    </label>
                </div>

                <div style="position:absolute;bottom:1.25rem;left:1.5rem;display:flex;align-items:flex-end;gap:1rem;">
                    <div style="position:relative;">
                        <div id="profile-logo-preview" style="width:5rem;height:5rem;border-radius:0.875rem;border:3px solid #fff;overflow:hidden;background:#f0ebe2;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 12px rgba(0,0,0,0.25);">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo del negocio" id="profile-logo-img" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <svg style="width:2rem;height:2rem;color:#9a9390;" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="profile-logo-placeholder">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @endif
                        </div>
                        <input type="hidden" name="remove_logo" id="remove-logo-input" value="0">
                        <label style="position:absolute;bottom:-6px;right:-6px;cursor:pointer;width:1.75rem;height:1.75rem;background:#2d6a4f;border-radius:9999px;display:flex;align-items:center;justify-content:center;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.25);" title="Cambiar logo">
                            <svg style="width:0.875rem;height:0.875rem;color:#fff;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                            </svg>
                            <input type="file" name="logo" id="profile-logo-input" accept="image/jpeg,image/png,image/jpg" class="hidden">
                        </label>
                        @if($logoUrl)
                        <button type="button" onclick="removeLogo()" style="position:absolute;top:-6px;right:-6px;cursor:pointer;width:1.5rem;height:1.5rem;background:#b91c1c;border-radius:9999px;display:flex;align-items:center;justify-content:center;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.25);" title="Eliminar logo">
                            <svg style="width:0.7rem;height:0.7rem;color:#fff;" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                    <div style="padding-bottom:0.25rem;">
                        <p style="color:#fff;font-weight:700;font-size:1.125rem;text-shadow:0 1px 6px rgba(0,0,0,0.5);line-height:1.2;">{{ $profile->business_name ?? 'Tu emprendimiento' }}</p>
                        <p style="color:rgba(255,255,255,0.7);font-size:0.75rem;margin-top:0.1rem;">Perfil del emprendimiento</p>
                    </div>
                </div>
            </div>

            @error('cover_image')
                <p class="profile-field-error">{{ $message }}</p>
            @enderror

            <div class="profile-card relative">
                <div class="absolute inset-x-0 top-0 auth-accent-bar-seller rounded-t-2xl"></div>

                <div class="profile-card-header">
                    <h2 class="profile-card-title">Datos del negocio</h2>
                    <p class="profile-card-subtitle">Contacto y ubicación en el mapa.</p>
                </div>

                <div class="space-y-6">

                    <div class="profile-field">
                        <label for="business_name" class="profile-label">Nombre comercial</label>
                        <input type="text" name="business_name" id="business_name"
                            value="{{ old('business_name', $profile->business_name ?? '') }}"
                            class="profile-input @error('business_name') profile-input-error @enderror"
                            placeholder="Ej. Arte y Sabor Pastelería" required>
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



                    <div class="profile-section-divider">
                        <span class="profile-section-label">Mercado Pago</span>
                    </div>

                    <div class="profile-field">
                        <label class="profile-label font-semibold text-slate-100">Cobros Electrónicos (Vinculación 1-Click)</label>
                        <p class="text-xs text-slate-400 mb-3">Conectá tu cuenta de Mercado Pago para recibir el dinero de tus ventas de forma inmediata y automática.</p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-2">
                            <a href="{{ route('business_profile.mercadopago.connect') }}" class="btn-mp-connect flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 12H9v-1.41l3.59-3.59H9V7.59L13.41 12H9v1.41l4.41-4.41z"/></svg>
                                Conectar mi cuenta de Mercado Pago
                            </a>
                            @if(!empty($profile->mp_access_token))
                                <span class="text-mp-connected">
                                    ✓ Conectado correctamente
                                </span>
                            @else
                                <span class="text-mp-disconnected">Sin vincular actualmente</span>
                            @endif
                        </div>
                    </div>

                    <div class="profile-section-divider">
                        <span class="profile-section-label">Entregas</span>
                    </div>

                    <div class="profile-field">
                        <label for="shipping_cost" class="profile-label">Costo de envío a domicilio</label>
                        <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                            <div class="flex items-center gap-2 w-full sm:w-44">
                                <span class="text-slate-400 text-sm font-semibold shrink-0">$</span>
                                <input type="number" name="shipping_cost" id="shipping_cost"
                                    step="1" min="0"
                                    value="{{ old('shipping_cost', $profile->shipping_cost ?? 0) }}"
                                    class="profile-input flex-1 @error('shipping_cost') profile-input-error @enderror">
                            </div>
                            <p class="profile-field-hint sm:pt-2">
                                Monto fijo que se suma al total cuando el cliente elige envío a domicilio. Poné 0 si el envío es sin costo.
                            </p>
                        </div>
                        @error('shipping_cost')
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
                                Multiplicador sobre el costo (ej: 3 = 300%). Se usa como margen por defecto en todos tus productos, salvo que definas uno personalizado.
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
                </div>
            </div>

        </form>{{-- /form principal --}}
        </div>{{-- /col-span-2 --}}

        {{-- ══════════════════════════════════════════
             COLUMNA DERECHA — SIDEBAR (fuera del form principal)
             ══════════════════════════════════════════ --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Vista previa --}}
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
                            <a href="{{ route('map.index') }}" class="profile-preview-link">Ver en el mapa público</a>
                        @else
                            <span class="profile-preview-muted">Agregá una dirección para aparecer en el mapa.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══ FORM CONTRASEÑA — separado del form principal ══ --}}
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

        </div>{{-- /col-span-1 sidebar --}}

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
function removeCover() {
    document.getElementById('remove-cover-input').value = '1';
    var img = document.getElementById('cover-preview-img');
    var placeholder = document.getElementById('cover-placeholder');
    var btn = document.getElementById('btn-remove-cover');
    if (img) { img.src = ''; img.style.display = 'none'; }
    if (placeholder) placeholder.style.display = 'block';
    if (btn) btn.style.display = 'none';
    var ci = document.getElementById('cover-image-input');
    if (ci) ci.value = '';
}

function removeLogo() {
    document.getElementById('remove-logo-input').value = '1';
    var previewWrap = document.getElementById('profile-logo-preview');
    var previewSidebar = document.querySelector('.profile-preview-logo');
    previewWrap.innerHTML = '<svg style="width:2rem;height:2rem;color:#9a9390;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>';
    if (previewSidebar) previewSidebar.innerHTML = '<svg class="w-7 h-7" style="color:#2d6a4f" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>';
    var logoInput = document.getElementById('profile-logo-input');
    if (logoInput) logoInput.value = '';
}

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

    var coverInput = document.getElementById('cover-image-input');
    if (coverInput) {
        coverInput.addEventListener('change', function () {
            var file = coverInput.files && coverInput.files[0];
            if (!file) return;
            document.getElementById('remove-cover-input').value = '0';
            var reader = new FileReader();
            reader.onload = function (event) {
                var img = document.getElementById('cover-preview-img');
                var placeholder = document.getElementById('cover-placeholder');
                img.src = event.target.result;
                img.style.display = 'block';
                img.classList.remove('hidden');
                if (placeholder) placeholder.style.display = 'none';
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

    function geocodeAddress(address, silent) {
        if (!address) {
            if (!silent) {
                feedback.textContent = 'Ingresá una dirección antes de ubicar.';
                feedback.classList.add('profile-field-error');
            }
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
            if (!silent) {
                feedback.textContent = 'No se encontró esa dirección. Probá con más detalle o mové el marcador manualmente.';
                feedback.classList.add('profile-field-error');
            } else {
                feedback.textContent = '';
            }
        });
    }

    document.getElementById('btn-geocode-address').addEventListener('click', function () {
        geocodeAddress(addressInput.value.trim(), false);
    });

    addressInput.addEventListener('blur', function () {
        var address = addressInput.value.trim();
        var hasCoords = latInput.value !== '' && lngInput.value !== '';
        if (address && !hasCoords) {
            geocodeAddress(address, true);
        }
    });

    // Mercado Pago Credentials Test
    var btnTestMp = document.getElementById('btn-test-mp');
    var testMpResult = document.getElementById('test-mp-result');
    var mpAccessTokenInput = document.getElementById('mp_access_token');

    if (btnTestMp) {
        btnTestMp.addEventListener('click', function () {
            var token = mpAccessTokenInput.value.trim();
            if (!token) {
                testMpResult.textContent = 'Ingresá un Access Token primero.';
                testMpResult.style.color = '#ef4444';
                return;
            }

            testMpResult.textContent = 'Probando conexión...';
            testMpResult.style.color = '#555555';

            fetch('{{ route('business_profile.mercadopago.test') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ mp_access_token: token })
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (response.ok) {
                        testMpResult.textContent = data.message;
                        testMpResult.style.color = '#10b981';
                    } else {
                        testMpResult.textContent = data.message || 'Error al validar las credenciales.';
                        testMpResult.style.color = '#ef4444';
                    }
                });
            })
            .catch(function () {
                testMpResult.textContent = 'Error de conexión con el servidor.';
                testMpResult.style.color = '#ef4444';
            });
        });
    }

    setTimeout(function () { map.invalidateSize(); }, 150);
});
</script>
@endpush
