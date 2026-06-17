const reservationsManager = (() => {
  'use strict';

  const STATUS_LABELS = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    completed: 'Completada',
    cancelled: 'Cancelada',
  };

  const STATUS_CLASSES = {
    pending: 'reservations-manage__badge--pending',
    confirmed: 'reservations-manage__badge--confirmed',
    completed: 'reservations-manage__badge--completed',
    cancelled: 'reservations-manage__badge--cancelled',
  };

  let currentFilter = 'today';

  function init() {
    const filters = document.querySelectorAll('.reservations-manage__filter');
    filters.forEach((btn) => {
      btn.addEventListener('click', () => {
        const filter = btn.getAttribute('data-filter');
        if (filter && filter !== currentFilter) {
          setActiveFilter(btn);
          currentFilter = filter;
          loadReservations(filter);
        }
      });
    });

    loadReservations(currentFilter);
  }

  function setActiveFilter(activeBtn) {
    document.querySelectorAll('.reservations-manage__filter').forEach((btn) => {
      btn.classList.remove('reservations-manage__filter--active');
      btn.setAttribute('aria-selected', 'false');
    });
    activeBtn.classList.add('reservations-manage__filter--active');
    activeBtn.setAttribute('aria-selected', 'true');
  }

  function showLoader() {
    document.getElementById('rm-loader')?.classList.add('reservations-manage__loader--visible');
    document.getElementById('rm-grid').innerHTML = '';
    document.getElementById('rm-empty')?.classList.add('hidden');
    document.getElementById('rm-error')?.classList.add('hidden');
  }

  function hideLoader() {
    document.getElementById('rm-loader')?.classList.remove('reservations-manage__loader--visible');
  }

  function showError(msg) {
    const el = document.getElementById('rm-error');
    if (el) {
      el.textContent = msg;
      el.classList.remove('hidden');
    }
  }

  function showEmpty() {
    document.getElementById('rm-empty')?.classList.remove('hidden');
  }

  function loadReservations(filter) {
    showLoader();

    fetch('/reservations/manage/data?filter=' + encodeURIComponent(filter))
      .then((res) => {
        if (!res.ok) {
          return res.json().then((data) => {
            throw new Error(data.message || 'Error al cargar pedidos');
          });
        }
        return res.json();
      })
      .then((response) => {
        hideLoader();
        if (response.success && response.data && response.data.length > 0) {
          renderGrid(response.data);
        } else {
          showEmpty();
        }
      })
      .catch((err) => {
        hideLoader();
        showError(err.message || 'Error de conexión al servidor.');
      });
  }

  function renderGrid(reservations) {
    const grid = document.getElementById('rm-grid');
    grid.innerHTML = '';

    reservations.forEach((reservation) => {
      const card = createCard(reservation);
      grid.appendChild(card);
    });
  }

  function createCard(reservation) {
    const clientName = reservation.client_name || 'Sin nombre';
    const initials = getInitials(clientName);
    const productName = reservation.product ? reservation.product.name : 'Producto eliminado';
    const statusClass = STATUS_CLASSES[reservation.status] || STATUS_CLASSES.pending;
    const statusLabel = STATUS_LABELS[reservation.status] || reservation.status;

    const card = document.createElement('article');
    card.className = 'reservations-manage__card';
    card.dataset.id = reservation.id;

    card.innerHTML =
      '<div class="reservations-manage__card-header">' +
        '<div class="reservations-manage__card-client">' +
          '<div class="reservations-manage__card-avatar" aria-hidden="true">' + escapeHtml(initials) + '</div>' +
          '<div>' +
            '<div class="reservations-manage__card-name">' + escapeHtml(clientName) + '</div>' +
            '<div class="reservations-manage__card-email">' + escapeHtml(reservation.client_email || '') + '</div>' +
          '</div>' +
        '</div>' +
        '<span class="reservations-manage__badge ' + statusClass + '">' +
          '<span class="reservations-manage__badge-dot" aria-hidden="true"></span>' +
          statusLabel +
        '</span>' +
      '</div>' +
      '<div class="reservations-manage__card-body">' +
        '<div class="reservations-manage__card-info">' +
          '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>' +
          '<span><strong>' + escapeHtml(productName) + '</strong></span>' +
        '</div>' +
        '<div class="reservations-manage__card-info">' +
          '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>' +
          '<span>' + formatDate(reservation.reservation_date) + ' - ' + formatTime(reservation.reservation_time) + '</span>' +
        '</div>' +
        (reservation.notes ? '<div class="reservations-manage__card-notes">' + escapeHtml(reservation.notes) + '</div>' : '') +
      '</div>' +
      '<div class="reservations-manage__card-footer">' +
        '<select class="reservations-manage__select" aria-label="Cambiar estado">' +
          '<option value="pending"' + (reservation.status === 'pending' ? ' selected' : '') + '>Pendiente</option>' +
          '<option value="confirmed"' + (reservation.status === 'confirmed' ? ' selected' : '') + '>Confirmada</option>' +
          '<option value="completed"' + (reservation.status === 'completed' ? ' selected' : '') + '>Completada</option>' +
          '<option value="cancelled"' + (reservation.status === 'cancelled' ? ' selected' : '') + '>Cancelada</option>' +
        '</select>' +
      '</div>';

    const select = card.querySelector('.reservations-manage__select');
    select.addEventListener('change', () => {
      updateStatus(reservation.id, select.value, card, select);
    });

    return card;
  }

  function updateStatus(id, newStatus, card, select) {
    select.disabled = true;

    fetch('/reservations/' + id + '/status', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
      },
      body: JSON.stringify({ status: newStatus }),
    })
      .then((res) => {
        if (!res.ok) {
          return res.json().then((data) => {
            throw new Error(data.message || 'Error al actualizar estado');
          });
        }
        return res.json();
      })
      .then((response) => {
        if (response.success) {
          updateCardVisual(card, newStatus);
          showToast(response.message || 'Estado actualizado correctamente.', 'success');
        } else {
          throw new Error(response.message || 'Error al actualizar estado');
        }
      })
      .catch((err) => {
        select.value = card.dataset.originalStatus || 'pending';
        showToast(err.message || 'Error de conexión.', 'error');
      })
      .finally(() => {
        select.disabled = false;
      });
  }

  function updateCardVisual(card, newStatus) {
    const badge = card.querySelector('.reservations-manage__badge');
    if (badge) {
      Object.values(STATUS_CLASSES).forEach((cls) => badge.classList.remove(cls));
      badge.className = 'reservations-manage__badge ' + (STATUS_CLASSES[newStatus] || STATUS_CLASSES.pending);
      badge.innerHTML = '<span class="reservations-manage__badge-dot" aria-hidden="true"></span>' + (STATUS_LABELS[newStatus] || newStatus);
    }
    card.dataset.originalStatus = newStatus;
  }

  function showToast(message, type) {
    const existing = document.querySelector('.reservations-manage__toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'reservations-manage__toast reservations-manage__toast--' + type + ' reservations-manage__toast--visible';
    toast.textContent = message;
    toast.setAttribute('role', 'alert');
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.classList.remove('reservations-manage__toast--visible');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T12:00:00');
    return d.toLocaleDateString('es-AR', { day: 'numeric', month: 'short', year: 'numeric' });
  }

  function formatTime(timeStr) {
    if (!timeStr) return '';
    return timeStr.substring(0, 5);
  }

  function getInitials(name) {
    if (!name) return '?';
    return name
      .split(' ')
      .map((w) => w.charAt(0))
      .filter((c) => c.match(/[a-záéíóúñ]/i))
      .slice(0, 2)
      .join('')
      .toUpperCase() || '?';
  }

  function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  document.addEventListener('DOMContentLoaded', init);

  return { init: init };
})();
