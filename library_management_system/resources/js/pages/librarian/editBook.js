import { editBookHandler } from "../../api/bookHandler.js";
import { initImagePreview } from "../librarian/imagePreview.js";
import { showError } from "../../utils.js";
import { clearInputError } from "../../helpers.js";
import { handleCategoryChange } from "./addNewBook.js";

export async function initializeEditForm(form, book) {
    // Ensure the hidden book id is updated to the current book
    const hiddenBookId = form.querySelector('#edit-book-id');
    if (hiddenBookId) hiddenBookId.value = book.id ?? '';
    form.setAttribute('data-book-id', book.id ?? '');

    // Grab fields
    const editTitleField = form.querySelector('#title');
    const editIsbnField = form.querySelector('#isbn');
    const editDescriptionField = form.querySelector('#description');
    const editPriceField = form.querySelector('#price');

    // Author fields
    const editAuthorFirstNameField = form.querySelector('#author_firstname');
    const editAuthorLastNameField = form.querySelector('#author_lastname');
    const editAuthorMiddleInitialField = form.querySelector('#author_middle_initial');

    // Cover Image fields
    const editCoverInputField = form.querySelector('#cover-input');
    const editCoverImagePreview = form.querySelector('#cover-preview');
    const editCoverPlaceholder = form.querySelector('#cover-placeholder');
    const coverImagePreview = form.querySelector('#cover-preview');
    const editCoverErrorPlaceholder = form.querySelector('#cover-error-placeholder');

    // Publication fields
    const editPublisherField = form.querySelector('#publisher');
    const editPublicationYearField = form.querySelector('#publication_year');
    const editCategorySelect = form.querySelector('#category');
    const editGenreSelect = form.querySelector('#genre');
    const editLanguageSelect = form.querySelector('#language');
    
    const editBookCopiesContainer = form.querySelector('#copies-table-container');

    // Fill form - determine category and selected genre
    editTitleField.value = book.title || '';
    const categoryId = book.genre?.category_id ?? '';
    const selectedGenreId = book.genre_id ?? '';
    editCategorySelect.value = categoryId;

    editAuthorFirstNameField.value = book.author.firstname || '';
    editAuthorLastNameField.value = book.author.lastname || '';
    editAuthorMiddleInitialField.value = book.author.middle_initial || '';

    editIsbnField.value = book.isbn || '';
    editPublicationYearField.value = book.publication_year || '';
    editLanguageSelect.value = book.language || '';
    editPublisherField.value = book.publisher || '';
    editDescriptionField.value = book.description || '';
    editPriceField.value = book.price || '';

    const editGenreLoading = form.querySelector('#genre-loading') || null;
    
    if (categoryId) {
        await handleCategoryChange(editCategorySelect, editGenreSelect, editGenreLoading);
        editGenreSelect.value = selectedGenreId || '';
    }

    // Handle cover preview
    if (book.cover_image) {
        coverImagePreview.src = `/storage/${book.cover_image}`;
        coverImagePreview.classList.remove('hidden');
        editCoverPlaceholder?.classList.add('hidden');
    } else {
        coverImagePreview.src = '';
        coverImagePreview.classList.add('hidden');
        editCoverPlaceholder?.classList.remove('hidden');
    }

    if (editCoverInputField) {
        initImagePreview(editCoverInputField, editCoverImagePreview, editCoverPlaceholder, editCoverErrorPlaceholder);
    }
    

    // Define a single reusable event handler
    if (!form._categoryChangeHandler) {
        form._categoryChangeHandler = async () => {
            await handleCategoryChange(editCategorySelect, editGenreSelect, editGenreLoading);
         };
    }

    renderCopiesTable(editBookCopiesContainer, book.copies);

    // Always remove before re-adding
    editCategorySelect.removeEventListener('change', form._categoryChangeHandler);
    editCategorySelect.addEventListener('change', form._categoryChangeHandler);

    initializeErrorClearing(form);


    // Preload genres for this book’s category (if any)
    const editBookForm = form.querySelector('.edit-book-form');
    if (editBookForm && !editBookForm.dataset.submitBound) {
        editBookForm.dataset.submitBound = 'true';
        editBookForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            try {
                const formData = new FormData(editBookForm);
                formData.set('_method', 'PUT');
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) formData.set('_token', meta.getAttribute('content'));
                const bookIdEl = editBookForm.querySelector('#book-id');
                if (bookIdEl) formData.set('book_id', bookIdEl.value);

               
                if (!formData.has('genre')) {
                    formData.set('genre', null);
                }
                console.log('Submitting edit form for book ID:', bookIdEl?.value);
                await editBookHandler(formData, form.id);
                // Optionally hide form and show table after success
                // document.getElementById('books-table-container').classList.remove('hidden');
                // document.getElementById('edit-book-form-container').classList.add('hidden');
            } catch (err) {
                console.error('Error submitting edit form:', err);
                showError('Submission Error', err.message || 'An error occurred while submitting the form. Please try again.');
            }
        });
    }
}

export function renderCopiesTable(container, copies = []) {
    if (!container) return;
    container.innerHTML = ''; // Clear container

    if (!copies.length) {
        const noCopiesEl = document.createElement('div');
        noCopiesEl.className = 'h-40 flex items-center justify-center text-gray-500';
        noCopiesEl.textContent = 'No copies found for this book.';
        container.appendChild(noCopiesEl);
        return;
    }

    const table = document.createElement('table');
    table.className = 'min-w-full text-sm text-left border-collapse';

    table.innerHTML = `
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold sticky top-0 z-10">
            <tr>
                <th class="px-4 py-3 border-b">Copy No.</th>
                <th class="px-4 py-3 border-b">Status</th>
                <th class="px-4 py-3 border-b">Date Added</th>
                <th class="px-4 py-3 border-b">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-800">
            ${copies.map(copy => {
                const statusLower = copy.status?.toLowerCase() || '';
                const statusColor = statusLower === 'available' ? 'text-green-600 font-semibold' : 'text-gray-800';
                const possibleStatuses = ['available', 'lost', 'damaged', 'withdrawn'];

                // Build options dynamically, exclude current status
                const options = possibleStatuses
                    .filter(s => s !== statusLower)
                    .map(s => `<option value="${s}">Mark as ${s.charAt(0).toUpperCase() + s.slice(1)}</option>`)
                    .join('');

                return `
                    <tr class="hover:bg-gray-50 transition" data-copy-id="${copy.id}">
                        <td class="px-4 py-3 text-gray-600">${copy.copy_number}</td>
                        <td class="px-4 py-3 copy-status ${statusColor}">${copy.status ?? 'Unknown'}</td>
                        <td class="px-4 py-3 text-gray-500">${copy.created_at ? new Date(copy.created_at).toLocaleDateString() : ''}</td>
                        <td class="px-4 py-3 text-right w-32 text-center">
                            <select data-copy-id="${copy.id}" class="copy-action-select bg-secondary text-white w-full border rounded-md p-1 text-sm">
                                <option value="" selected disabled>Change Status</option>
                                ${options}
                            </select>
                            <!-- hidden input to ensure status is always submitted -->
                            <input type="hidden" name="copies[${copy.id}]" value="${statusLower}">
                        </td>
                    </tr>
                `;
            }).join('')}
        </tbody>
    `;

    container.appendChild(table);

    // Add dynamic change listener to each select
    table.querySelectorAll('.copy-action-select').forEach(select => {
        select.addEventListener('change', e => {
            const newStatus = e.target.value;
            const row = e.target.closest('tr');
            const statusCell = row.querySelector('.copy-status');

            // Update status text
            statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

            // Update color
            statusCell.classList.remove('text-green-600', 'font-semibold', 'text-gray-800');
            if (newStatus.toLowerCase() === 'available') {
                statusCell.classList.add('text-green-600', 'font-semibold');
            } else {
                statusCell.classList.add('text-gray-800');
            }

            // Rebuild select options dynamically, exclude new current status
            const possibleStatuses = ['available', 'lost', 'damaged', 'withdrawn'];
            const filteredOptions = possibleStatuses
                .filter(s => s !== newStatus.toLowerCase())
                .map(s => `<option value="${s}">Mark as ${s.charAt(0).toUpperCase() + s.slice(1)}</option>`)
                .join('');

            // Reset select with disabled default
            e.target.innerHTML = `<option value="" selected disabled>Change Status</option>${filteredOptions}`;

            // Update corresponding hidden input value so FormData(form) includes the current status
            const hiddenInput = row.querySelector(`input[type="hidden"][name="copies[${row.dataset.copyId}]"]`);
            if (hiddenInput) {
                hiddenInput.value = newStatus.toLowerCase();
            } else {
                // fallback: create/update hidden input
                const newHidden = document.createElement('input');
                newHidden.type = 'hidden';
                newHidden.name = `copies[${row.dataset.copyId}]`;
                newHidden.value = newStatus.toLowerCase();
                e.target.insertAdjacentElement('afterend', newHidden);
            }

            // Ensure select keeps a name if you rely on select values too
            e.target.name = `copies_select[${row.dataset.copyId}]`;
        });
    });
}

export function initializeErrorClearing(form) {
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(el => {
        if (el.dataset.errorListenerAttached) return;
        el.dataset.errorListenerAttached = 'true';

        ['focus', 'input', 'change'].forEach(evt => {
            el.addEventListener(evt, () => clearInputError(el));
        });
    });
}