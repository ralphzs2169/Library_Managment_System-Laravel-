import { addBookHandler } from "../../api/bookHandler.js";
import { blurActiveElement, clearInputError } from "../../helpers.js";
import { fetchGenresByCategory } from "../../api/genreHandler.js";
import { initImagePreview } from "../librarian/imagePreview.js";

// Individual element references (no refs object)
const addBookform = document.getElementById('add-book-form');

const coverInputField = document.getElementById('cover-input');

// Cover input preview 
const coverPreview = document.getElementById('cover-preview');
const coverPlaceholder = document.getElementById('cover-placeholder');
const coverErrorPlaceholder = document.getElementById('cover-error-placeholder');


// attach once at script load
if (coverInputField) {
	initImagePreview(coverInputField, coverPreview, coverPlaceholder, coverErrorPlaceholder);
}


if (addBookform) {
    addBookform.addEventListener('submit', (e) => {
        e.preventDefault();

        blurActiveElement();

        const form = e.target;
        const formData = new FormData(form);

        // Map normal form fields using ids that match backend validation keys
        formData.set('title', document.querySelector('#title')?.value ?? '');
        formData.set('isbn', document.querySelector('#isbn')?.value ?? '');
        formData.set('description', document.querySelector('#description')?.value ?? '');
        formData.set('publisher', document.querySelector('#publisher')?.value ?? '');
        formData.set('publication_year', document.querySelector('#publication_year')?.value ?? '');
        formData.set('copies_available', document.querySelector('#copies_available')?.value ?? '');
        formData.set('genre', document.querySelector('#genre')?.value ?? '');
        formData.set('category', document.querySelector('#category')?.value ?? '');
        formData.set('language', document.querySelector('#language')?.value ?? '');
        formData.set('price', document.querySelector('#price')?.value ?? '');
        formData.set('author_firstname', document.querySelector('#author_firstname')?.value ?? '');
        formData.set('author_lastname', document.querySelector('#author_lastname')?.value ?? '');
        formData.set('author_middle_initial', document.querySelector('#author_middle_initial')?.value ?? '');

        // File: ensure cover-input id remains 'cover-input'
        if (coverInputField && coverInputField.files && coverInputField.files.length > 0) {
            formData.set('cover', coverInputField.files[0]);
        }

        // Send FormData (multipart) to handler
        addBookHandler(formData, form.id);
    });
}

const categorySelect = document.getElementById('category');
const genreSelect = document.getElementById('genre');
const genreLoading = document.getElementById('genre-loading');

// Disable genre select initially
if (genreSelect) genreSelect.disabled = true;

if (categorySelect && genreSelect) {
    categorySelect.addEventListener('change', async function () {
        const categoryId = this.value;

        if (!categoryId) {
            genreSelect.innerHTML = '<option value="">Select Genre...</option>';
            genreSelect.disabled = true;
            if (genreLoading) genreLoading.classList.add('hidden');
            return;
        }

        await fetchGenresByCategory(categorySelect, genreSelect, genreLoading, categoryId);

        const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
        genreSelect.options[0].text = `Select ${categoryName} genres...`;
    });
}


// Attach clear logic to all relevant fields
document.querySelectorAll(
    '#add-book-form input, #add-book-form select, #add-book-form textarea'
).forEach(el => {
    el.addEventListener('focus', () => clearInputError(el));
    el.addEventListener('input', () => clearInputError(el));
    el.addEventListener('change', () => clearInputError(el));
});
