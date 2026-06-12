const TimeSlotSelector = (() => {
  function create(containerEl, options) {
    options = options || {};
    var onTimeSelect = options.onTimeSelect || null;
    var selectedTime = null;

    function render(state, slots) {
      containerEl.innerHTML = '';

      switch (state) {
        case 'idle':
          renderIdle();
          break;
        case 'loading':
          renderLoading();
          break;
        case 'success':
          renderSlots(slots);
          break;
        case 'empty':
          renderEmpty();
          break;
        case 'error':
          renderError();
          break;
        default:
          renderIdle();
      }
    }

    function renderIdle() {
      var el = document.createElement('div');
      el.className = 'time-slots__state time-slots__state--idle';
      el.innerHTML =
        '<svg class="time-slots__icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>' +
        '<p class="time-slots__text">Selecciona una fecha para ver los horarios disponibles.</p>';
      containerEl.appendChild(el);
    }

    function renderLoading() {
      var el = document.createElement('div');
      el.className = 'time-slots__state time-slots__state--loading';
      el.setAttribute('role', 'status');
      el.setAttribute('aria-live', 'polite');
      el.innerHTML =
        '<div class="time-slots__spinner"></div>' +
        '<p class="time-slots__text">Consultando disponibilidad...</p>';
      containerEl.appendChild(el);
    }

    function renderSlots(slots) {
      if (!slots || slots.length === 0) {
        renderEmpty();
        return;
      }

      selectedTime = null;
      var wrapper = document.createElement('div');
      wrapper.className = 'time-slots__wrapper';

      var heading = document.createElement('p');
      heading.className = 'time-slots__heading';
      heading.textContent = 'Horarios disponibles (' + slots.length + ')';
      wrapper.appendChild(heading);

      var grid = document.createElement('div');
      grid.className = 'time-slots__grid';
      grid.setAttribute('role', 'radiogroup');
      grid.setAttribute('aria-label', 'Horarios disponibles');

      slots.forEach(function (time) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'time-slots__slot';
        btn.setAttribute('role', 'radio');
        btn.setAttribute('aria-checked', 'false');
        btn.setAttribute('aria-label', 'Horario ' + time);
        btn.textContent = time;

        btn.addEventListener('click', function () {
          selectTime(time, grid);
        });
        btn.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            selectTime(time, grid);
          }
        });

        grid.appendChild(btn);
      });

      wrapper.appendChild(grid);
      containerEl.appendChild(wrapper);
    }

    function selectTime(time, grid) {
      selectedTime = time;
      var slots = grid.querySelectorAll('.time-slots__slot');
      slots.forEach(function (btn) {
        btn.classList.remove('time-slots__slot--selected');
        btn.setAttribute('aria-checked', 'false');
      });
      var selectedBtn = grid.querySelector('.time-slots__slot[aria-label="Horario ' + time + '"]');
      if (selectedBtn) {
        selectedBtn.classList.add('time-slots__slot--selected');
        selectedBtn.setAttribute('aria-checked', 'true');
        selectedBtn.focus();
      }
      if (onTimeSelect) {
        onTimeSelect(time);
      }
    }

    function renderEmpty() {
      var el = document.createElement('div');
      el.className = 'time-slots__state time-slots__state--empty';
      el.setAttribute('role', 'status');
      el.setAttribute('aria-live', 'polite');
      el.innerHTML =
        '<svg class="time-slots__icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>' +
        '<p class="time-slots__text">No hay horarios disponibles para esta fecha.</p>';
      containerEl.appendChild(el);
    }

    function renderError() {
      var el = document.createElement('div');
      el.className = 'time-slots__state time-slots__state--error';
      el.setAttribute('role', 'alert');
      el.setAttribute('aria-live', 'assertive');
      el.innerHTML =
        '<svg class="time-slots__icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>' +
        '<p class="time-slots__text">Ocurrio un error al consultar los horarios. Intenta de nuevo.</p>';
      containerEl.appendChild(el);
    }

    function getSelectedTime() {
      return selectedTime;
    }

    function reset() {
      selectedTime = null;
      render('idle');
    }

    render('idle');

    return {
      render: render,
      getSelectedTime: getSelectedTime,
      reset: reset,
    };
  }

  return { create: create };
})();
