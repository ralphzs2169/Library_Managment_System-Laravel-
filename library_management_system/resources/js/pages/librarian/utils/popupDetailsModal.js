export async function openDetailsModal(modal, modalContent, data, initializer) {
    // Reset to initial hidden state before showing
    modal.classList.add('bg-opacity-0');
    modal.classList.remove('bg-opacity-50');
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    modal.classList.remove('hidden');
    
    // Use double requestAnimationFrame for smoother animation
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        });
    });
    
    await initializer(modal, data);
}

// Role badge logic
export function populateRoleBadge(modal, badge, borrower) {
    const roleBadge = modal.querySelector(badge);
    if (!roleBadge) return;

    const role = borrower.role ? borrower.role.charAt(0).toUpperCase() + borrower.role.slice(1) : 'N/A';

    // Clear existing content and classes
    roleBadge.innerHTML = '';
    roleBadge.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold';

    // Determine role and apply classes + icon
    let iconHTML = '';
    if (borrower.role === 'student') {
        roleBadge.classList.add('bg-blue-100', 'text-blue-700', 'border', 'border-blue-100');
        iconHTML = `<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">`;
    } else if (borrower.role === 'teacher') {
        roleBadge.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-100');
        iconHTML = `<img src="/build/assets/icons/teacher-role-badge.svg" alt="Teacher Badge" class="w-3.5 h-3.5">`;
    } else {
        roleBadge.classList.add('bg-gray-100', 'text-gray-600', 'border', 'border-gray-200');
        iconHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                    </svg>`;
    }

    // Set innerHTML with icon + role text
    roleBadge.innerHTML = `${iconHTML} ${role}`;
}