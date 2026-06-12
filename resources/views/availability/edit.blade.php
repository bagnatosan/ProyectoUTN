@extends('layouts.app')

@section('title', 'Configurar Disponibilidad | ProyectoUTN')

@section('content')
<style>
  /* =========================================
     DAY SECTION
     ========================================= */
  .day-section {
    border: 1px solid rgba(51, 65, 85, 0.6);
    background-color: rgba(51, 65, 85, 0.2);
    border-radius: 1rem;
    padding: 1.25rem;
    transition: all 0.2s ease;
  }

  .day-section:focus-within {
    border-color: rgba(34, 197, 94, 0.4);
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.15);
  }

  .day-section__header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .day-section__indicator {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background-color: #334155;
    flex-shrink: 0;
    transition: background-color 0.3s ease;
  }

  .day-section__indicator--active {
    background-color: #4ade80;
    box-shadow: 0 0 6px rgba(74, 222, 128, 0.4);
  }

  .day-section__title {
    font-size: 1rem;
    font-weight: 600;
    color: #f1f5f9;
  }

  .day-section__day-badge {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    background-color: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    color: #818cf8;
  }

  /* =========================================
     SLOT ROW
     ========================================= */
  .slot-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 0;
    border-bottom: 1px solid rgba(51, 65, 85, 0.3);
  }

  .slot-row:last-child {
    border-bottom: none;
  }

  .slot-row__time-wrapper {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    flex: 1;
  }

  .slot-row__time-sep {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    flex-shrink: 0;
  }

  .slot-row__input {
    flex: 1;
    min-width: 0;
    padding: 0.5rem 0.75rem;
    background-color: rgba(30, 41, 59, 0.6);
    border: 1px solid #334155;
    border-radius: 0.625rem;
    color: #f1f5f9;
    font-size: 0.875rem;
    font-family: inherit;
    transition: all 0.2s ease;
  }

  .slot-row__input:focus {
    outline: none;
    border-color: rgba(34, 197, 94, 0.5);
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
  }

  .slot-row__input--error {
    border-color: rgba(244, 63, 94, 0.6) !important;
    box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2) !important;
  }

  /* =========================================
     SLOT REMOVE BUTTON
     ========================================= */
  .slot-remove {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    background: transparent;
    border: 1px solid rgba(244, 63, 94, 0.2);
    color: #fb7185;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  .slot-remove:hover {
    background-color: rgba(244, 63, 94, 0.1);
    border-color: rgba(244, 63, 94, 0.4);
    color: #f43f5e;
  }

  .slot-remove:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.3);
  }

  /* =========================================
     ADD BUTTON
     ========================================= */
  .btn-add-slot {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.625rem;
    padding: 0.5rem 0.875rem;
    border-radius: 0.625rem;
    font-size: 0.8125rem;
    font-weight: 600;
    background: transparent;
    border: 1px dashed rgba(99, 102, 241, 0.3);
    color: #818cf8;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-add-slot:hover {
    background-color: rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.5);
    color: #a5b4fc;
  }

  .btn-add-slot:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
  }

  /* =========================================
     EMPTY STATE
     ========================================= */
  .empty-day {
    text-align: center;
    padding: 1.5rem 1rem;
    color: #64748b;
    font-size: 0.8125rem;
  }

  .empty-day__icon {
    display: block;
    margin: 0 auto 0.5rem;
    width: 2rem;
    height: 2rem;
    opacity: 0.4;
  }

  /* =========================================
     FORM FOOTER
     ========================================= */
  .form-footer {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(51, 65, 85, 0.6);
  }

  @media (min-width: 640px) {
    .form-footer {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }
  }

  .form-footer__info {
    font-size: 0.75rem;
    color: #64748b;
  }

  /* =========================================
     SAVE BUTTON
     ========================================= */
  .btn-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    background: linear-gradient(to right, #16a34a, #22c55e);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.25);
    width: 100%;
  }

  @media (min-width: 640px) {
    .btn-save {
      width: auto;
    }
  }

  .btn-save:hover {
    background: linear-gradient(to right, #22c55e, #4ade80);
    box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
  }

  .btn-save:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5), 0 0 0 4px rgba(30, 41, 59, 1);
  }

  .btn-save:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }

  /* =========================================
     FEEDBACK
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

  .form-feedback--error p {
    margin-bottom: 0.25rem;
  }

  .form-feedback--error p:last-child {
    margin-bottom: 0;
  }

  /* =========================================
     RESPONSIVE
     ========================================= */
  @media (max-width: 640px) {
    .slot-row {
      flex-wrap: wrap;
    }
    .slot-row__time-wrapper {
      flex-basis: calc(100% - 2.5rem);
    }
  }
</style>

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
            Defini tus dias y horarios de atencion. <span class="text-indigo-400 font-medium">Total libertad para organizar tu agenda.</span>
          </p>
        </div>
      </div>

      <div id="form-feedback" class="form-feedback form-feedback--hidden mb-6" role="status" aria-live="polite"></div>

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
        @endphp

        <div class="space-y-4">
          @foreach($days as $day)
            @php
              $daySlots = isset($slots[$day]) ? $slots[$day] : collect();
              $hasSlots = $daySlots->isNotEmpty();
            @endphp

            <section class="day-section">
              <div class="day-section__header">
                <span class="day-section__indicator {{ $hasSlots ? 'day-section__indicator--active' : '' }}"></span>
                <h2 class="day-section__title">{{ $dayLabels[$day] }}</h2>
                <span class="day-section__day-badge">{{ $dayShort[$day] }}</span>
              </div>

              <div data-day-slots="{{ $day }}">
                @if($hasSlots)
                  @foreach($daySlots as $slot)
                    @php
                      $loopIndex = $loop->index;
                      $startVal = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                      $endVal = \Carbon\Carbon::parse($slot->end_time)->format('H:i');
                    @endphp
                    <div class="slot-row" data-slot-id="existing-{{ $day }}-{{ $loopIndex }}">
                      <input type="hidden"
                             class="slot-weekday"
                             name="slots[{{ $loopIndex }}][weekday]"
                             value="{{ $day }}">
                      <div class="slot-row__time-wrapper">
                        <input type="time"
                               class="slot-row__input slot-start"
                               name="slots[{{ $loopIndex }}][start_time]"
                               value="{{ $startVal }}"
                               aria-label="Hora de inicio {{ $dayLabels[$day] }}">
                        <span class="slot-row__time-sep" aria-hidden="true">&ndash;</span>
                        <input type="time"
                               class="slot-row__input slot-end"
                               name="slots[{{ $loopIndex }}][end_time]"
                               value="{{ $endVal }}"
                               aria-label="Hora de fin {{ $dayLabels[$day] }}">
                      </div>
                      <button type="button"
                              class="slot-remove"
                              aria-label="Eliminar horario {{ $startVal }} - {{ $endVal }} de {{ $dayLabels[$day] }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  @endforeach
                @endif

                <div class="slot-row slot-template hidden" data-slot-id="template" aria-hidden="true">
                  <input type="hidden"
                         class="slot-weekday"
                         name="slots[0][weekday]"
                         value="">
                  <div class="slot-row__time-wrapper">
                    <input type="time"
                           class="slot-row__input slot-start"
                           name="slots[0][start_time]"
                           value=""
                           aria-label="Hora de inicio">
                    <span class="slot-row__time-sep" aria-hidden="true">&ndash;</span>
                    <input type="time"
                           class="slot-row__input slot-end"
                           name="slots[0][end_time]"
                           value=""
                           aria-label="Hora de fin">
                  </div>
                  <button type="button"
                          class="slot-remove"
                          aria-label="Eliminar horario">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>

              <div class="empty-day {{ $hasSlots ? 'hidden' : '' }}" data-empty-day="{{ $day }}">
                <svg class="empty-day__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Sin horarios configurados
              </div>

              <button type="button"
                      class="btn-add-slot"
                      data-day="{{ $day }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M12 5v14m-7-7h14"/>
                </svg>
                Agregar horario
              </button>
            </section>
          @endforeach
        </div>

        <div class="form-footer mt-8">
          <p class="form-footer__info">
            <svg class="inline-block w-3.5 h-3.5 align-text-top mr-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Todos los cambios se guardan en simultaneo al presionar Guardar.
          </p>
          <button type="submit"
                  id="btn-save-availability"
                  class="btn-save">
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

<script src="{{ asset('js/reservations/availability-editor.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    AvailabilityEditor.init({
      formId: 'availability-form',
      feedbackId: 'form-feedback',
      saveBtnId: 'btn-save-availability',
    });
  });
</script>
@endsection
