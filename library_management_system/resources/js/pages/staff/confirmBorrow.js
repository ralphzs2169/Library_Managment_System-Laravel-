import { showBookSelectionContent, restoreProfileContent} from './bookSelection.js';
import { borrowBook } from '../../ajax/staffTransactionHandler.js';
import { clearInputError } from '../../helpers.js';
import { fetchSettings } from '../../ajax/settingsHandler.js';
import { closeConfirmReservationModal } from './transactions/confirmReservation.js';
import { closeBorrowerModal, openBorrowerProfileModal } from './borrower/borrowerProfileModal.js';
import { fetchCopiesForReservation } from '../../ajax/bookHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js'; 
import { fetchBorrowerDetails } from '../../ajax/borrowerHandler.js';
import { closeConfirmReturnModal } from './confirmReturn.js';
const confirmBorrowModal = document.getElementById('confirm-borrow-modal');

// Store borrower data for navigation
let currentBorrower = null;
let currentBook = null;

const copySelect = document.getElementById('book_copy_id');
const dueDateInput = document.getElementById('due_date');

export async function initializeConfirmBorrowModal(modal, borrower, book, isFromReservation = false) {
    currentBorrower = borrower;
    currentBook = book;
    
    // Populate modal with borrower and book data
    if (borrower) {
        const borrowerName = modal.querySelector('#confirm-borrower-name');
        const borrowerId = modal.querySelector('#confirm-borrower-id');
        
        if (borrowerName) borrowerName.textContent = borrower.fullname || 'N/A';
        if (borrowerId) {
            const idNumber = borrower.role === 'student' 
                ? borrower.students?.student_number 
                : borrower.teachers?.employee_number;
            borrowerId.textContent = idNumber || 'N/A';
        }
    }

    // const book = currentBook;
    // attach a flag to the back button to indicate if coming from reservation
    const backButton = document.getElementById('back-to-borrow-book-button');
    let copiesForReservation = [];
    if (isFromReservation) {
        backButton.dataset.fromReservation = 'true';
        backButton.dataset.borrowerId = borrower.id;
        console.log('Fetching copies for reservation for borrower ID:', currentBorrower.id, 'and book ID:', currentBook.id);
        copiesForReservation = await fetchCopiesForReservation(currentBorrower.id, currentBook.id);

    } else {
        backButton.dataset.fromReservation = 'false';
        backButton.dataset.borrowerId = '';
    }
    
    if (book) { 

        const profileIcon = modal.querySelector('#confirm-borrower-initials');
        if (profileIcon && borrower) {
            const names = borrower.firstname.charAt(0).toUpperCase() + (borrower.lastname?.charAt(0).toUpperCase() || '');
            profileIcon.textContent = names || '--';
        }
        const bookCover = modal.querySelector('#confirm-book-cover');
        const bookTitle = modal.querySelector('#confirm-book-title');
        const bookAuthor = modal.querySelector('#confirm-book-author');
        const bookIsbn = modal.querySelector('#confirm-book-isbn');
        const bookYear = modal.querySelector('#confirm-book-year');
        const bookCategory = modal.querySelector('#confirm-book-category');
        const bookGenre = modal.querySelector('#confirm-book-genre');
        
        if (bookCover) bookCover.src = `/storage/${book.cover_image}`;
        if (bookTitle) bookTitle.textContent = book.title || 'Untitled';
        if (bookAuthor) bookAuthor.textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
        if (bookIsbn) bookIsbn.textContent = book.isbn || 'N/A';
        if (bookYear) bookYear.textContent = book.publication_year || 'N/A';
        if (bookCategory) bookCategory.textContent = book.genre?.category?.name || 'N/A';
        if (bookGenre) bookGenre.textContent = book.genre?.name || 'N/A';
        
        // Populate copy number dropdown
        populateCopyNumbers(modal, book, isFromReservation, copiesForReservation);
    }

   const settings = await fetchSettings();

   const borrowDuration = borrower.role === 'student'
    ? settings['borrowing.student_duration']
    : settings['borrowing.teacher_duration'];

    // Elements
    const copySelect = modal.querySelector('#book_copy_id');
    const dueDateInput = modal.querySelector('#due_date');
    const borrowDurationMsg = modal.querySelector('#borrow-duration');

    // Display standard duration
    if (borrowDurationMsg && borrowDuration) {
        borrowDurationMsg.textContent = `Standard: ${borrowDuration} days from today`;
    }

    // Set default due date
    if (dueDateInput && borrowDuration) {
        const today = new Date();
        today.setHours(0, 0, 0, 0); // force 00:00

        // Add borrow duration
        const dueDate = new Date(today);
        // The backend expects "up to N days from today", so due date should be today + (borrowDuration - 1)
        dueDate.setDate(dueDate.getDate() + parseInt(borrowDuration, 10) - 1);

        // Format YYYY-MM-DD
        const year = dueDate.getFullYear();
        const month = String(dueDate.getMonth() + 1).padStart(2, '0');
        const day = String(dueDate.getDate()).padStart(2, '0');

        dueDateInput.value = `${year}-${month}-${day}`;
    }


    if (copySelect) {
        copySelect.addEventListener('change', () => clearInputError(copySelect));
    }

    if (dueDateInput) {
        dueDateInput.addEventListener('change', () => clearInputError(dueDateInput));
        dueDateInput.addEventListener('input', () => clearInputError(dueDateInput));
    }

    // Attach confirm button event listener here
    const confirmBorrowBtn = modal.querySelector('#confirm-borrow-button');
    if (confirmBorrowBtn) {
        // Remove any existing listeners
        const newBtn = confirmBorrowBtn.cloneNode(true);
        confirmBorrowBtn.parentNode.replaceChild(newBtn, confirmBorrowBtn);
        
        newBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            const result = await handleConfirmBorrow(isFromReservation);
             if (result) {
                // returnToBorrowerProfile();
                const modal = document.getElementById('borrower-profile-modal');
                const freshBorrowerDetails = await fetchBorrowerDetails(borrower.id);
                await initializeBorrowerProfileUI(modal, freshBorrowerDetails, true);    
                closeConfirmReturnModal();
            }
        });
    }
}

function populateCopyNumbers(modal, book ,isFromReservation = false, copiesForReservation) {
    const copySelect = modal.querySelector('#book_copy_id');
    const copiesAvailableText = modal.querySelector('#copies-available-text');
    if (!copySelect) return;

    copySelect.innerHTML = '';

    let availableCopies = [];
    let infoMessage = '';

    if (isFromReservation) {
        availableCopies = copiesForReservation;
    } else {
        availableCopies = (book.copies || []).filter(copy => copy.status === 'available');
    }

    availableCopies.forEach(copy => {
        const option = document.createElement('option');
        option.value = copy.id;
        option.textContent = `Copy No. ${copy.copy_number}`;
        option.dataset.copyNumber = copy.copy_number;
        option.dataset.status = copy.status;
        copySelect.appendChild(option);
    });
 

    copySelect.disabled = false;

    if (copiesAvailableText) {
        copiesAvailableText.textContent = `${availableCopies.length} ${availableCopies.length === 1 ? 'copy' : 'copies'} available`;
        copiesAvailableText.classList.remove('text-red-600');
        copiesAvailableText.classList.add('text-green-600', 'font-semibold');
        if (infoMessage) {
            copiesAvailableText.textContent += ` (${infoMessage})`;
        }
    }

    copySelect.selectedIndex = 0;
}

const backToBorrowBookButton = document.getElementById('back-to-borrow-book-button');
if (backToBorrowBookButton) {
    backToBorrowBookButton.addEventListener('click', function(e) {
        e.preventDefault();

        if (backToBorrowBookButton.dataset.fromReservation === 'true') {
        
            closeConfirmBorrowModal();
            openBorrowerProfileModal(backToBorrowBookButton.dataset.borrowerId, false, 'reservations-tab');
            return;
        } 
        returnToBookSelection('borrow', currentBorrower);
    });
}

const closeConfirmModalBtn = document.getElementById('close-confirm-modal');
if (closeConfirmModalBtn) {
    closeConfirmModalBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeConfirmBorrowModal();
    });
}

const cancelBorrowBtn = document.getElementById('cancel-borrow-button');
if (cancelBorrowBtn) {
    cancelBorrowBtn.addEventListener('click', function(e) {
        e.preventDefault();
        returnToBookSelection('borrow', currentBorrower);
    });
}

document.addEventListener('click', function(event) {
    if (event.target === confirmBorrowModal) {
        closeConfirmBorrowModal();
    }
});

export function openConfirmBorrowModal(borrower, book, isFromReservation = false ) {
    currentBorrower = borrower;
    currentBook = book;
    
    const modal = document.getElementById('confirm-borrow-modal');
    const modalContent = document.getElementById('confirm-borrow-content');
    
    if (isFromReservation) {
        closeBorrowerModal(false);
    }
    // Show modal immediately but invisible
    modal.classList.remove('hidden');
    
    // Trigger animation on next frame
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });
    
    // Initialize modal with data
    initializeConfirmBorrowModal(modal, borrower, book, isFromReservation);
}

export function closeConfirmBorrowModal() {
    const modalContent = document.getElementById('confirm-borrow-content');
    const modal = document.getElementById('confirm-borrow-modal');

    clearInputError(dueDateInput);
    clearInputError(copySelect);

    // Start closing animation
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    // Hide modal after animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
        // Pass the active borrower so profile buttons re-bind correctly
        const profileModal = document.getElementById('borrower-profile-modal');

        if (profileModal && currentBorrower) {
            restoreProfileContent(profileModal, currentBorrower, false);
        }
    }, 150);
}

export function returnToBookSelection(transactionType, member) {
    // Close confirm modal
    if (transactionType === 'borrow') {
        closeConfirmBorrowModal();
    } else if (transactionType === 'reservation') {
        closeConfirmReservationModal();
    }
    
    // Reopen borrower modal with borrow book view
    setTimeout(() => {
        const borrowerModal = document.getElementById('borrower-profile-modal');
        const borrowerModalContent = document.getElementById('borrower-profile-content');
        
        if (borrowerModal && member) {
            // Show borrower modal
            borrowerModal.classList.remove('hidden');
            
            // Trigger animation
            requestAnimationFrame(() => {
                borrowerModal.classList.remove('bg-opacity-0');
                borrowerModal.classList.add('bg-opacity-50');
                borrowerModalContent.classList.remove('scale-95', 'opacity-0');
                borrowerModalContent.classList.add('scale-100', 'opacity-100');
            });
            
            // Show borrow book content with restored state (pass true)
            if (transactionType === 'borrow') {
                showBookSelectionContent(borrowerModal, member, 'borrow', true);
            } else if (transactionType === 'reservation') {
                showBookSelectionContent(borrowerModal, member, 'reservation', true);
            }
        }
    }, 150); // Wait for close animation to complete
}

async function handleConfirmBorrow(isFromReservation = false) {
    const copySelect = document.getElementById('book_copy_id');
    const dueDateInput = document.getElementById('due_date');

    console.log('Handling confirm borrow. isFromReservation:', isFromReservation);
    const selectedCopyId = copySelect?.value;
    const dueDate = dueDateInput?.value;
    
    // Prepare form data
    const formData = new FormData();
    formData.append('book_copy_id', selectedCopyId);
    formData.append('borrower_id', currentBorrower.id);
    formData.append('due_date', dueDate);

    if(isFromReservation) {
        formData.append('is_from_reservation', '1');
    } else {
        formData.append('is_from_reservation', '0');
    }
    
    // Call the borrow handler
    await borrowBook(formData);
}

//Add listener to clear input errors
if (copySelect) {
    copySelect.addEventListener('change', function() {
        clearInputError(copySelect);
    });
}

if (dueDateInput) {
    dueDateInput.addEventListener('focus', function() {
        clearInputError(dueDateInput);
    });
}
if (dueDateInput) {
    dueDateInput.addEventListener('change', function() {
        clearInputError(dueDateInput);
    }); 
}