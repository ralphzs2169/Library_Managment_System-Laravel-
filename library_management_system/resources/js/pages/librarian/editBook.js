import { editBookHandler } from "../../api/bookHandler.js";
import { fetchGenresByCategory } from "../../api/genreHandler.js"; // ensure this exists in api

// Single-form initializer. Call with the form element (scoped).
export function initEditBookForm(form) {
	// Avoid double initialization
	if (!form || form.dataset.editInitialized === 'true') return;
	form.dataset.editInitialized = 'true';

	// Log missing handlers as warnings so they surface but don't stop other logic
	if (typeof editBookHandler !== 'function') {
		console.warn('editBookHandler is not a function — check import path: "../../api/bookHandler.js"');
	}
	if (typeof fetchGenresByCategory !== 'function') {
		console.warn('fetchGenresByCategory is not a function — check import path: "../../api/genreHandler.js"');
	}

	try {
		// Scoped element queries
		const coverInputField = form.querySelector('#edit-cover-input');
		const coverPreview = form.querySelector('#edit-cover-preview');
		const coverPlaceholder = form.querySelector('#edit-cover-placeholder');
		const coverErrorPlaceholder = form.querySelector('#edit-cover-error-placeholder');
		const addAuthorBtn = form.querySelector('#edit-add-author-btn');
		const authorsContainer = form.querySelector('#edit-authors-container');

		// Cover preview logic (scoped)
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

		// Dynamic author fields (scoped)
		let dynamicAuthorIndex = authorsContainer ? authorsContainer.querySelectorAll('.author-group').length : 0;
		if (addAuthorBtn && authorsContainer) {
			addAuthorBtn.addEventListener('click', () => {
				const newAuthorGroup = document.createElement('div');
				newAuthorGroup.classList.add('author-group', 'dynamic', 'grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-2', 'mt-2');
				newAuthorGroup.innerHTML = `
					<div class="flex items-center md:col-span-3 mb-1">
						<span class="author-label font-medium mr-2">Author ${dynamicAuthorIndex + 1}</span>
						<button type="button" class="remove-author-btn px-2 text-red-600 border border-red-600 rounded hover:bg-red-600 hover:text-white transition">Remove</button>
					</div>
					<div class="field-container flex flex-col">
						<div class="error-placeholder" id="edit-authors[${dynamicAuthorIndex}][firstname]-error-placeholder"></div>
						<input type="text" name="edit-authors[${dynamicAuthorIndex}][firstname]" placeholder="First Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
					</div>
					<div class="field-container flex flex-col">
						<div class="error-placeholder" id="edit-authors[${dynamicAuthorIndex}][lastname]-error-placeholder"></div>
						<input type="text" name="edit-authors[${dynamicAuthorIndex}][lastname]" placeholder="Last Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
					</div>
					<div class="field-container flex flex-col">
						<div class="error-placeholder" id="edit-authors[${dynamicAuthorIndex}][middle_initial]-error-placeholder"></div>
						<input type="text" name="edit-authors[${dynamicAuthorIndex}][middle_initial]" placeholder="M.I." class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
					</div>
				`;
				authorsContainer.appendChild(newAuthorGroup);
				dynamicAuthorIndex++;

				newAuthorGroup.querySelector('.remove-author-btn').addEventListener('click', () => {
					newAuthorGroup.remove();
					dynamicAuthorIndex = authorsContainer.querySelectorAll('.author-group').length;
				});
			});
		}

		// Form submission logic (scoped)
		const editBookForm = form.querySelector('.edit-book-form') || form;
		if (editBookForm) {
			editBookForm.addEventListener('submit', async function (e) {
				e.preventDefault();
				e.stopPropagation();

				try {
					const fd = new FormData();

					// CSRF + method spoofing
					const meta = document.querySelector('meta[name="csrf-token"]');
					if (meta && meta.getAttribute('content')) {
						fd.append('_token', meta.getAttribute('content'));
					}
					fd.append('_method', 'PUT');

					// include book id (from hidden input inside this form)
					const bookIdEl = editBookForm.querySelector('#edit-book-id');
					if (bookIdEl && bookIdEl.value) fd.set('book_id', bookIdEl.value);

					// scalar fields (scoped lookups)
					const scalarFields = [
						'title','isbn','description','price','publisher',
						'publication_year','genre_id','category_id','language','copies_available'
					];
					scalarFields.forEach(field => {
						const element = editBookForm.querySelector('#edit-' + field);
						const key = 'updated_' + field;
						if (element) {
							fd.set(key, element.value ?? '');
						} else {
							fd.set(key, '');
						}
					});

					// Cover file
					const coverEl = editBookForm.querySelector('#edit-cover-input');
					if (coverEl && coverEl.files && coverEl.files.length > 0) {
						fd.set('updated_cover', coverEl.files[0]);
					}

					// Authors (scoped)
					const authorInputs = authorsContainer
						? authorsContainer.querySelectorAll('input[name^="edit-authors"], input[name^="authors"], input[name^="updated-authors"]')
						: [];
					authorInputs.forEach(input => {
						fd.set(input.name, input.value ?? '');
					});

					// call handler

					await editBookHandler(fd);
				
				} catch (err) {
					console.error('Error in edit form submit:', err);
				}
			});
		} else {
			console.warn('Scoped edit form not found.');
		}

		// --- load genres for this form ---
		const categorySelect = form.querySelector('#edit-category_id');
		const genreSelect = form.querySelector('#edit-genre_id');
		const genreLoading = form.querySelector('#edit-genre-loading');

		if (genreSelect && (!genreSelect.dataset.selected || !categorySelect || !categorySelect.value)) {
			genreSelect.disabled = true;
		}

		if (categorySelect && genreSelect) {
			categorySelect.addEventListener('change', function () {
				const categoryId = this.value;
				genreSelect.innerHTML = '<option value="">Select Genre...</option>';
				if (genreLoading) genreLoading.classList.remove('hidden');
				if (!categoryId) {
					if (genreLoading) genreLoading.classList.add('hidden');
					genreSelect.disabled = true;
					return;
				}
				if (typeof fetchGenresByCategory === 'function') {
					fetchGenresByCategory(categorySelect, genreSelect, genreLoading, categoryId, null);
				} else {
					console.error('fetchGenresByCategory missing');
					if (genreLoading) genreLoading.classList.add('hidden');
				}
			});

			const initialCategory = categorySelect.value;
			const initialSelectedGenre = genreSelect ? genreSelect.dataset.selected || null : null;
			if (initialCategory) {
				if (typeof fetchGenresByCategory === 'function') {
					fetchGenresByCategory(categorySelect, genreSelect, genreLoading, initialCategory, initialSelectedGenre);
				} else {
					console.error('fetchGenresByCategory missing for initial load');
				}
			}
		}
	} catch (err) {
		console.error('Error initializing edit form:', err);
	}
}

// Lazy initialization triggers:
// 1) when user clicks an .edit-book-btn (uses data-book-id => container id)
// 2) or when focus enters a form (focusin) — covers other flows that reveal the form
document.addEventListener('click', (ev) => {
	const btn = ev.target.closest('.edit-book-btn');
	if (!btn) return;
	const bookId = btn.getAttribute('data-book-id') || btn.dataset.bookId;
	if (!bookId) return;
	const container = document.getElementById(`edit-book-form-container-${bookId}`);
	if (!container) return;

	// find the form inside container (partial may be inside)
	const form = container.querySelector('.edit-book-form');
	if (form) {
		initEditBookForm(form);
	}
});

// Also initialize when user focuses into a form area (covers keyboard navigation or other triggers)
document.addEventListener('focusin', (ev) => {
	const form = ev.target.closest('.edit-book-form');
	if (form) initEditBookForm(form);
});


