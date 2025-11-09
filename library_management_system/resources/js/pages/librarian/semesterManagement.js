import { getCreateSemesterForm, storeNewSemester, activateSemester, fetchSemesterDetails, updateSemester } from '../../api/semesterHandler.js';
import { clearInputError } from '../../helpers.js';

const addSemesterModal = document.getElementById('add-semester-modal');
const closeAddSemesterModal = document.getElementById('close-add-semester-modal');
const cancelAddSemester = document.getElementById('cancel-add-semester');
const openAddSemesterBtn = document.getElementById('open-add-semester-modal');
const addSemesterForm = document.getElementById('add-semester-form');

const editSemesterModal = document.getElementById('edit-semester-modal');
const closeEditSemesterModal = document.getElementById('close-edit-semester-modal');
const cancelEditSemester = document.getElementById('cancel-edit-semester');
const editSemesterForm = document.getElementById('edit-semester-form');

// Open modal and fetch create form
openAddSemesterBtn.addEventListener('click', async () => {
    const result = await getCreateSemesterForm();
    
    if (result.success) {
        addSemesterModal.classList.remove('hidden');
    }
});

// Handle form submission
addSemesterForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('semester_name', document.getElementById('semester_name').value);
    formData.append('semester_start', document.getElementById('semester_start').value);
    formData.append('semester_end', document.getElementById('semester_end').value);

    await storeNewSemester(formData, addSemesterForm);
});

// Handle activate semester buttons
document.addEventListener('click', async (e) => {
    if (e.target.closest('.activate-semester')) {
        const button = e.target.closest('.activate-semester');
        const semesterId = button.dataset.semesterId;
        const semesterName = button.closest('tr').querySelector('.font-semibold').textContent;
        
        await activateSemester(semesterId, semesterName);
    }
    
    if (e.target.closest('.edit-semester')) {
        const button = e.target.closest('.edit-semester');
        const semesterId = button.dataset.semesterId;
        
        const semester = await fetchSemesterDetails(semesterId);
        if (semester) {
            document.getElementById('edit_semester_id').value = semester.id;
            document.getElementById('edit_semester_name').value = semester.name;
            document.getElementById('edit_semester_start').value = semester.start_date;
            document.getElementById('edit_semester_end').value = semester.end_date;
            
            editSemesterModal.classList.remove('hidden');
        }
    }
});

// Handle edit form submission
editSemesterForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const semesterId = document.getElementById('edit_semester_id').value;
    const formData = new FormData();
    formData.append('semester_name', document.getElementById('edit_semester_name').value);
    formData.append('semester_start', document.getElementById('edit_semester_start').value);
    formData.append('semester_end', document.getElementById('edit_semester_end').value);

    await updateSemester(semesterId, formData, editSemesterForm);
});

// Close modal when clicking outside
addSemesterModal.addEventListener('click', (e) => {
    if (e.target === addSemesterModal) {
        addSemesterModal.classList.add('hidden');
       resetFormErrors();
    }
});

// Close modal handlers
closeAddSemesterModal.addEventListener('click', () => {
    addSemesterModal.classList.add('hidden');
    resetFormErrors();
});

cancelAddSemester.addEventListener('click', () => {
    addSemesterModal.classList.add('hidden');
    resetFormErrors();
});

// Close edit modal handlers
closeEditSemesterModal.addEventListener('click', () => {
    editSemesterModal.classList.add('hidden');
    resetEditFormErrors();
});

cancelEditSemester.addEventListener('click', () => {
    editSemesterModal.classList.add('hidden');
    resetEditFormErrors();
});

// Close edit modal when clicking outside
editSemesterModal.addEventListener('click', (e) => {
    if (e.target === editSemesterModal) {
        editSemesterModal.classList.add('hidden');
        resetEditFormErrors();
    }
});

// Attach clear logic to all relevant fields
document.querySelectorAll(
    '#add-semester-form input'
).forEach(el => {
    el.addEventListener('focus', () => clearInputError(el));
    el.addEventListener('input', () => clearInputError(el));
    el.addEventListener('change', () => clearInputError(el));
});

// Attach clear logic to edit form fields
document.querySelectorAll(
    '#edit-semester-form input'
).forEach(el => {
    el.addEventListener('focus', () => clearInputError(el));
    el.addEventListener('input', () => clearInputError(el));
    el.addEventListener('change', () => clearInputError(el));
});

function resetFormErrors() {
    document.querySelectorAll('#add-semester-form input').forEach(el => {
        clearInputError(el);
    });
}

function resetEditFormErrors() {
    document.querySelectorAll('#edit-semester-form input').forEach(el => {
        clearInputError(el);
    });
    editSemesterForm.reset();
}