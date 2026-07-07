@extends('layouts.app')

@section('title', 'Agendar Reserva | ProyectoUTN')

@section('content')
<style>
  /* =========================================
     FORM FIELD COMPONENT
     ========================================= */
  .form-field {
    position: relative;
  }

  .form-field__label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.375rem;
  }

  .form-field__input {
    display: block;
    width: 100%;
    padding: 0.625rem 1rem;
    background-color: rgba(255, 255, 255, 0.85);
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    color: #0f172a;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    box-sizing: border-box;
  }

  .form-field__input::placeholder {
    color: #94a3b8;
  }

  .form-field__input:-webkit-autofill,
  .form-field__input:-webkit-autofill:hover,
  .form-field__input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px rgba(255, 255, 255, 0.92) inset !important;
    -webkit-text-fill-color: #0f172a !important;
    border-color: #cbd5e1 !important;
  }

  .form-field__input:focus {
    outline: none;
    border-color: rgba(34, 197, 94, 0.6);
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
  }

  .form-field__input--select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    padding-right: 2.5rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
  }

  .form-field__input--textarea {
    resize: vertical;
    min-height: 80px;
  }

  .form-field__input--error {
    border-color: rgba(244, 63, 94, 0.6) !important;
    box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2) !important;
  }

  .form-field__errors {
    list-style: disc;
    list-style-position: inside;
    font-size: 0.75rem;
    color: #fb7185;
    margin-top: 0.375rem;
    padding-left: 0;
  }

  .form-field__errors li {
    margin-bottom: 0.125rem;
  }

  .form-field__errors--hidden {
    display: none !important;
  }

  /* =========================================
     CALENDAR COMPONENT
     ========================================= */
  .calendar {
    border: 1px solid rgba(22, 163, 74, 0.3);
    background-color: rgba(22, 163, 74, 0.07);
    border-radius: 1rem;
    padding: 1rem;
    user-select: none;
    -webkit-user-select: none;
    max-width: 100%;
  }

  .calendar__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
  }

  .calendar__nav-btn {
    width: 2.25rem;
    height: 2.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    color: #94a3b8;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .calendar__nav-btn:hover {
    color: #fff;
    background-color: rgba(51, 65, 85, 0.6);
  }

  .calendar__nav-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
  }

  .calendar__label-container {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
  }

  .calendar__month-label {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
  }

  .calendar__year-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e3a2f;
  }

  .calendar__day-headers {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 0.5rem;
  }

  .calendar__day-header {
    text-align: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e3a2f;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.25rem 0;
  }

  .calendar__days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.25rem;
  }

  .calendar__day {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    color: #0f172a;
    background: transparent;
    padding: 0;
  }

  .calendar__day:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25), 0 0 0 3px rgba(30, 41, 59, 1);
  }

  .calendar__day:hover:not(.calendar__day--past):not(.calendar__day--empty) {
    background-color: rgba(22, 163, 74, 0.2);
    border-color: rgba(22, 163, 74, 0.45);
  }

  .calendar__day--empty {
    cursor: default;
  }

  .calendar__day--past {
    color: #334155;
    cursor: not-allowed;
  }

  .calendar__day--today {
    font-weight: 700;
    color: #15803d;
  }

  .calendar__day--selected {
    background-color: #16a34a;
    color: #fff;
    font-weight: 600;
    border-color: rgba(74, 222, 128, 0.6);
    box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2);
  }

  .calendar__day--selected:hover {
    background-color: #22c55e;
  }

  .calendar__day--available::after {
    content: '';
    position: absolute;
    bottom: 0.2rem;
    left: 50%;
    transform: translateX(-50%);
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 50%;
    background-color: #4ade80;
  }

  .calendar__day--unavailable::after {
    content: '';
    position: absolute;
    bottom: 0.2rem;
    left: 50%;
    transform: translateX(-50%);
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 50%;
    background-color: #fb7185;
  }

  /* =========================================
     TIME SLOTS COMPONENT
     ========================================= */
  .time-slots__state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    text-align: center;
    border: 1px dashed rgba(51, 65, 85, 0.5);
    border-radius: 1rem;
  }

  .time-slots__icon {
    color: #64748b;
    margin-bottom: 0.75rem;
  }

  .time-slots__text {
    font-size: 0.875rem;
    color: #94a3b8;
    max-width: 20rem;
  }

  .time-slots__spinner {
    width: 2rem;
    height: 2rem;
    border: 2px solid #475569;
    border-top-color: #4ade80;
    border-radius: 50%;
    margin-bottom: 0.75rem;
    animation: time-slots-spin 0.8s linear infinite;
  }

  @keyframes time-slots-spin {
    to { transform: rotate(360deg); }
  }

  .time-slots__wrapper {
    border: 1px solid rgba(22, 163, 74, 0.3);
    background-color: rgba(22, 163, 74, 0.07);
    border-radius: 1rem;
    padding: 1rem;
  }

  .time-slots__heading {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0.75rem;
  }

  .time-slots__grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
  }

  @media (min-width: 641px) {
    .time-slots__grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (min-width: 1024px) {
    .time-slots__grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  .time-slots__slot {
    padding: 0.625rem 0.75rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    border: 1px solid rgba(22, 163, 74, 0.35);
    background-color: rgba(22, 163, 74, 0.1);
    color: #0f172a;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .time-slots__slot:hover {
    background-color: rgba(22, 163, 74, 0.25);
    border-color: rgba(22, 163, 74, 0.6);
    color: #0f172a;
  }

  .time-slots__slot:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
  }

  .time-slots__slot--selected {
    background-color: rgba(34, 197, 94, 0.15);
    border-color: rgba(34, 197, 94, 0.6);
    color: #4ade80;
    font-weight: 600;
    box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.1);
  }

  .time-slots__slot--selected:hover {
    background-color: rgba(34, 197, 94, 0.2);
  }

  /* =========================================
     FEEDBACK COMPONENT
     ========================================= */
  .form-feedback {
    padding: 1rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .form-feedback--hidden {
    display: none !important;
  }

  .form-feedback--success {
    border: 1px solid rgba(52, 211, 153, 0.2);
    background-color: rgba(52, 211, 153, 0.1);
    color: #6ee7b7;
  }

  .form-feedback--error {
    border: 1px solid rgba(244, 63, 94, 0.2);
    background-color: rgba(244, 63, 94, 0.1);
    color: #fb7185;
  }

  /* =========================================
     BUTTON COMPONENT
     ========================================= */
  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
  }

  .btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5), 0 0 0 4px rgba(30, 41, 59, 1);
  }

  .btn--primary {
    background: linear-gradient(to right, #16a34a, #22c55e);
    color: #fff;
    box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.25);
  }

  .btn--primary:hover {
    background: linear-gradient(to right, #22c55e, #4ade80);
    box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
  }

  .btn--loading {
    opacity: 0.6;
    pointer-events: none;
  }

  /* =========================================
     DELIVERY TOGGLE
     ========================================= */
  .delivery-toggle {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
  }

  .delivery-btn {
    flex: 1;
    padding: 0.625rem 1rem;
    border-radius: 0.75rem;
    border: 1.5px solid #334155;
    background: transparent;
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
  }

  .delivery-btn:hover {
    border-color: rgba(34, 197, 94, 0.5);
    color: #e2e8f0;
  }

  .delivery-btn--active {
    border-color: rgba(34, 197, 94, 0.7);
    background: rgba(34, 197, 94, 0.12);
    color: #4ade80;
  }

  .delivery-info {
    margin-top: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    background: rgba(226, 232, 240, 0.5);
    border: 1px solid #cbd5e1;
    font-size: 0.8rem;
    color: #475569;
  }

  .delivery-info strong {
    color: #1e293b;
  }

  /* =========================================
     QUANTITY SELECTOR
     ========================================= */
  .qty-selector {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    overflow: hidden;
    width: fit-content;
    transition: border-color 0.2s;
  }

  .qty-selector:focus-within {
    border-color: rgba(34, 197, 94, 0.6);
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
  }

  .qty-btn {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
  }

  .qty-btn:hover:not(:disabled) {
    background: rgba(22, 163, 74, 0.15);
    color: #15803d;
  }

  .qty-btn:disabled {
    color: #cbd5e1;
    cursor: not-allowed;
  }

  .qty-display {
    min-width: 3rem;
    text-align: center;
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    border-left: 1px solid #cbd5e1;
    border-right: 1px solid #cbd5e1;
    padding: 0.5rem 0.25rem;
    user-select: none;
  }

  /* =========================================
     RESPONSIVE ADJUSTMENTS
     ========================================= */
  @media (max-width: 640px) {
    .calendar__day {
      font-size: 0.8125rem;
    }
  }
</style>

<div class="max-w-5xl mx-auto py-6 sm:py-8">
  <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl shadow-xl overflow-hidden">
    <div class="p-6 sm:p-8">
      <div class="flex items-center space-x-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-600 to-green-400 flex items-center justify-center shadow-lg shadow-green-600/20">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Agendar una Reserva
          </h1>
          <p class="text-slate-400 text-sm mt-0.5">
            Completá los datos y elegí la fecha y horario que prefieras.
          </p>
        </div>
      </div>

      <div id="form-feedback" class="form-feedback form-feedback--hidden mb-6" role="status" aria-live="polite"></div>

      <form id="reservation-form" method="POST" action="{{ route('reservations.store') }}" novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
          <div class="space-y-6">
            <div>
              <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4 flex items-center space-x-2">
                <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Producto</span>
              </h2>

              <div class="form-field">
                <select name="product_id"
                        id="product_id"
                        class="form-field__input form-field__input--select w-full"
                        data-validate="product"
                        required
                        aria-required="true">
                  <option value="">Seleccioná un producto</option>
                  @foreach($products as $product)
                    <option value="{{ $product->id }}"
                            data-user-id="{{ $product->businessProfile->user_id }}"
                            {{ (old('product_id', $selectedProduct->id ?? '') == $product->id) ? 'selected' : '' }}>
                      {{ $product->name }} - ${{ number_format($product->price, 2) }}
                    </option>
                  @endforeach
                </select>
                <ul id="error-product" class="form-field__errors form-field__errors--hidden" data-error-for="product" role="alert"></ul>
              </div>

              {{-- Cantidad --}}
              <div class="form-field mt-5">
                <label class="form-field__label">
                  Cantidad <span class="text-rose-400" aria-hidden="true">*</span>
                </label>
                <div class="qty-selector" role="group" aria-label="Selector de cantidad">
                  <button type="button" class="qty-btn" id="qty-minus" aria-label="Reducir cantidad" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                  </button>
                  <span class="qty-display" id="qty-display" aria-live="polite">1</span>
                  <button type="button" class="qty-btn" id="qty-plus" aria-label="Aumentar cantidad">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                  </button>
                </div>
                <input type="hidden" name="quantity" id="quantity" value="{{ old('quantity', 1) }}">
                <p class="text-xs text-slate-500 mt-1.5">Máximo 50 unidades por reserva.</p>
              </div>

              {{-- ── Modalidad de entrega ──────────────────── --}}
              @php
                $shippingCost = $business?->shipping_cost ?? 0;
                $pickupAddress = $business?->address ?? null;
                $oldDelivery = old('delivery_type', 'pickup');
              @endphp
              <div class="form-field mt-5">
                <label class="form-field__label">
                  ¿Cómo recibís tu pedido? <span class="text-rose-400" aria-hidden="true">*</span>
                </label>
                <div class="delivery-toggle" role="group" aria-label="Modalidad de entrega">
                  <button type="button"
                          class="delivery-btn {{ $oldDelivery === 'pickup' ? 'delivery-btn--active' : '' }}"
                          id="btn-pickup" data-type="pickup">
                    🏪 Retiro en local
                  </button>
                  <button type="button"
                          class="delivery-btn {{ $oldDelivery === 'delivery' ? 'delivery-btn--active' : '' }}"
                          id="btn-delivery" data-type="delivery">
                    🏠 Envío a domicilio
                  </button>
                </div>
                <input type="hidden" name="delivery_type" id="delivery_type" value="{{ $oldDelivery }}">

                {{-- Info retiro en local --}}
                <div id="pickup-info" class="delivery-info {{ $oldDelivery === 'delivery' ? 'hidden' : '' }}">
                  @if($pickupAddress)
                    <p>📍 Retirás en: <strong>{{ $pickupAddress }}</strong></p>
                  @else
                    <p>El emprendedor te va a indicar el punto de retiro.</p>
                  @endif
                  <p class="mt-1" style="color:#16a34a;font-weight:600;">✓ Sin costo de envío</p>
                </div>

                {{-- Info envío a domicilio --}}
                <div id="delivery-info" class="{{ $oldDelivery === 'pickup' ? 'hidden' : '' }}">
                  <div class="delivery-info">
                    @if($shippingCost > 0)
                      <p>🚚 Costo de envío: <strong style="color:#16a34a;">${{ number_format($shippingCost, 2) }}</strong></p>
                    @else
                      <p style="color:#16a34a;font-weight:600;">🚚 Envío sin costo adicional</p>
                    @endif
                  </div>
                  <div class="form-field mt-2">
                    <label for="shipping_address" class="form-field__label text-sm">
                      Dirección de envío <span class="text-rose-400">*</span>
                    </label>
                    <input type="text"
                           name="shipping_address"
                           id="shipping_address"
                           class="form-field__input w-full {{ $errors->has('shipping_address') ? 'form-field__input--error' : '' }}"
                           placeholder="Ej. Av. Corrientes 1234, CABA"
                           value="{{ old('shipping_address', $clientAddress ?? '') }}">
                    @error('shipping_address')
                      <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>
              {{-- ── Fin modalidad de entrega ──────────────── --}}
            </div>

            <div>
              <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4 flex items-center space-x-2">
                <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Tus Datos</span>
              </h2>

              <div class="space-y-4">
                <div class="form-field">
                  <label for="client_name" class="form-field__label">
                    Nombre Completo <span class="text-rose-400" aria-hidden="true">*</span>
                  </label>
                  <input type="text"
                         id="client_name"
                         name="client_name"
                         class="form-field__input w-full"
                         placeholder="Ej: Juan Pérez"
                         value="{{ old('client_name', (auth()->check() && auth()->user()->role === 'client') ? auth()->user()->name : '') }}"
                         maxlength="100"
                         data-validate="name"
                         required
                         aria-required="true"
                         autocomplete="name">
                  <ul id="error-name" class="form-field__errors form-field__errors--hidden" data-error-for="name" role="alert"></ul>
                </div>

                <div class="form-field">
                  <label for="client_email" class="form-field__label">
                    Correo Electrónico <span class="text-rose-400" aria-hidden="true">*</span>
                  </label>
                  <input type="email"
                         id="client_email"
                         name="client_email"
                         class="form-field__input w-full"
                         placeholder="ejemplo@correo.com"
                         value="{{ old('client_email', (auth()->check() && auth()->user()->role === 'client') ? auth()->user()->email : '') }}"
                         data-validate="email"
                         required
                         aria-required="true"
                         autocomplete="email">
                  <ul id="error-email" class="form-field__errors form-field__errors--hidden" data-error-for="email" role="alert"></ul>
                </div>

                <div class="form-field">
                  <label for="client_phone" class="form-field__label">
                    Teléfono <span class="text-rose-400" aria-hidden="true">*</span>
                  </label>
                  <input type="tel"
                         id="client_phone"
                         name="client_phone"
                         class="form-field__input w-full"
                         placeholder="Ej: +54 11 1234-5678"
                         value="{{ old('client_phone') }}"
                         data-validate="phone"
                         required
                         aria-required="true"
                         autocomplete="tel">
                  <ul id="error-phone" class="form-field__errors form-field__errors--hidden" data-error-for="phone" role="alert"></ul>
                </div>

                <div class="form-field">
                  <label for="notes" class="form-field__label">
                    Notas del Pedido <span class="text-slate-500 text-xs">(opcional)</span>
                  </label>
                  <textarea id="notes"
                            name="notes"
                            class="form-field__input form-field__input--textarea w-full"
                            placeholder="Contanos detalles de tu pedido..."
                            maxlength="500"
                            data-validate="notes"
                            rows="3">{{ old('notes') }}</textarea>
                  <div class="flex justify-between items-center mt-1">
                    <ul id="error-notes" class="form-field__errors form-field__errors--hidden" data-error-for="notes" role="alert"></ul>
                    <span class="text-xs text-slate-500 ml-auto" data-counter-for="notes" aria-live="polite">0/500</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div>
              <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4 flex items-center space-x-2">
                <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <span>Fecha y Horario</span>
              </h2>

              <div id="calendar-container" class="mb-4"></div>

              <ul id="error-date" class="form-field__errors form-field__errors--hidden mb-3" data-error-for="date" role="alert"></ul>

              <input type="hidden" name="reservation_date" id="reservation_date" value="{{ old('reservation_date') }}">
              <input type="hidden" name="reservation_time" id="reservation_time" value="{{ old('reservation_time') }}">

              <div id="time-slots-container" class="mt-2"></div>

              <ul id="error-time" class="form-field__errors form-field__errors--hidden mt-2" data-error-for="time" role="alert"></ul>
            </div>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <p class="text-xs text-slate-500">
            <span class="text-rose-400" aria-hidden="true">*</span> Campos obligatorios
          </p>
          <button type="submit"
                  id="btn-submit-reservation"
                  class="btn btn--primary w-full sm:w-auto">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Solicitar Reserva</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{ asset('js/reservations/validation-module.js') }}"></script>
<script src="{{ asset('js/reservations/availability-service.js') }}"></script>
<script src="{{ asset('js/reservations/calendar-component.js') }}"></script>
<script src="{{ asset('js/reservations/time-slot-selector.js') }}"></script>
<script src="{{ asset('js/reservations/reservation-form.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    ReservationForm.init({
      formId: 'reservation-form',
      calendarContainerId: 'calendar-container',
      timeSlotsContainerId: 'time-slots-container',
      feedbackId: 'form-feedback',
      submitBtnId: 'btn-submit-reservation',
    });

    // ── Selector de Cantidad ──────────────────────────────
    const qtyMinus   = document.getElementById('qty-minus');
    const qtyPlus    = document.getElementById('qty-plus');
    const qtyDisplay = document.getElementById('qty-display');
    const qtyInput   = document.getElementById('quantity');
    const MAX_QTY    = 50;

    let qty = parseInt(qtyInput.value) || 1;

    function updateQty(newQty) {
      qty = Math.max(1, Math.min(MAX_QTY, newQty));
      qtyDisplay.textContent = qty;
      qtyInput.value = qty;
      qtyMinus.disabled = qty <= 1;
      qtyPlus.disabled  = qty >= MAX_QTY;
    }

    updateQty(qty); // Estado inicial

    qtyMinus.addEventListener('click', () => updateQty(qty - 1));
    qtyPlus.addEventListener('click',  () => updateQty(qty + 1));

    // ── Toggle Modalidad de Entrega ───────────────────────
    const btnPickup     = document.getElementById('btn-pickup');
    const btnDelivery   = document.getElementById('btn-delivery');
    const deliveryInput = document.getElementById('delivery_type');
    const pickupInfo    = document.getElementById('pickup-info');
    const deliveryInfo  = document.getElementById('delivery-info');
    const shippingAddr  = document.getElementById('shipping_address');

    function setDeliveryType(type) {
      deliveryInput.value = type;

      if (type === 'pickup') {
        btnPickup.classList.add('delivery-btn--active');
        btnDelivery.classList.remove('delivery-btn--active');
        pickupInfo.classList.remove('hidden');
        deliveryInfo.classList.add('hidden');
        if (shippingAddr) shippingAddr.removeAttribute('required');
      } else {
        btnDelivery.classList.add('delivery-btn--active');
        btnPickup.classList.remove('delivery-btn--active');
        deliveryInfo.classList.remove('hidden');
        pickupInfo.classList.add('hidden');
        if (shippingAddr) shippingAddr.setAttribute('required', 'required');
      }
    }

    btnPickup.addEventListener('click',   () => setDeliveryType('pickup'));
    btnDelivery.addEventListener('click', () => setDeliveryType('delivery'));

    // Estado inicial
    setDeliveryType(deliveryInput.value || 'pickup');
  });
</script>
@endsection
