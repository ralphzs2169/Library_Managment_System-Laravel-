document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('books-table-container');

    document.querySelectorAll('.edit-book-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const bookId = this.getAttribute('data-book-id');
            tableContainer.classList.add('hidden');
            document.querySelectorAll('[id^="edit-book-form-container-"]').forEach(form => form.classList.add('hidden'));
            const editForm = document.getElementById('edit-book-form-container-' + bookId);
            if (editForm) editForm.classList.remove('hidden');
        });
    });

    // Improved cancel logic: works for button, icon, and text
    document.querySelectorAll('[id^="edit-book-form-container-"]').forEach(form => {
        form.addEventListener('click', function (e) {
            const cancelBtn = e.target.closest('#cancel-edit-book');
            if (cancelBtn) {
                form.classList.add('hidden');
                tableContainer.classList.remove('hidden');
            }
        });
    });
});
