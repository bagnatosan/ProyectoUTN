const CalendarComponent = (() => {
  var DAYS_SHORT = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
  var MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  function create(containerEl, options) {
    options = options || {};
    var today = new Date();
    today.setHours(0, 0, 0, 0);

    var state = {
      currentMonth: today.getMonth(),
      currentYear: today.getFullYear(),
      selectedDateStr: null,
      dateStates: {},
      onDateSelect: options.onDateSelect || null,
    };

    var rootEl = null;
    var monthLabelEl = null;
    var yearLabelEl = null;
    var daysGridEl = null;

    function init() {
      containerEl.innerHTML = '';
      rootEl = document.createElement('div');
      rootEl.className = 'calendar';
      rootEl.setAttribute('role', 'application');
      rootEl.setAttribute('aria-label', 'Calendario para seleccion de fecha');

      buildHeader();
      buildDayHeaders();
      buildDaysGrid();
      updateLabels();
      renderDays();
    }

    function buildHeader() {
      var header = document.createElement('div');
      header.className = 'calendar__header';

      var prevBtn = document.createElement('button');
      prevBtn.type = 'button';
      prevBtn.className = 'calendar__nav-btn';
      prevBtn.setAttribute('aria-label', 'Mes anterior');
      prevBtn.innerHTML =
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>';
      prevBtn.addEventListener('click', function () { navigate(-1); });

      var labelWrap = document.createElement('div');
      labelWrap.className = 'calendar__label-container';
      monthLabelEl = document.createElement('span');
      monthLabelEl.className = 'calendar__month-label';
      yearLabelEl = document.createElement('span');
      yearLabelEl.className = 'calendar__year-label';
      labelWrap.appendChild(monthLabelEl);
      labelWrap.appendChild(yearLabelEl);

      var nextBtn = document.createElement('button');
      nextBtn.type = 'button';
      nextBtn.className = 'calendar__nav-btn';
      nextBtn.setAttribute('aria-label', 'Mes siguiente');
      nextBtn.innerHTML =
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>';
      nextBtn.addEventListener('click', function () { navigate(1); });

      header.appendChild(prevBtn);
      header.appendChild(labelWrap);
      header.appendChild(nextBtn);
      rootEl.appendChild(header);
    }

    function buildDayHeaders() {
      var row = document.createElement('div');
      row.className = 'calendar__day-headers';
      row.setAttribute('role', 'row');
      DAYS_SHORT.forEach(function (label) {
        var el = document.createElement('div');
        el.className = 'calendar__day-header';
        el.setAttribute('role', 'columnheader');
        el.setAttribute('aria-label', label);
        el.textContent = label;
        row.appendChild(el);
      });
      rootEl.appendChild(row);
    }

    function buildDaysGrid() {
      daysGridEl = document.createElement('div');
      daysGridEl.className = 'calendar__days-grid';
      daysGridEl.setAttribute('role', 'grid');
      daysGridEl.setAttribute('aria-label', 'Dias del mes');
      rootEl.appendChild(daysGridEl);
      containerEl.appendChild(rootEl);
    }

    function renderDays() {
      daysGridEl.innerHTML = '';
      var firstDay = new Date(state.currentYear, state.currentMonth, 1).getDay();
      var daysInMonth = new Date(state.currentYear, state.currentMonth + 1, 0).getDate();

      for (var i = 0; i < firstDay; i++) {
        var empty = document.createElement('div');
        empty.className = 'calendar__day calendar__day--empty';
        daysGridEl.appendChild(empty);
      }

      for (var day = 1; day <= daysInMonth; day++) {
        var date = new Date(state.currentYear, state.currentMonth, day);
        date.setHours(0, 0, 0, 0);
        var dateStr = formatDate(date);
        var isPast = date.getTime() < today.getTime();
        var isToday = date.getTime() === today.getTime();
        var isSelected = state.selectedDateStr === dateStr;
        var dateState = state.dateStates[dateStr];

        var dayEl = document.createElement('button');
        dayEl.type = 'button';
        dayEl.className = 'calendar__day';
        dayEl.setAttribute('role', 'gridcell');
        dayEl.setAttribute('aria-label', day + ' de ' + MONTHS[state.currentMonth] + ' de ' + state.currentYear);
        dayEl.textContent = day;

        if (isPast) {
          dayEl.classList.add('calendar__day--past');
          dayEl.disabled = true;
          dayEl.setAttribute('aria-disabled', 'true');
          dayEl.setAttribute('tabindex', '-1');
        } else {
          dayEl.setAttribute('tabindex', '0');
        }

        if (isToday) {
          dayEl.classList.add('calendar__day--today');
        }

        if (isSelected) {
          dayEl.classList.add('calendar__day--selected');
          dayEl.setAttribute('aria-current', 'date');
        }

        if (dateState === 'available') {
          dayEl.classList.add('calendar__day--available');
        } else if (dateState === 'unavailable') {
          dayEl.classList.add('calendar__day--unavailable');
        }

        if (!isPast) {
          dayEl.addEventListener('click', (function (ds) {
            return function () { handleDateClick(ds); };
          })(dateStr));
          dayEl.addEventListener('keydown', (function (ds) {
            return function (e) {
              if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleDateClick(ds);
              }
            };
          })(dateStr));
        }

        daysGridEl.appendChild(dayEl);
      }
    }

    function handleDateClick(dateStr) {
      state.selectedDateStr = dateStr;
      renderDays();
      if (state.onDateSelect) {
        state.onDateSelect(dateStr);
      }
    }

    function navigate(direction) {
      state.currentMonth += direction;
      if (state.currentMonth < 0) {
        state.currentMonth = 11;
        state.currentYear -= 1;
      } else if (state.currentMonth > 11) {
        state.currentMonth = 0;
        state.currentYear += 1;
      }
      updateLabels();
      renderDays();
    }

    function updateLabels() {
      if (monthLabelEl) monthLabelEl.textContent = MONTHS[state.currentMonth];
      if (yearLabelEl) yearLabelEl.textContent = String(state.currentYear);
    }

    function formatDate(date) {
      var y = date.getFullYear();
      var m = String(date.getMonth() + 1).padStart(2, '0');
      var d = String(date.getDate()).padStart(2, '0');
      return y + '-' + m + '-' + d;
    }

    function setDateState(dateStr, stateValue) {
      state.dateStates[dateStr] = stateValue;
      renderDays();
    }

    function getSelectedDate() {
      return state.selectedDateStr;
    }

    function reset() {
      state.selectedDateStr = null;
      state.dateStates = {};
      renderDays();
    }

    init();

    return {
      getSelectedDate: getSelectedDate,
      setDateState: setDateState,
      reset: reset,
      element: rootEl,
    };
  }

  return { create: create };
})();
