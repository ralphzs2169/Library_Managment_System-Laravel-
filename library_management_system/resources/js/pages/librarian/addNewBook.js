import { addBookHandler } from "../../api/bookHandler.js";
import { blurActiveElement, clearInputError } from "../../helpers.js";
import { fetchGenresByCategory } from "../../api/genreHandler.js";

// Individual element references (no refs object)
const addBookform = document.getElementById('add-book-form');

const coverInputField = document.getElementById('cover-input');

// Cover input preview 
const coverPreview = document.getElementById('cover-preview');
const coverPlaceholder = document.getElementById('cover-placeholder');
const coverErrorPlaceholder = document.getElementById('cover-error-placeholder');

// Cover input preview (uses individual variables)
if (coverInputField) {
	coverInputField.addEventListener('change', (e) => {
		const file = e.target.files && e.target.files[0];
		if (coverErrorPlaceholder) coverErrorPlaceholder.textContent = '';
		if (!file) {
			if (coverPreview) coverPreview.classList.add('hidden');
			if (coverPlaceholder) coverPlaceholder.classList.remove('hidden');
			return;
		}
		if (!file.type.startsWith('image/')) {
			if (coverErrorPlaceholder) coverErrorPlaceholder.textContent = 'Invalid file type';
			coverInputField.value = '';
			return;
		}
		const reader = new FileReader();
		reader.onload = () => {
			if (coverPreview) {
				coverPreview.src = reader.result;
				coverPreview.classList.remove('hidden');
			}
			if (coverPlaceholder) coverPlaceholder.classList.add('hidden');
		};
		reader.readAsDataURL(file);
	});
}


const addAuthorBtn = document.getElementById('add-author-btn');
const authorsContainer = document.getElementById('authors-container');

let dynamicAuthorIndex = 1; // Dynamic authors start at 1 because 0 is the static first author

function updateDynamicAuthorNumbers() {
    const dynamicRows = authorsContainer.querySelectorAll('.author-group.dynamic');
    dynamicRows.forEach((group, i) => {
        const label = group.querySelector('.author-label');
        if (label) label.textContent = `Author ${i + 2}`; // +2 because first row is static Author 1
        // Update input names
        group.querySelector('input[name*="[firstname]"]').name = `authors[${i + 1}][firstname]`;
        group.querySelector('input[name*="[lastname]"]').name = `authors[${i + 1}][lastname]`;
        group.querySelector('input[name*="[middle_initial]"]').name = `authors[${i + 1}][middle_initial]`;
    });
}

addAuthorBtn.addEventListener('click', () => {
    const newAuthorGroup = document.createElement('div');
    newAuthorGroup.classList.add('author-group', 'dynamic', 'grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-2', 'mt-2');

    newAuthorGroup.innerHTML = `
        <div class="flex items-center md:col-span-3 mb-1">
            <span class="author-label font-medium mr-2">Author ${dynamicAuthorIndex + 1}</span>
            <button type="button" class="remove-author-btn px-2 text-red-600 border border-red-600 rounded hover:bg-red-600 hover:text-white transition">Remove</button>
        </div>
        <div class="field-container">
            <div class="error-placeholder" id="authors[${dynamicAuthorIndex}][firstname]-error-placeholder"></div>
            <input type="text" name="authors[${dynamicAuthorIndex}][firstname]" placeholder="First Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
        </div>
        <div class="field-container">
            <div class="error-placeholder" id="authors[${dynamicAuthorIndex}][lastname]-error-placeholder"></div>
            <input type="text" name="authors[${dynamicAuthorIndex}][lastname]" placeholder="Last Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
        </div>
        <div class="field-container">
            <div class="error-placeholder" id="authors[${dynamicAuthorIndex}][middle_initial]-error-placeholder"></div>
            <input type="text" name="authors[${dynamicAuthorIndex}][middle_initial]" placeholder="M.I." class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
    `;

    authorsContainer.appendChild(newAuthorGroup);
    dynamicAuthorIndex++;

    newAuthorGroup.querySelector('.remove-author-btn').addEventListener('click', () => {
        newAuthorGroup.remove();
        updateDynamicAuthorNumbers();
        dynamicAuthorIndex = authorsContainer.querySelectorAll('.author-group.dynamic').length + 1;
    });
});


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
        formData.set('genre_id', document.querySelector('#genre_id')?.value ?? '');
        formData.set('category_id', document.querySelector('#category_id')?.value ?? '');
        formData.set('language', document.querySelector('#language')?.value ?? '');
        formData.set('price', document.querySelector('#price')?.value ?? '');

        // File: ensure cover-input id remains 'cover-input'
        if (coverInputField && coverInputField.files && coverInputField.files.length > 0) {
            formData.set('cover', coverInputField.files[0]);
        }

        // === NEW: Handle multiple authors dynamically ===
        const authorGroups = document.querySelectorAll('.author-group');
        authorGroups.forEach((group, index) => {
            const firstname = group.querySelector(`input[name*="[firstname]"]`)?.value ?? '';
            const lastname = group.querySelector(`input[name*="[lastname]"]`)?.value ?? '';
            const middle_initial = group.querySelector(`input[name*="[middle_initial]"]`)?.value ?? '';

            // Only append non-empty author data
            formData.set(`authors[${index}][firstname]`, firstname);
            formData.set(`authors[${index}][lastname]`, lastname);
            formData.set(`authors[${index}][middle_initial]`, middle_initial);
        });

        // Send FormData (multipart) to handler
        addBookHandler(formData);
    });
}

const categorySelect = document.getElementById('category_id');
const genreSelect = document.getElementById('genre_id');
const genreLoading = document.getElementById('genre-loading');

// Disable genre select initially
if (genreSelect) genreSelect.disabled = true;

if (categorySelect && genreSelect) {
    categorySelect.addEventListener('change', function () {
        const categoryId = this.value;
        genreSelect.disabled = true;
        if (genreLoading) genreLoading.classList.remove('hidden');
        genreSelect.innerHTML = '<option value="">Select Genre...</option>';

        if (!categoryId) {
            if (genreLoading) genreLoading.classList.add('hidden');
            return;
        }
        fetchGenresByCategory(categorySelect, genreSelect, genreLoading, categoryId);
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
