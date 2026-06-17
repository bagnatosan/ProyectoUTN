@extends('layouts.app')

@section('title', 'Configurar Disponibilidad | ProyectoUTN')

@section('content')
<link rel="stylesheet" href="{{ asset('css/sections/availability.css') }}">

<div class="max-w-3xl mx-auto py-6 sm:py-8">
  <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl shadow-xl overflow-hidden">
    <div class="p-6 sm:p-8">
      <div class="flex items-center space-x-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-400 flex items-center justify-center shadow-lg shadow-indigo-600/20">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Configurar Disponibilidad
          </h1>
          <p class="text-slate-400 text-sm mt-0.5">
            Definí tus días y horarios de atención. <span class="text-indigo-400 font-medium">Total libertad para organizar tu agenda.</span>
          </p>
        </div>
      </div>

      <div id="form-feedback" class="availability__feedback availability__feedback--hidden mb-6" role="status" aria-live="polite"></div>

      <form id="availability-form" method="POST" action="{{ route('availability.update') }}" novalidate>
        @csrf
        @method('PUT')

        @php
          $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
          $dayLabels = [
            'monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
            'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo',
          ];
          $dayShort = [
            'monday' => 'LUN', 'tuesday' => 'MAR', 'wednesday' => 'MIE',
            'thursday' => 'JUE', 'friday' => 'VIE', 'saturday' => 'SAB', 'sunday' => 'DOM',
          ];
          $globalIndex = 0;
        @endphp

        <div class="space-y-4">
          @foreach($days as $day)
            @php
              $daySlots = isset($slots[$day]) ? $slots[$day] : collect();
              $hasSlots = $daySlots->isNotEmpty();
            @endphp

            <section class="availability__day-section">
              <div class="availability__day-header">
                <span class="availability__indicator {{ $hasSlots ? 'availability__indicator--active' : '' }}"></span>
                <h2 class="availability__day-title">{{ $dayLabels[$day] }}</h2>
                <span class="availability__day-badge">{{ $dayShort[$day] }}</span>
              </div>

              <div data-day-slots="{{ $day }}">
                @if($hasSlots)
                  @foreach($daySlots as $slot)
                    @php
                      $startVal = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                      $endVal = \Carbon\Carbon::parse($slot->end_time)->format('H:i');
                    @endphp
                    <div class="availability__slot-row" data-index="{{ $globalIndex }}">
                      <input type="hidden"
                             class="slot-weekday"
                             name="slots[{{ $globalIndex }}][weekday]"
                             value="{{ $day }}">
                      <div class="availability__time-wrapper">
                        <input type="time"
                               class="availability__input slot-start"
                               name="slots[{{ $globalIndex }}][start_time]"
                               value="{{ $startVal }}"
                               aria-label="Hora de inicio {{ $dayLabels[$day] }}">
                        <span class="availability__time-sep" aria-hidden="true">&ndash;</span>
                        <input type="time"
                               class="availability__input slot-end"
                               name="slots[{{ $globalIndex }}][end_time]"
                               value="{{ $endVal }}"
                               aria-label="Hora de fin {{ $dayLabels[$day] }}">
                      </div>
                      <button type="button"
                              class="availability__btn-remove"
                              aria-label="Eliminar horario {{ $startVal }} - {{ $endVal }} de {{ $dayLabels[$day] }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                    @php $globalIndex++; @endphp
                  @endforeach
                @endif

                <div class="availability__slot-row availability__slot-template hidden" data-index="{{ $globalIndex }}" aria-hidden="true">
                  <input type="hidden"
                         class="slot-weekday"
                         name="slots[{{ $globalIndex }}][weekday]"
                         value="">
                  <div class="availability__time-wrapper">
                    <input type="time"
                           class="availability__input slot-start"
                           name="slots[{{ $globalIndex }}][start_time]"
                           value=""
                           aria-label="Hora de inicio">
                    <span class="availability__time-sep" aria-hidden="true">&ndash;</span>
                    <input type="time"
                           class="availability__input slot-end"
                           name="slots[{{ $globalIndex }}][end_time]"
                           value=""
                           aria-label="Hora de fin">
                  </div>
                  <button type="button"
                          class="availability__btn-remove"
                          aria-label="Eliminar horario">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
                @php $globalIndex++; @endphp
              </div>

              <div class="availability__empty-day {{ $hasSlots ? 'hidden' : '' }}" data-empty-day="{{ $day }}">
                <svg class="availability__empty-day-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Sin horarios configurados
              </div>

              <button type="button"
                      class="availability__btn-add"
                      data-day="{{ $day }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M12 5v14m-7-7h14"/>
                </svg>
                Agregar horario
              </button>
            </section>
          @endforeach
        </div>

        <div class="availability__footer">
          <p class="availability__footer-info">
            <svg class="availability__footer-info-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Todos los cambios se guardan en simultáneo al presionar Guardar.
          </p>
          <button type="submit"
                  id="btn-save-availability"
                  class="availability__btn-save">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{ asset('js/sections/availability.js') }}"></script>
@endsection
