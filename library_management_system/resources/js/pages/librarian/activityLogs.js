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
    const user = log.user;

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
    } else {
        userId.textContent = user.role;
    }

    // Populate role badge
    populateRoleBadge(modal, '#log-user-role-badge', user);

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
