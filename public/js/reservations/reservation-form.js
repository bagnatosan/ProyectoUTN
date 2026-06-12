const ReservationForm = (() => {
  function init(config) {
    config = config || {};
    var formEl = document.getElementById(config.formId || 'reservation-form');
    var calendarContainer = document.getElementById(config.calendarContainerId || 'calendar-container');
    var timeSlotsContainer = document.getElementById(config.timeSlotsContainerId || 'time-slots-container');
    var feedbackEl = document.getElementById(config.feedbackId || 'form-feedback');

    var productSelect = formEl ? formEl.querySelector('[name="product_id"]') : null;
    var nameInput = formEl ? formEl.querySelector('[name="client_name"]') : null;
    var emailInput = formEl ? formEl.querySelector('[name="client_email"]') : null;
    var phoneInput = formEl ? formEl.querySelector('[name="client_phone"]') : null;
    var notesInput = formEl ? formEl.querySelector('[name="notes"]') : null;
    var dateInput = formEl ? formEl.querySelector('[name="reservation_date"]') : null;
    var timeInput = formEl ? formEl.querySelector('[name="reservation_time"]') : null;

    var selectedDate = null;
    var selectedTime = null;
    var calendar = null;
    var timeSlots = null;

    function getBusinessProfileId() {
      if (!productSelect) return null;
      var option = productSelect.options[productSelect.selectedIndex];
      if (option && option.dataset.businessProfileId) {
        return option.dataset.businessProfileId;
      }
      var selectedValue = productSelect.value;
      if (!selectedValue) return null;
      var allOptions = productSelect.querySelectorAll('option');
      for (var i = 0; i < allOptions.length; i++) {
        if (allOptions[i].value === selectedValue) {
          return allOptions[i].dataset.businessProfileId || null;
        }
      }
      return null;
    }

    function initCalendar() {
      if (!calendarContainer) return;
      calendar = CalendarComponent.create(calendarContainer, {
        onDateSelect: function (dateStr) {
          selectedDate = dateStr;
          selectedTime = null;
          if (dateInput) dateInput.value = dateStr;
          if (timeInput) timeInput.value = '';
          if (timeSlots) timeSlots.reset();
          fetchAvailability(dateStr);
        },
      });
    }

    function initTimeSlots() {
      if (!timeSlotsContainer) return;
      timeSlots = TimeSlotSelector.create(timeSlotsContainer, {
        onTimeSelect: function (time) {
          selectedTime = time;
          if (timeInput) timeInput.value = time;
        },
      });
    }

    async function fetchAvailability(dateStr) {
      var bpId = getBusinessProfileId();
      if (!bpId) {
        if (timeSlots) timeSlots.render('empty');
        return;
      }
      if (timeSlots) timeSlots.render('loading');
      if (calendar) calendar.setDateState(dateStr, null);
      try {
        var result = await AvailabilityService.fetchSlots(bpId, dateStr);
        var slots = result.slots || [];
        if (slots.length > 0) {
          if (timeSlots) timeSlots.render('success', slots);
          if (calendar) calendar.setDateState(dateStr, 'available');
        } else {
          if (timeSlots) timeSlots.render('empty');
          if (calendar) calendar.setDateState(dateStr, 'unavailable');
        }
      } catch (err) {
        if (timeSlots) timeSlots.render('error');
      }
    }

    function showFieldErrors(errors) {
      var fields = ['name', 'email', 'phone', 'notes', 'date', 'time'];
      fields.forEach(function (field) {
        var input = formEl.querySelector('[data-validate="' + field + '"]');
        var errorContainer = formEl.querySelector('[data-error-for="' + field + '"]');
        if (errorContainer) {
          errorContainer.innerHTML = '';
          errorContainer.classList.add('form-field__errors--hidden');
        }
        if (input) {
          input.classList.remove('form-field__input--error');
          input.removeAttribute('aria-invalid');
          input.removeAttribute('aria-describedby');
        }
      });
      Object.keys(errors).forEach(function (field) {
        var input = formEl.querySelector('[data-validate="' + field + '"]');
        var errorContainer = formEl.querySelector('[data-error-for="' + field + '"]');
        if (input) {
          input.classList.add('form-field__input--error');
          input.setAttribute('aria-invalid', 'true');
          if (errorContainer) {
            input.setAttribute('aria-describedby', errorContainer.id);
          }
        }
        if (errorContainer) {
          errorContainer.innerHTML = '';
          errorContainer.classList.remove('form-field__errors--hidden');
          errors[field].forEach(function (msg) {
            var li = document.createElement('li');
            li.textContent = msg;
            errorContainer.appendChild(li);
          });
        }
      });
    }

    function clearFieldErrors() {
      showFieldErrors({});
    }

    function validateForm() {
      var data = {
        name: nameInput ? nameInput.value : '',
        email: emailInput ? emailInput.value : '',
        phone: phoneInput ? phoneInput.value : '',
        notes: notesInput ? notesInput.value : '',
        date: dateInput ? dateInput.value : '',
        time: timeInput ? timeInput.value : '',
      };
      var result = ValidationModule.validateAll(data);
      showFieldErrors(result.errors);
      return result;
    }

    function showFeedback(type, message) {
      if (!feedbackEl) return;
      feedbackEl.className = 'form-feedback form-feedback--' + type;
      feedbackEl.setAttribute('role', type === 'error' ? 'alert' : 'status');
      feedbackEl.setAttribute('aria-live', 'polite');
      feedbackEl.innerHTML = message;
      feedbackEl.classList.remove('form-feedback--hidden');
      feedbackEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function hideFeedback() {
      if (!feedbackEl) return;
      feedbackEl.classList.add('form-feedback--hidden');
    }

    function handleSubmit(e) {
      hideFeedback();
      var validation = validateForm();
      if (!validation.isValid) {
        e.preventDefault();
        showFeedback('error', 'Corrige los errores marcados en el formulario antes de continuar.');
        return;
      }
    }

    function handleProductChange() {
      if (calendar) calendar.reset();
      if (timeSlots) timeSlots.reset();
      selectedDate = null;
      selectedTime = null;
      if (dateInput) dateInput.value = '';
      if (timeInput) timeInput.value = '';
      hideFeedback();
    }

    function initForm() {
      if (!formEl) return;
      formEl.addEventListener('submit', handleSubmit);
      if (productSelect) {
        productSelect.addEventListener('change', handleProductChange);
      }
      if (nameInput) {
        nameInput.addEventListener('blur', function () {
          var errs = ValidationModule.validateName(nameInput.value);
          var container = formEl.querySelector('[data-error-for="name"]');
          if (container) {
            container.innerHTML = '';
            container.classList.add('form-field__errors--hidden');
          }
          nameInput.classList.remove('form-field__input--error');
          nameInput.removeAttribute('aria-invalid');
          if (errs.length > 0 && nameInput.value.trim()) {
            nameInput.classList.add('form-field__input--error');
            nameInput.setAttribute('aria-invalid', 'true');
            if (container) {
              container.classList.remove('form-field__errors--hidden');
              errs.forEach(function (msg) {
                var li = document.createElement('li');
                li.textContent = msg;
                container.appendChild(li);
              });
            }
          }
        });
      }
      if (emailInput) {
        emailInput.addEventListener('blur', function () {
          var errs = ValidationModule.validateEmail(emailInput.value);
          var container = formEl.querySelector('[data-error-for="email"]');
          if (container) {
            container.innerHTML = '';
            container.classList.add('form-field__errors--hidden');
          }
          emailInput.classList.remove('form-field__input--error');
          emailInput.removeAttribute('aria-invalid');
          if (errs.length > 0 && emailInput.value.trim()) {
            emailInput.classList.add('form-field__input--error');
            emailInput.setAttribute('aria-invalid', 'true');
            if (container) {
              container.classList.remove('form-field__errors--hidden');
              errs.forEach(function (msg) {
                var li = document.createElement('li');
                li.textContent = msg;
                container.appendChild(li);
              });
            }
          }
        });
      }
      if (phoneInput) {
        phoneInput.addEventListener('blur', function () {
          var errs = ValidationModule.validatePhone(phoneInput.value);
          var container = formEl.querySelector('[data-error-for="phone"]');
          if (container) {
            container.innerHTML = '';
            container.classList.add('form-field__errors--hidden');
          }
          phoneInput.classList.remove('form-field__input--error');
          phoneInput.removeAttribute('aria-invalid');
          if (errs.length > 0 && phoneInput.value.trim()) {
            phoneInput.classList.add('form-field__input--error');
            phoneInput.setAttribute('aria-invalid', 'true');
            if (container) {
              container.classList.remove('form-field__errors--hidden');
              errs.forEach(function (msg) {
                var li = document.createElement('li');
                li.textContent = msg;
                container.appendChild(li);
              });
            }
          }
        });
      }
      if (notesInput) {
        var counter = formEl.querySelector('[data-counter-for="notes"]');
        notesInput.addEventListener('input', function () {
          if (counter) {
            counter.textContent = notesInput.value.length + '/500';
          }
        });
      }
    }

    initCalendar();
    initTimeSlots();
    initForm();
  }

  return { init: init };
})();
