@extends('layouts.app')

@section('title', 'Gesti\u00F3n de Pedidos | ProyectoUTN')

@section('content')
<link rel="stylesheet" href="{{ asset('css/sections/seller-reservations.css') }}">

<div class="seller-reservations">
  {{-- Header --}}
  <div class="seller-reservations__header">
    <div class="seller-reservations__header-left">
      <div class="seller-reservations__icon">
        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
        </svg>
      </div>
      <div>
        <h1 class="seller-reservations__title">Gesti\u00F3n de Pedidos</h1>
        <p class="seller-reservations__subtitle">Administr\u00E1 las reservas de tus productos.</p>
      </div>
    </div>
    <div class="seller-reservations__total-badge" id="sr-total" role="status" aria-live="polite">
      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span>0 reservas</span>
    </div>
  </div>

  {{-- Filters --}}
  <div class="seller-reservations__filters">
    <div class="seller-reservations__filter-group" role="tablist" aria-label="Filtros temporales">
      <button class="seller-reservations__filter-btn seller-reservations__filter-btn--active" data-sr-filter="today" role="tab" aria-selected="true">Hoy</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="tomorrow" role="tab" aria-selected="false">Ma\u00F1ana</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="week" role="tab" aria-selected="false">Esta Semana</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="month" role="tab" aria-selected="false">Este Mes</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="all" role="tab" aria-selected="false">Todas</button>
    </div>

    <select id="sr-status-select" class="seller-reservations__status-select" aria-label="Filtrar por estado">
      <option value="">Todos los estados</option>
      <option value="pending">Pendiente</option>
      <option value="confirmed">Confirmada</option>
      <option value="completed">Completada</option>
      <option value="cancelled">Cancelada</option>
    </select>

    <div class="seller-reservations__search-wrapper">
      <svg class="seller-reservations__search-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
      </svg>
      <input type="search"
             id="sr-search-input"
             class="seller-reservations__search-input"
             placeholder="Buscar cliente o producto..."
             aria-label="Buscar reservas"
             autocomplete="off">
      <button type="button"
              id="sr-search-clear"
              class="seller-reservations__search-clear"
              aria-label="Limpiar b\u00FAsqueda">&times;</button>
    </div>
  </div>

  {{-- Loader --}}
  <div id="sr-loader" class="seller-reservations__loader" aria-hidden="true">
    <div class="seller-reservations__spinner"></div>
    <span class="seller-reservations__loader-text">Cargando pedidos...</span>
  </div>

  {{-- Error state --}}
  <div id="sr-error" class="seller-reservations__error" role="alert">
    <svg class="seller-reservations__error-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
    </svg>
    <p class="seller-reservations__error-text" id="sr-error-text">Error al cargar los pedidos.</p>
  </div>

  {{-- Empty state --}}
  <div id="sr-empty" class="seller-reservations__empty">
    <svg class="seller-reservations__empty-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
    </svg>
    <p class="seller-reservations__empty-title">No hay pedidos</p>
    <p class="seller-reservations__empty-text">No se encontraron reservas para los filtros seleccionados.</p>
  </div>

  {{-- Cards grid --}}
  <div id="sr-grid" class="seller-reservations__grid" aria-live="polite"></div>

  {{-- Pagination --}}
  <div id="sr-pagination" class="seller-reservations__pagination sr-hidden"></div>
</div>

{{-- Detail Modal --}}
<div id="sr-modal-overlay" class="seller-reservations__modal-overlay" role="dialog" aria-modal="true" aria-labelledby="sr-modal-title">
  <div class="seller-reservations__modal">
    <div class="seller-reservations__modal-header">
      <h2 id="sr-modal-title" class="seller-reservations__modal-title">Detalle de la Reserva</h2>
      <button type="button" class="seller-reservations__modal-close" aria-label="Cerrar" onclick="document.getElementById('sr-modal-overlay').classList.remove('seller-reservations__modal-overlay--visible'); document.body.style.overflow = '';">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div id="sr-modal-body"></div>
  </div>
</div>

{{-- Cancel Modal --}}
<div id="sr-cancel-overlay" class="seller-reservations__modal-overlay" role="dialog" aria-modal="true" aria-labelledby="sr-cancel-title">
  <div class="seller-reservations__modal">
    <div class="seller-reservations__modal-header">
      <h2 id="sr-cancel-title" class="seller-reservations__modal-title">Cancelar Reserva</h2>
      <button type="button" id="sr-cancel-close" class="seller-reservations__modal-close" aria-label="Cerrar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div>
      <p class="seller-reservations__modal-value" style="margin-bottom: 0.75rem;">&iquest;Est\u00E1s seguro de cancelar esta reserva?</p>
      <label for="sr-cancel-reason" class="seller-reservations__modal-label">Motivo de cancelaci\u00F3n <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(opcional)</span></label>
      <textarea id="sr-cancel-reason" class="seller-reservations__cancel-reason" placeholder="Ej: El cliente solicit\u00F3 la cancelaci\u00F3n..."></textarea>
      <div class="seller-reservations__modal-actions">
        <button type="button" class="seller-reservations__btn seller-reservations__btn--detail" onclick="document.getElementById('sr-cancel-overlay').classList.remove('seller-reservations__modal-overlay--visible'); document.body.style.overflow = '';">Volver</button>
        <button type="button" id="sr-cancel-confirm" class="seller-reservations__btn seller-reservations__btn--cancel">S\u00ED, cancelar</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/sections/seller-reservations.js') }}"></script>
@endsection
