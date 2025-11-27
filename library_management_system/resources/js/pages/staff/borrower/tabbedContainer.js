export function initializeBorrowerTabs(initialActiveTab = 'currently-borrowed-tab') {
    const tabBorrowedBtn = document.getElementById('tab-borrowed-btn');
    const tabPenaltiesBtn = document.getElementById('tab-penalties-btn');
    const tabReservationsBtn = document.getElementById('tab-reservations-btn');
    const tabBorrowedContent = document.getElementById('tab-borrowed-content');
    const tabPenaltiesContent = document.getElementById('tab-penalties-content');
    const tabReservationsContent = document.getElementById('tab-reservations-content');

    const icons = {
        borrowed: document.getElementById('tab-borrowed-icon'),
        penalties: document.getElementById('tab-penalties-icon'),
        reservations: document.getElementById('tab-reservations-icon'),
    };

    // [0] => default icon
    // [1] => accent icon (when tab is active)
    const iconSrc = {
        borrowed: ["/build/assets/icons/currently-borrowed.svg", "/build/assets/icons/currently-borrowed-accent.svg"],
        penalties: ["/build/assets/icons/unpaid.svg", "/build/assets/icons/unpaid-accent.svg"],
        reservations: ["/build/assets/icons/reservation.svg", "/build/assets/icons/reservation-accent.svg"],
    };

    // Badges
    const badges = {
        borrowed: document.getElementById('badge-borrowed-count'),
        penalties: document.getElementById('badge-penalties-count'),
        reservations: document.getElementById('badge-reservations-count'),
    };

    const tabs = [
        { key: 'borrowed', btn: tabBorrowedBtn, content: tabBorrowedContent },
        { key: 'penalties', btn: tabPenaltiesBtn, content: tabPenaltiesContent },
        { key: 'reservations', btn: tabReservationsBtn, content: tabReservationsContent },
    ];

    function activateTab(tab) {
        tab.btn.classList.add('border-accent', 'text-accent', 'bg-white');
        tab.btn.style.borderBottom = 'none';
        tab.btn.style.marginBottom = '1.5rem';
        if (icons[tab.key]) icons[tab.key].src = iconSrc[tab.key][1];
        if (badges[tab.key]) badges[tab.key].style.display = 'none';
    }

    function deactivateTab(tab) {
        tab.btn.classList.remove('border-accent', 'text-accent', 'bg-white');
        tab.btn.style.borderBottom = '';
        tab.btn.style.marginBottom = '';
        if (icons[tab.key]) icons[tab.key].src = iconSrc[tab.key][0];
        if (badges[tab.key]) {
            const count = parseInt(badges[tab.key].querySelector('span')?.textContent || '0', 10);
            badges[tab.key].style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

    function setActiveTab(activeTab) {
        tabs.forEach(tab => {
            if (tab === activeTab) {
                activateTab(tab);
                tab.content.classList.remove('hidden');
            } else {
                deactivateTab(tab);
                tab.content.classList.add('hidden');
            }
        });
    }

    // Attach click handlers
    tabs.forEach(tab => {
        if (tab.btn && tab.content) {
            tab.btn.onclick = () => setActiveTab(tab);
        }
    });

    // Reset all tabs to default (badges visible if count > 0)
    function resetTabsToDefault() {
        tabs.forEach(deactivateTab);
    }

    // Default state
    resetTabsToDefault();

    if (initialActiveTab === 'currently-borrowed-tab') {
        setActiveTab(tabs[0]);
    } else if (initialActiveTab === 'penalties-tab') {
        setActiveTab(tabs[1]);
    } else if (initialActiveTab === 'reservations-tab') {
        setActiveTab(tabs[2]);
    }

    return { resetTabsToDefault };
}
