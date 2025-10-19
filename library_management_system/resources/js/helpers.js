// ============= FORM ERROR HANDLING ==============



export function displayInputErrors(errors) {
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

    if (field.startsWith('authors.')) {
        // Split authors.0.firstname
        const parts = field.split('.');
        const index = parseInt(parts[1], 10); // 0-based
        const subfield = parts[2]; // firstname, lastname, middle_initial

        // Find input element by name
        inputElement = document.querySelector(`input[name="authors[${index}][${subfield}]"]`);
        if (!inputElement) continue;

        // Create or reuse error placeholder
        errorPlaceholder = inputElement.parentElement.querySelector('.error-placeholder');
        if (!errorPlaceholder) {
            errorPlaceholder = document.createElement('div');
            errorPlaceholder.classList.add('error-placeholder', 'text-red-600', 'text-sm', 'mt-1');
            inputElement.parentElement.appendChild(errorPlaceholder);
        }

        // Friendly field names mapping
        const fieldNames = {
            firstname: 'First Name',
            lastname: 'Last Name',
            middle_initial: 'Middle Initial'
        };
        const nameText = fieldNames[subfield] || subfield;

        // Prefix with Author number if not the first
        const authorText = index === 0 ? 'Author' : `Author ${index + 1}`;

        // Laravel error messages are arrays, join them
        const rawMessage = Array.isArray(errors[field]) ? errors[field].join(', ') : errors[field];

        // Replace the original field mention with friendly text
        friendlyMessage = rawMessage.replace(new RegExp(`authors\\.\\d+\\.${subfield}`, 'g'), `${authorText} ${nameText}`);

    } else {
        // Non-author fields
        const fieldMapping = {
            student_no: 'id_number',
            employee_no: 'id_number'
        };
        const inputId = fieldMapping[field] || field;
        inputElement = document.getElementById(inputId);
        errorPlaceholder = document.getElementById(inputId + '-error-placeholder');

        friendlyMessage = Array.isArray(errors[field]) ? errors[field].join(', ') : errors[field];
    }

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
                confirmPasswordPlaceholder.textContent = Array.isArray(errors[field]) ? errors[field].join(', ') : errors[field];
                confirmPasswordPlaceholder.classList.add('visible');
            }
        }
    }

    normalizePlaceholdersByRow();
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