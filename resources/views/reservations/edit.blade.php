@extends('layouts.app')

@section('title', 'Modificar Compra | ProyectoUTN')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections/client-reservations.css') }}">
<style>
  .qty-selector {
    display: flex;
    align-items: center;
    background: #f8f5f0;
    border: 1px solid #d1c9be;
    border-radius: 0.75rem;
    overflow: hidden;
    width: fit-content;
  }
  .qty-btn {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: #6b7280;
    cursor: pointer;
    font-size: 1.25rem;
    font-weight: 600;
    transition: all 0.2s;
  }
  .qty-btn:hover:not(:disabled) {
    background: #ede8e0;
    color: #1a1a1a;
  }
  .qty-btn:disabled {
    color: #c9c3bb;
    cursor: not-allowed;
  }
  .qty-display {
    min-width: 3rem;
    text-align: center;
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a1a;
    border-left: 1px solid #d1c9be;
    border-right: 1px solid #d1c9be;
    padding: 0.5rem 0.25rem;
    user-select: none;
  }
  .qty-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #374151;
  }
</style>
@endpush

@section('content')
<div class="cr-edit">

  <a href="{{ route('reservations.index') }}" class="cr-form__cancel" style="display:inline-flex;margin-bottom:16px">
    &larr; Volver a mis compras
  </a>

  <h1 class="cr-edit__heading">Modificar compra</h1>
  <p class="cr-edit__sub">Cambiá el producto, la fecha, el horario o agregá notas a tu compra.</p>

  <div class="cr-alert cr-alert--warning" id="cr-warning-advance">
    Solo podés modificar compras con más de 2 días de anticipación.
  </div>

  <form id="cr-edit-form" method="POST" action="{{ route('reservations.update', $reservation) }}" novalidate>
    @csrf
    @method('PUT')

    {{-- Producto --}}
    <div class="cr-form__group">
      <label for="product_id" class="cr-form__label cr-form__label--required">Producto</label>
      <select name="product_id" id="product_id"
              class="cr-form__select @error('product_id') cr-form__select--error @enderror">
        <option value="">Seleccioná un producto</option>
        @foreach($products as $product)
          <option value="{{ $product->id }}" {{ old('product_id', $reservation->product_id) == $product->id ? 'selected' : '' }}>
            {{ $product->name }} @if($product->price > 0)- ${{ number_format($product->price, 2) }}@endif
          </option>
        @endforeach
      </select>
      @error('product_id')
        <p class="cr-form__error">{{ $message }}</p>
      @enderror
    </div>

    {{-- Cantidad --}}
    <div class="cr-form__group">
      <label class="qty-label">Cantidad <span style="color:#ef4444">*</span></label>
      <div class="qty-selector" role="group" aria-label="Selector de cantidad">
        <button type="button" class="qty-btn" id="qty-minus" aria-label="Reducir cantidad">−</button>
        <span class="qty-display" id="qty-display" aria-live="polite">{{ old('quantity', $reservation->quantity ?? 1) }}</span>
        <button type="button" class="qty-btn" id="qty-plus" aria-label="Aumentar cantidad">+</button>
      </div>
      <input type="hidden" name="quantity" id="quantity" value="{{ old('quantity', $reservation->quantity ?? 1) }}">
      <p style="font-size:0.75rem;color:#9ca3af;margin-top:0.375rem;">Máximo 50 unidades por compra.</p>
    </div>

    {{-- Fecha --}}
    <div class="cr-form__group">
      <label for="reservation_date" class="cr-form__label cr-form__label--required">Nueva fecha</label>
      <input type="date" name="reservation_date" id="reservation_date"
             class="cr-form__input @error('reservation_date') cr-form__input--error @enderror"
             value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}"
             min="{{ now()->addDays(2)->format('Y-m-d') }}">
      @error('reservation_date')
        <p class="cr-form__error">{{ $message }}</p>
      @enderror
    </div>

    {{-- Horarios --}}
    <div class="cr-form__group">
      <label class="cr-form__label cr-form__label--required">Horario disponible</label>
      @php $currentTimeValue = old('reservation_time', substr($reservation->reservation_time, 0, 5)); @endphp
      <input type="hidden" name="reservation_time" id="reservation_time" value="{{ $currentTimeValue }}">

      <div id="cr-slots-container">
        <div class="cr-slots__loading" id="cr-slots-loading">Cargando horarios disponibles...</div>
      </div>
      @error('reservation_time')
        <p class="cr-form__error">{{ $message }}</p>
      @enderror
    </div>

    {{-- Notas --}}
    <div class="cr-form__group">
      <label for="notes" class="cr-form__label">
        Notas <span class="cr-form__optional">(opcional)</span>
      </label>
      <textarea name="notes" id="notes"
                class="cr-form__textarea @error('notes') cr-form__textarea--error @enderror"
                rows="3" placeholder="Alguna aclaración para el emprendedor...">{{ old('notes', $reservation->notes) }}</textarea>
      @error('notes')
        <p class="cr-form__error">{{ $message }}</p>
      @enderror
    </div>

    {{-- Acciones --}}
    <div class="cr-form__actions">
      <button type="submit" class="cr-form__submit" id="cr-submit">Guardar cambios</button>
      <a href="{{ route('reservations.index') }}" class="cr-form__cancel">Cancelar</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var sellerId = {{ $sellerId }};
  var currentTime = '{{ $currentTimeValue }}';
  var dateInput = document.getElementById('reservation_date');
  var timeInput = document.getElementById('reservation_time');
  var slotsContainer = document.getElementById('cr-slots-container');
  var form = document.getElementById('cr-edit-form');

  function fetchSlots(date) {
    if (!date) {
      slotsContainer.innerHTML = '<div class="cr-slots__empty">Seleccioná una fecha primero.</div>';
      return;
    }

    slotsContainer.innerHTML = '<div class="cr-slots__loading">Cargando horarios disponibles...</div>';

    fetch('/available-slots/' + sellerId + '/' + date + '?exclude_reservation={{ $reservation->id }}')
      .then(function (res) {
        if (!res.ok) throw new Error('Error de red');
        return res.json();
      })
      .then(function (data) {
        var slots = data.slots || data || [];
        renderSlots(slots);
      })
      .catch(function () {
        slotsContainer.innerHTML = '<div class="cr-slots__empty">Error al cargar horarios. Intentalo de nuevo.</div>';
      });
  }

  function renderSlots(slots) {
    if (!slots || slots.length === 0) {
      slotsContainer.innerHTML = '<div class="cr-slots__empty">No hay horarios disponibles para esta fecha.</div>';
      timeInput.value = '';
      return;
    }

    var html = '<div class="cr-slots">';
    for (var i = 0; i < slots.length; i++) {
      var slot = slots[i];
      var selected = (slot === currentTime) ? ' cr-slots__btn--selected' : '';
      html += '<button type="button" class="cr-slots__btn' + selected + '" data-time="' + slot + '">' + slot + '</button>';
    }
    html += '</div>';
    slotsContainer.innerHTML = html;

    var buttons = slotsContainer.querySelectorAll('.cr-slots__btn');
    for (var j = 0; j < buttons.length; j++) {
      buttons[j].addEventListener('click', function () {
        var selected = slotsContainer.querySelectorAll('.cr-slots__btn--selected');
        for (var k = 0; k < selected.length; k++) {
          selected[k].classList.remove('cr-slots__btn--selected');
        }
        this.classList.add('cr-slots__btn--selected');
        timeInput.value = this.getAttribute('data-time');
      });
    }

    if (currentTime) {
      var match = slotsContainer.querySelector('.cr-slots__btn[data-time="' + currentTime + '"]');
      if (match) {
        match.classList.add('cr-slots__btn--selected');
        timeInput.value = currentTime;
      }
    }
  }

  if (dateInput.value) {
    fetchSlots(dateInput.value);
  }

  dateInput.addEventListener('change', function () {
    timeInput.value = '';
    fetchSlots(this.value);
  });

  form.addEventListener('submit', function (e) {
    var errors = [];
    var product = document.getElementById('product_id');

    if (!product.value) {
      errors.push('Seleccioná un producto.');
    }

    if (!dateInput.value) {
      errors.push('Seleccioná una fecha.');
    }

    if (!timeInput.value) {
      errors.push('Seleccioná un horario disponible.');
    }

    if (errors.length > 0) {
      e.preventDefault();
      alert('Corregí los siguientes errores:\n\n- ' + errors.join('\n- '));
    }
  });

  // ── Selector de Cantidad ──────────────────────────────
  var qtyMinus   = document.getElementById('qty-minus');
  var qtyPlus    = document.getElementById('qty-plus');
  var qtyDisplay = document.getElementById('qty-display');
  var qtyInput   = document.getElementById('quantity');
  var MAX_QTY    = 50;
  var qty        = parseInt(qtyInput.value) || 1;

  function updateQty(newQty) {
    qty = Math.max(1, Math.min(MAX_QTY, newQty));
    qtyDisplay.textContent = qty;
    qtyInput.value = qty;
    qtyMinus.disabled = qty <= 1;
    qtyPlus.disabled  = qty >= MAX_QTY;
  }

  updateQty(qty);
  qtyMinus.addEventListener('click', function () { updateQty(qty - 1); });
  qtyPlus.addEventListener('click',  function () { updateQty(qty + 1); });
});
</script>
@endsection
