(function () {
  'use strict';

  var DAYS_LABELS = {
    monday: 'Lunes', tuesday: 'Martes', wednesday: 'Miércoles',
    thursday: 'Jueves', friday: 'Viernes', saturday: 'Sábado', sunday: 'Domingo'
  };

  var slotCounter = 0;
  var formEl = null;
  var feedbackEl = null;
  var saveBtn = null;

  function init(config) {
    config = config || {};
    formEl = document.getElementById(config.formId || 'availability-form');
    feedbackEl = document.getElementById(config.feedbackId || 'form-feedback');
    saveBtn = document.getElementById(config.saveBtnId || 'btn-save-availability');

    if (!formEl) return;

    formEl.querySelectorAll('.availability__btn-remove').forEach(function (btn) {
      btn.addEventListener('click', function () {
        removeSlot(this);
      });
    });

    formEl.querySelectorAll('.availability__btn-add').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var day = btn.getAttribute('data-day');
        addSlot(day);
      });
    });

    formEl.addEventListener('submit', handleSubmit);

    formEl.querySelectorAll('.availability__slot-row').forEach(function (row) {
      var idx = row.getAttribute('data-index');
      var num = parseInt(idx, 10);
      if (!isNaN(num) && num >= slotCounter) {
        slotCounter = num + 1;
      }
    });

    Object.keys(DAYS_LABELS).forEach(function (day) {
      updateDayState(day);
    });
  }

  function addSlot(day) {
    var container = formEl.querySelector('[data-day-slots="' + day + '"]');
    if (!container) return;

    var template = container.querySelector('.availability__slot-template');
    if (!template) return;

    var clone = template.cloneNode(true);
    clone.classList.remove('availability__slot-template', 'hidden');
    clone.classList.add('availability__slot-row');
    clone.removeAttribute('aria-hidden');
    clone.setAttribute('data-index', slotCounter);

    clone.querySelectorAll('input').forEach(function (input) {
      input.value = '';
      if (input.classList.contains('slot-weekday')) {
        input.value = day;
      }
    });

    var removeBtn = clone.querySelector('.availability__btn-remove');
    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        removeSlot(this);
      });
    }

    container.appendChild(clone);

    var startInput = clone.querySelector('.slot-start');
    if (startInput) startInput.focus();

    slotCounter++;
    updateDayState(day);
  }

  function removeSlot(element) {
    var row = element.closest('.availability__slot-row');
    if (!row) return;

    var day = row.querySelector('.slot-weekday');
    var dayVal = day ? day.value : null;

    var container = dayVal ? formEl.querySelector('[data-day-slots="' + dayVal + '"]') : null;
    var rows = container ? container.querySelectorAll('.availability__slot-row') : [];

    if (rows.length <= 1) {
      alert('Debes tener al menos un horario por día. Si no deseas atender este día, simplemente deja el día sin horarios.');
      return;
    }

    row.remove();
    if (dayVal) updateDayState(dayVal);
  }

  function updateDayState(day) {
    var container = formEl.querySelector('[data-day-slots="' + day + '"]');
    var emptyMsg = formEl.querySelector('[data-empty-day="' + day + '"]');
    if (!container) return;
    var rows = container.querySelectorAll('.availability__slot-row');
    if (emptyMsg) {
      if (rows.length === 0) {
        emptyMsg.classList.remove('hidden');
      } else {
        emptyMsg.classList.add('hidden');
      }
    }
  }

  function reindex() {
    var slotRows = formEl.querySelectorAll('.availability__slot-row');
    var index = 0;
    slotRows.forEach(function (row) {
      row.querySelectorAll('input').forEach(function (input) {
        var name = input.getAttribute('name');
        if (name) {
          input.setAttribute('name', name.replace(/slots\[\d+\]/, 'slots[' + index + ']'));
        }
      });
      index++;
    });
  }

  function validateAll() {
    var errors = [];
    var slotRows = formEl.querySelectorAll('.availability__slot-row');

    if (slotRows.length === 0) {
      errors.push('Debes agregar al menos un horario de atención.');
      return errors;
    }

    slotRows.forEach(function (row) {
      var startInput = row.querySelector('.slot-start');
      var endInput = row.querySelector('.slot-end');
      var start = startInput ? startInput.value : '';
      var end = endInput ? endInput.value : '';

      if (!start || !end) {
        errors.push('Completa la hora de inicio y fin en todos los horarios.');
        return;
      }

      if (start >= end) {
        var dayInput = row.querySelector('.slot-weekday');
        var dayName = dayInput ? (DAYS_LABELS[dayInput.value] || dayInput.value) : '';
        errors.push('En ' + dayName + ': la hora de fin debe ser posterior a la de inicio (' + start + ' - ' + end + ').');
      }
    });

    var byDay = {};
    slotRows.forEach(function (row) {
      var dayInput = row.querySelector('.slot-weekday');
      if (!dayInput) return;
      var day = dayInput.value;
      if (!byDay[day]) byDay[day] = [];
      byDay[day].push({
        start: row.querySelector('.slot-start') ? row.querySelector('.slot-start').value : '',
        end: row.querySelector('.slot-end') ? row.querySelector('.slot-end').value : ''
      });
    });

    Object.keys(byDay).forEach(function (day) {
      var slots = byDay[day];
      for (var i = 0; i < slots.length; i++) {
        for (var j = i + 1; j < slots.length; j++) {
          if (slots[i].start && slots[i].end && slots[j].start && slots[j].end) {
            if (slots[i].start < slots[j].end && slots[j].start < slots[i].end) {
              var dayName = DAYS_LABELS[day] || day;
              errors.push('En ' + dayName + ': los horarios no pueden superponerse.');
              return;
            }
          }
        }
      }
    });

    return errors;
  }

  function showFeedback(errors) {
    if (!feedbackEl) return;
    if (!errors || errors.length === 0) {
      feedbackEl.classList.add('availability__feedback--hidden');
      return;
    }
    feedbackEl.className = 'availability__feedback availability__feedback--error';
    feedbackEl.setAttribute('role', 'alert');
    feedbackEl.innerHTML = '';
    errors.forEach(function (msg) {
      var p = document.createElement('p');
      p.textContent = msg;
      feedbackEl.appendChild(p);
    });
    feedbackEl.classList.remove('availability__feedback--hidden');
    feedbackEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function handleSubmit(e) {
    var errors = validateAll();
    if (errors.length > 0) {
      e.preventDefault();
      showFeedback(errors);
      if (saveBtn) {
        saveBtn.disabled = false;
      }
      return;
    }
    reindex();
    showFeedback([]);
    if (saveBtn) {
      saveBtn.disabled = true;
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    init({
      formId: 'availability-form',
      feedbackId: 'form-feedback',
      saveBtnId: 'btn-save-availability'
    });
  });
})();
