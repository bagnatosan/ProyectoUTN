@extends('layouts.app')

@section('title', 'Gestión de Pedidos | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@section('content')
<link rel="stylesheet" href="{{ asset('css/sections/seller-reservations.css') }}">

<div class="page-banner" style="margin-bottom:1.5rem;">
  <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
  <div class="page-banner__overlay"></div>
  <div class="page-banner__content">
    <h1 class="page-banner__title">Gestión de Pedidos</h1>
    <p class="page-banner__subtitle">Revisá y gestioná las reservas de tus clientes.</p>
  </div>
</div>

<div class="seller-reservations">
  {{-- Header acciones --}}
  <div class="seller-reservations__header">
    <div class="seller-reservations__header-left">
    <div class="seller-reservations__header-right">
      <div class="seller-reservations__total-badge" id="sr-total" role="status" aria-live="polite">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>0 reservas</span>
      </div>
      <a href="{{ route('reservations.export', request()->query()) }}" class="seller-reservations__export-btn" title="Exportar a CSV">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        <span>Exportar</span>
      </a>
    </div>
  </div>

  {{-- Filters --}}
  <div class="seller-reservations__filters">
    <div class="seller-reservations__filter-group" role="tablist" aria-label="Filtros temporales">
      <button class="seller-reservations__filter-btn seller-reservations__filter-btn--active" data-sr-filter="today" role="tab" aria-selected="true">Hoy</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="tomorrow" role="tab" aria-selected="false">Mañana</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="week" role="tab" aria-selected="false">Esta Semana</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="month" role="tab" aria-selected="false">Este Mes</button>
      <button class="seller-reservations__filter-btn" data-sr-filter="all" role="tab" aria-selected="false">Todas</button>
    </div>

    <div class="seller-reservations__date-range">
      <input type="date" id="sr-date-from" class="seller-reservations__date-input" aria-label="Desde fecha" title="Desde">
      <span class="seller-reservations__date-sep">a</span>
      <input type="date" id="sr-date-to" class="seller-reservations__date-input" aria-label="Hasta fecha" title="Hasta">
    </div>

    <select id="sr-status-select" class="seller-reservations__status-select" aria-label="Filtrar por estado">
      <option value="">Todos los estados</option>
      <option value="pending">Pendiente</option>
      <option value="confirmed">Confirmada</option>
      <option value="completed">Completada</option>
      <option value="cancelled">Cancelada</option>
    </select>

    <select id="sr-sort-select" class="seller-reservations__sort-select" aria-label="Ordenar por">
      <option value="reservation_date">Fecha</option>
      <option value="client_name">Cliente</option>
      <option value="status">Estado</option>
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
              aria-label="Limpiar búsqueda">&times;</button>
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
      <p class="seller-reservations__modal-value" style="margin-bottom: 0.75rem;">¿Estás seguro de cancelar esta reserva?</p>
      <label for="sr-cancel-reason" class="seller-reservations__modal-label">Motivo de cancelación <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(opcional)</span></label>
      <textarea id="sr-cancel-reason" class="seller-reservations__cancel-reason" placeholder="Ej: El cliente solicitó la cancelación..."></textarea>
      <div class="seller-reservations__modal-actions">
        <button type="button" class="seller-reservations__btn seller-reservations__btn--detail" onclick="document.getElementById('sr-cancel-overlay').classList.remove('seller-reservations__modal-overlay--visible'); document.body.style.overflow = '';">Volver</button>
        <button type="button" id="sr-cancel-confirm" class="seller-reservations__btn seller-reservations__btn--cancel">Sí, cancelar</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/sections/seller-reservations.js') }}"></script>
@endsection
