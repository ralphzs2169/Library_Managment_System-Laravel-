import { restoreProfileContent, showBorrowBookContent } from './borrowBook.js';
import { fetchBorrowerDetails } from '../../api/borrowerHandler.js';

const closeBorrowerModalBtn = document.getElementById('close-borrower-modal');
const borrowerModal = document.getElementById('borrower-profile-modal');

// Prevent duplicate event listener attachment
if (!window._borrowerEventsInitialized) {
    window._borrowerEventsInitialized = true;

    // Use event delegation for dynamically loaded content
    document.addEventListener('click', async function(e) {
        const borrowerBtn = e.target.closest('.open-borrower-modal');
        if (borrowerBtn) {
            e.preventDefault();
            const userId = borrowerBtn.getAttribute('data-user-id');
            openBorrowerProfileModal(userId);
        }
    });

    if (closeBorrowerModalBtn) {
        closeBorrowerModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeBorrowerModal();
        });
    }

    document.addEventListener('click', function(event) {
        if (event.target === borrowerModal) {
            closeBorrowerModal();
        }
    });
}

export function initializeBorrowerProfileModal(modal, borrower) {

    if (!borrower) return;
    
    // Main profile name
    const fullName = modal.querySelector('#borrower-name');
    if (fullName) fullName.textContent = borrower.full_name || 'N/A';

    // Role badge
    const roleBadge = modal.querySelector('#borrower-role-badge');
    if (roleBadge) {
        const role = borrower.role ? borrower.role.charAt(0).toUpperCase() + borrower.role.slice(1) : 'N/A';
        roleBadge.textContent = role;
        
        if (borrower.role === 'teacher') {
            roleBadge.classList.remove('bg-blue-50', 'text-blue-700');
            roleBadge.classList.add('bg-green-50', 'text-green-700');
        } else {
            roleBadge.classList.remove('bg-green-50', 'text-green-700');
            roleBadge.classList.add('bg-blue-50', 'text-blue-700');
        }
    }
    
    // Status badge
    const statusBadge = modal.querySelector('#borrower-status-badge');
    if (statusBadge && borrower.library_status) {
        const status = borrower.library_status.toLowerCase();
        const statusText = status.charAt(0).toUpperCase() + status.slice(1);
        
        // Reset classes
        statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold';
        
        if (status === 'active') {
            statusBadge.classList.add('bg-green-50', 'text-green-700');
            statusBadge.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ${statusText}
            `;
        } else if (status === 'penalty') {
            statusBadge.classList.add('bg-red-50', 'text-red-700');
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
    
    // ID Number
    const idNumber = modal.querySelector('#borrower-id-number');
    if (idNumber) {
        const idNumberValue = borrower.role === 'student' 
            ? borrower.students?.student_number 
            : borrower.teachers?.employee_number;
        idNumber.textContent = idNumberValue || 'N/A';
    }
    
    // Email
    const email = modal.querySelector('#borrower-email');
    if (email) email.textContent = borrower.email || 'N/A';

    // Year Level
    const yearLevel = modal.querySelector('#borrower-year-level');
    if (yearLevel) {
        if (borrower.role === 'student' && borrower.students?.year_level) {
            const year = borrower.students.year_level;
            const suffix = ['st', 'nd', 'rd'][year - 1] || 'th';
            yearLevel.textContent = `${year}${suffix} Year`;
        } else {
            yearLevel.textContent = 'N/A';
        }
    }
    
    // Department
    const department = modal.querySelector('#borrower-department');
    if (department) {
        const departmentName = borrower.role === 'student' && borrower.students?.department?.name
            ? borrower.students.department.name
            : borrower.role === 'teacher' && borrower.teachers?.department?.name
            ? borrower.teachers.department.name
            : 'N/A';
        department.textContent = departmentName;
    }
    
    // Contact
    const contact = modal.querySelector('#borrower-contact');
    if (contact) contact.textContent = borrower.contact_number || 'N/A';
    
    // Member Since
    const memberSince = modal.querySelector('#borrower-member-since');
    if (memberSince) {
        const memberSinceDate = borrower.created_at
            ? new Date(borrower.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'N/A';
        memberSince.textContent = memberSinceDate;
    }
    
    // Penalties (placeholder)
    const penalty = modal.querySelector('#borrower-penalties');
    if (penalty) penalty.textContent = '₱0.00';

    // Reservations (placeholder)
    const reservation = modal.querySelector('#borrower-reservations');
    if (reservation) reservation.textContent = 'None';

    const borrowBookButton = modal.querySelector('#borrow-book-button');
    if (borrowBookButton) {
        borrowBookButton.onclick = () => {
            showBorrowBookContent(modal, borrower);
        };
    }
}

async function openBorrowerProfileModal(userId) {
    console.log('Here   in openBorrowerProfileModal');
    const modal = document.getElementById('borrower-profile-modal');
    const modalContent = document.getElementById('borrower-profile-content');
    
    // Show modal immediately but invisible
    modal.classList.remove('hidden');
    
    // Trigger animation on next frame
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });
    
    const borrower = await fetchBorrowerDetails(userId);
    initializeBorrowerProfileModal(modal, borrower);    
}

// Close modal with animation
export function closeBorrowerModal() {
    const modalContent = document.getElementById('borrower-profile-content');
   
    // Start closing animation
    borrowerModal.classList.remove('bg-opacity-50');
    borrowerModal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    // Hide modal after animation completes
    setTimeout(() => {
        borrowerModal.classList.add('hidden');
        restoreProfileContent(borrowerModal, null);
    }, 150);
}
