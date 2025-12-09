import { clearInputError } from "../../helpers.js";
import { addCategoryHandler, deleteCategoryHandler, editCategoryHandler } from "../../ajax/categoryHandler.js";
import { addGenreHandler, editGenreHandler, deleteGenreHandler } from "../../ajax/genreHandler.js";
import { showDangerConfirmation } from "../../utils/alerts.js";

function setupModal(modalId, closeButtonId, cancelButtonId, formId, nameInputId, onSubmit, parentId = null) {
    const modal = document.getElementById(modalId);
    if (!modal) return null;

    const modalContent = document.getElementById(`${modalId}-content`);
    const closeBtn = document.getElementById(closeButtonId);
    const cancelBtn = document.getElementById(cancelButtonId);
    const form = document.getElementById(formId);
    const nameInput = document.getElementById(nameInputId);

    const closeModal = () => {
        // Fade out animation
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        if (modalContent) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
        }
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            if (form) form.reset();
        }, 200);
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    if (nameInput) nameInput.addEventListener('focus', () => clearInputError(nameInput));
    
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const success = await onSubmit();
            if (success) {
                closeModal();
            }
        });
    }

    return { modal, modalContent, form, nameInput, closeModal };
}

function openModal(setup) {
    if (!setup) return;
    const { modal, modalContent } = setup;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Trigger reflow
    modal.offsetHeight;
    
    // Fade in animation
    setTimeout(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        if (modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }
    }, 10);
}

// Genre Section Toggle
const showGenresBtns = document.querySelectorAll('.show-genres-btn');

showGenresBtns.forEach(btn => {
    const categoryId = btn.getAttribute('data-category-id');
    const categorySection = document.getElementById(`category-section-${categoryId}`);
    const genresSection = document.getElementById(`genres-section-${categoryId}`);
    const dropdownIcon = btn.querySelector('.dropdown-icon');
    const genreCount = document.getElementById(`genre-count-${categoryId}`);
    
    if (!categorySection || !genresSection || !dropdownIcon || !genreCount) return;

    let isExpanded = false;

    const toggleGenreSection = () => {
        if (isExpanded) {
            // Collapse
            genresSection.style.maxHeight = '0';
            genresSection.style.opacity = '0';
            dropdownIcon.style.transform = 'rotate(0deg)';
            categorySection.classList.add('mb-6');
        } else {
            // Expand
            genresSection.style.maxHeight = genresSection.scrollHeight + 'px';
            genresSection.style.opacity = '1';
            dropdownIcon.style.transform = 'rotate(180deg)';
            categorySection.classList.remove('mb-6');
        }
        isExpanded = !isExpanded;
    };

    btn.addEventListener('click', toggleGenreSection);
    genreCount.addEventListener('click', toggleGenreSection);
});

// Add Category Modal
const addCategorySetup = setupModal(
    'add-category-modal',
    'close-add-category-modal',
    'cancel-add-category',
    'add-category-form',
    'category_name',
    () => addCategoryHandler(addCategorySetup.nameInput.value.trim()) // call handler directly
);

const openAddCategoryBtn = document.getElementById('open-add-category-modal');
if (openAddCategoryBtn && addCategorySetup) {
    openAddCategoryBtn.addEventListener('click', () => {
        openModal(addCategorySetup);
    });
}

// Add Genre Modal
const addGenreCategoryIdInput = document.getElementById('add-genre-category-id');
const addGenreCategoryNameDisplay = document.getElementById('add-genre-category-name');

const addGenreSetup = setupModal(
    'add-genre-modal',
    'close-add-genre-modal',
    'cancel-add-genre',
    'add-genre-form',
    'genre_name',
    () => addGenreHandler(addGenreCategoryIdInput.value, addGenreSetup.nameInput.value.trim())
);

document.querySelectorAll('.open-add-genre-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        if (addGenreCategoryIdInput && addGenreCategoryNameDisplay && addGenreSetup) {
            addGenreCategoryIdInput.value = btn.getAttribute('data-category-id');
            addGenreCategoryNameDisplay.textContent = btn.getAttribute('data-category-name');
            openModal(addGenreSetup);
        }
    });
});

// Edit Category Modal

const editCategoryIdInput = document.getElementById('edit-category-id');

const editCategorySetup = setupModal(
    'edit-category-modal',
    'close-edit-category-modal',
    'cancel-edit-category',
    'edit-category-form',
    'updated_category_name',
    () => editCategoryHandler(editCategoryIdInput.value, editCategorySetup.nameInput.value.trim()) // call handler directly
);


// Edit Genre Modal
const editGenreIdInput = document.getElementById('edit-genre-id');

const editGenreSetup = setupModal(
    'edit-genre-modal',
    'close-edit-genre-modal',
    'cancel-edit-genre',
    'edit-genre-form',
    'updated_genre_name',
    () => editGenreHandler(editGenreIdInput.value, editGenreSetup.nameInput.value.trim())
);

//Category Edit/Delete Buttons
document.querySelectorAll('.category-item').forEach(category => {
    const categoryId = category.getAttribute('data-category-id');
    const categoryName = category.querySelector('h2')?.textContent.trim();
    
    const editBtn = category.querySelector(`#edit-category-${categoryId}`);
    if (editBtn && editCategorySetup && editCategoryIdInput) {
        editBtn.addEventListener('click', () => {
            editCategoryIdInput.value = categoryId;
            editCategorySetup.nameInput.value = categoryName;
            openModal(editCategorySetup);
        });
    }

    const deleteBtn = category.querySelector(`#delete-category-${categoryId}`);
    if (deleteBtn) {
        deleteBtn.addEventListener('click', async () => {
            const confirmed = await showDangerConfirmation(
                'Delete Category?',
                `This will permanently delete the category "${categoryName}" and cannot be undone!`
            );
            if (confirmed) deleteCategoryHandler(categoryId);
        });
    }
});

//Genre Edit/Delete Buttons
document.querySelectorAll('.genre-item').forEach(genre => {
    const genreId = genre.getAttribute('data-genre-id');
    const genreName = genre.querySelector('span')?.textContent.trim();
    const deleteBtn = genre.querySelector(`#delete-genre-${genreId}`);

    const editBtn = genre.querySelector(`#edit-genre-${genreId}`);
    if (editBtn && editGenreSetup && editGenreIdInput) {
        editBtn.addEventListener('click', () => {
            editGenreIdInput.value = genreId;
            editGenreSetup.nameInput.value = genreName;
            openModal(editGenreSetup);
        });
    }

    if (deleteBtn && deleteBtn.dataset.hasBooks !== 'true') {
        deleteBtn.addEventListener('click', async () => {
            const confirmed = await showDangerConfirmation(
                'Delete Genre?',
                `This will permanently delete the genre "${genreName}" and cannot be undone!`
            );
            if (confirmed) deleteGenreHandler(genreId);
        });
    }
});

// Export function to re-initialize listeners after AJAX reload
export function initCategoryGenreListeners() {
    // Genre Section Toggle
    const showGenresBtns = document.querySelectorAll('.show-genres-btn');

    showGenresBtns.forEach(btn => {
        const categoryId = btn.getAttribute('data-category-id');
        const categorySection = document.getElementById(`category-section-${categoryId}`);
        const genresSection = document.getElementById(`genres-section-${categoryId}`);
        const dropdownIcon = btn.querySelector('.dropdown-icon');
        const genreCount = document.getElementById(`genre-count-${categoryId}`);
        
        if (!categorySection || !genresSection || !dropdownIcon || !genreCount) return;

        let isExpanded = false;

        const toggleGenreSection = () => {
            if (isExpanded) {
                // Collapse
                genresSection.style.maxHeight = '0';
                genresSection.style.opacity = '0';
                dropdownIcon.style.transform = 'rotate(0deg)';
                categorySection.classList.add('mb-6');
            } else {
                // Expand
                genresSection.style.maxHeight = genresSection.scrollHeight + 'px';
                genresSection.style.opacity = '1';
                dropdownIcon.style.transform = 'rotate(180deg)';
                categorySection.classList.remove('mb-6');
            }
            isExpanded = !isExpanded;
        };

        btn.addEventListener('click', toggleGenreSection);
        genreCount.addEventListener('click', toggleGenreSection);
    });

    // Category Edit/Delete Buttons
    document.querySelectorAll('.category-item').forEach(category => {
        const categoryId = category.getAttribute('data-category-id');
        const categoryName = category.querySelector('h2')?.textContent.trim();

        const editBtn = category.querySelector(`#edit-category-${categoryId}`);
        if (editBtn && editCategorySetup && editCategoryIdInput) {
            editBtn.addEventListener('click', () => {
                editCategoryIdInput.value = categoryId;
                editCategorySetup.nameInput.value = categoryName;
                openModal(editCategorySetup);
            });
        }

        const deleteBtn = category.querySelector(`#delete-category-${categoryId}`);
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async () => {
                const confirmed = await showDangerConfirmation(
                    'Delete Category?',
                    `This will permanently delete the category "${categoryName}" and cannot be undone!`
                );
                if (confirmed) deleteCategoryHandler(categoryId);
            });
        }
    });

    // Genre Edit/Delete Buttons
    document.querySelectorAll('.genre-item').forEach(genre => {
        const genreId = genre.getAttribute('data-genre-id');
        const genreName = genre.querySelector('span')?.textContent.trim();
        const deleteBtn = genre.querySelector(`#delete-genre-${genreId}`);

        const editBtn = genre.querySelector(`#edit-genre-${genreId}`);
        if (editBtn && editGenreSetup && editGenreIdInput) {
            editBtn.addEventListener('click', () => {
                editGenreIdInput.value = genreId;
                editGenreSetup.nameInput.value = genreName;
                openModal(editGenreSetup);
            });
        }

        if (deleteBtn && deleteBtn.dataset.hasBooks !== 'true') {
            deleteBtn.addEventListener('click', async () => {
                const confirmed = await showDangerConfirmation(
                    'Delete Genre?',
                    `This will permanently delete the genre "${genreName}" and cannot be undone!`
                );
                if (confirmed) deleteGenreHandler(genreId);
            });
        }
    });

    // Re-attach "Add Genre" modal listeners
    document.querySelectorAll('.open-add-genre-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (addGenreCategoryIdInput && addGenreCategoryNameDisplay && addGenreSetup) {
                addGenreCategoryIdInput.value = btn.getAttribute('data-category-id');
                addGenreCategoryNameDisplay.textContent = btn.getAttribute('data-category-name');
                openModal(addGenreSetup);
            }
        });
    });
}

// Initialize on page load
initCategoryGenreListeners();
