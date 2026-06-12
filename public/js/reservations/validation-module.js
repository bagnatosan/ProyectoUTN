const ValidationModule = (() => {
  const CONFIG = {
    nameMinLength: 3,
    nameMaxLength: 100,
    phoneMinLength: 7,
    notesMaxLength: 500,
  };

  function validateName(value) {
    const errors = [];
    const v = (value || '').trim();
    if (!v) {
      errors.push('El nombre es obligatorio.');
    } else if (v.length < CONFIG.nameMinLength) {
      errors.push('El nombre debe tener al menos ' + CONFIG.nameMinLength + ' caracteres.');
    } else if (v.length > CONFIG.nameMaxLength) {
      errors.push('El nombre no puede exceder ' + CONFIG.nameMaxLength + ' caracteres.');
    }
    return errors;
  }

  function validateEmail(value) {
    const errors = [];
    const v = (value || '').trim();
    if (!v) {
      errors.push('El correo electrónico es obligatorio.');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
      errors.push('El correo electrónico no es válido.');
    }
    return errors;
  }

  function validatePhone(value) {
    const errors = [];
    const v = (value || '').trim();
    if (!v) {
      errors.push('El teléfono es obligatorio.');
    } else if (v.replace(/[\s\-\+\(\)]/g, '').length < CONFIG.phoneMinLength) {
      errors.push('El teléfono debe tener al menos ' + CONFIG.phoneMinLength + ' dígitos.');
    }
    return errors;
  }

  function validateNotes(value) {
    const errors = [];
    if (value && value.length > CONFIG.notesMaxLength) {
      errors.push('Las notas no pueden exceder ' + CONFIG.notesMaxLength + ' caracteres.');
    }
    return errors;
  }

  function validateDate(value) {
    const errors = [];
    if (!value) {
      errors.push('Debes seleccionar una fecha.');
    }
    return errors;
  }

  function validateTime(value) {
    const errors = [];
    if (!value) {
      errors.push('Debes seleccionar un horario.');
    }
    return errors;
  }

  function validateAll(data) {
    const errors = {};
    const checks = {
      name: validateName(data.name),
      email: validateEmail(data.email),
      phone: validatePhone(data.phone),
      notes: validateNotes(data.notes),
      date: validateDate(data.date),
      time: validateTime(data.time),
    };
    Object.entries(checks).forEach(([field, fieldErrors]) => {
      if (fieldErrors.length > 0) {
        errors[field] = fieldErrors;
      }
    });
    return { errors, isValid: Object.keys(errors).length === 0 };
  }

  return { validateName, validateEmail, validatePhone, validateNotes, validateDate, validateTime, validateAll };
})();
