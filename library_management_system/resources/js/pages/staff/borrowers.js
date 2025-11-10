import { restoreProfileContent, showBorrowBookContent } from './borrowBook.js';
import { fetchBorrowerDetails } from '../../api/borrowerHandler.js';
import { checkActiveSemester } from '../../api/semesterHandler.js';
import { disableButton, resetButton } from '../../utils.js';

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

export async function initializeBorrowerProfileModal(modal, borrower) {
    if (!borrower) return;
    
    populateBasicInfo(modal, borrower);
    populateRoleBadge(modal, borrower);
    populateStatusBadge(modal, borrower);
    populateContactDetails(modal, borrower);
    populatePlaceholderData(modal);
    populateCurrentlyBorrowedBooks(modal, borrower);
    
    await setupBorrowBookButton(modal, borrower);
}

function populateBasicInfo(modal, borrower) {
    const fullName = modal.querySelector('#borrower-name');
    if (fullName) fullName.textContent = borrower.full_name || 'N/A';
}

function populateRoleBadge(modal, borrower) {
    const roleBadge = modal.querySelector('#borrower-role-badge');
    if (!roleBadge) return;
    
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

function populateStatusBadge(modal, borrower) {
    const statusBadge = modal.querySelector('#borrower-status-badge');
    if (!statusBadge || !borrower.library_status) return;
    
    const status = borrower.library_status.toLowerCase();
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    
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

function populateContactDetails(modal, borrower) {
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
}

function populatePlaceholderData(modal) {
    const penalty = modal.querySelector('#borrower-penalties');
    if (penalty) penalty.textContent = '₱0.00';

    const reservation = modal.querySelector('#borrower-reservations');
    if (reservation) reservation.textContent = 'None';
}

function populateCurrentlyBorrowedBooks(modal, borrower) {
    const tbody = modal.querySelector('#currently-borrowed-tbody');
    const borrowedCount = modal.querySelector('#borrowed-count');
    
    if (!tbody) return;
    
    const borrowedBooks = borrower.borrow_transactions || [];
    
    // Update count
    if (borrowedCount) {
        borrowedCount.textContent = `(${borrowedBooks.length} / 3)`;
    }
    
    // Clear existing rows
    tbody.innerHTML = '';
    
    if (borrowedBooks.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="py-10 text-center text-gray-500 text-sm">
                    No borrowed books
                </td>
            </tr>
        `;
        return;
    }
    
    borrowedBooks.forEach((transaction, index) => {
        const book = transaction.book_copy.book;
        const copyNumber = transaction.book_copy.copy_number;
        const author = book?.author;
        const dueDate = new Date(transaction.due_at);
        const status = transaction.status;
        const today = new Date();
        const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
        
        let statusBadge = '';
        let statusIcon = '';
        let borderClass = '';
        let showRenewButton = false;
        let dueDateText = dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        // Status-based badge rendering
        if (status === 'overdue') {
            borderClass = 'border-l-4 border-l-red-500';
            statusIcon = '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-700"> <img src="/build/assets/icons/overdue-badge.svg" alt="Overdue Icon" class="w-3.5 h-3.5"></span>';
            statusBadge = `<span class="text-red-700 font-medium">${Math.abs(daysUntilDue)} days overdue</span>`;
        } else if (status === 'borrowed' && daysUntilDue <= 3) {
            borderClass = 'border-l-4 border-l-yellow-400';
            statusIcon = '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100 text-yellow-700"> <img src="/build/assets/icons/due-soon-badge.svg" alt="Due Soon Icon" class="w-3.5 h-3.5"></span>';
            statusBadge = `<span class="text-yellow-700 font-medium">Due in ${daysUntilDue} day${daysUntilDue !== 1 ? 's' : ''}</span>`;
            showRenewButton = true;
        } else if (status === 'borrowed') {
            statusIcon = '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 text-green-700"> <img src="/build/assets/icons/on-time-badge.svg" alt="On Time Icon" class="w-3.5 h-3.5"></span>';
            statusBadge = `<span class="text-green-700 font-medium">On time</span>`;
            showRenewButton = true;
        } else if (status === 'lost') {
            borderClass = 'border-l-4 border-l-rose-500';
            statusIcon = '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-100 text-rose-700"> <img src="/build/assets/icons/lost-badge.svg" alt="Lost Icon" class="w-3.5 h-3.5"></span>';
            statusBadge = `<span class="text-rose-700 font-medium">Lost</span>`;
        } else if (status === 'damaged') {
            borderClass = 'border-l-4 border-l-orange-500';
            statusIcon = '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-100 text-orange-700"> <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5"></span>';
            statusBadge = `<span class="text-orange-700 font-medium">Damaged</span>`;
        }
        
        const coverImage = book?.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        const authorName = author ? `${author.firstname} ${author.lastname}` : 'Unknown Author';
        const borrowedDate = new Date(transaction.borrowed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        // Calculate penalty for overdue books
        const penalty = status === 'overdue' && daysUntilDue < 0 
            ? '₱' + (Math.abs(daysUntilDue) * 3.32).toFixed(2) 
            : '—';
        
        const row = `
            <tr class="hover:bg-gray-50 transition-colors ${borderClass}">
                <td class="py-3 px-4 text-gray-600 font-medium">${index + 1}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        <img src="${coverImage}" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0 border border-gray-200">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 truncate">${book?.title || 'Unknown'}</p>
                            <p class="text-xs text-gray-500">Copy #${copyNumber || 'N/A'} • ${authorName}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-700 text-sm">${borrowedDate}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        ${statusIcon}
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500">${dueDateText}</span>
                            ${statusBadge}
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <span class="text-${status === 'overdue' ? 'red' : 'gray'}-600 font-semibold text-xs">
                        ${penalty}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        ${showRenewButton ? `
                            <button class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent hover:bg-accent/90 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Renew
                            </button>
                        ` : ''}
                        ${status !== 'returned' ? `
                            <button class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-transaction-id="${transaction.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Return
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
        
        tbody.innerHTML += row;
    });
}

async function setupBorrowBookButton(modal, borrower) {
    const borrowBookBtn = modal.querySelector('#borrow-book-btn');
    if (!borrowBookBtn) return;
    
    // Reset button state first
    resetButton(borrowBookBtn);

    // 1. Check for outstanding penalties
    const hasPenalty = borrower.borrow_transactions?.some(transaction =>
        ['overdue', 'lost', 'damaged'].includes(transaction.status)
    );
    if (hasPenalty) {
        disableButton(borrowBookBtn, "Borrowing suspended due to an outstanding penalty.");
        return;
    }

    // 2. For students, check active semester
    if (borrower.role === 'student') {
        // Disable button while checking
        borrowBookBtn.disabled = true;
        borrowBookBtn.classList.add('opacity-50', 'cursor-not-allowed');
        borrowBookBtn.classList.remove('hover:bg-accent/90');
        
        const hasActive = await checkActiveSemester();

        if (!hasActive) {
            disableButton(borrowBookBtn, 'No active semester. Students can only borrow during an active semester.');
            return;
        }

        // 3. Check borrow limit
        if (borrower.borrow_transactions && borrower.borrow_transactions.length >= 3) {
            disableButton(borrowBookBtn, 'Borrower has reached the maximum limit of 3 borrowed books.');
            return;
        }
        
        // Re-enable if active semester exists
        borrowBookBtn.disabled = false;
        borrowBookBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        borrowBookBtn.classList.add('hover:bg-accent/90');
    } 
    
    // Enable button for teachers or students with active semester
    enableBorrowButton(borrowBookBtn, modal, borrower);
}


function enableBorrowButton(button, modal, borrower) {
    button.onclick = (e) => {
        e.preventDefault();
        showBorrowBookContent(modal, borrower);
    };
}

async function openBorrowerProfileModal(userId) {
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
