import { fetchBookDetails, loadBooks } from '../../api/bookHandler.js';
import { clearAllErrors } from '../../helpers.js';
import { initPagination, initSearch } from '../../tableControls.js';
import { initializeEditForm } from './editBook.js';
import { BOOK_CATALOG_SEARCH_COLUMN_INDEXES } from '../../config.js';
import { 
	renderCopiesTable, 
	populateSummaryHeader,
	addNewCopy
} from './editBookHelpers.js';

initPagination(loadBooks);
initSearch('#books-search', loadBooks, '#books-table-body', BOOK_CATALOG_SEARCH_COLUMN_INDEXES);

// Add filter event listeners
const categoryFilter = document.querySelector('#books-category-filter');
const statusFilter = document.querySelector('#books-status-filter');
const sortSelect = document.querySelector('#books-sort');
const resetFiltersBtn = document.querySelector('#reset-books-filters');
const searchInput = document.querySelector('#books-search');

if (categoryFilter) {
    categoryFilter.addEventListener('change', () => loadBooks(1));
}

if (statusFilter) {
    statusFilter.addEventListener('change', () => loadBooks(1));
}

if (sortSelect) {
    sortSelect.addEventListener('change', () => loadBooks(1));
}

// Reset filters functionality
if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener('click', () => {
        // Reset all filter inputs to default values
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (statusFilter) statusFilter.value = 'all';
        if (sortSelect) sortSelect.value = 'newest';
        
        // Reload books with default filters
        loadBooks(1);
    });
}

const tableContainer = document.getElementById('books-table-container');
const editFormContainer = document.getElementById('edit-book-form-container');
const filtersContainer = document.getElementById('books-filters');

// Show edit form, hide table
function showEditForm() {
	tableContainer.classList.add('hidden');
	editFormContainer.classList.remove('hidden');
	if (filtersContainer) filtersContainer.classList.add('hidden');
	window.scrollTo({ top: 0 });
}

// Show table, hide edit form
function showTable() {
	editFormContainer.classList.add('hidden');
	tableContainer.classList.remove('hidden');
	if (filtersContainer) filtersContainer.classList.remove('hidden');
	clearAllErrors(editFormContainer);
	editFormContainer.setAttribute('data-book-id', '');
}

// Populate the static form markup with book data
function populateEditForm(book) {
	if (!editFormContainer) return;

	// Basic fields
	const title = editFormContainer.querySelector('#title');
	const isbn = editFormContainer.querySelector('#isbn');
	const description = editFormContainer.querySelector('#description');
	const price = editFormContainer.querySelector('#price');
	const authorFirst = editFormContainer.querySelector('#author_firstname');
	const authorLast = editFormContainer.querySelector('#author_lastname');
	const authorMi = editFormContainer.querySelector('#author_middle_initial');
	const publisher = editFormContainer.querySelector('#publisher');
	const pubYear = editFormContainer.querySelector('#publication_year');
	const language = editFormContainer.querySelector('#language');
	const category = editFormContainer.querySelector('#category');
	const genre = editFormContainer.querySelector('#genre');
	const coverPreview = editFormContainer.querySelector('#cover-preview');
	const hiddenId = editFormContainer.querySelector('#edit-book-id');

	// Assign basic fields
	if (hiddenId) hiddenId.value = book.id || '';
	if (title) title.value = book.title || '';
	if (isbn) isbn.value = book.isbn || '';
	if (description) description.value = book.description || '';
	if (price) price.value = book.price ?? '';
	if (authorFirst) authorFirst.value = book.author?.firstname || '';
	if (authorLast) authorLast.value = book.author?.lastname || '';
	if (authorMi) authorMi.value = book.author?.middle_initial || '';
	if (publisher) publisher.value = book.publisher || '';
	if (pubYear) pubYear.value = book.publication_year || '';
	if (language) language.value = book.language || '';

	// Category and Genre select
	if (category && book.genre?.category?.id) {
		category.value = String(book.genre.category.id);
		category.dispatchEvent(new Event('change', { bubbles: true }));
	}
	if (genre && book.genre?.id) {
		genre.value = String(book.genre.id);
	}

	// Cover preview
	if (coverPreview) {
		if (book.cover_image) {
			coverPreview.src = `/storage/${book.cover_image}`;
			coverPreview.classList.remove('hidden');
		} else {
			coverPreview.src = '';
			coverPreview.classList.add('hidden');
		}
	}

	// Use helper functions
	populateSummaryHeader(book, editFormContainer);
	renderCopiesTable(book, editFormContainer);
	
}

// Event listener for add copy button
document.addEventListener('click', (e) => {
	// Add new copy button
	if (e.target.id === 'add-copy-btn' || e.target.closest('#add-copy-btn')) {
		e.preventDefault();
		addNewCopy(editFormContainer);
	}
});

// Event delegation for Edit and Cancel
document.addEventListener('click', async (e) => {
	// Edit button
	const editBtn = e.target.closest('.edit-book-btn');
	if (editBtn) {
		e.preventDefault();

		const bookId = editBtn.getAttribute('data-book-id');
		if (!bookId) return;

		editFormContainer.setAttribute('data-book-id', bookId);

		// Fetch details and hydrate
		const book = await fetchBookDetails({ id: bookId });
		if (!book) return;

		initializeEditForm(editFormContainer, book);

		populateEditForm(book);
		showEditForm();
		return;
	}

	// Cancel button in form
	if (e.target.closest('#cancel-edit-book')) {
		e.preventDefault();
		showTable();
	}
});


