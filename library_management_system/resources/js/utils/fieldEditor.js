/**
 * Enable editing for a specific input field
 * @param {HTMLElement} input - The input element to enable
 */
export function enableFieldEdit(input) {
    if (!input) return;
    
    input.disabled = false;
    input.focus();
    
    // For text inputs, move cursor to end (skip for number/date/select)
    if (input.type === 'text' || input.tagName === 'TEXTAREA') {
        const length = input.value.length;
        input.setSelectionRange(length, length);
    }

    if(input.tagName === 'SELECT') {
            input.classList.remove('appearance-none');
    }
}

/**
 * Disable editing for a specific input field
 * @param {HTMLElement} input - The input element to disable
 */
export function disableFieldEdit(input) {
    if (!input) return;
    input.disabled = true;
    // console.log('Disabling field edit for', input);
    if(input.tagName === 'SELECT') {
            input.classList.add('appearance-none');
    }
}

export function initializeEditableFields(container) {
    const containerEl = typeof container === 'string' 
        ? document.querySelector(container) 
        : container;
    
    if (!containerEl) return;
    
    // Find all field containers with inputs
    const fieldContainers = containerEl.querySelectorAll('.field-container');
    
    fieldContainers.forEach(fieldContainer => {
        const input = fieldContainer.querySelector('input, textarea, select');
        
        if (!input) return;
        
        // Skip checkboxes and radio buttons
        if (input.type === 'checkbox' || input.type === 'radio') return;

        // Skip if already initialized
        if (input.parentElement.querySelector('.edit-field-icon')) return;
        
        // Disable input initially
        input.disabled = true;
        
        // Wrap input in relative container if not already
        let wrapper = input.parentElement;
        if (!wrapper.classList.contains('relative')) {
            wrapper = document.createElement('div');
            wrapper.className = 'relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
        }
        
        // Create edit icon
        const editIcon = document.createElement('button');
        editIcon.type = 'button';
        editIcon.className = 'edit-field-icon absolute right-3 top-1/2 -translate-y-1/2 text-accent hover:text-accent/80 transition cursor-pointer z-10';
        editIcon.innerHTML = `
            <div class="relative group">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    Click to edit
                     <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-800"></div>
                </div>
            </div>
        `;

        
        // Add icon inside the wrapper
        wrapper.appendChild(editIcon);
        
        // Adjust input padding to prevent text overlap with icon
        input.style.paddingRight = '2.5rem';
        
        // Handle edit icon click
        editIcon.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            enableFieldEdit(input);
            editIcon.style.display = 'none'; // Hide icon when editing
        });
        
        // Handle input blur (disable when unfocused)
        input.addEventListener('blur', () => {
            // Small delay to allow other interactions
            setTimeout(() => {
                disableFieldEdit(input);
                editIcon.style.display = 'block'; // Show icon again
            }, 100);
        });
        
        // Prevent form submission on Enter for text inputs (except textarea)
        if (input.tagName !== 'TEXTAREA') {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                }
            });
        }

        if(input.tagName === 'SELECT') {
            input.classList.add('appearance-none');
            disableFieldEdit(input);
        }
    });
}


export function enableAllFields(container) {
    const containerEl = typeof container === 'string' 
        ? document.querySelector(container) 
        : container;
    
    if (!containerEl) return;
    
    const inputs = containerEl.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.disabled = false;
        if(input.tagName === 'SELECT') {
            input.classList.remove('appearance-none');
        }
    });
}

export function resetEditableFields(container) {
    const containerEl = typeof container === 'string' 
        ? document.querySelector(container) 
        : container;
    
    if (!containerEl) return;
    
    const inputs = containerEl.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        // Skip hidden inputs, cover image input, and copies management fields
        if (input.type === 'hidden' || 
            input.id === 'edit-cover-input' || 
            input.closest('.copy-row') || 
            input.closest('#copies-table-container')) {
            return;
        }

        // Disable input
        input.disabled = true;
        
        if(input.tagName === 'SELECT') {
            input.classList.add('appearance-none');
        }

        // Show icon if it exists in the wrapper
        const wrapper = input.parentElement;
        if (wrapper.classList.contains('relative')) {
            const icon = wrapper.querySelector('.edit-field-icon');
            if (icon) {
                icon.style.display = 'block';
            }
        }
    });
}
