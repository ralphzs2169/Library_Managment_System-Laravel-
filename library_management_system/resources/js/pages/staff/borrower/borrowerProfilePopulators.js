import { initializeBorrowerTabs } from './tabbedContainer.js';
import { showSkeleton, hideSkeleton, disableButton, resetButton } from '../../../utils.js';
import { showBookSelectionContent } from '../bookSelection.js';
import { populateCurrentlyBorrowedBooks } from './tableBorrowedBooks.js';
import { populateActivePenalties } from './tablePenalties.js';
import { populateReservationsTable } from './tableReservations.js';
import { requestClearance } from '../../../ajax/clearanceHandler.js';
export async function initializeBorrowerProfileUI(modal, borrower, reloadProfileTabs = true, initialActiveTab = 'currently-borrowed-tab', actionPerformer = null) {

    if (!borrower) return;
    // Show skeleton
    let skeletonTimer = setTimeout(() => {
        showSkeleton(modal, '#borrower-profile-skeleton', '#borrower-profile-real');
    }, 100); 

    try {
        populateProfilePicture(modal, borrower);
        populateBasicInfo(modal, borrower);
        
        populateRoleBadge(modal, borrower);
        populateStatusBadge(modal, borrower);

        populateContactDetails(modal, borrower);
        populateTotalFines(modal, borrower);
        // populateReservationCountBadge(modal, borrower);

        setupProfileButton(modal, borrower, 'borrow');
        setupProfileButton(modal, borrower, 'reservation');
        setupProfileButton(modal, borrower, 'clearance', actionPerformer);
        // Borrower Section Tabs/Tables
        populateCurrentlyBorrowedBooks(modal, borrower);
        populateActivePenalties(modal, borrower);
        populateReservationsTable(modal, borrower);

        if (reloadProfileTabs) {
            initializeBorrowerTabs(initialActiveTab);
        }        
    } finally {
        clearTimeout(skeletonTimer);
        hideSkeleton(modal, '#borrower-profile-skeleton', '#borrower-profile-real');
    }
}

function setupProfileButton(modal, borrower, type, actionPerformer = null) {
    let button = '';
    let defaultIcon = '';
    let disabledIcon = '';
    let condition = false;

    switch (type) {
        case 'borrow':
            button = modal.querySelector("#borrow-book-btn");
            defaultIcon = '/build/assets/icons/add-book-white.svg';
            disabledIcon = '/build/assets/icons/add-book-gray.svg';
            condition = borrower.can_borrow.result === 'success';
            break;
        case 'reservation':
            button = modal.querySelector("#add-reservation-btn");
            defaultIcon = '/build/assets/icons/reservation-white.svg';
            disabledIcon = '/build/assets/icons/reservation-gray.svg';
            condition = borrower.can_reserve.result === 'success';
            break;
        case 'clearance':
            button = modal.querySelector("#clearance-btn");
            defaultIcon = '/build/assets/icons/clearance-white.svg'; // Ensure this icon exists or use a fallback
            disabledIcon = '/build/assets/icons/clearance-gray.svg';
            break;
        default:
            return; // nothing to do
    }

    if (!button) return;

    // Reset button to default state
    resetButton(button, defaultIcon);

    // Clone the button to remove old event listeners
    const clone = button.cloneNode(true);
    button.parentNode.replaceChild(clone, button);
    button = clone;

    let isAuthorized = false;
    let disableMessage = '';
    
    switch (type) {
        case 'borrow':
            if (borrower.can_borrow.result === 'success'){
               isAuthorized = true;
               break;
            }
            disableMessage = borrower.can_borrow.message;
            break;
        case 'reservation':
            if (borrower.can_reserve.result === 'success'){
                isAuthorized = true;
                break;
            }
            disableMessage = borrower.can_reserve.message;
            break;
        case 'clearance':
            console.log('Action performer for clearance:', actionPerformer);
            if (actionPerformer?.role === 'staff') {
                // Change button text for staff
                button.innerHTML = `<img src="${defaultIcon}" class="w-5 h-5"> Request Clearance`;
                
                if (borrower.can_request_clearance && borrower.can_request_clearance.result === 'success') {
                    isAuthorized = true;
                } else {
                    disableMessage = borrower.can_request_clearance?.message || 'Clearance request not available.';
                }
            } else {
                // Logic for librarian or other roles if needed (e.g., Approve Clearance)
                // For now, disable if not staff based on current requirements
                disableMessage = 'Only staff can request clearance.';
            }
            break;
        default:
            return; 
    }

    if (isAuthorized) {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            if (type === 'clearance') {
                const requestorId = actionPerformer.id;
                requestClearance(borrower.id, requestorId);
            } else {
                showBookSelectionContent(modal, borrower, type, false);
            }
        });
        return;
    } 
    disableButton(button, disableMessage, disabledIcon);
}




function populateProfilePicture(modal, borrower) {  
    const profilePicture = modal.querySelector('#borrower-profile-picture');

    if (profilePicture) {   
        const initials = borrower.firstname[0].toUpperCase() + borrower.lastname[0].toUpperCase();
        profilePicture.textContent = initials;
    }
}

function populateBasicInfo(modal, borrower) {
    const fullName = modal.querySelector('#borrower-name');
    if (fullName) fullName.textContent = borrower.fullname || 'N/A';
}


export function populateRoleBadge(modal, borrower) {
    const roleBadge = modal.querySelector('#borrower-role-badge');
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

function populateStatusBadge(modal, borrower) {
    const statusBadge = modal.querySelector('#borrower-status-badge');
    if (!statusBadge || !borrower.library_status) return;
    
    const status = borrower.library_status.toLowerCase();
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    
    statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold';
    
    if (status === 'active') {
        statusBadge.classList.add('bg-green-200', 'text-green-700');
        statusBadge.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            ${statusText}
        `;
    } else if (status === 'suspended') {
        statusBadge.classList.add('bg-red-100', 'text-red-700');
        statusBadge.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            ${statusText}
        `;
    } else {
        statusBadge.classList.add('bg-gray-100', 'text-gray-600');
        statusBadge.textContent = statusText;
    }
}

function populateContactDetails(modal, borrower) {
    const idNumber = modal.querySelector('#borrower-id-number');
    if (idNumber) {
        const idNumberValue = borrower.role === 'student' 
            ? borrower.students?.student_number 
            : borrower.teachers?.employee_number;
        idNumber.textContent = idNumberValue || 'N/A';
    }
    
    const email = modal.querySelector('#borrower-email');
    if (email) email.textContent = borrower.email || 'N/A';

    const yearLevelLabel = document.getElementById('borrower-year-level-container');
    if(borrower.role === 'student') {
        yearLevelLabel?.classList.remove('hidden');
        yearLevelLabel?.classList.add('block');

        const yearLevel = modal.querySelector('#borrower-year-level');
        if (yearLevel && borrower.students?.year_level) {
            const year = borrower.students.year_level;
            const suffix = ['st', 'nd', 'rd'][year - 1] || 'th';
            yearLevel.textContent = `${year}${suffix} Year`;
        }
    } else {
        yearLevelLabel?.classList.remove('block');
        yearLevelLabel?.classList.add('hidden');
    }
    
    const department = modal.querySelector('#borrower-department');
    if (department) {
        const departmentName = borrower.role === 'student' && borrower.students?.department?.name
            ? borrower.students.department.name
            : borrower.role === 'teacher' && borrower.teachers?.department?.name
            ? borrower.teachers.department.name
            : 'N/A';
        department.textContent = departmentName;
    }
    
    const contact = modal.querySelector('#borrower-contact');
    if (contact) contact.textContent = borrower.contact_number || 'N/A';
    
    const memberSince = modal.querySelector('#borrower-member-since');
    if (memberSince) {
        const memberSinceDate = borrower.created_at
            ? new Date(borrower.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'N/A';
        memberSince.textContent = memberSinceDate;
    }
}

function populateTotalFines(modal, borrower) {
    const penalties = modal.querySelector('#borrower-total-fine-ampount');
    if (penalties) penalties.textContent = `₱${borrower.total_unpaid_fines.toFixed(2)}` || '₱0.00';
}



