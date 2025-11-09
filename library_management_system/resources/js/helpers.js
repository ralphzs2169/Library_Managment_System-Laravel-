// ============= FORM ERROR HANDLING ==============



export function displayInputErrors(errors, form, allowScroll = true) {
    // Clear previous errors and reset all placeholders

    const errorPlaceholders = Array.from(document.querySelectorAll('.error-placeholder'));
  
    errorPlaceholders.forEach((placeholder, i) => {
        placeholder.textContent = '';
        placeholder.classList.remove('visible');
        placeholder.style.height = '';
    });

    for (const field in errors) {
    let inputElement, errorPlaceholder;
    let friendlyMessage;

        // Non-author fields
        const fieldMapping = {
            student_no: 'id_number',
            employee_no: 'id_number'
        };
        const inputId = fieldMapping[field] || field;
        inputElement = document.getElementById(inputId);
        errorPlaceholder = document.getElementById(inputId + '-error-placeholder');

        // Clean the error message by removing "edit_" prefix
        friendlyMessage = Array.isArray(errors[field]) ? errors[field].join(', ') : errors[field];
        friendlyMessage = friendlyMessage.replace(/\bedit_/gi, '').replace(/\bedit /gi, '');

    if (inputElement && errorPlaceholder) {
        inputElement.classList.add('invalid-input');
        errorPlaceholder.textContent = friendlyMessage;
        errorPlaceholder.classList.add('visible');
    }
      if (field === 'password') {
            const confirmPasswordElement = document.getElementById('confirm_password');
            const confirmPasswordPlaceholder = document.getElementById('confirm_password-error-placeholder');

            if (confirmPasswordElement && confirmPasswordPlaceholder) {
                confirmPasswordElement.classList.add('invalid-input');
                let cleanMessage = Array.isArray(errors[field]) ? errors[field].join(', ') : errors[field];
                cleanMessage = cleanMessage.replace(/\bedit_/gi, '').replace(/\bedit /gi, '');
                confirmPasswordPlaceholder.textContent = cleanMessage;
                confirmPasswordPlaceholder.classList.add('visible');
            }
        }
    }

    normalizePlaceholdersByRow();

    if (allowScroll) {
        scrollToFirstError(form);
    }
}

// Clear errors; optional recompute to avoid repeated recompute per field
export function clearInputError(inputField, recompute = true) {
    // Add null check to prevent the error
    if (!inputField) {
        console.warn('Input field is null');
        return;
    }

    const errorPlaceholder = document.getElementById(inputField.id + '-error-placeholder');

    if (errorPlaceholder) {
        errorPlaceholder.textContent = '';
        errorPlaceholder.classList.remove('visible');
        errorPlaceholder.style.height = '';
    }
    inputField.classList.remove('invalid-input');

    if (recompute) {
        normalizePlaceholdersByRow();
    }
}

// group placeholders by their top position and set equal height per group
export function normalizePlaceholdersByRow() {
    const placeholders = Array.from(document.querySelectorAll('#signup-form .error-placeholder'));
    if (!placeholders.length) return;

    // Reset any inline heights first
    placeholders.forEach(p => { p.style.height = ''; });

    // Group placeholders by their rounded top coordinate
    const groups = {};
    placeholders.forEach(p => {
        const rect = p.getBoundingClientRect();
        const topKey = Math.round(rect.top);
        if (!groups[topKey]) groups[topKey] = [];
        groups[topKey].push(p);
    });

    // For each group compute the max content height (scrollHeight) and set on all
    Object.values(groups).forEach(group => {
        let max = 0;
        group.forEach(p => {
            // scrollHeight works even when visibility:hidden
            const contentHeight = p.scrollHeight;
            if (contentHeight > max) max = contentHeight;
        });

        // Apply max (or clear if zero)
        group.forEach(p => {
            if (max > 0) {
                p.style.height = max + 'px';
            } else {
                p.style.height = '';
            }
        });
    });
}

// Smooth-scroll to first visible error placeholder
export function scrollToFirstError(formSelector = null) {
    if (!formSelector) return;
    const form = typeof formSelector === 'string' 
        ? document.querySelector(`#${formSelector}`) 
        : formSelector;
    if (!form) return;

    const firstError = form.querySelector('.error-placeholder:not(:empty)');
    if (!firstError) return;

    // Optional: adjust for fixed header height if needed
    const header = document.querySelector('header, .site-header');
    const headerHeight = header ? header.getBoundingClientRect().height + 20 : 60;

    const rect = firstError.getBoundingClientRect();
    const scrollY = window.pageYOffset + rect.top - headerHeight;

    window.scrollTo({ top: Math.max(0, scrollY), behavior: 'smooth' });
}

//clear all errors in a form
export function clearAllErrors(form) {
    if (!form) return;

    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        const errorPlaceholder = document.getElementById(input.id + '-error-placeholder');
        if (errorPlaceholder) {
            errorPlaceholder.textContent = '';
            errorPlaceholder.classList.remove('visible');
            errorPlaceholder.style.height = '';
        }

        input.classList.remove('invalid-input');
        input.style.borderColor = ''; // reset to default
        input.style.backgroundColor = ''; // optional if used for errors
    });
}


// ============= AUTO-CAPITALIZATION ==============
export function autoCapitalizeWords(field) {
  field.addEventListener('input', function () {
    let words = this.value.split(' ');
    this.value = words
      .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ');
  });
}

export function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export function autoCapitalizeOnBlur(fields) {
  fields.forEach(field => {
    field.addEventListener('blur', () => {
      const value = field.value.trim();
      if (value) field.value = capitalizeFirstLetter(value);
    });
  });
}

export function blurActiveElement() {
    const active = document.activeElement;
  if (active && ['INPUT','SELECT','TEXTAREA','BUTTON'].includes(active.tagName)) {
      try { active.blur(); } catch (err) { /* ignore */ }
  }
}