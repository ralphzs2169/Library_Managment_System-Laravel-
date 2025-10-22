import { fetchBookDetails } from '../../api/bookHandler.js';
import { clearAllErrors } from '../../helpers.js';
import { initializeEditForm } from './editBook.js';

const tableContainer = document.getElementById('books-table-container');
const editButtons = document.querySelectorAll('.edit-book-btn');
const editForm = document.getElementById('edit-book-form-container');
const cancelEditButton = document.getElementById('cancel-edit-book');


cancelEditButton.addEventListener('click', function (e) {
    e.preventDefault();
    tableContainer.classList.remove('hidden');
    editForm.classList.add('hidden');
    clearAllErrors(editForm);
});

editButtons.forEach(btn => {
    btn.addEventListener('click', async function (e) {
        e.preventDefault();
        const bookId = this.getAttribute('data-book-id');
        console.log('Edit button clicked for book ID:', bookId);
        editForm.setAttribute('data-book-id', bookId);

        const book = await fetchBookDetails({ id: bookId });

        tableContainer.classList.add('hidden');
        editForm.classList.remove('hidden');
        initializeEditForm(editForm, book);

    });
});


