@extends('layouts.app')

@section('title', 'Gestión de Pedidos | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-6xl mx-auto')

@section('content')
<link rel="stylesheet" href="{{ asset('css/sections/reservations.css') }}">

<div class="page-banner" style="margin-bottom:1.5rem;">
  <img src="{{ asset('images/banner-home.png') }}" alt="" class="page-banner__bg">
  <div class="page-banner__overlay"></div>
  <div class="page-banner__content">
    <h1 class="page-banner__title">Gestión de Pedidos</h1>
    <p class="page-banner__subtitle">Revisá y gestioná las reservas de tus clientes.</p>
  </div>
</div>

<div class="reservations-manage">

  <div class="reservations-manage__filters" role="tablist" aria-label="Filtros temporales">
    <button class="reservations-manage__filter reservations-manage__filter--active" data-filter="today" role="tab" aria-selected="true">Hoy</button>
    <button class="reservations-manage__filter" data-filter="tomorrow" role="tab" aria-selected="false">Manana</button>
    <button class="reservations-manage__filter" data-filter="week" role="tab" aria-selected="false">Semana</button>
    <button class="reservations-manage__filter" data-filter="month" role="tab" aria-selected="false">Mes</button>
  </div>

  <div id="rm-loader" class="reservations-manage__loader" aria-hidden="true">
    <div class="reservations-manage__spinner"></div>
    <span class="reservations-manage__loader-text">Cargando pedidos...</span>
  </div>

  <div id="rm-error" class="reservations-manage__error hidden" role="alert"></div>

  <div id="rm-empty" class="reservations-manage__empty hidden">
    <svg class="reservations-manage__empty-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
    </svg>
    <p class="reservations-manage__empty-title">No hay pedidos</p>
    <p class="reservations-manage__empty-text">No se encontraron reservas para el filtro seleccionado.</p>
  </div>

  <div id="rm-grid" class="reservations-manage__grid" aria-live="polite"></div>
</div>

<script src="{{ asset('js/sections/reservations-manager.js') }}"></script>
@endsection
