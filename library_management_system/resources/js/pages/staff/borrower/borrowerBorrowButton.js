import { checkActiveSemester } from '../../../api/semesterHandler.js';
import { disableButton, resetButton } from '../../../utils.js';
import { showBorrowBookContent } from '../borrowBook.js';

export async function setupBorrowBookButton(modal, borrower) {
    const borrowBookBtn = modal.querySelector('#borrow-book-btn');
    if (!borrowBookBtn) return;
    
    resetButton(borrowBookBtn);

    // 1. Check for outstanding penalties
    if (borrower.library_status === 'suspended') {
        disableButton(borrowBookBtn, "Borrowing suspended due to an outstanding penalty.");
        return;
    }

    // 2. For students, check active semester
    if (borrower.role === 'student') {
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
        
        borrowBookBtn.disabled = false;
        borrowBookBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        borrowBookBtn.classList.add('hover:bg-accent/90');
    } 
    
    enableBorrowButton(borrowBookBtn, modal, borrower);
}

function enableBorrowButton(button, modal, borrower) {
    button.onclick = (e) => {
        e.preventDefault();
        showBorrowBookContent(modal, borrower, false);
    };
}
