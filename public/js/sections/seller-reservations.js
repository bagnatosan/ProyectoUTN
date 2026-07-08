const SellerReservations = (() => {
  'use strict';

  var STATUS_LABELS = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    completed: 'Completada',
    cancelled: 'Cancelada',
  };

  var STATUS_CLASSES = {
    pending: 'seller-reservations__badge--pending',
    confirmed: 'seller-reservations__badge--confirmed',
    completed: 'seller-reservations__badge--completed',
    cancelled: 'seller-reservations__badge--cancelled',
  };

  var state = {
    filter: 'all',
    status: '',
    search: '',
    page: 1,
    date_from: '',
    date_to: '',
    sort_by: 'reservation_date',
    sort_dir: 'desc',
  };

  var debounceTimer = null;

  function init() {
    initFilterButtons();
    initStatusSelect();
    initDateRange();
    initSortSelect();
    initSearchInput();
    initPagination();
    initModalClose();
    initCancelModal();

    // Marcar el botón "Todas" como activo al cargar
    var allBtn = document.querySelector('[data-sr-filter="all"]');
    if (allBtn) setActiveFilter(allBtn);

    loadReservations();
  }

  /* =============================
     FILTER BUTTONS
     ============================= */
  function initFilterButtons() {
    var btns = document.querySelectorAll('[data-sr-filter]');
    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var filter = btn.getAttribute('data-sr-filter');
        if (!filter || filter === state.filter) return;
        setActiveFilter(btn);
        state.filter = filter;
        state.page = 1;
        resetDateRange();
        state.sort_by = 'reservation_date';
        state.sort_dir = 'desc';
        syncSortSelect();
        loadReservations();
      });
    });
  }

  function setActiveFilter(activeBtn) {
    document.querySelectorAll('[data-sr-filter]').forEach(function (btn) {
      btn.classList.remove('seller-reservations__filter-btn--active');
    });
    activeBtn.classList.add('seller-reservations__filter-btn--active');
  }

  /* =============================
     STATUS SELECT
     ============================= */
  function initStatusSelect() {
    var sel = document.getElementById('sr-status-select');
    if (!sel) return;
    sel.addEventListener('change', function () {
      state.status = sel.value;
      state.page = 1;
      loadReservations();
    });
  }

  /* =============================
     DATE RANGE
     ============================= */
  function initDateRange() {
    var from = document.getElementById('sr-date-from');
    var to = document.getElementById('sr-date-to');
    if (from) {
      from.addEventListener('change', function () {
        state.date_from = from.value;
        state.page = 1;
        loadReservations();
      });
    }
    if (to) {
      to.addEventListener('change', function () {
        state.date_to = to.value;
        state.page = 1;
        loadReservations();
      });
    }
  }

  function resetDateRange() {
    state.date_from = '';
    state.date_to = '';
    var from = document.getElementById('sr-date-from');
    var to = document.getElementById('sr-date-to');
    if (from) from.value = '';
    if (to) to.value = '';
  }

  /* =============================
     SORT SELECT
     ============================= */
  function initSortSelect() {
    var sel = document.getElementById('sr-sort-select');
    if (!sel) return;
    sel.addEventListener('change', function () {
      state.sort_by = sel.value;
      state.page = 1;
      loadReservations();
    });
  }

  function syncSortSelect() {
    var sel = document.getElementById('sr-sort-select');
    if (sel) sel.value = state.sort_by;
  }

  /* =============================
     SEARCH INPUT
     ============================= */
  function initSearchInput() {
    var input = document.getElementById('sr-search-input');
    var clear = document.getElementById('sr-search-clear');
    if (!input) return;

    input.addEventListener('input', function () {
      if (debounceTimer) clearTimeout(debounceTimer);
      var val = input.value.trim();
      if (clear) {
        clear.classList.toggle('seller-reservations__search-clear--visible', val.length > 0);
      }
      debounceTimer = setTimeout(function () {
        state.search = val;
        state.page = 1;
        loadReservations();
      }, 350);
    });

    if (clear) {
      clear.addEventListener('click', function () {
        input.value = '';
        clear.classList.remove('seller-reservations__search-clear--visible');
        state.search = '';
        state.page = 1;
        loadReservations();
        input.focus();
      });
    }
  }

  /* =============================
     PAGINATION
     ============================= */
  function initPagination() {
    document.getElementById('sr-pagination')?.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-sr-page]');
      if (!btn || btn.classList.contains('seller-reservations__page-btn--disabled')) return;
      var page = parseInt(btn.getAttribute('data-sr-page'), 10);
      if (isNaN(page) || page === state.page) return;
      state.page = page;
      loadReservations();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* =============================
     MODAL
     ============================= */
  function initModalClose() {
    var overlay = document.getElementById('sr-modal-overlay');
    if (!overlay) return;
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeModal();
    });
  }

  function openModal(html) {
    var overlay = document.getElementById('sr-modal-overlay');
    var body = document.getElementById('sr-modal-body');
    if (!overlay || !body) return;
    body.innerHTML = html;
    overlay.classList.add('seller-reservations__modal-overlay--visible');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    var overlay = document.getElementById('sr-modal-overlay');
    if (overlay) overlay.classList.remove('seller-reservations__modal-overlay--visible');
    document.body.style.overflow = '';
  }

  /* =============================
     CANCEL MODAL
     ============================= */
  function initCancelModal() {
    var overlay = document.getElementById('sr-cancel-overlay');
    if (!overlay) return;
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeCancelModal();
    });
    document.getElementById('sr-cancel-close')?.addEventListener('click', closeCancelModal);

    var confirmBtn = document.getElementById('sr-cancel-confirm');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', function () {
        if (cancelReservationId) {
          var reason = document.getElementById('sr-cancel-reason')?.value || '';
          updateStatus(cancelReservationId, 'cancelled', null, reason);
          closeCancelModal();
        }
      });
    }
  }

  var cancelReservationId = null;

  function openCancelModal(id) {
    cancelReservationId = id;
    var reasonEl = document.getElementById('sr-cancel-reason');
    if (reasonEl) {
      reasonEl.value = '';
      reasonEl.focus();
    }
    var overlay = document.getElementById('sr-cancel-overlay');
    if (overlay) overlay.classList.add('seller-reservations__modal-overlay--visible');
    document.body.style.overflow = 'hidden';
  }

  function closeCancelModal() {
    cancelReservationId = null;
    var overlay = document.getElementById('sr-cancel-overlay');
    if (overlay) overlay.classList.remove('seller-reservations__modal-overlay--visible');
    document.body.style.overflow = '';
  }

  /* =============================
     FETCH RESERVATIONS
     ============================= */
  function buildUrl() {
    var params = new URLSearchParams();
    params.set('filter', state.filter);
    if (state.status) params.set('status', state.status);
    if (state.search) params.set('search', state.search);
    if (state.date_from) params.set('date_from', state.date_from);
    if (state.date_to) params.set('date_to', state.date_to);
    params.set('sort_by', state.sort_by);
    params.set('sort_dir', state.sort_dir);
    params.set('page', String(state.page));
    params.set('per_page', '12');
    return '/reservations/manage/data?' + params.toString();
  }

  function loadReservations() {
    showLoader();

    fetch(buildUrl(), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
      .then(function (res) {
        if (!res.ok) {
          return res.json().then(function (data) {
            throw new Error(data.message || 'Error al cargar pedidos');
          });
        }
        return res.json();
      })
      .then(function (response) {
        hideLoader();
        if (response.success) {
          updateTotal(response.total);
          updateExportLink();
          if (response.data && response.data.length > 0) {
            renderGrid(response.data);
            renderPagination(response.total, response.current_page, response.last_page);
          } else {
            showEmpty();
          }
        } else {
          showError(response.message || 'Error al cargar pedidos.');
        }
      })
      .catch(function (err) {
        hideLoader();
        showError(err.message || 'Error de conexión al servidor.');
      });
  }

  /* =============================
     UI HELPERS
     ============================= */
  function showLoader() {
    var loader = document.getElementById('sr-loader');
    if (loader) loader.classList.add('seller-reservations__loader--visible');
    var grid = document.getElementById('sr-grid');
    if (grid) grid.innerHTML = '';
    var empty = document.getElementById('sr-empty');
    if (empty) empty.classList.remove('seller-reservations__empty--visible');
    var error = document.getElementById('sr-error');
    if (error) error.classList.remove('seller-reservations__error--visible');
    var pag = document.getElementById('sr-pagination');
    if (pag) pag.classList.add('sr-hidden');
  }

  function hideLoader() {
    var loader = document.getElementById('sr-loader');
    if (loader) loader.classList.remove('seller-reservations__loader--visible');
  }

  function showEmpty() {
    var empty = document.getElementById('sr-empty');
    if (empty) empty.classList.add('seller-reservations__empty--visible');
  }

  function showError(msg) {
    var error = document.getElementById('sr-error');
    var text = document.getElementById('sr-error-text');
    if (text) text.textContent = msg;
    if (error) error.classList.add('seller-reservations__error--visible');
  }

  function updateTotal(total) {
    var el = document.getElementById('sr-total');
    if (el) {
      el.textContent = total + ' ' + (total === 1 ? 'compra' : 'compras');
    }
  }

  /* =============================
     RENDER GRID
     ============================= */
  function renderGrid(reservations) {
    var grid = document.getElementById('sr-grid');
    if (!grid) return;
    grid.innerHTML = '';
    reservations.forEach(function (r) {
      grid.appendChild(createCard(r));
    });
  }

  function createCard(r) {
    var clientName = r.client_name || 'Sin nombre';
    var initials = getInitials(clientName);
    var productName = r.product ? r.product.name : 'Producto eliminado';
    var productImg = r.product && r.product.image ? r.product.image : null;
    var statusClass = STATUS_CLASSES[r.status] || STATUS_CLASSES.pending;
    var statusLabel = STATUS_LABELS[r.status] || r.status;

    var card = document.createElement('article');
    var isOverdue = isReservationOverdue(r);
    card.className = 'seller-reservations__card' + (isOverdue ? ' seller-reservations__card--overdue' : '');
    card.dataset.id = r.id;

    var pendingBadge = '';
    if (r.status === 'pending' && r.created_at) {
      var created = new Date(r.created_at.replace(' ', 'T'));
      var hoursAgo = Math.floor((Date.now() - created.getTime()) / 3600000);
      if (hoursAgo >= 2) {
        var label = hoursAgo >= 24 ? Math.floor(hoursAgo / 24) + 'd sin confirmar' : hoursAgo + 'h sin confirmar';
        pendingBadge = '<span style="font-size:0.65rem;font-weight:700;color:#f59e0b;background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.3);padding:0.15rem 0.5rem;border-radius:0.375rem;white-space:nowrap;">' + label + '</span>';
      }
    }

    var productHtml = '';
    if (productImg) {
      productHtml = '<img class="seller-reservations__card-product-img" src="' + escapeHtml(productImg) + '" alt="' + escapeHtml(productName) + '" loading="lazy">';
    } else {
      productHtml = '<div class="seller-reservations__card-product-img seller-reservations__card-product-img--placeholder"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg></div>';
    }

    var notesHtml = '';
    if (r.notes) {
      notesHtml =
        '<div class="seller-reservations__card-notes">' +
          '<span class="seller-reservations__card-notes-label">Notas del cliente</span>' +
          escapeHtml(r.notes) +
        '</div>';
    }

    var actionsHtml = buildActionsHtml(r);

    card.innerHTML =
      '<div class="seller-reservations__card-header">' +
        '<div class="seller-reservations__card-client">' +
          '<div class="seller-reservations__card-avatar" aria-hidden="true">' + escapeHtml(initials) + '</div>' +
          '<div class="seller-reservations__card-client-info">' +
            '<div class="seller-reservations__card-client-name">' + escapeHtml(clientName) + '</div>' +
            '<div class="seller-reservations__card-client-email">' + escapeHtml(r.client_email || '') + '</div>' +
            (r.client_phone ? '<div class="seller-reservations__card-client-email">' + escapeHtml(r.client_phone) + '</div>' : '') +
          '</div>' +
        '</div>' +
        '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.375rem;flex-shrink:0;">' +
          '<span class="seller-reservations__badge ' + statusClass + '">' +
            '<span class="seller-reservations__badge-dot" aria-hidden="true"></span>' +
            statusLabel +
          '</span>' +
          pendingBadge +
          '<span style="font-size:0.7rem;font-family:monospace;font-weight:700;color:#6a6966;background:#f5f1ea;border:1px solid #e8e0d0;padding:0.15rem 0.5rem;border-radius:0.375rem;">' +
            '#' + String(r.id).padStart(5, '0') +
          '</span>' +
        '</div>' +
      '</div>' +
      '<div class="seller-reservations__card-body">' +
        '<div class="seller-reservations__card-row">' +
          '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>' +
          '<div class="seller-reservations__card-product">' +
            productHtml +
            '<span class="seller-reservations__card-product-name">' + escapeHtml(productName) + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="seller-reservations__card-row">' +
          '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>' +
          '<span class="seller-reservations__card-datetime">' + formatDate(r.reservation_date) + ' \u00B7 ' + formatTime(r.reservation_time) + '</span>' +
        '</div>' +
        (r.quantity > 1 ?
          '<div class="seller-reservations__card-row">' +
            '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>' +
            '<span class="seller-reservations__card-datetime"><strong>' + r.quantity + ' unidades</strong></span>' +
          '</div>' : '') +
        notesHtml +
      '</div>' +
      '<div class="seller-reservations__card-actions">' +
        actionsHtml +
      '</div>';

    bindActionButtons(card, r);
    return card;
  }

  function buildActionsHtml(r) {
    var html = '';
    html += '<button type="button" class="seller-reservations__btn seller-reservations__btn--detail" data-action="detail">Ver Detalle</button>';

    if (r.status === 'pending') {
      html += '<button type="button" class="seller-reservations__btn seller-reservations__btn--confirm" data-action="confirm">Confirmar</button>';
    }
    if (r.status === 'confirmed') {
      html += '<button type="button" class="seller-reservations__btn seller-reservations__btn--complete" data-action="complete">Completar</button>';
    }
    if (r.status === 'pending' || r.status === 'confirmed') {
      html += '<button type="button" class="seller-reservations__btn seller-reservations__btn--cancel" data-action="cancel">Cancelar</button>';
    }
    return html;
  }

  function bindActionButtons(card, r) {
    card.querySelectorAll('[data-action]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var action = btn.getAttribute('data-action');
        switch (action) {
          case 'detail':
            showDetail(r);
            break;
          case 'confirm':
            updateStatus(r.id, 'confirmed', btn);
            break;
          case 'complete':
            updateStatus(r.id, 'completed', btn);
            break;
          case 'cancel':
            openCancelModal(r.id);
            break;
        }
      });
    });
  }

  /* =============================
     UPDATE STATUS
     ============================= */
  function updateStatus(id, newStatus, btnEl, reason) {
    if (btnEl) {
      btnEl.disabled = true;
      btnEl.textContent = '...';
    }

    var body = { status: newStatus };
    if (reason) {
      body.cancellation_reason = reason;
    }

    fetch('/reservations/' + id + '/status', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
      },
      body: JSON.stringify(body),
    })
      .then(function (res) {
        if (!res.ok) {
          return res.json().then(function (data) {
            throw new Error(data.message || 'Error al actualizar estado');
          });
        }
        return res.json();
      })
      .then(function (response) {
        if (response.success) {
          showToast(response.message || 'Estado actualizado.', 'success');
          loadReservations();
        } else {
          throw new Error(response.message || 'Error al actualizar estado');
        }
      })
      .catch(function (err) {
        if (btnEl) {
          btnEl.disabled = false;
          btnEl.textContent = btnEl.getAttribute('data-original-text') || 'Error';
        }
        showToast(err.message || 'Error de conexión.', 'error');
      });
  }

  /* =============================
     DETAIL MODAL
     ============================= */
  function showDetail(r) {
    var clientName = r.client_name || 'Sin nombre';
    var productName = r.product ? r.product.name : 'Producto eliminado';
    var statusLabel = STATUS_LABELS[r.status] || r.status;
    var phone = r.client_phone || 'No registrado';
    var orderNumber = '#' + String(r.id).padStart(5, '0');

    var html =
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">N\u00famero de Compra</span>' +
        '<div class="seller-reservations__modal-value" style="font-family:monospace;font-weight:700;color:#2d8c4e;">' + escapeHtml(orderNumber) + '</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Estado</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(statusLabel) + '</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Cliente</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(clientName) + '</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Email</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(r.client_email || '') + '</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Tel\u00E9fono</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(phone) + '</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Producto</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(productName) + '</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Fecha y Hora</span>' +
        '<div class="seller-reservations__modal-value">' + formatDate(r.reservation_date) + ' a las ' + formatTime(r.reservation_time) + '</div>' +
      '</div>';

    if (r.quantity > 1) {
      var price = r.product ? r.product.price : 0;
      var total = price * r.quantity;
      html +=
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Cantidad</span>' +
        '<div class="seller-reservations__modal-value">' + r.quantity + ' unidades</div>' +
      '</div>' +
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Total del Pedido</span>' +
        '<div class="seller-reservations__modal-value" style="color:#2d8c4e;font-weight:700;">$' + total.toLocaleString('es-AR') + '</div>' +
      '</div>';
    }

    if (r.notes) {
      html +=
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Notas del Pedido</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(r.notes) + '</div>' +
      '</div>';
    }

    if (r.cancellation_reason) {
      html +=
      '<div class="seller-reservations__modal-section">' +
        '<span class="seller-reservations__modal-label">Motivo de Cancelaci\u00F3n</span>' +
        '<div class="seller-reservations__modal-value">' + escapeHtml(r.cancellation_reason) + '</div>' +
      '</div>';
    }

    html +=
      '<div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #334155;">' +
        '<a href="/reservations/' + r.id + '/detail" ' +
           'style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8125rem;font-weight:600;color:#818cf8;text-decoration:none;">' +
          '<svg style="width:0.9rem;height:0.9rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">' +
            '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>' +
          '</svg>' +
          'Ver detalle completo' +
        '</a>' +
      '</div>';

    openModal(html);
  }

  /* =============================
     RENDER PAGINATION
     ============================= */
  function renderPagination(total, current, last) {
    var pag = document.getElementById('sr-pagination');
    if (!pag) return;
    pag.innerHTML = '';
    pag.classList.remove('sr-hidden');

    if (last <= 1) {
      pag.classList.add('sr-hidden');
      return;
    }

    var prevBtn = createPageBtn('Anterior', Math.max(1, current - 1));
    if (current <= 1) prevBtn.classList.add('seller-reservations__page-btn--disabled');
    pag.appendChild(prevBtn);

    var startPage = Math.max(1, current - 2);
    var endPage = Math.min(last, current + 2);

    if (startPage > 1) {
      pag.appendChild(createPageBtn('1', 1));
      if (startPage > 2) {
        var dots = document.createElement('span');
        dots.className = 'seller-reservations__page-info';
        dots.textContent = '...';
        pag.appendChild(dots);
      }
    }

    for (var i = startPage; i <= endPage; i++) {
      var pageBtn = createPageBtn(String(i), i);
      if (i === current) pageBtn.classList.add('seller-reservations__page-btn--active');
      pag.appendChild(pageBtn);
    }

    if (endPage < last) {
      if (endPage < last - 1) {
        var dots2 = document.createElement('span');
        dots2.className = 'seller-reservations__page-info';
        dots2.textContent = '...';
        pag.appendChild(dots2);
      }
      pag.appendChild(createPageBtn(String(last), last));
    }

    var nextBtn = createPageBtn('Siguiente', Math.min(last, current + 1));
    if (current >= last) nextBtn.classList.add('seller-reservations__page-btn--disabled');
    pag.appendChild(nextBtn);
  }

  function createPageBtn(label, page) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'seller-reservations__page-btn';
    btn.setAttribute('data-sr-page', String(page));
    btn.setAttribute('aria-label', 'Ir a p\u00E1gina ' + page);
    btn.textContent = label;
    return btn;
  }

  /* =============================
     TOAST
     ============================= */
  function showToast(message, type) {
    var existing = document.querySelector('.seller-reservations__toast');
    if (existing) existing.remove();

    var toast = document.createElement('div');
    toast.className = 'seller-reservations__toast seller-reservations__toast--' + type + ' seller-reservations__toast--visible';
    toast.textContent = message;
    toast.setAttribute('role', 'alert');
    document.body.appendChild(toast);

    setTimeout(function () {
      toast.classList.remove('seller-reservations__toast--visible');
      setTimeout(function () { toast.remove(); }, 300);
    }, 3000);
  }

  /* =============================
     UTILITIES
     ============================= */
  function isReservationOverdue(r) {
    if (!r.reservation_date) return false;
    if (r.status !== 'pending' && r.status !== 'confirmed') return false;
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var parts = r.reservation_date.split('-');
    var resDate = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    return resDate < today;
  }

  function updateExportLink() {
    var link = document.querySelector('.seller-reservations__export-btn');
    if (!link) return;
    var params = new URLSearchParams();
    if (state.status) params.set('status', state.status);
    if (state.search) params.set('search', state.search);
    if (state.date_from) params.set('date_from', state.date_from);
    if (state.date_to) params.set('date_to', state.date_to);
    params.set('sort_by', state.sort_by);
    params.set('sort_dir', state.sort_dir);
    link.href = '/reservations/manage/export?' + params.toString();
  }

  function formatDate(dateStr) {
    if (!dateStr) return '';
    var parts = dateStr.split('-');
    if (parts.length === 3) {
      return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    var d = new Date(dateStr + 'T12:00:00');
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
      .map(function (w) { return w.charAt(0); })
      .filter(function (c) { return c.match(/[a-z\u00E0-\u00FC]/i); })
      .slice(0, 2)
      .join('')
      .toUpperCase() || '?';
  }

  function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  document.addEventListener('DOMContentLoaded', init);

  return { init: init };
})();
