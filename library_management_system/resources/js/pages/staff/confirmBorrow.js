import { showBorrowBookContent, restoreProfileContent } from './borrowBook.js';
const confirmBorrowModal = document.getElementById('confirm-borrow-modal');

// Store borrower data for navigation
let currentBorrower = null;
let currentBook = null;

export function initializeConfirmBorrowModal(modal, borrower, book) {
    currentBorrower = borrower;
    currentBook = book;
    
    // Populate modal with borrower and book data
    if (borrower) {
        const borrowerName = modal.querySelector('#confirm-borrower-name');
        const borrowerId = modal.querySelector('#confirm-borrower-id');
        
        if (borrowerName) borrowerName.textContent = borrower.full_name || 'N/A';
        if (borrowerId) {
            const idNumber = borrower.role === 'student' 
                ? borrower.students?.student_number 
                : borrower.teachers?.employee_number;
            borrowerId.textContent = idNumber || 'N/A';
        }
    }

    if (book) {
        const bookCover = modal.querySelector('#confirm-book-cover');
        const bookTitle = modal.querySelector('#confirm-book-title');
        const bookAuthor = modal.querySelector('#confirm-book-author');
        const bookIsbn = modal.querySelector('#confirm-book-isbn');
        const bookYear = modal.querySelector('#confirm-book-year');
        const bookCategory = modal.querySelector('#confirm-book-category');
        const bookGenre = modal.querySelector('#confirm-book-genre');
        
        if (bookCover) bookCover.src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        if (bookTitle) bookTitle.textContent = book.title || 'Untitled';
        if (bookAuthor) bookAuthor.textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
        if (bookIsbn) bookIsbn.textContent = book.isbn || 'N/A';
        if (bookYear) bookYear.textContent = book.publication_year || 'N/A';
        if (bookCategory) bookCategory.textContent = book.genre?.category?.name || 'N/A';
        if (bookGenre) bookGenre.textContent = book.genre?.name || 'N/A';
        
        // Populate copy number dropdown
        populateCopyNumbers(modal, book);
    }
}

function populateCopyNumbers(modal, book) {
    const copySelect = modal.querySelector('#confirm-copy-number');
    const copiesAvailableText = modal.querySelector('#copies-available-text');
    
    if (!copySelect) return;
    
    // Clear existing options
    copySelect.innerHTML = '';
    
    // Filter available copies (status: 'available' only)
    const availableCopies = (book.copies || []).filter(copy => 
        copy.status === 'available'
    );
    
    // Populate with available copies
    availableCopies.forEach(copy => {
        const option = document.createElement('option');
        option.value = copy.id;
        option.textContent = `Copy No. ${copy.copy_number}`;
        option.dataset.copyNumber = copy.copy_number;
        option.dataset.status = copy.status;
        copySelect.appendChild(option);
    });
    
    // Enable the select
    copySelect.disabled = false;
    
    // Update available text
    if (copiesAvailableText) {
        copiesAvailableText.textContent = `${availableCopies.length} ${availableCopies.length === 1 ? 'copy' : 'copies'} available`;
        copiesAvailableText.classList.remove('text-red-600');
        copiesAvailableText.classList.add('text-green-600', 'font-semibold');
    }
    
    // Auto-select first copy (since there's no placeholder)
    copySelect.selectedIndex = 0;
}

const backToBorrowBookButton = document.getElementById('back-to-borrow-book-button');
if (backToBorrowBookButton) {
    backToBorrowBookButton.addEventListener('click', function(e) {
        e.preventDefault();
        returnToBorrowBookModal();
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
        returnToBorrowBookModal();
    });
}

document.addEventListener('click', function(event) {
    if (event.target === confirmBorrowModal) {
        closeConfirmBorrowModal();
    }
});

export function openConfirmBorrowModal(borrower, book) {
    currentBorrower = borrower;
    currentBook = book;
    
    const modal = document.getElementById('confirm-borrow-modal');
    const modalContent = document.getElementById('confirm-borrow-content');
    
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
    initializeConfirmBorrowModal(modal, borrower, book);
}

function closeConfirmBorrowModal() {
    const modalContent = document.getElementById('confirm-borrow-content');
    const modal = document.getElementById('confirm-borrow-modal');

    // Start closing animation
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    // Hide modal after animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
        restoreProfileContent(document.getElementById('borrower-profile-modal'), null);
    }, 150);
}

function returnToBorrowBookModal() {
    // Close confirm modal
    closeConfirmBorrowModal();
    
    // Reopen borrower modal with borrow book view
    setTimeout(() => {
        const borrowerModal = document.getElementById('borrower-profile-modal');
        const borrowerModalContent = document.getElementById('borrower-profile-content');
        
        if (borrowerModal && currentBorrower) {
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
            showBorrowBookContent(borrowerModal, currentBorrower, true);
        }
    }, 150); // Wait for close animation to complete
}
