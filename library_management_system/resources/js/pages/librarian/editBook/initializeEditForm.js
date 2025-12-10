import { editBookHandler } from "../../../ajax/bookHandler.js";
import { initImagePreview } from "../imagePreview.js";
import { showError } from "../../../utils/alerts.js";
import { clearInputError } from "../../../helpers.js";
import { handleCategoryChange } from "../addNewBook.js";
import { 
    updatePendingIssueResolvedFlag, 
    resetAllCopiesToOriginal, 
    renderCopiesTable, 
    populateSummaryHeader, 
    addNewCopy 
} from "./editBookHelpers.js";

export async function initializeEditForm(form, book) {

    // Ensure the hidden book id is updated to the current book
    const hiddenBookId = form.querySelector('#edit-book-id');
    if (hiddenBookId) hiddenBookId.value = book.id ?? '';
    form.setAttribute('data-book-id', book.id ?? '');

    // Populate summary header
    populateSummaryHeader(book, form);

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
    const editCoverInputField = form.querySelector('#edit-cover-input');
    const editCoverImagePreview = form.querySelector('#cover-preview');
    const editCoverPlaceholder = form.querySelector('#cover-placeholder');
    const editCoverErrorPlaceholder = form.querySelector('#cover-error-placeholder');

    // Publication fields
    const editPublisherField = form.querySelector('#publisher');
    const editPublicationYearField = form.querySelector('#publication_year');
    const editCategorySelect = form.querySelector('#category');
    const editGenreSelect = form.querySelector('#genre');
    const editLanguageSelect = form.querySelector('#language');
    
    const editBookCopiesContainer = form.querySelector('#copies-table-container');

    // Fill form - determine category and selected genre
    book?.title && (editTitleField.value = book.title);
    const categoryId = book.genre?.category_id ?? '';
    const selectedGenreId = book.genre_id ?? '';
    editCategorySelect.value = categoryId;

    book?.author?.firstname && (editAuthorFirstNameField.value = book.author.firstname);
    book?.author?.lastname && (editAuthorLastNameField.value = book.author.lastname);
    book?.author?.middle_initial && (editAuthorMiddleInitialField.value = book.author.middle_initial);

    book?.isbn && (editIsbnField.value = book.isbn);
    book?.publication_year && (editPublicationYearField.value = book.publication_year);
    editLanguageSelect.value = book.language || '';
    editPublisherField.value = book.publisher || '';
    book?.description && (editDescriptionField.value = book.description);
    editPriceField.value = book.price || '';

    const editGenreLoading = form.querySelector('#genre-loading') || null;
    
    if (categoryId) {
        await handleCategoryChange(editCategorySelect, editGenreSelect, editGenreLoading);
        editGenreSelect.value = selectedGenreId || '';
    }

    // Handle cover preview
    if (book.cover_image) {
        editCoverImagePreview.src = `/storage/${book.cover_image}`;
        editCoverImagePreview.classList.remove('hidden');
        editCoverPlaceholder?.classList.add('hidden');
    } else {
        editCoverImagePreview.src = '';
        editCoverImagePreview.classList.add('hidden');
        editCoverPlaceholder?.classList.remove('hidden');
    }

    if (editCoverInputField) {
        // Clear previous value to ensure change event fires even if same file is selected
        editCoverInputField.value = '';

        // Only initialize if not already initialized to prevent duplicate listeners
        if (!editCoverInputField.dataset.previewInitialized) {
            initImagePreview(editCoverInputField, editCoverImagePreview, editCoverPlaceholder, editCoverErrorPlaceholder);
            editCoverInputField.dataset.previewInitialized = 'true';
        }
    }
    

    // Define a single reusable event handler
    if (!form._categoryChangeHandler) {
        form._categoryChangeHandler = async () => {
            await handleCategoryChange(editCategorySelect, editGenreSelect, editGenreLoading);
         };
    }

    // Setup and render copies table using helper
    setupCopiesTableStructure(editBookCopiesContainer, form);
    renderCopiesTable(book, form);

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

                editBookHandler(formData, form.id);

                // Immediately scroll to top after editBookHandler resolves
                
            } catch (err) {
                console.error('Error submitting edit form:', err);
                showError('Submission Error', err.message || 'An error occurred while submitting the form. Please try again.');
            }
        });

        // Add reset handler
        editBookForm.addEventListener('reset', function(e) {
            // We don't prevent default here because we want the form to reset its fields
            // But we need to handle the copies table reset manually
            setTimeout(() => {
                 resetAllCopiesToOriginal(form);
            }, 0);
        });
    }

    const globalResetButton = form.querySelector('#edit-form-reset-button');
    globalResetButton?.addEventListener('click', () => {
        console.log('Resetting form...');
        updatePendingIssueResolvedFlag(form);
    });

}

function setupCopiesTableStructure(container, form) {
   
    if (!container) return;
    
    // Check if structure exists
    if (!container.querySelector('#copies-table-body')) {
        container.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    Manage Copies
                    <span id="copies-count-badge" class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">0</span>
                </h3>
                <button type="button" id="add-copy-btn" class="text-sm text-accent font-medium hover:text-accent/80 flex items-center gap-1 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Copy
                </button>
            </div>
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Copy Number</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Controls</th>
                        </tr>
                    </thead>
                    <tbody id="copies-table-body" class="bg-white divide-y divide-gray-200">
                        <!-- Rows will be inserted here -->
                    </tbody>
                </table>
            </div>
            <div id="copies-pagination" class="mt-4 hidden"></div>
        `;
    }

    // Re-attach Add Copy listener
    const addBtn = container.querySelector('#add-copy-btn');
    if (addBtn) {
        const newBtn = addBtn.cloneNode(true);
        addBtn.parentNode.replaceChild(newBtn, addBtn);
        newBtn.addEventListener('click', () => addNewCopy(form));
    }
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