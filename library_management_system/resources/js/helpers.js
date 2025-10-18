// ============= FORM ERROR HANDLING ==============

export function displayInputErrors(errors) {
    // Clear previous errors and reset all placeholders
    const errorPlaceholders = Array.from(document.querySelectorAll('.error-placeholder'));
    console.log(errorPlaceholders);
    // clear text, visible class and any inline height from previous run
    errorPlaceholders.forEach(placeholder => {
        placeholder.textContent = '';
        placeholder.classList.remove('visible');
        placeholder.style.height = '';
    });

    // Display new errors
    for (const field in errors) {
        console.log(field);
        const fieldMapping = {
            'student_no': 'id_number',
            'employee_no': 'id_number'
        };

        const inputId = fieldMapping[field] || field;
        const inputElement = document.getElementById(inputId);
        console.log(inputElement + ' | ' + inputId);
        const errorPlaceholder = document.getElementById(inputId + '-error-placeholder');

        if (inputElement && errorPlaceholder) {
            // Add error styling to input
            inputElement.classList.add('invalid-input');

            // Show error text (we'll compute heights afterwards)
            errorPlaceholder.textContent = errors[field];
            errorPlaceholder.classList.add('visible');
        }

        // Special case: show password errors on both password and confirm_password fields
        if (field === 'password') {
            const confirmPasswordElement = document.getElementById('confirm_password');
            const confirmPasswordPlaceholder = document.getElementById('confirm_password-error-placeholder');

            if (confirmPasswordElement && confirmPasswordPlaceholder) {
                confirmPasswordElement.classList.add('invalid-input');
                confirmPasswordPlaceholder.textContent = errors[field];
                confirmPasswordPlaceholder.classList.add('visible');
            }
        }
    }

    // After rendering errors, compute and apply equalized heights per grid row
    normalizePlaceholdersByRow();

    // After heights applied, scroll to first invalid field smoothly
    scrollToFirstError();
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

// Smooth-scroll to first invalid input, focus and briefly highlight it
export function scrollToFirstError() {
	const firstInvalid = document.querySelector('#signup-form .invalid-input');
	if (!firstInvalid) return;

	// Try to account for a fixed header if present
	const header = document.querySelector('header, .site-header');
	const headerHeight = header ? header.getBoundingClientRect().height + 50 : 80;

	const rect = firstInvalid.getBoundingClientRect();
	const scrollY = window.pageYOffset + rect.top - headerHeight;

	// Smooth scroll
	window.scrollTo({ top: Math.max(0, scrollY), behavior: 'smooth' });
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