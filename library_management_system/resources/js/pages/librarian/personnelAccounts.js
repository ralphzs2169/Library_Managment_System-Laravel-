// ...existing code...

// Auto-open register modal if action=register parameter is present
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    
    if (action === 'register') {
        // Open the register personnel modal
        const registerButton = document.getElementById('open-register-personnel-modal');
        if (registerButton) {
            registerButton.click();
        }
        
        // Remove the query parameter from URL without reloading
        const url = new URL(window.location);
        url.searchParams.delete('action');
        window.history.replaceState({}, '', url);
    }
});

// ...existing code...