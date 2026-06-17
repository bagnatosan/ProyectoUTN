const SellerReservations = (() => {
  var STATUS_LABELS = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    completed: 'Completada',
    cancelled: 'Cancelada',
  };

  var STATUS_COLORS = {
    pending: 'badge--pending',
    confirmed: 'badge--confirmed',
    completed: 'badge--completed',
    cancelled: 'badge--cancelled',
  };

  var csrfToken = document.querySelector('meta[name="csrf-token"]');
  csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

  function init() {
    document.querySelectorAll('.reservation-row').forEach(function (row) {
      attachRowHandlers(row);
    });
    initDateFilter();
    initStatusButtons();
  }

  function attachRowHandlers(row) {
    row.querySelectorAll('.btn-status').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var reservationId = btn.getAttribute('data-reservation-id');
        var newStatus = btn.getAttribute('data-status');
        updateStatus(reservationId, newStatus, row, btn);
      });
    });
  }

  function initDateFilter() {
    var dateInput = document.getElementById('filter-date');
    var filterForm = document.getElementById('filter-form');
    if (dateInput && filterForm) {
      dateInput.addEventListener('change', function () {
        filterForm.submit();
      });
    }
    var clearBtn = document.getElementById('clear-filter');
    if (clearBtn && dateInput && filterForm) {
      clearBtn.addEventListener('click', function () {
        dateInput.value = '';
        filterForm.submit();
      });
    }
  }

  function initStatusButtons() {
    document.querySelectorAll('.reservation-row').forEach(function (row) {
      row.removeEventListener('click', handleRowQuickClick);
      row.addEventListener('click', handleRowQuickClick);
    });
  }

  function handleRowQuickClick(e) {
    var btn = e.target.closest('.btn-status');
    if (!btn) return;
    e.preventDefault();
    var reservationId = btn.getAttribute('data-reservation-id');
    var newStatus = btn.getAttribute('data-status');
    var row = btn.closest('.reservation-row');
    updateStatus(reservationId, newStatus, row, btn);
  }

  async function updateStatus(reservationId, newStatus, row, btn) {
    var actionBtn = btn || row.querySelector('.btn-status[data-status="' + newStatus + '"]');
    if (actionBtn) {
      actionBtn.disabled = true;
      actionBtn.classList.add('btn--loading');
    }
    var statusBadge = row.querySelector('.badge');
    var actionsContainer = row.querySelector('.reservation-actions');
    try {
      var response = await fetch('/reservations/' + reservationId + '/status', {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ status: newStatus }),
      });
      var data = await response.json();
      if (response.ok && data.success) {
        if (statusBadge) {
          statusBadge.className = 'badge ' + (STATUS_COLORS[newStatus] || '');
          statusBadge.textContent = STATUS_LABELS[newStatus] || newStatus;
        }
        if (actionsContainer) {
          actionsContainer.innerHTML = renderActions(reservationId, newStatus);
          attachRowHandlers(row);
          initStatusButtons();
        }
        showFeedback('success', data.message || 'Estado actualizado correctamente.');
      } else {
        showFeedback('error', data.message || 'Error al actualizar el estado.');
        if (actionBtn) {
          actionBtn.disabled = false;
          actionBtn.classList.remove('btn--loading');
        }
      }
    } catch (err) {
      showFeedback('error', 'Error de conexion. Intenta de nuevo.');
      if (actionBtn) {
        actionBtn.disabled = false;
        actionBtn.classList.remove('btn--loading');
      }
    }
  }

  function renderActions(reservationId, status) {
    var actions = '';
    if (status === 'pending') {
      actions += '<button type="button" class="btn-status btn-status--confirm" data-reservation-id="' + reservationId + '" data-status="confirmed">Confirmar</button>';
      actions += '<button type="button" class="btn-status btn-status--cancel" data-reservation-id="' + reservationId + '" data-status="cancelled">Cancelar</button>';
    } else if (status === 'confirmed') {
      actions += '<button type="button" class="btn-status btn-status--complete" data-reservation-id="' + reservationId + '" data-status="completed">Completar</button>';
      actions += '<button type="button" class="btn-status btn-status--cancel" data-reservation-id="' + reservationId + '" data-status="cancelled">Cancelar</button>';
    }
    return actions;
  }

  function showFeedback(type, message) {
    var el = document.getElementById('form-feedback');
    if (!el) return;
    el.className = 'form-feedback form-feedback--' + type;
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.innerHTML = message;
    el.classList.remove('form-feedback--hidden');
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    setTimeout(function () {
      el.classList.add('form-feedback--hidden');
    }, 5000);
  }

  return { init: init };
})();

document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('reservations-list')) {
    SellerReservations.init();
  }
});
