@extends('layouts.app')

@section('title', 'Mis Reservas | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections/client-reservations.css') }}">
@endpush

@section('content')
<div class="cr-history">
  {{-- Banner --}}
  <div class="page-banner">
    <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__content">
      <h1 class="page-banner__title">Mis reservas</h1>
      <p class="page-banner__subtitle">Turnos y pedidos que hiciste desde la plataforma.</p>
    </div>
  </div>

  <div style="max-width:720px;margin:0 auto;">
  <div class="cr-filters" role="tablist" aria-label="Filtrar por estado">
    <button class="cr-filters__btn cr-filters__btn--active" data-filter="all" role="tab" aria-selected="true">Todas</button>
    <button class="cr-filters__btn" data-filter="pending" role="tab">Pendientes</button>
    <button class="cr-filters__btn" data-filter="confirmed" role="tab">Confirmadas</button>
    <button class="cr-filters__btn" data-filter="completed" role="tab">Completadas</button>
    <button class="cr-filters__btn" data-filter="cancelled" role="tab">Canceladas</button>
  </div>

  <div id="cr-list" class="cr-list">
    @forelse($reservations as $reservation)
      @php
        $product = $reservation->product;
        $sellerName = $product?->businessProfile?->business_name ?? $product?->businessProfile?->user?->name ?? 'Emprendedor';
        $imagePath = $product?->image ? asset('storage/' . $product->image) : null;
        $minDate = now()->addDays(2)->format('Y-m-d');
        $canModify = $reservation->status === 'pending'
          && $reservation->reservation_date->format('Y-m-d') >= $minDate;
        $wasModified = $reservation->updated_at && $reservation->created_at
          && $reservation->updated_at->diffInMinutes($reservation->created_at) > 1;
      @endphp
      <div class="cr-card" data-status="{{ $reservation->status }}">
        @if($imagePath)
          <img class="cr-card__image" src="{{ $imagePath }}" alt="{{ $product->name }}" loading="lazy">
        @else
          <div class="cr-card__image cr-card__image--placeholder">📷</div>
        @endif

        <div class="cr-card__body">
          <div class="flex items-center justify-between mb-1">
            <h3 class="cr-card__product">{{ $product->name ?? 'Producto' }}</h3>
            <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0 ml-2">
              #{{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}
            </span>
          </div>
          <p class="cr-card__seller">{{ $sellerName }}</p>

          <div class="cr-card__meta">
            <span class="cr-card__meta-item">
              {{ $reservation->reservation_date->format('d/m/Y') }}
            </span>
            <span class="cr-card__meta-item">
              {{ \Illuminate\Support\Str::of($reservation->reservation_time)->substr(0, 5) }} hs
            </span>
            @if(($reservation->quantity ?? 1) > 1)
              <span class="cr-card__meta-item">{{ $reservation->quantity }} unid.</span>
            @endif
            @if($product && $product->price > 0)
              <span class="cr-card__meta-item cr-card__price">${{ number_format($product->price * ($reservation->quantity ?? 1), 2) }}</span>
            @endif
          </div>

          @if($wasModified)
            <p class="cr-card__modified">Modificada</p>
          @endif

          @if($reservation->notes)
            <p class="cr-card__notes">{{ $reservation->notes }}</p>
          @endif

          @if($reservation->status === 'cancelled' && $reservation->cancellation_reason)
            <p class="cr-card__cancel-reason">Motivo: {{ $reservation->cancellation_reason }}</p>
          @endif
        </div>

        <div class="cr-card__actions">
          <span class="cr-badge cr-badge--{{ $reservation->status }}">
            @switch($reservation->status)
              @case('pending') Pendiente @break
              @case('confirmed') Confirmada @break
              @case('completed') Completada @break
              @case('cancelled') Cancelada @break
              @default Desconocido
            @endswitch
          </span>

          @if($canModify)
            <a href="{{ route('reservations.edit', $reservation) }}" class="cr-btn cr-btn--primary">Modificar</a>
          @endif

          @if($reservation->isCancellable())
            <button type="button"
                    class="cr-btn cr-btn--danger cr-cancel-trigger"
                    data-id="{{ $reservation->id }}"
                    data-product="{{ $product->name ?? 'Producto' }}"
                    data-date="{{ $reservation->reservation_date->format('d/m/Y') }}"
                    data-time="{{ \Illuminate\Support\Str::of($reservation->reservation_time)->substr(0, 5) }}">
              Cancelar
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="cr-empty">
        <p class="cr-empty__title">No hay reservas</p>
        <p class="cr-empty__text">Explorá los catálogos y reservá un producto o turno.</p>
        <a href="{{ route('dashboard') }}" class="cr-empty__link">Ver catálogos</a>
      </div>
    @endforelse
  </div>

  <div id="cr-empty-filter" class="cr-empty" style="display:none">
    <p class="cr-empty__title" id="cr-empty-text">No hay reservas en este estado</p>
    <p class="cr-empty__text">Probá con otro filtro.</p>
  </div>
</div>

<!-- Cancel Modal -->
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

<script>
document.addEventListener('DOMContentLoaded', function () {
  /* ---- Filtros ---- */
  var filterBtns = document.querySelectorAll('.cr-filters__btn');
  var cards = document.querySelectorAll('.cr-card');
  var list = document.getElementById('cr-list');
  var emptyFilter = document.getElementById('cr-empty-filter');
  var emptyText = document.getElementById('cr-empty-text');

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterBtns.forEach(function (b) {
        b.classList.remove('cr-filters__btn--active');
        b.setAttribute('aria-selected', 'false');
      });
      this.classList.add('cr-filters__btn--active');
      this.setAttribute('aria-selected', 'true');

      var filter = this.getAttribute('data-filter');
      var visibleCount = 0;

      cards.forEach(function (card) {
        if (filter === 'all' || card.getAttribute('data-status') === filter) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      var originalEmpty = document.querySelector('.cr-empty');
      if (originalEmpty && filter !== 'all') {
        originalEmpty.style.display = 'none';
      }

      if (visibleCount === 0) {
        var labels = { pending: 'pendientes', confirmed: 'confirmadas', completed: 'completadas', cancelled: 'canceladas' };
        emptyText.textContent = 'No hay reservas ' + (labels[filter] || '');
        emptyFilter.style.display = '';
        if (list) list.style.display = 'none';
      } else {
        emptyFilter.style.display = 'none';
        if (list) list.style.display = '';
      }
    });
  });

  /* ---- Cancelación ---- */
  var modal = document.getElementById('cr-cancel-modal');
  var modalInfo = document.getElementById('cr-cancel-info');
  var modalClose = document.getElementById('cr-cancel-close');
  var modalConfirm = document.getElementById('cr-cancel-confirm');
  var cancelReason = document.getElementById('cr-cancel-reason');
  var currentId = null;

  document.querySelectorAll('.cr-cancel-trigger').forEach(function (btn) {
    btn.addEventListener('click', function () {
      currentId = this.getAttribute('data-id');
      var product = this.getAttribute('data-product');
      var date = this.getAttribute('data-date');
      var time = this.getAttribute('data-time');

      modalInfo.textContent = '¿Estás seguro de cancelar la reserva de "' + product + '" para el ' + date + ' a las ' + time + '?';
      cancelReason.value = '';
      modal.classList.remove('cr-modal-overlay--hidden');
    });
  });

  function closeCancelModal() {
    modal.classList.add('cr-modal-overlay--hidden');
    currentId = null;
  }

  modalClose.addEventListener('click', closeCancelModal);
  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeCancelModal();
  });

  modalConfirm.addEventListener('click', function () {
    if (!currentId) return;

    var formData = new FormData();
    formData.append('reason', cancelReason.value);

    fetch('/reservations/' + currentId + '/cancel', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: formData,
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) {
        location.reload();
      } else {
        alert(data.message || 'Error al cancelar la reserva.');
      }
    })
    .catch(function () {
      alert('Error de conexión. Intentalo de nuevo.');
    })
    .finally(function () {
      closeCancelModal();
    });
  });
});
</script>
  </div>
@endsection
