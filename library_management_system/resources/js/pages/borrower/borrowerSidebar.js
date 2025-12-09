    document.addEventListener('DOMContentLoaded', function() {
        const categoryToggles = document.querySelectorAll('.category-toggle');

        categoryToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                console.log("asdasd");
                const categoryItem = this.closest('.category-item');
                const genresList = categoryItem.querySelector('.genres-list');
                const chevron = this.querySelector('.chevron');

                // Toggle the genres list
                genresList.classList.toggle('hidden');

                // Rotate chevron
                chevron.classList.toggle('rotate-180');
            });
        });
    });