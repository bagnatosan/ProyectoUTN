@extends('layouts.app')

@section('title', 'Configurar Disponibilidad | ProyectoUTN')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections/availability.css') }}">
@endpush

@section('content')
<div class="max-w-3xl mx-auto py-6 sm:py-8 px-4">
  <div class="availability__card">
    <div style="text-align:center;margin-bottom:1.5rem;">
      <h1 style="font-size:1.75rem;font-weight:800;color:#1a1918;letter-spacing:-0.02em;">Configurar Disponibilidad</h1>
      <p style="font-size:0.9rem;color:#6a6966;margin-top:0.375rem;">Definí tus días y horarios de atención.</p>
    </div>

    <div id="form-feedback" class="availability__feedback availability__feedback--hidden" role="alert"></div>

    <div id="availability-form">
      @php
        $days = [0, 1, 2, 3, 4, 5, 6];
        $dayLabels = [0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
        $dayShort  = [0 => 'DOM', 1 => 'LUN', 2 => 'MAR', 3 => 'MIE', 4 => 'JUE', 5 => 'VIE', 6 => 'SAB'];
        $defaultSlots = [1, 2, 3, 4, 5]; // Lun-Vie por defecto
      @endphp

      <div class="availability__day-list">
        @foreach ($days as $day)
          @php
            $daySlots = $slots[$day] ?? collect();
            $hasSlots = $daySlots->isNotEmpty();
          @endphp

          <section class="availability__day-section">
            <div class="availability__day-header">
              <span class="availability__indicator {{ $hasSlots ? 'availability__indicator--active' : '' }}"></span>
              <h2 class="availability__day-title">{{ $dayLabels[$day] }}</h2>
              <span class="availability__day-badge">{{ $dayShort[$day] }}</span>
            </div>

            <div class="availability__slot-container" data-day="{{ $day }}">
              @if ($hasSlots)
                @foreach ($daySlots as $slot)
                  <div class="availability__slot-row">
                    <input type="hidden" class="slot-day" value="{{ $day }}">
                    <div class="availability__time-wrapper">
                      <input type="time" class="availability__input slot-start" value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" aria-label="Inicio {{ $dayLabels[$day] }}">
                      <span class="availability__time-sep">&ndash;</span>
                      <input type="time" class="availability__input slot-end" value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" aria-label="Fin {{ $dayLabels[$day] }}">
                    </div>
                    <button type="button" class="availability__btn-remove" aria-label="Quitar horario">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                  </div>
                @endforeach
              @elseif (in_array($day, $defaultSlots))
                <div class="availability__slot-row">
                  <input type="hidden" class="slot-day" value="{{ $day }}">
                  <div class="availability__time-wrapper">
                    <input type="time" class="availability__input slot-start" value="08:00" aria-label="Inicio {{ $dayLabels[$day] }}">
                    <span class="availability__time-sep">&ndash;</span>
                    <input type="time" class="availability__input slot-end" value="12:00" aria-label="Fin {{ $dayLabels[$day] }}">
                  </div>
                  <button type="button" class="availability__btn-remove" aria-label="Quitar horario">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                  </button>
                </div>
              @endif
            </div>

            <div class="availability__empty-day {{ $hasSlots || in_array($day, $defaultSlots) ? 'hidden' : '' }}" data-empty="{{ $day }}">
              <svg class="availability__empty-day-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
              Sin horarios configurados
            </div>

            <button type="button" class="availability__btn-add" data-day="{{ $day }}">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14m-7-7h14"/></svg>
              Agregar horario
            </button>
          </section>
        @endforeach
      </div>

      <div class="availability__template" hidden aria-hidden="true">
        <div class="availability__slot-row">
          <input type="hidden" class="slot-day" value="">
          <div class="availability__time-wrapper">
            <input type="time" class="availability__input slot-start" value="" aria-label="Inicio">
            <span class="availability__time-sep">&ndash;</span>
            <input type="time" class="availability__input slot-end" value="" aria-label="Fin">
          </div>
          <button type="button" class="availability__btn-remove" aria-label="Quitar horario">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <div class="availability__footer">
        <p class="availability__footer-info">
          <svg class="availability__footer-info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Todos los cambios se guardan al presionar Guardar.
        </p>
        <button type="button" id="btn-save-availability" class="availability__btn-save">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
          Guardar horarios
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  var DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
  var formEl = document.getElementById('availability-form');
  var feedbackEl = document.getElementById('form-feedback');
  var saveBtn = document.getElementById('btn-save-availability');

  if (!formEl) return;

  /* ── Delegación de eventos ── */
  formEl.addEventListener('click', function (e) {
    var target = e.target.closest('.availability__btn-remove');
    if (target) { e.preventDefault(); removeSlot(target); }
  });

  formEl.addEventListener('click', function (e) {
    var target = e.target.closest('.availability__btn-add');
    if (target) { e.preventDefault(); addSlot(target.getAttribute('data-day')); }
  });

  /* ── Add slot ── */
  function addSlot(day) {
    var container = formEl.querySelector('[data-day="' + day + '"]');
    if (!container) return;
    var template = formEl.querySelector('.availability__template');
    if (!template) return;
    var clone = template.firstElementChild.cloneNode(true);
    clone.querySelector('.slot-day').value = day;
    container.appendChild(clone);
    var startInput = clone.querySelector('.slot-start');
    if (startInput) startInput.focus();
    updateDayState(day);
    clearFeedback();
  }

  /* ── Remove slot ── */
  function removeSlot(btn) {
    var row = btn.closest('.availability__slot-row');
    if (!row) return;
    var container = row.closest('.availability__slot-container');
    var rows = container ? container.querySelectorAll('.availability__slot-row') : [];
    if (rows.length <= 1) {
      showFeedback(['Debe haber al menos un horario por día activo. Si no querés atender, dejá el día sin horarios.']);
      return;
    }
    var day = container ? container.getAttribute('data-day') : null;
    row.remove();
    if (day) updateDayState(day);
    clearFeedback();
  }

  /* ── Day state (indicator + empty msg) ── */
  function updateDayState(day) {
    var container = formEl.querySelector('[data-day="' + day + '"]');
    var section = container ? container.closest('.availability__day-section') : null;
    if (!section) return;
    var rows = container.querySelectorAll('.availability__slot-row');
    var indicator = section.querySelector('.availability__indicator');
    var emptyMsg = section.querySelector('[data-empty="' + day + '"]');
    if (indicator) indicator.classList.toggle('availability__indicator--active', rows.length > 0);
    if (emptyMsg) emptyMsg.classList.toggle('hidden', rows.length > 0);
  }

  /* ── Build payload ── */
  function collectSlots() {
    var slots = [];
    var rows = formEl.querySelectorAll('.availability__slot-row');
    rows.forEach(function (row) {
      var day = row.querySelector('.slot-day');
      var start = row.querySelector('.slot-start');
      var end = row.querySelector('.slot-end');
      if (day && start && end && start.value && end.value) {
        slots.push({
          day_of_week: parseInt(day.value, 10),
          start_time: start.value,
          end_time: end.value
        });
      }
    });
    return slots;
  }

  /* ── Validation ── */
  function validate(slots) {
    var errors = [];
    if (slots.length === 0) {
      errors.push('Agregá al menos un horario de atención.');
      return errors;
    }

    var byDay = {};
    slots.forEach(function (s) {
      if (s.start_time >= s.end_time) {
        errors.push('En ' + DAYS[s.day_of_week] + ': la hora de fin debe ser posterior a la de inicio.');
      }
      if (!byDay[s.day_of_week]) byDay[s.day_of_week] = [];
      byDay[s.day_of_week].push(s);
    });

    Object.keys(byDay).forEach(function (d) {
      var list = byDay[d];
      for (var i = 0; i < list.length; i++) {
        for (var j = i + 1; j < list.length; j++) {
          if (list[i].start_time < list[j].end_time && list[j].start_time < list[i].end_time) {
            errors.push('En ' + DAYS[d] + ': los horarios no pueden superponerse.');
            return;
          }
        }
      }
    });

    return errors;
  }

  /* ── UI helpers ── */
  function showFeedback(errors) {
    if (!feedbackEl) return;
    feedbackEl.className = 'availability__feedback availability__feedback--error';
    feedbackEl.innerHTML = errors.map(function (m) { return '<p>' + m + '</p>'; }).join('');
    feedbackEl.style.display = 'block';
    feedbackEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function showSuccess(msg) {
    if (!feedbackEl) return;
    feedbackEl.className = 'availability__feedback availability__feedback--success';
    feedbackEl.innerHTML = '<p>' + msg + '</p>';
    feedbackEl.style.display = 'block';
    feedbackEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function clearFeedback() {
    if (!feedbackEl) return;
    feedbackEl.style.display = 'none';
    feedbackEl.className = 'availability__feedback availability__feedback--hidden';
  }

  /* ── Submit via Fetch API ── */
  function handleSave() {
    var slots = collectSlots();
    var errs = validate(slots);
    if (errs.length > 0) { showFeedback(errs); return; }

    saveBtn.disabled = true;
    saveBtn.textContent = 'Guardando…';

    var token = document.querySelector('meta[name="csrf-token"]');
    fetch('{{ route("availability.update") }}', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ slots: slots })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) {
        showSuccess(data.message || 'Horarios guardados.');
        // Actualizar indicadores visuales por si cambió algo
        for (var d = 0; d <= 6; d++) updateDayState(d);
      } else {
        showFeedback(data.message ? [data.message] : ['Error al guardar.']);
      }
    })
    .catch(function () {
      showFeedback(['Error de conexión. Intentá de nuevo.']);
    })
    .finally(function () {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg> Guardar horarios';
    });
  }

  saveBtn.addEventListener('click', handleSave);

  /* ── Init day states ── */
  for (var d = 0; d <= 6; d++) updateDayState(d);
})();
</script>
@endpush
