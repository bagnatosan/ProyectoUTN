@extends('layouts.app')

@section('title', 'Mis Reservas | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections/client-reservations.css') }}">
<link rel="stylesheet" href="{{ asset('css/sections/my-reservations.css') }}">
@endpush

@section('content')
<div class="mr-page">

  <div class="page-banner">
    <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__content">
      <h1 class="page-banner__title">Mis reservas</h1>
      <p class="page-banner__subtitle">Todos tus pedidos y turnos en un solo lugar. Filtralos, buscalos y gestionálos.</p>
    </div>
  </div>

  <div class="mr-layout">

    <div class="mr-filters" id="mr-filters">
      <button class="mr-filters__toggle" id="mr-filters-toggle" type="button">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
        </svg>
        <span>Filtros</span>
        <svg class="mr-filters__toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
      </button>

      <div class="mr-filters__panel" id="mr-filters-panel">

        <div class="mr-filters__row mr-filters__row--search">
          <div class="mr-search">
            <svg class="mr-search__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="search" class="mr-search__input" id="mr-search" placeholder="Buscar por producto, notas, vendedor..." autocomplete="off">
          </div>
          <button class="mr-btn mr-btn--clear" id="mr-clear-filters" type="button">Limpiar</button>
        </div>

        <div class="mr-filters__row mr-filters__row--selects">
          <select class="mr-select" id="mr-filter-status" data-filter="status">
            <option value="">Todos los estados</option>
            <option value="pending">Pendientes</option>
            <option value="confirmed">Confirmadas</option>
            <option value="completed">Completadas</option>
            <option value="cancelled">Canceladas</option>
          </select>

          <select class="mr-select" id="mr-filter-product" data-filter="product_id">
            <option value="">Todos los productos</option>
            @foreach($products as $product)
              <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
          </select>

          <select class="mr-select" id="mr-filter-sort" data-filter="sort">
            <option value="date_desc">Más próximas primero</option>
            <option value="date_asc">Más lejanas primero</option>
            <option value="created_desc">Más recientes creadas</option>
            <option value="created_asc">Más antiguas creadas</option>
          </select>
        </div>

        <div class="mr-quick-filters" id="mr-quick-filters">
          <button class="mr-quick-btn" data-quick="today">Hoy</button>
          <button class="mr-quick-btn" data-quick="tomorrow">Mañana</button>
          <button class="mr-quick-btn" data-quick="week">Esta semana</button>
          <button class="mr-quick-btn" data-quick="month">Este mes</button>
          <button class="mr-quick-btn" data-quick="next_7_days">Próximos 7 días</button>
          <button class="mr-quick-btn" data-quick="next_30_days">Próximos 30 días</button>
          <button class="mr-quick-btn" data-quick="upcoming">Próximas</button>
          <button class="mr-quick-btn" data-quick="past">Pasadas</button>
        </div>

        <div class="mr-filters__row mr-filters__row--dates">
          <div class="mr-field">
            <label class="mr-field__label" for="mr-date-from">Desde</label>
            <input type="date" class="mr-field__input" id="mr-date-from" data-filter="date_from">
          </div>
          <div class="mr-field">
            <label class="mr-field__label" for="mr-date-to">Hasta</label>
            <input type="date" class="mr-field__input" id="mr-date-to" data-filter="date_to">
          </div>
          <div class="mr-field">
            <label class="mr-field__label" for="mr-filter-scope">Ciclo</label>
            <select class="mr-field__input" id="mr-filter-scope" data-filter="reservation_scope">
              <option value="">Todas</option>
              <option value="upcoming">Futuras</option>
              <option value="past">Pasadas</option>
              <option value="active">Activas</option>
              <option value="closed">Cerradas</option>
            </select>
          </div>
          <div class="mr-field mr-field--checkbox">
            <label class="mr-field__label-check">
              <input type="checkbox" id="mr-filter-notes" data-filter="has_notes" value="1">
              <span>Solo con notas</span>
            </label>
          </div>
        </div>

      </div>
    </div>

    <div class="mr-results" id="mr-results">
      <div class="mr-results__header">
        <p class="mr-results__count" id="mr-results-count"></p>
      </div>

      <div class="mr-loader" id="mr-loader">
        <div class="mr-loader__spinner"></div>
        <p class="mr-loader__text">Cargando reservas...</p>
      </div>

      <div class="mr-empty" id="mr-empty" style="display:none">
        <p class="mr-empty__title" id="mr-empty-title">No se encontraron reservas</p>
        <p class="mr-empty__text" id="mr-empty-text">Probá cambiando los filtros o creá una nueva reserva.</p>
      </div>

      <div class="mr-error" id="mr-error" style="display:none">
        <p class="mr-error__title">Error de conexión</p>
        <p class="mr-error__text">No se pudieron cargar las reservas. Verificá tu conexión e intentá de nuevo.</p>
        <button class="mr-btn mr-btn--retry" id="mr-retry-btn">Reintentar</button>
      </div>

      <div class="cr-list" id="mr-list"></div>

      <div class="mr-load-more" id="mr-load-more" style="display:none">
        <button class="mr-btn mr-btn--load-more" id="mr-load-more-btn">Cargar más reservas</button>
      </div>
    </div>

  </div>
</div>

<div id="cr-cancel-modal" class="cr-modal-overlay cr-modal-overlay--hidden" role="dialog" aria-modal="true">
  <div class="cr-modal">
    <h2 class="cr-modal__title">Cancelar reserva</h2>
    <p class="cr-modal__text" id="cr-cancel-info"></p>

    <div>
      <label class="cr-modal__label" for="cr-cancel-reason">Motivo <span style="color:var(--cr-clr-muted);font-weight:400">(opcional)</span></label>
      <textarea id="cr-cancel-reason" class="cr-modal__textarea" placeholder="Contanos por qué cancelás..."></textarea>
    </div>

    <div class="cr-modal__footer">
      <button type="button" id="cr-cancel-close" class="cr-btn">Volver</button>
      <button type="button" id="cr-cancel-confirm" class="cr-btn cr-btn--danger" style="background:var(--cr-clr-rose);border-color:var(--cr-clr-rose);color:#fff">Sí, cancelar reserva</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sections/my-reservations.js') }}"></script>
@endpush