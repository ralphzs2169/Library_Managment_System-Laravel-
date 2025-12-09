import { formatDate } from "../../utils.js";
import { openDetailsModal } from "./utils/popupDetailsModal.js";
import { showError } from "../../utils/alerts.js";
import { populateRoleBadge } from "./utils/popupDetailsModal.js";

// Initialize activity log details modal
export function initActivityLogDetailsListeners() {
    const detailButtons = document.querySelectorAll('.view-activity-details');
    
    detailButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const activityLog = JSON.parse(this.getAttribute('data-activity-log'));
            console.log(activityLog);
            if (!activityLog) {
                showError('Data Error', 'Activity log data is missing or invalid.');
                return;
            }

            const modal = document.getElementById('activity-log-details-modal');
            const modalContent = document.getElementById('activity-log-details-content');
            
            if (!modal || !modalContent) {
                showError('UI Error', 'Activity log details modal not found in DOM.');
                return;
            }

            // Scroll to top before showing
            const scrollableContainer = modalContent.querySelector('.px-6.py-5.overflow-y-auto');
            if (scrollableContainer) {
                scrollableContainer.scrollTop = 0;
            }

            await openDetailsModal(modal, modalContent, { activityLog }, initializeActivityLogDetailsModal);
        });
    });
}

let activityLogModalListenersInitialized = false;

function initializeActivityLogDetailsModal(modal, data) {
    const log = data.activityLog;
    
    // Handle null user (System)
    const user = log.user || {
        firstname: 'System',
        lastname: 'Automation',
        full_name: 'System Automation',
        role: 'system'
    };

    // User info
    const userInitials = document.getElementById('log-user-initials');
    const userName = document.getElementById('log-user-name');
    const userId = document.getElementById('log-user-id');

    userInitials.textContent = (user.firstname?.[0] ?? '') + (user.lastname?.[0] ?? '');
    userName.textContent = user.full_name ?? 'N/A';
    
    if (user.role === 'student') {
        userId.textContent = user.students?.student_number ?? 'N/A';
    } else if (user.role === 'teacher') {
        userId.textContent = user.teachers?.employee_number ?? 'N/A';
    } else if (user.role === 'system') {
        userId.textContent = 'N/A';
    } else {
        userId.textContent = user.role;
    }

    // Populate role badge
    populateRoleBadge(modal, '#log-user-role-badge', user);
    
    // Manual override for system role if populateRoleBadge doesn't handle it
    if (user.role === 'system') {
        const roleBadge = modal.querySelector('#log-user-role-badge');
        if (roleBadge) {
             roleBadge.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-100';
             roleBadge.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35-.61.614-.736 1.543-.377 2.573.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35.61-.614.736-1.543.377-2.573-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg> System`;
        }
    }

    // Activity details
    document.getElementById('log-action').textContent = log.action.charAt(0).toUpperCase() + log.action.slice(1);
    document.getElementById('log-entity-type').textContent = log.entity_type;
    document.getElementById('log-entity-id').textContent = log.entity_id;
    document.getElementById('log-timestamp').textContent = formatDate(log.created_at);
    document.getElementById('log-details').textContent = log.details;

    // Attach close modal listeners only once
    if (!activityLogModalListenersInitialized) {
        activityLogModalListenersInitialized = true;

        const closeBtn = document.getElementById('close-activity-log-details');
        if (closeBtn) {
            closeBtn.onclick = closeActivityLogModal;
        }

        const closeButton = document.getElementById('close-activity-log-button');
        if (closeButton) {
            closeButton.onclick = closeActivityLogModal;
        }

        // Click outside modal to close
        document.addEventListener('click', function(event) {
            const modalEl = document.getElementById('activity-log-details-modal');
            if (event.target === modalEl) {
                closeActivityLogModal();
            }
        });
    }
}

// Close modal function
function closeActivityLogModal(withAnimation = true) {
    const modal = document.getElementById('activity-log-details-modal');
    const modalContent = document.getElementById('activity-log-details-content');
    
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    if (withAnimation) {
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150); 
    } else {
        modal.classList.add('hidden');
    }
}

// Initialize on page load
initActivityLogDetailsListeners();

// Re-export for AJAX reloads
export { initActivityLogDetailsListeners as default };
