import { formatDate } from "../../utils.js";
import { openDetailsModal } from "./utils/popupDetailsModal.js";
import { showError } from "../../utils/alerts.js";

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
        fullname: 'System Automation',
        role: 'system'
    };

    // User info
    const userInitials = document.getElementById('log-user-initials');
    const userName = document.getElementById('log-user-name');
    const userId = document.getElementById('log-user-id');

    userInitials.textContent = (user.firstname?.[0] ?? '') + (user.lastname?.[0] ?? '');
    userName.textContent = user.fullname ?? 'N/A';
    
    if (user.role === 'student') {
        userId.textContent = user.students?.student_number ?? 'N/A';
    } else if (user.role === 'teacher') {
        userId.textContent = user.teachers?.employee_number ?? 'N/A';
    } else if (user.role === 'system') {
        userId.textContent = 'N/A';
    } else {
        userId.textContent = user.role;
    }

    // Populate role badge with specific design matching the table
    const roleBadge = modal.querySelector('#log-user-role-badge');
    if (roleBadge) {
        let badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
        let iconHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" /></svg>';
        let roleLabel = user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'N/A';

        switch (user.role) {
            case 'student':
                badgeClass = 'bg-blue-100 text-blue-700 border-blue-100';
                iconHTML = '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">';
                break;
            case 'teacher':
                badgeClass = 'bg-green-100 text-green-700 border-green-100';
                iconHTML = '<img src="/build/assets/icons/teacher-role-badge.svg" alt="Teacher Badge" class="w-3.5 h-3.5">';
                break;
            case 'staff':
                badgeClass = 'bg-orange-100 text-orange-700 border-orange-100';
                iconHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 48 48"><path fill="#c2410c" d="M8 12.25A4.25 4.25 0 0 1 12.25 8h23.5A4.25 4.25 0 0 1 40 12.25v8.424A7 7 0 0 0 32.101 32H12.25A4.25 4.25 0 0 1 8 27.75zm18 24.748c0-.522.08-1.025.228-1.498H5.25a1.25 1.25 0 1 0 0 2.5h20.766a8 8 0 0 1-.016-.5zM42 27a5 5 0 1 1-10 0a5 5 0 0 1 10 0m4 10.5c0 3.5-3.15 6.5-9 6.5s-9-3-9-6.5v-.502A3 3 0 0 1 31 34h12c1.657 0 3 1.34 3 2.998z" /></svg>';
                break;
            case 'librarian':
                badgeClass = 'bg-purple-100 text-purple-700 border-purple-100';
                iconHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20"><path fill="#7e22ce" d="M9 2a4 4 0 1 0 0 8a4 4 0 0 0 0-8Zm-4.991 9A2.001 2.001 0 0 0 2 13c0 1.691.833 2.966 2.135 3.797C5.417 17.614 7.145 18 9 18c.41 0 .816-.019 1.21-.057A5.477 5.477 0 0 1 9 14.5c0-1.33.472-2.55 1.257-3.5H4.01Zm6.626 2.92a2 2 0 0 0 1.43-2.478l-.155-.557c.254-.195.529-.362.821-.497l.338.358a2 2 0 0 0 2.91.001l.324-.344c.298.14.578.315.835.518l-.126.423a2 2 0 0 0 1.456 2.519l.349.082a4.7 4.7 0 0 1 .01 1.017l-.46.117a2 2 0 0 0-1.431 2.479l.156.556a4.35 4.35 0 0 1-.822.498l-.338-.358a2 2 0 0 0-2.909-.002l-.325.344a4.32 4.32 0 0 1-.835-.518l.127-.422a2 2 0 0 0-1.456-2.52l-.35-.082a4.713 4.713 0 0 1-.01-1.016l.461-.118Zm4.865.58a1 1 0 1 0-2 0a1 1 0 0 0 2 0Z" /></svg>';
                break;
            case 'system':
                badgeClass = 'bg-red-100 text-red-700 border-red-100';
                iconHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35-.61.614-.736 1.543-.377 2.573.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35.61-.614.736-1.543.377-2.573-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>';
                roleLabel = 'System';
                break;
        }

        roleBadge.className = `inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}`;
        roleBadge.innerHTML = `${iconHTML} ${roleLabel}`;
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
