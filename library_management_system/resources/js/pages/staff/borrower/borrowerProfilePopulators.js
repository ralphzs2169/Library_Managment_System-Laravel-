import { attachTransactionActions } from './borrowerTransactionActions.js';
import { attachPaymentActions } from './borrowerPaymentHandler.js';
import { initializeBorrowerTabs } from './tabbedContainer.js';
import { showSkeleton, hideSkeleton, disableButton, showWarning, resetButton } from '../../../utils.js';
import { fetchSettings } from '../../../api/settingsHandler.js';
import { showBorrowBookContent } from '../borrowBook.js';

export async function initializeBorrowerProfileUI(modal, borrower, reloadProfileTabs = true, initialActiveTab = 'currently-borrowed-tab') {
    if (!borrower) return;
    
    // Show skeleton
    let skeletonTimer = setTimeout(() => {
        showSkeleton(modal, '#borrower-profile-skeleton', '#borrower-profile-real');
    }, 100); // Show skeleton if loading takes more than 100ms
    try {
        populateProfilePicture(modal, borrower);
        populateBasicInfo(modal, borrower);
        populateRoleBadge(modal, borrower);
        populateStatusBadge(modal, borrower);
        populateContactDetails(modal, borrower);
        populateTotalFines(modal, borrower);
        populatePlaceholderData(modal);
        setupBorrowBookButton(modal, borrower);
        populateCurrentlyBorrowedBooks(modal, borrower);
        populateActivePenalties(modal, borrower);

        if (reloadProfileTabs) {
            initializeBorrowerTabs(initialActiveTab);
            console.log('Reloading profile tabs with initial active tab:', initialActiveTab);
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
            showBorrowBookContent(modal, borrower, false);
        });
        return;
    }

    disableButton(newBtn, borrower.can_borrow.message || 'Borrowing not allowed.');
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
    if (fullName) fullName.textContent = borrower.full_name || 'N/A';
}

function populateRoleBadge(modal, borrower) {
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

function populatePlaceholderData(modal) {
    const reservation = modal.querySelector('#borrower-reservations');
    if (reservation) reservation.textContent = 'None';
}

async function populateCurrentlyBorrowedBooks(modal, borrower) {
    const tbody = modal.querySelector('#currently-borrowed-tbody');
    const borrowedCountElem = modal.querySelector('#borrowed-count');
    const borrowedHeaderCountElem = modal.querySelector('.bg-accent\\/10.text-accent.font-bold'); // header count badge

    const settings = await fetchSettings(); 

    if (!settings) {
        showWarning('Something went wrong', 'Unable to load application settings. Some features may not work as expected.');
        return;
    }
    
    const dueReminderThreshold = parseInt(settings['notifications.reminder_days_before_due']);
    
    if (!tbody) return;
    
    const borrowedBooks = borrower.active_borrows || [];
    const borrowedCount = borrowedBooks.length;

    // Set borrowed count in tab badge
    if (borrowedCountElem) {
        borrowedCountElem.textContent = `${borrowedCount}`;
    }

    // Set borrowed count in header badge (dynamic)
    if (borrowedHeaderCountElem) {
        if (borrower.role === 'student') {
            borrowedHeaderCountElem.textContent = `${borrowedCount} / 3`;
        } else {
            borrowedHeaderCountElem.textContent = `${borrowedCount}`;
        }
    }
    
    tbody.innerHTML = '';
    
    if (borrowedBooks.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="py-10 text-center">
                        <p class="text-gray-500 text-md font-medium mb-2">No currently borrowed books</p>
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
        const daysUntilDue = transaction.days_until_due;
        const daysOverdue = transaction.days_overdue;
        const isReturned = transaction.returned_at !== null;

        const dueDateText = dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        // Badge configuration matching Blade table style
        const badgeConfig = {
            overdue: {
                class: 'bg-red-200 text-red-700',
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`,
                text: `${daysOverdue} day${daysOverdue !== 1 ? 's' : ''} overdue`
            },
            due_soon: {
                class: 'bg-orange-200 text-orange-700',
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>`,
                text: `Due in ${daysUntilDue} day${daysUntilDue !== 1 ? 's' : ''}`
            },
            on_time: {
                class: 'bg-green-200 text-green-700',
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`,
                text: 'On time'
            }
        };

        let currentBadge;
        let borderClass = '';

        if (status === 'overdue') {
            currentBadge = badgeConfig.overdue;
            borderClass = 'border-l-4 border-l-red-500';
        } else if (status === 'borrowed' && daysUntilDue <= dueReminderThreshold) {
            currentBadge = badgeConfig.due_soon;
            borderClass = 'border-l-4 border-l-yellow-400';
        } else if (status === 'borrowed') {
            currentBadge = badgeConfig.on_time;
            borderClass = 'border-l-4 border-l-green-500';
        }

        const coverImage = book?.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        const authorName = author ? `${author.firstname} ${author.lastname}` : 'Unknown Author';
        const borrowedDate = new Date(transaction.borrowed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        const { renewButton, returnButton } = generateTransactionButtons(transaction, isReturned, status, daysOverdue);

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
                    <div class="flex flex-col gap-1">
                        <span class="text-sm text-gray-800">${dueDateText}</span>
                       
                    </div>
                </td>
                <td class="py-3 px-4">
                    <span class="inline-flex items-center w-full gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${currentBadge.class}">
                        ${currentBadge.icon}${currentBadge.text}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        ${renewButton}
                        ${returnButton}
                    </div>
                </td>
            </tr>
        `;

        tbody.innerHTML += row;
    });


    attachTransactionActions(tbody, borrowedBooks, borrower);
}

function generateTransactionButtons(transaction, isReturned, status, daysOverdue) {
    let renewButton = '';
    let returnButton = '';

    // Can renew if not returned, status is 'borrowed', and not overdue
    if (transaction.can_renew.result === 'success') {
        renewButton = `
            <button class="renew-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent hover:bg-accent/90 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-transaction-id="${transaction.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Renew
            </button>
        `;
    } else {
        const tooltip = transaction.can_renew.message;
        renewButton = `
            <div class="relative inline-block group">
                <button disabled class="cursor-not-allowed inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-xs font-medium opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Renew
                </button>
                <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                    ${tooltip}
                    <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                </div>
            </div>
        `;
       
    }

    // Return button logic
    if (!isReturned) {
        returnButton = `
            <button class="return-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-transaction-id="${transaction.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Return
            </button>
        `;
    } else {
        const tooltip = 'Book already returned';
        returnButton = `
            <div class="relative group">
                <button disabled class="cursor-not-allowed inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-xs font-medium opacity-50" title="${tooltip}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Return
                </button>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    ${tooltip}
                </div>
            </div>
        `;
    }

    return { renewButton, returnButton };
}

function populateActivePenalties(modal, borrower) {
    const tbody = modal.querySelector('#active-penalties-tbody');
    const penaltiesCountElem = modal.querySelector('#penalties-count');
    const penaltiesHeaderCountElem = modal.querySelector('#penalties-header-count');
    
    if (!tbody) return;

    const penaltyTransactions = (borrower.transactions_with_penalties || []);
    const penaltiesCount = penaltyTransactions.length;

    // Set penalties count in tab badge
    if (penaltiesCountElem) {
        penaltiesCountElem.textContent = `${penaltiesCount}`;
    }

    // Set penalties count in header badge (dynamic)
    if (penaltiesHeaderCountElem) {
        penaltiesHeaderCountElem.textContent = `${penaltiesCount}`;
    }
    
    tbody.innerHTML = '';
    
    if (penaltyTransactions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-500 text-sm">
                    No active penalties
                </td>
            </tr>
        `;
        return;
    }

    penaltyTransactions.forEach((transaction, index) => {
        console.log('Rendering penalty transaction:', transaction);
        const book = transaction.book_copy?.book;
        const copyNumber = transaction.book_copy?.copy_number;
        const author = book?.author;
        const returnedDate = new Date(transaction.returned_at);
        
        // Reason badge (unchanged)
        let reasonBadge = '';
        const penaltyType = transaction.penalty.type;
        const status = transaction.penalty.status;

        if (penaltyType === 'late_return') {
            reasonBadge = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Late Return
                </span>
            `;
        } else if (penaltyType === 'lost_book') {
            reasonBadge = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Lost
                </span>
            `;
        } else if (penaltyType === 'damaged_book') {
            reasonBadge = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
                    <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">
                    Damaged
                </span>
            `;
        }

        // Border color based on penalty status
        let borderClass = '';
        switch (status) {
            case 'unpaid':
                borderClass = 'border-l-4 border-l-red-500';
                break;
            case 'partially_paid':
                borderClass = 'border-l-4 border-l-orange-400';
                break;
            case 'paid':
                borderClass = 'border-l-4 border-l-green-500';
                break;
            case 'cancelled':
                borderClass = 'border-l-4 border-l-gray-400';
                break;
            default:
                borderClass = '';
        }

        const coverImage = book?.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        const authorName = author ? `${author.firstname} ${author.lastname}` : 'Unknown Author';
        const returnedDateText = returnedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const statusBadge = renderPenaltyStatusBadge(status);

        let amount = 0;
        if (status === 'unpaid') {
            amount = transaction.penalty.amount;
        } else if (status === 'partially_paid') {
            amount = transaction.penalty.remaining_amount;
        }
        
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
                <td class="py-3 px-4 text-gray-700 text-sm">${returnedDateText}</td>
                <td class="py-3 px-4">${reasonBadge}</td>
                <td class="py-3 px-4">
                    <span class="text-red-700 font-bold text-sm">₱${Number(amount).toFixed(2)}</span>
                </td>
                <td class="py-3 px-4">
                    ${statusBadge}
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <button class="pay-penalty-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs tracking-wide font-medium transition-all shadow-sm" data-transaction="${JSON.stringify(transaction).replace(/"/g, '&quot;')}">
                            <img src="/build/assets/icons/pay.svg" alt="Pay Fine Icon" class="w-4 h-4">
                            Settle
                        </button>
                    </div>
                    
                </td>
            </tr>
        `;
        
        tbody.innerHTML += row;
    });
    
    attachPaymentActions(tbody, borrower);
}

export function renderPenaltyStatusBadge(status) {
    let badgeHTML = '';
    switch (status) {
        case 'unpaid':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    Unpaid
                </span>
            `;
            break;
        case 'paid':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Paid
                </span>
            `;
            break;
        case 'partially_paid':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700 whitespace-nowrap">
                    <img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">
                    Partially Paid
                </span>
            `;
            break;
        case 'cancelled':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancelled
                </span>
            `;
            break;
        default:
            badgeHTML = `<span class="text-gray-500">Unknown</span>`;
    }
    return badgeHTML;
}
