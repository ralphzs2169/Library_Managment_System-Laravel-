import { initializeBorrowerTabs } from './tabbedContainer.js';
import { showSkeleton, hideSkeleton, disableButton, resetButton } from '../../../utils.js';
import { showBookSelectionContent } from '../bookSelection.js';
import { populateCurrentlyBorrowedBooks } from './tableBorrowedBooks.js';
import { populateActivePenalties } from './tablePenalties.js';
import { populateReservationsTable } from './tableReservations.js';

export async function initializeBorrowerProfileUI(modal, borrower, reloadProfileTabs = true, initialActiveTab = 'currently-borrowed-tab') {
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

        setupBorrowBookButton(modal, borrower);
        setupAddReservationButton(modal, borrower);
        
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

function setupBorrowBookButton(modal, borrower) {
    const borrowBookBtn = modal.querySelector('#borrow-book-btn');

    resetButton(borrowBookBtn);
    // Remove any existing click listener first
    borrowBookBtn.replaceWith(borrowBookBtn.cloneNode(true)); 
    const newBtn = modal.querySelector('#borrow-book-btn');

    if (borrower.can_borrow.result === 'success') {
        newBtn.addEventListener('click', (e) => {
            e.preventDefault();
            showBookSelectionContent(modal, borrower, 'borrow', false);
        });
        return;
    }

    disableButton(newBtn, borrower.can_borrow.message || 'Borrowing not allowed.');
}

function setupAddReservationButton(modal, reserver) {
    const addReservationBtn = modal.querySelector('#add-reservation-btn');

    resetButton(addReservationBtn);
    // Remove any existing click listener first
    addReservationBtn.replaceWith(addReservationBtn.cloneNode(true)); 
    const newBtn = modal.querySelector('#add-reservation-btn'); 

    newBtn.addEventListener('click', (e) => {
        e.preventDefault();
        showBookSelectionContent(modal, reserver, 'reservation', false);
    });

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



