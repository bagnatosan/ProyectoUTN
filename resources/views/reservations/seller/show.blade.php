@extends('layouts.app')

@section('title', 'Detalle de Reserva | ProyectoUTN')

@section('content')
<style>
  /* =============================================
     sr-detail — Detalle de Reserva (Vendedor)
     BEM · Mobile-First · Dark Theme
     ============================================= */

  :root {
    --sd-bg-card: #ffffff;
    --sd-border: #e8e0d0;
    --sd-text: #1a1918;
    --sd-text-muted: #6a6966;
    --sd-text-secondary: #8a8986;
    --sd-primary: #2d8c4e;
    --sd-primary-dim: rgba(45, 140, 78, 0.1);
    --sd-warning: #e09010;
    --sd-warning-dim: rgba(224, 144, 16, 0.1);
    --sd-info: #f5a623;
    --sd-info-dim: rgba(245, 166, 35, 0.1);
    --sd-success: #2d8c4e;
    --sd-success-dim: rgba(45, 140, 78, 0.1);
    --sd-danger: #c64545;
    --sd-danger-dim: rgba(198, 69, 69, 0.1);
    --sd-input-bg: #f9f7f2;
    --sd-radius: 1rem;
    --sd-radius-sm: 0.75rem;
    --sd-radius-xs: 0.5rem;
  }

  .sr-detail {
    max-width: 56rem;
    margin: 0 auto;
    padding: 1.5rem 0;
  }

  /* --- Back Link --- */
  .sr-detail__back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--sd-text-muted);
    text-decoration: none;
    margin-bottom: 1.5rem;
    transition: color 0.2s;
  }

  .sr-detail__back:hover {
    color: var(--sd-text);
  }

  .sr-detail__back svg {
    width: 1rem;
    height: 1rem;
  }

  /* --- Header --- */
  .sr-detail__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
  }

  .sr-detail__client-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
  }

  .sr-detail__avatar {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: var(--sd-primary-dim);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sd-primary);
    font-size: 1.25rem;
    font-weight: 700;
    flex-shrink: 0;
  }

  .sr-detail__client-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--sd-text);
    word-break: break-word;
  }

  .sr-detail__client-meta {
    font-size: 0.8125rem;
    color: var(--sd-text-secondary);
    margin-top: 0.25rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem 1rem;
  }

  .sr-detail__client-meta a {
    color: var(--sd-info);
    text-decoration: none;
  }

  .sr-detail__client-meta a:hover {
    text-decoration: underline;
  }

  .sr-detail__badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 1rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .sr-detail__badge-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
  }

  .sr-detail__badge--pending {
    background: var(--sd-warning-dim);
    color: var(--sd-warning);
    border: 1px solid rgba(234, 179, 8, 0.2);
  }
  .sr-detail__badge--pending .sr-detail__badge-dot { background: var(--sd-warning); }

  .sr-detail__badge--confirmed {
    background: var(--sd-info-dim);
    color: var(--sd-info);
    border: 1px solid rgba(56, 189, 248, 0.2);
  }
  .sr-detail__badge--confirmed .sr-detail__badge-dot { background: var(--sd-info); }

  .sr-detail__badge--completed {
    background: var(--sd-success-dim);
    color: var(--sd-success);
    border: 1px solid rgba(74, 222, 128, 0.2);
  }
  .sr-detail__badge--completed .sr-detail__badge-dot { background: var(--sd-success); }

  .sr-detail__badge--cancelled {
    background: var(--sd-danger-dim);
    color: var(--sd-danger);
    border: 1px solid rgba(244, 63, 94, 0.2);
  }
  .sr-detail__badge--cancelled .sr-detail__badge-dot { background: var(--sd-danger); }

  /* --- Section Card --- */
  .sr-detail__section {
    background: var(--sd-bg-card);
    border: 1px solid var(--sd-border);
    border-radius: var(--sd-radius);
    padding: 1.25rem;
    margin-bottom: 1rem;
  }

  .sr-detail__section-title {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--sd-text-secondary);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .sr-detail__section-title svg {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
  }

  .sr-detail__grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  @media (min-width: 640px) {
    .sr-detail__grid {
      grid-template-columns: 1fr 1fr;
    }
  }

  .sr-detail__field {
    min-width: 0;
  }

  .sr-detail__field-label {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--sd-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 0.25rem;
    display: block;
  }

  .sr-detail__field-value {
    font-size: 0.9375rem;
    color: var(--sd-text);
    word-break: break-word;
  }

  .sr-detail__field-value--muted {
    color: var(--sd-text-muted);
    font-size: 0.8125rem;
  }

  .sr-detail__field-value--price {
    color: var(--sd-primary);
    font-weight: 600;
  }

  .sr-detail__field-value--cost {
    color: var(--sd-text-muted);
  }

  /* --- Product Row --- */
  .sr-detail__product {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
  }

  .sr-detail__product-image {
    width: 6rem;
    height: 6rem;
    border-radius: var(--sd-radius-sm);
    object-fit: cover;
    background: var(--sd-input-bg);
    border: 1px solid var(--sd-border);
    flex-shrink: 0;
  }

  .sr-detail__product-image--placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sd-text-secondary);
  }

  .sr-detail__product-image--placeholder svg {
    width: 2rem;
    height: 2rem;
  }

  .sr-detail__product-info {
    min-width: 0;
    flex: 1;
  }

  .sr-detail__product-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--sd-text);
    margin-bottom: 0.375rem;
  }

  .sr-detail__product-desc {
    font-size: 0.8125rem;
    color: var(--sd-text-muted);
    line-height: 1.5;
    margin-bottom: 0.75rem;
  }

  .sr-detail__product-prices {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
  }

  .sr-detail__product-price-item {
    display: flex;
    flex-direction: column;
  }

  .sr-detail__product-price-label {
    font-size: 0.625rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--sd-text-secondary);
  }

  .sr-detail__product-price-amount {
    font-size: 1rem;
    font-weight: 700;
  }

  .sr-detail__product-price-amount--sale {
    color: var(--sd-primary);
  }

  .sr-detail__product-price-amount--cost {
    color: var(--sd-text-muted);
  }

  /* --- Notes --- */
  .sr-detail__notes {
    font-size: 0.875rem;
    color: var(--sd-text-muted);
    line-height: 1.5;
    font-style: italic;
    background: var(--sd-input-bg);
    border-radius: var(--sd-radius-xs);
    padding: 0.75rem;
  }

  .sr-detail__notes--empty {
    color: var(--sd-text-secondary);
    font-style: normal;
  }

  /* --- Timeline --- */
  .sr-detail__timeline {
    position: relative;
    padding-left: 1.5rem;
  }

  .sr-detail__timeline::before {
    content: '';
    position: absolute;
    left: 0.4375rem;
    top: 0.375rem;
    bottom: 0.375rem;
    width: 2px;
    background: var(--sd-border);
  }

  .sr-detail__timeline-item {
    position: relative;
    padding-bottom: 1rem;
    padding-left: 0.75rem;
  }

  .sr-detail__timeline-item:last-child {
    padding-bottom: 0;
  }

  .sr-detail__timeline-dot {
    position: absolute;
    left: -1.5rem;
    top: 0.375rem;
    width: 0.875rem;
    height: 0.875rem;
    border-radius: 50%;
    border: 2px solid var(--sd-border);
    background: var(--sd-bg-card);
  }

  .sr-detail__timeline-dot--active {
    border-color: var(--sd-primary);
    background: var(--sd-primary-dim);
  }

  .sr-detail__timeline-dot--warning {
    border-color: var(--sd-warning);
    background: var(--sd-warning-dim);
  }

  .sr-detail__timeline-dot--danger {
    border-color: var(--sd-danger);
    background: var(--sd-danger-dim);
  }

  .sr-detail__timeline-dot--success {
    border-color: var(--sd-success);
    background: var(--sd-success-dim);
  }

  .sr-detail__timeline-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--sd-text);
  }

  .sr-detail__timeline-date {
    font-size: 0.75rem;
    color: var(--sd-text-secondary);
    margin-top: 0.125rem;
  }

  .sr-detail__timeline-reason {
    font-size: 0.75rem;
    color: var(--sd-danger);
    margin-top: 0.125rem;
  }

  /* --- Seller Notes --- */
  .sr-detail__textarea {
    width: 100%;
    padding: 0.75rem;
    background: var(--sd-input-bg);
    border: 1px solid var(--sd-border);
    border-radius: var(--sd-radius-xs);
    color: var(--sd-text);
    font-size: 0.875rem;
    font-family: inherit;
    resize: vertical;
    min-height: 5rem;
    box-sizing: border-box;
    transition: border-color 0.2s;
  }

  .sr-detail__textarea:focus {
    outline: none;
    border-color: var(--sd-primary);
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
  }

  .sr-detail__notes-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.75rem;
  }

  /* --- Action Buttons --- */
  .sr-detail__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--sd-border);
  }

  .sr-detail__action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: var(--sd-radius-sm);
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    font-family: inherit;
    flex: 1;
    min-width: 10rem;
  }

  .sr-detail__action-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3);
  }

  .sr-detail__action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }

  .sr-detail__action-btn svg {
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
  }

  .sr-detail__action-btn--confirm {
    background: var(--sd-primary-dim);
    border-color: rgba(34, 197, 94, 0.3);
    color: var(--sd-primary);
  }

  .sr-detail__action-btn--confirm:hover {
    background: rgba(34, 197, 94, 0.2);
  }

  .sr-detail__action-btn--complete {
    background: var(--sd-success-dim);
    border-color: rgba(74, 222, 128, 0.3);
    color: var(--sd-success);
  }

  .sr-detail__action-btn--complete:hover {
    background: rgba(74, 222, 128, 0.2);
  }

  .sr-detail__action-btn--cancel {
    background: var(--sd-danger-dim);
    border-color: rgba(244, 63, 94, 0.3);
    color: var(--sd-danger);
  }

  .sr-detail__action-btn--cancel:hover {
    background: rgba(244, 63, 94, 0.2);
  }

  .sr-detail__actions-info {
    width: 100%;
    text-align: center;
    font-size: 0.8125rem;
    color: var(--sd-text-secondary);
    padding: 1rem;
  }

  /* --- Small BEM utility --- */
  .sd-hidden {
    display: none !important;
  }
</style>

@php
  $statusLabels = [
    'pending'   => 'Pendiente',
    'confirmed' => 'Confirmada',
    'completed' => 'Completada',
    'cancelled' => 'Cancelada',
  ];
  $statusBadge = $reservation->status;
  $now = \Carbon\Carbon::now();
  $product = $reservation->product;
  $margin = null;
  if ($product && $product->estimated_cost > 0) {
    $margin = round(($product->price - $product->estimated_cost) / $product->estimated_cost * 100);
  }
@endphp

<div class="sr-detail">
  {{-- Back link --}}
  <a href="{{ route('reservations.manage') }}" class="sr-detail__back">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Volver a gestion de pedidos
  </a>

  {{-- Header --}}
  <div class="sr-detail__header">
    <div class="sr-detail__client-info">
      <div class="sr-detail__avatar" aria-hidden="true">
        {{ strtoupper(substr($reservation->client_name, 0, 2)) }}
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:0.625rem;flex-wrap:wrap;">
          <h1 class="sr-detail__client-name">{{ $reservation->client_name }}</h1>
          <span style="font-size:0.75rem;font-family:monospace;font-weight:700;color:#2d8c4e;background:rgba(45,140,78,0.08);border:1px solid rgba(45,140,78,0.2);padding:0.2rem 0.6rem;border-radius:0.5rem;white-space:nowrap;">
            #{{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}
          </span>
        </div>
        <div class="sr-detail__client-meta">
          @if($reservation->client_email)
            <span>
              <a href="mailto:{{ $reservation->client_email }}">{{ $reservation->client_email }}</a>
            </span>
          @endif
          @if($reservation->client_phone)
            <span>
              <a href="tel:{{ $reservation->client_phone }}">{{ $reservation->client_phone }}</a>
            </span>
          @endif
        </div>
      </div>
    </div>
    <span class="sr-detail__badge sr-detail__badge--{{ $statusBadge }}">
      <span class="sr-detail__badge-dot" aria-hidden="true"></span>
      {{ $statusLabels[$reservation->status] ?? $reservation->status }}
    </span>
  </div>

  {{-- Product Section --}}
  <div class="sr-detail__section">
    <h2 class="sr-detail__section-title">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
      Producto
    </h2>

    @if($product)
      <div class="sr-detail__product">
        @if($product->image)
          <img class="sr-detail__product-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
        @else
          <div class="sr-detail__product-image sr-detail__product-image--placeholder">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
          </div>
        @endif
        <div class="sr-detail__product-info">
          <div class="sr-detail__product-name">{{ $product->name }}</div>
          @if($product->description)
            <div class="sr-detail__product-desc">{{ $product->description }}</div>
          @endif
          <div class="sr-detail__product-prices">
            <div class="sr-detail__product-price-item">
              <span class="sr-detail__product-price-label">Precio de venta</span>
              <span class="sr-detail__product-price-amount sr-detail__product-price-amount--sale">${{ number_format($product->price, 2) }}</span>
            </div>
            @if($product->estimated_cost > 0)
              <div class="sr-detail__product-price-item">
                <span class="sr-detail__product-price-label">Costo estimado</span>
                <span class="sr-detail__product-price-amount sr-detail__product-price-amount--cost">${{ number_format($product->estimated_cost, 2) }}</span>
              </div>
              <div class="sr-detail__product-price-item">
                <span class="sr-detail__product-price-label">Margen</span>
                <span class="sr-detail__product-price-amount sr-detail__product-price-amount--cost">{{ $margin }}%</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    @else
      <div class="sr-detail__field-value sr-detail__field-value--muted">Producto eliminado</div>
    @endif
  </div>

  {{-- Date & Time Section --}}
  <div class="sr-detail__section">
    <h2 class="sr-detail__section-title">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
      Fecha y Hora
    </h2>
    <div class="sr-detail__grid">
      <div class="sr-detail__field">
        <span class="sr-detail__field-label">Fecha</span>
        <span class="sr-detail__field-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</span>
      </div>
      <div class="sr-detail__field">
        <span class="sr-detail__field-label">Horario</span>
        <span class="sr-detail__field-value">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }} hs</span>
      </div>
    </div>
  </div>

  {{-- Client Notes Section --}}
  <div class="sr-detail__section">
    <h2 class="sr-detail__section-title">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
      Notas del Cliente
    </h2>
    @if($reservation->notes)
      <div class="sr-detail__notes">{{ $reservation->notes }}</div>
    @else
      <div class="sr-detail__notes sr-detail__notes--empty">El cliente no ha dejado notas en su pedido.</div>
    @endif
  </div>

  {{-- Status Timeline Section --}}
  <div class="sr-detail__section">
    <h2 class="sr-detail__section-title">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Historial de Estados
    </h2>
    <div class="sr-detail__timeline">
      {{-- Created --}}
      <div class="sr-detail__timeline-item">
        <div class="sr-detail__timeline-dot sr-detail__timeline-dot--warning"></div>
        <div class="sr-detail__timeline-label">Pendiente</div>
        <div class="sr-detail__timeline-date">Creada el {{ $reservation->created_at->format('d/m/Y \a \l\a\s H:i') }}</div>
      </div>

      {{-- Confirmed (if status advanced beyond pending) --}}
      @if(in_array($reservation->status, ['confirmed', 'completed']))
        <div class="sr-detail__timeline-item">
          <div class="sr-detail__timeline-dot sr-detail__timeline-dot--active"></div>
          <div class="sr-detail__timeline-label">Confirmada</div>
          <div class="sr-detail__timeline-date">
            @if($reservation->created_at->format('Y-m-d H:i:s') !== $reservation->updated_at->format('Y-m-d H:i:s'))
              {{ $reservation->updated_at->format('d/m/Y \a \l\a\s H:i') }}
            @else
              Pendiente de confirmacion manual
            @endif
          </div>
        </div>
      @endif

      {{-- Completed --}}
      @if($reservation->completed_at)
        <div class="sr-detail__timeline-item">
          <div class="sr-detail__timeline-dot sr-detail__timeline-dot--success"></div>
          <div class="sr-detail__timeline-label">Completada</div>
          <div class="sr-detail__timeline-date">
            {{ \Carbon\Carbon::parse($reservation->completed_at)->format('d/m/Y \a \l\a\s H:i') }}
          </div>
        </div>
      @endif

      {{-- Cancelled --}}
      @if($reservation->status === 'cancelled')
        <div class="sr-detail__timeline-item">
          <div class="sr-detail__timeline-dot sr-detail__timeline-dot--danger"></div>
          <div class="sr-detail__timeline-label">Cancelada</div>
          <div class="sr-detail__timeline-date">{{ $reservation->updated_at->format('d/m/Y \a \l\a\s H:i') }}</div>
          @if($reservation->cancellation_reason)
            <div class="sr-detail__timeline-reason">Motivo: {{ $reservation->cancellation_reason }}</div>
          @endif
          @if($reservation->canceller && $reservation->canceller->exists)
            <div class="sr-detail__timeline-date" style="margin-top:0.125rem;">Cancelada por {{ $reservation->canceller->name }}</div>
          @endif
        </div>
      @endif
    </div>
  </div>

  {{-- Seller Internal Note --}}
  <div class="sr-detail__section">
    <h2 class="sr-detail__section-title">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
      Nota Interna
      <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--sd-text-secondary);font-size:0.75rem;">(opcional, solo visible para vos)</span>
    </h2>
    <textarea id="sr-detail-seller-notes" class="sr-detail__textarea" placeholder="Escribe una nota interna sobre esta reserva...">{{ $reservation->seller_notes }}</textarea>
    <div class="sr-detail__notes-actions">
      <button type="button"
              id="sr-detail-save-notes"
              class="btn btn--primary"
              style="padding:0.5rem 1.25rem;font-size:0.8125rem;">
        <span id="sr-detail-save-text">Guardar Nota</span>
      </button>
    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="sr-detail__actions">
    @if($reservation->status === 'pending')
      <button type="button" class="sr-detail__action-btn sr-detail__action-btn--confirm" data-action="confirm" data-id="{{ $reservation->id }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Confirmar Reserva
      </button>
      <button type="button" class="sr-detail__action-btn sr-detail__action-btn--cancel" data-action="cancel" data-id="{{ $reservation->id }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        Cancelar Reserva
      </button>
    @elseif($reservation->status === 'confirmed')
      <button type="button" class="sr-detail__action-btn sr-detail__action-btn--complete" data-action="complete" data-id="{{ $reservation->id }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Marcar como Completada
      </button>
      <button type="button" class="sr-detail__action-btn sr-detail__action-btn--cancel" data-action="cancel" data-id="{{ $reservation->id }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        Cancelar Reserva
      </button>
    @else
      <div class="sr-detail__actions-info">
        @if($reservation->status === 'completed')
          Esta reserva ya fue completada.
        @elseif($reservation->status === 'cancelled')
          Esta reserva fue cancelada.
        @endif
      </div>
    @endif
  </div>

  {{-- Sección comprobante de pago --}}
  @if($reservation->payment_status === 'uploaded' || $reservation->payment_status === 'confirmed')
  <div style="margin-top:1.5rem;background:#ffffff;border:1px solid #e8e0d0;border-radius:1rem;padding:1.5rem;">
    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
      <div style="width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(245,166,35,0.1);border:1px solid rgba(245,166,35,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg style="width:1.1rem;height:1.1rem;color:#f5a623;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
      </div>
      <div>
        <p style="font-size:0.875rem;font-weight:700;color:#1a1918;">Comprobante de transferencia</p>
        <p style="font-size:0.75rem;color:#6a6966;">
          @if($reservation->payment_status === 'confirmed')
            Pago verificado y confirmado
          @else
            El cliente subió el comprobante — revisalo y confirmá el pago
          @endif
        </p>
      </div>
      @if($reservation->payment_status === 'confirmed')
        <span style="margin-left:auto;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:0.25rem 0.625rem;border-radius:9999px;background:rgba(45,140,78,0.1);color:#2d8c4e;border:1px solid rgba(45,140,78,0.2);">Confirmado</span>
      @else
        <span style="margin-left:auto;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:0.25rem 0.625rem;border-radius:9999px;background:rgba(224,144,16,0.1);color:#e09010;border:1px solid rgba(224,144,16,0.2);">Pendiente revisión</span>
      @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.25rem;">
      @if($reservation->transfer_amount)
      <div style="background:#f9f7f2;border:1px solid #e8e0d0;border-radius:0.75rem;padding:0.75rem;">
        <p style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;color:#6a6966;margin-bottom:0.25rem;">Monto</p>
        <p style="font-size:1rem;font-weight:700;color:#2d8c4e;">${{ number_format($reservation->transfer_amount, 2) }}</p>
      </div>
      @endif
      @if($reservation->transfer_date)
      <div style="background:#f9f7f2;border:1px solid #e8e0d0;border-radius:0.75rem;padding:0.75rem;">
        <p style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;color:#6a6966;margin-bottom:0.25rem;">Fecha</p>
        <p style="font-size:0.875rem;font-weight:600;color:#1a1918;">{{ $reservation->transfer_date->format('d/m/Y') }}</p>
      </div>
      @endif
      @if($reservation->transfer_reference)
      <div style="background:#f9f7f2;border:1px solid #e8e0d0;border-radius:0.75rem;padding:0.75rem;grid-column:span 2;">
        <p style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;color:#6a6966;margin-bottom:0.25rem;">Nº Operación / Referencia</p>
        <p style="font-size:0.875rem;font-weight:600;color:#1a1918;font-family:monospace;">{{ $reservation->transfer_reference }}</p>
      </div>
      @endif
    </div>

    @if($reservation->receipt_path)
    <a href="{{ Storage::url($reservation->receipt_path) }}" target="_blank"
       style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.8125rem;font-weight:600;color:#f5a623;text-decoration:none;margin-bottom:1.25rem;">
      <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
      </svg>
      Ver comprobante adjunto
    </a>
    @endif

    @if($reservation->payment_status === 'uploaded')
    <form action="{{ route('reservations.confirm-payment', $reservation) }}" method="POST">
      @csrf
      <button type="submit"
        style="width:100%;display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.75rem 1rem;border-radius:0.75rem;background:#2d8c4e;border:none;color:#fff;font-size:0.875rem;font-weight:600;cursor:pointer;transition:background 0.2s;"
        onmouseover="this.style.background='#1e6a38'" onmouseout="this.style.background='#2d8c4e'">
        <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Confirmar pago y reserva
      </button>
    </form>
    @endif

    @if($reservation->payment_confirmed_at)
    <p style="font-size:0.75rem;color:#6a6966;margin-top:0.75rem;text-align:center;">
      Pago confirmado el {{ $reservation->payment_confirmed_at->format('d/m/Y \a \l\a\s H:i') }} hs
    </p>
    @endif
  </div>
  @endif

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
  var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function getStatusLabel(s) {
    var map = { pending: 'Pendiente', confirmed: 'Confirmada', completed: 'Completada', cancelled: 'Cancelada' };
    return map[s] || s;
  }

  function showToast(message, type) {
    var existing = document.querySelector('.sd-toast');
    if (existing) existing.remove();
    var toast = document.createElement('div');
    toast.className = 'sd-toast';
    toast.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;padding:0.75rem 1.25rem;border-radius:0.75rem;font-size:0.8125rem;font-weight:600;color:#fff;z-index:999;max-width:20rem;transition:all 0.3s ease;background:' + (type === 'success' ? '#166534' : '#9f1239');
    toast.textContent = message;
    toast.setAttribute('role', 'alert');
    document.body.appendChild(toast);
    setTimeout(function () { toast.remove(); }, 3000);
  }

  // Action buttons
  document.querySelectorAll('[data-action]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var action = btn.getAttribute('data-action');
      var id = btn.getAttribute('data-id');
      btn.disabled = true;

      var bodyData = {};
      if (action === 'confirm') bodyData.status = 'confirmed';
      else if (action === 'complete') bodyData.status = 'completed';
      else if (action === 'cancel') {
        var reason = prompt('Motivo de cancelación (opcional):');
        bodyData.status = 'cancelled';
        if (reason) bodyData.cancellation_reason = reason;
      }

      fetch('/reservations/' + id + '/status', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify(bodyData),
      })
      .then(function (r) { return r.json(); })
      .then(function (resp) {
        if (resp.success) {
          showToast(resp.message, 'success');
          setTimeout(function () { location.reload(); }, 800);
        } else {
          showToast(resp.message || 'Error al actualizar estado.', 'error');
          btn.disabled = false;
        }
      })
      .catch(function () {
        showToast('Error de conexión.', 'error');
        btn.disabled = false;
      });
    });
  });

  // Save seller notes
  document.getElementById('sr-detail-save-notes')?.addEventListener('click', function () {
    var btn = this;
    var textEl = document.getElementById('sr-detail-save-text');
    var notes = document.getElementById('sr-detail-seller-notes').value;

    btn.disabled = true;
    if (textEl) textEl.textContent = 'Guardando...';

    fetch("{{ route('reservations.seller-notes', $reservation) }}", {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      body: JSON.stringify({ seller_notes: notes }),
    })
    .then(function (r) { return r.json(); })
    .then(function (resp) {
      if (resp.success) {
        showToast('Nota interna guardada.', 'success');
      } else {
        showToast(resp.message || 'Error al guardar.', 'error');
      }
    })
    .catch(function () {
      showToast('Error de conexion.', 'error');
    })
    .finally(function () {
      btn.disabled = false;
      if (textEl) textEl.textContent = 'Guardar Nota';
    });
  });
});
</script>
@endsection
