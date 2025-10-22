// Initialize preview once and avoid duplicate handlers.
export function initImagePreview(input, preview, placeholder, errorPlaceholder) {
    if (!input || input._previewInit) return;
    input._previewInit = true;

    input.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (errorPlaceholder) errorPlaceholder.textContent = '';

        if (!file) {
            if (preview) preview.classList.add('hidden');
            if (placeholder) placeholder.classList.remove('hidden');
            return;
        }

        if (!file.type.startsWith('image/')) {
            if (errorPlaceholder) errorPlaceholder.textContent = 'Invalid file type';
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            if (preview) {
                preview.src = reader.result;
                preview.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
}
