export function initializeBorrowerTabs(initialActiveTab = 'currently-borrowed-tab') {
    const tabBorrowedBtn = document.getElementById('tab-borrowed-btn');
    const tabPenaltiesBtn = document.getElementById('tab-penalties-btn');
    const tabReservationsBtn = document.getElementById('tab-reservations-btn');
    const tabBorrowedContent = document.getElementById('tab-borrowed-content');
    const tabPenaltiesContent = document.getElementById('tab-penalties-content');

    // Icons
    const borrowedIcon = document.getElementById('tab-borrowed-icon');
    const penaltiesIcon = document.getElementById('tab-penalties-icon');
    const reservationsIcon = document.getElementById('tab-reservations-icon');
    const borrowedIconDefault = "/build/assets/icons/currently-borrowed.svg";
    const borrowedIconAccent = "/build/assets/icons/currently-borrowed-accent.svg";
    const penaltiesIconDefault = "/build/assets/icons/unpaid.svg";
    const penaltiesIconAccent = "/build/assets/icons/unpaid-accent.svg";
    const reservationsIconDefault = "/build/assets/icons/reservation.svg";
    const reservationsIconAccent = "/build/assets/icons/reservation-accent.svg";

    // Badges
    const borrowedCountBadge = document.getElementById('badge-borrowed-count');
    const penaltiesCountBadge = document.getElementById('badge-penalties-count');
    const reservationsCountBadge = document.getElementById('badge-reservations-count');

    function activateTab(tabBtn, iconEl, accentSrc, badge) {
        tabBtn.classList.add('border-accent', 'text-accent', 'bg-white');
        tabBtn.style.borderBottom = 'none';
        tabBtn.style.marginBottom = '1.5rem';
        if (iconEl) iconEl.src = accentSrc;
        if (badge) badge.style.display = 'none';
    }

    function deactivateTab(tabBtn, iconEl, defaultSrc, badge) {
        tabBtn.classList.remove('border-accent', 'text-accent', 'bg-white');
        tabBtn.style.borderBottom = '';
        tabBtn.style.marginBottom = '';
        if (iconEl) iconEl.src = defaultSrc;
        if (badge) {
            const count = parseInt(badge.querySelector('span')?.textContent || '0', 10);
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

    function setActiveTab(activeBtn) {
        if (tabBorrowedBtn) {
            if (activeBtn === tabBorrowedBtn) {
                activateTab(tabBorrowedBtn, borrowedIcon, borrowedIconAccent, borrowedCountBadge);
            } else {
                deactivateTab(tabBorrowedBtn, borrowedIcon, borrowedIconDefault, borrowedCountBadge);
            }
        }
        if (tabPenaltiesBtn) {
            if (activeBtn === tabPenaltiesBtn) {
                activateTab(tabPenaltiesBtn, penaltiesIcon, penaltiesIconAccent, penaltiesCountBadge);
            } else {
                deactivateTab(tabPenaltiesBtn, penaltiesIcon, penaltiesIconDefault, penaltiesCountBadge);
            }
        }
        if (tabReservationsBtn) {
            if (activeBtn === tabReservationsBtn) {
                activateTab(tabReservationsBtn, reservationsIcon, reservationsIconAccent, reservationsCountBadge);
            } else {
                deactivateTab(tabReservationsBtn, reservationsIcon, reservationsIconDefault, reservationsCountBadge);
            }
        }
    }

    // Reset all tabs to default (badges visible if count > 0)
    function resetTabsToDefault() {
        deactivateTab(tabBorrowedBtn, borrowedIcon, borrowedIconDefault, borrowedCountBadge);
        deactivateTab(tabPenaltiesBtn, penaltiesIcon, penaltiesIconDefault, penaltiesCountBadge);
        deactivateTab(tabReservationsBtn, reservationsIcon, reservationsIconDefault, reservationsCountBadge);
    }

    if (tabBorrowedBtn && tabPenaltiesBtn && tabBorrowedContent && tabPenaltiesContent) {
        tabBorrowedBtn.onclick = () => {
            setActiveTab(tabBorrowedBtn);
            tabBorrowedContent.classList.remove('hidden');
            tabPenaltiesContent.classList.add('hidden');
        };
        tabPenaltiesBtn.onclick = () => {
            setActiveTab(tabPenaltiesBtn);
            tabPenaltiesContent.classList.remove('hidden');
            tabBorrowedContent.classList.add('hidden');
        };
        if (tabReservationsBtn) {
            tabReservationsBtn.onclick = () => {
                setActiveTab(tabReservationsBtn);
                tabBorrowedContent.classList.add('hidden');
                tabPenaltiesContent.classList.add('hidden');
            };
        }

        // Default state
        resetTabsToDefault();
console.log('Initial Active Tab:', initialActiveTab);
        if (initialActiveTab === 'currently-borrowed-tab') {
            setActiveTab(tabBorrowedBtn);
            tabBorrowedContent.classList.remove('hidden');
            tabPenaltiesContent.classList.add('hidden');
        } else if (initialActiveTab === 'penalties-tab') {
            setActiveTab(tabPenaltiesBtn);
            tabPenaltiesContent.classList.remove('hidden');
            tabBorrowedContent.classList.add('hidden');
        }
        
    }

    return { resetTabsToDefault };
}
