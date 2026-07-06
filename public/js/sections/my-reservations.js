(function () {
  'use strict';

  var MyReservations = {
    filters: {},
    currentPage: 1,
    lastPage: 1,
    isLoading: false,
    loadMoreEnabled: false,

    els: {},

    init: function () {
      this.els = {
        search: document.getElementById('mr-search'),
        status: document.getElementById('mr-filter-status'),
        product: document.getElementById('mr-filter-product'),
        sort: document.getElementById('mr-filter-sort'),
        scope: document.getElementById('mr-filter-scope'),
        dateFrom: document.getElementById('mr-date-from'),
        dateTo: document.getElementById('mr-date-to'),
        hasNotes: document.getElementById('mr-filter-notes'),
        clearBtn: document.getElementById('mr-clear-filters'),
        retryBtn: document.getElementById('mr-retry-btn'),
        loadMoreBtn: document.getElementById('mr-load-more-btn'),
        filtersToggle: document.getElementById('mr-filters-toggle'),
        filtersPanel: document.getElementById('mr-filters-panel'),
        list: document.getElementById('mr-list'),
        loader: document.getElementById('mr-loader'),
        empty: document.getElementById('mr-empty'),
        emptyTitle: document.getElementById('mr-empty-title'),
        emptyText: document.getElementById('mr-empty-text'),
        error: document.getElementById('mr-error'),
        resultsCount: document.getElementById('mr-results-count'),
        loadMore: document.getElementById('mr-load-more'),
        quickBtns: document.querySelectorAll('.mr-quick-btn'),
        filterSelects: document.querySelectorAll('[data-filter]'),
      };

      this.bindEvents();
      this.fetchReservations(true);
    },

    bindEvents: function () {
      var self = this;

      this.els.search.addEventListener('input', function () {
        self.debouncedFetch();
      });

      this.els.status.addEventListener('change', function () {
        self.onFilterChange();
      });
      this.els.product.addEventListener('change', function () {
        self.onFilterChange();
      });
      this.els.sort.addEventListener('change', function () {
        self.onFilterChange();
      });
      this.els.scope.addEventListener('change', function () {
        self.onFilterChange();
      });
      this.els.dateFrom.addEventListener('change', function () {
        self.onFilterChange();
      });
      this.els.dateTo.addEventListener('change', function () {
        self.onFilterChange();
      });
      this.els.hasNotes.addEventListener('change', function () {
        self.onFilterChange();
      });

      this.els.quickBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          self.els.quickBtns.forEach(function (b) { b.classList.remove('mr-quick-btn--active'); });
          this.classList.add('mr-quick-btn--active');
          self.filters.quick_filter = this.getAttribute('data-quick');
          self.onFilterChange();
        });
      });

      this.els.clearBtn.addEventListener('click', function () {
        self.resetFilters();
      });

      this.els.retryBtn.addEventListener('click', function () {
        self.fetchReservations(true);
      });

      this.els.loadMoreBtn.addEventListener('click', function () {
        if (!self.isLoading && self.currentPage < self.lastPage) {
          self.currentPage++;
          self.fetchReservations(false);
        }
      });

      this.els.filtersToggle.addEventListener('click', function () {
        self.els.filtersPanel.classList.toggle('mr-filters__panel--open');
        self.els.filtersToggle.classList.toggle('mr-filters__toggle--open');
      });
    },

    debouncedFetch: null,

    onFilterChange: function () {
      this.currentPage = 1;
      this.collectFilters();
      this.fetchReservations(true);
    },

    collectFilters: function () {
      this.filters = {};

      var searchVal = this.els.search.value.trim();
      if (searchVal.length > 0) this.filters.search = searchVal;

      if (this.els.status.value) this.filters.status = this.els.status.value;
      if (this.els.product.value) this.filters.product_id = this.els.product.value;
      if (this.els.sort.value) this.filters.sort = this.els.sort.value;
      if (this.els.scope.value) this.filters.reservation_scope = this.els.scope.value;
      if (this.els.dateFrom.value) this.filters.date_from = this.els.dateFrom.value;
      if (this.els.dateTo.value) this.filters.date_to = this.els.dateTo.value;
      if (this.els.hasNotes.checked) this.filters.has_notes = '1';

      var activeQuick = document.querySelector('.mr-quick-btn--active');
      if (activeQuick) {
        this.filters.quick_filter = activeQuick.getAttribute('data-quick');
      }
    },

    buildQueryString: function () {
      var params = new URLSearchParams();
      params.set('page', this.currentPage);

      for (var key in this.filters) {
        if (this.filters.hasOwnProperty(key) && this.filters[key] !== undefined && this.filters[key] !== '') {
          params.set(key, this.filters[key]);
        }
      }

      return params.toString();
    },

    fetchReservations: function (replace) {
      if (this.isLoading) return;
      this.isLoading = true;

      if (replace) {
        this.els.list.innerHTML = '';
        this.showLoader();
        this.hideError();
      }

      var self = this;
      var qs = this.buildQueryString();

      fetch('/my-reservations/data?' + qs, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      })
      .then(function (response) {
        if (!response.ok) throw new Error('Network error');
        return response.json();
      })
      .then(function (data) {
        self.hideLoader();
        self.hideError();

        if (data.success) {
          self.lastPage = data.last_page;
          self.loadMoreEnabled = data.current_page < data.last_page;

          if (replace) {
            self.els.list.innerHTML = '';
          }

          if (data.data.length === 0 && self.els.list.children.length === 0) {
            self.showEmpty();
            self.els.loadMore.style.display = 'none';
          } else {
            self.hideEmpty();
            data.data.forEach(function (r) {
              self.els.list.appendChild(self.renderCard(r));
            });
            self.els.loadMore.style.display = self.loadMoreEnabled ? '' : 'none';
          }

          self.updateResultCount(data.total);
        } else {
          self.showError();
        }
      })
      .catch(function () {
        self.hideLoader();
        if (self.els.list.children.length === 0) {
          self.showError();
        }
      })
      .finally(function () {
        self.isLoading = false;
      });
    },

    renderCard: function (r) {
      var card = document.createElement('div');
      card.className = 'cr-card';
      card.setAttribute('data-status', r.status);

      var imgHtml = '';
      if (r.product && r.product.image) {
        imgHtml = '<img class="cr-card__image" src="/storage/' + r.product.image + '" alt="' + this.escHtml(r.product.name) + '" loading="lazy">';
      } else {
        imgHtml = '<div class="cr-card__image cr-card__image--placeholder">' + (r.product && r.product.name ? this.escHtml(r.product.name.charAt(0)) : '?') + '</div>';
      }

      var statusLabel = this.getStatusLabel(r.status);
      var totalPrice = (r.product && r.product.price) ? '$' + (r.product.price * (r.quantity || 1)).toFixed(2) : '';

      var formattedDate = this.formatDate(r.reservation_date);
      var sellerName = r.product ? this.escHtml(r.product.business_name) : 'Emprendedor';

      var notesHtml = '';
      if (r.notes) {
        notesHtml = '<p class="cr-card__notes">' + this.escHtml(r.notes) + '</p>';
      }

      var cancelReasonHtml = '';
      if (r.status === 'cancelled' && r.cancellation_reason) {
        cancelReasonHtml = '<p class="cr-card__cancel-reason">Motivo: ' + this.escHtml(r.cancellation_reason) + '</p>';
      }

      var modifiedHtml = '';
      if (r.was_modified) {
        modifiedHtml = '<p class="cr-card__modified">Modificada</p>';
      }

      var actionsHtml = '';
      var minDate = new Date();
      minDate.setDate(minDate.getDate() + 2);
      var resDate = new Date(r.reservation_date + 'T00:00:00');
      var canModify = r.status === 'pending' && resDate >= minDate;

      actionsHtml += '<span class="cr-badge cr-badge--' + r.status + '">' + statusLabel + '</span>';

      if (canModify) {
        actionsHtml += '<a href="/reservations/' + r.id + '/edit" class="cr-btn cr-btn--primary">Modificar</a>';
      }

      if (r.can_cancel) {
        actionsHtml += '<button type="button" class="cr-btn cr-btn--danger cr-cancel-trigger" data-id="' + r.id + '" data-product="' + this.escHtml(r.product ? r.product.name : 'Producto') + '" data-date="' + this.escHtml(formattedDate) + '" data-time="' + this.escHtml(r.reservation_time) + '">Cancelar</button>';
      }

      var quantityHtml = (r.quantity && r.quantity > 1) ? '<span class="cr-card__meta-item">' + r.quantity + ' unid.</span>' : '';

      card.innerHTML =
        imgHtml +
        '<div class="cr-card__body">' +
          '<div class="flex items-center justify-between mb-1">' +
            '<h3 class="cr-card__product">' + (r.product ? this.escHtml(r.product.name) : 'Producto eliminado') + '</h3>' +
            '<span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0 ml-2">#' + String(r.id).padStart(5, '0') + '</span>' +
          '</div>' +
          '<p class="cr-card__seller">' + sellerName + '</p>' +
          '<div class="cr-card__meta">' +
            '<span class="cr-card__meta-item">' + formattedDate + '</span>' +
            '<span class="cr-card__meta-item">' + this.escHtml(r.reservation_time) + ' hs</span>' +
            quantityHtml +
            (totalPrice ? '<span class="cr-card__meta-item cr-card__price">' + totalPrice + '</span>' : '') +
          '</div>' +
          modifiedHtml +
          notesHtml +
          cancelReasonHtml +
        '</div>' +
        '<div class="cr-card__actions">' + actionsHtml + '</div>';

      return card;
    },

    formatDate: function (dateStr) {
      var parts = dateStr.split('-');
      if (parts.length === 3) {
        return parts[2] + '/' + parts[1] + '/' + parts[0];
      }
      return dateStr;
    },

    getStatusLabel: function (status) {
      var labels = {
        pending: 'Pendiente',
        confirmed: 'Confirmada',
        completed: 'Completada',
        cancelled: 'Cancelada',
      };
      return labels[status] || 'Desconocido';
    },

    showLoader: function () {
      this.els.loader.style.display = '';
    },

    hideLoader: function () {
      this.els.loader.style.display = 'none';
    },

    showEmpty: function () {
      this.els.empty.style.display = '';
    },

    hideEmpty: function () {
      this.els.empty.style.display = 'none';
    },

    showError: function () {
      this.els.error.style.display = '';
      this.els.list.style.display = 'none';
    },

    hideError: function () {
      this.els.error.style.display = 'none';
      this.els.list.style.display = '';
    },

    updateResultCount: function (total) {
      if (total > 0) {
        this.els.resultsCount.textContent = total + ' reserva' + (total !== 1 ? 's' : '') + ' encontrada' + (total !== 1 ? 's' : '');
      } else {
        this.els.resultsCount.textContent = '';
      }
    },

    resetFilters: function () {
      this.els.search.value = '';
      this.els.status.value = '';
      this.els.product.value = '';
      this.els.sort.value = 'date_desc';
      this.els.scope.value = '';
      this.els.dateFrom.value = '';
      this.els.dateTo.value = '';
      this.els.hasNotes.checked = false;

      this.els.quickBtns.forEach(function (b) { b.classList.remove('mr-quick-btn--active'); });

      this.filters = {};
      this.currentPage = 1;
      this.fetchReservations(true);
    },

    escHtml: function (str) {
      if (typeof str !== 'string') return str;
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(str));
      return div.innerHTML;
    },
  };

  MyReservations.debouncedFetch = (function () {
    var timer;
    var self = MyReservations;
    return function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        self.currentPage = 1;
        self.collectFilters();
        self.fetchReservations(true);
      }, 300);
    };
  })();

  var cancelCurrentId = null;

  document.addEventListener('DOMContentLoaded', function () {
    MyReservations.init();

    document.getElementById('mr-list').addEventListener('click', function (e) {
      var btn = e.target.closest('.cr-cancel-trigger');
      if (btn) {
        cancelCurrentId = btn.getAttribute('data-id');
        var modal = document.getElementById('cr-cancel-modal');
        var modalInfo = document.getElementById('cr-cancel-info');
        document.getElementById('cr-cancel-reason').value = '';
        modalInfo.textContent = '¿Estás seguro de cancelar la reserva de "' + btn.getAttribute('data-product') + '" para el ' + btn.getAttribute('data-date') + ' a las ' + btn.getAttribute('data-time') + '?';
        modal.classList.remove('cr-modal-overlay--hidden');
      }
    });

    var modal = document.getElementById('cr-cancel-modal');
    var modalClose = document.getElementById('cr-cancel-close');
    var modalConfirm = document.getElementById('cr-cancel-confirm');
    var cancelReason = document.getElementById('cr-cancel-reason');

    function closeCancelModal() {
      modal.classList.add('cr-modal-overlay--hidden');
      cancelCurrentId = null;
    }

    modalClose.addEventListener('click', closeCancelModal);
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeCancelModal();
    });

    modalConfirm.addEventListener('click', function () {
      if (!cancelCurrentId) { closeCancelModal(); return; }

      var formData = new FormData();
      formData.append('reason', cancelReason.value);

      fetch('/reservations/' + cancelCurrentId + '/cancel', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
        body: formData,
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          MyReservations.currentPage = 1;
          MyReservations.collectFilters();
          MyReservations.fetchReservations(true);
        } else {
          alert(data.message || 'Error al cancelar la reserva.');
        }
      })
      .catch(function () {
        alert('Error de conexión. Intentalo de nuevo.');
      })
      .finally(function () {
        closeCancelModal();
      });
    });
  });

})();