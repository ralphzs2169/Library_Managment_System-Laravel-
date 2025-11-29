import { initBorrowersTableControls } from './dashboardTableControls.js';

export function initializeStaffDashboardTabs(initialActiveTab = 'members-table-tab') {
    const tabMembersBtn = document.getElementById('tab-members-btn');
    const tabBorrowsBtn = document.getElementById('tab-active-borrows-btn');
    const tabPenaltiesBtn = document.getElementById('tab-unpaid-penalties-btn');
    const tabReservationsBtn = document.getElementById('tab-reservations-btn');
    console.log("HELLLOOOO");
    const tabMembersContent = document.getElementById('tab-members-content');
    const tabBorrowsContent = document.getElementById('tab-active-borrows-content');
    const tabPenaltiesContent = document.getElementById('tab-unpaid-penalties-content');
    const tabReservationsContent = document.getElementById('tab-reservations-content');

    const icons = {
        members: document.getElementById('tab-members-icon'),
        borrows: document.getElementById('tab-active-borrows-icon'),
        penalties: document.getElementById('tab-unpaid-penalties-icon'),
        reservations: document.getElementById('tab-reservations-icon'),
    };

    const iconSrc = {
        members: ["/build/assets/icons/members-list-black.svg", "/build/assets/icons/members-list-accent.svg"],
        borrows: ["/build/assets/icons/currently-borrowed.svg", "/build/assets/icons/currently-borrowed-accent.svg"],
        penalties: ["/build/assets/icons/unpaid.svg", "/build/assets/icons/unpaid-accent.svg"],
        reservations: ["/build/assets/icons/reservation.svg", "/build/assets/icons/reservation-accent.svg"],
    };

    const badges = {
        members: document.getElementById('badge-members-count'),
        borrows: document.getElementById('badge-active-borrows-count'),
        penalties: document.getElementById('badge-unpaid-penalties-count'),
        reservations: document.getElementById('badge-reservations-count'),
    };

    const tabs = [
        { key: 'members', btn: tabMembersBtn, content: tabMembersContent },
        { key: 'borrows', btn: tabBorrowsBtn, content: tabBorrowsContent },
        { key: 'penalties', btn: tabPenaltiesBtn, content: tabPenaltiesContent },
        { key: 'reservations', btn: tabReservationsBtn, content: tabReservationsContent },
    ];

    function activateTab(tab) {
        tab.btn.classList.add('border-accent', 'text-accent', 'bg-white');
        tab.btn.style.borderBottom = 'none';
        tab.btn.style.marginBottom = '1.5rem';

        // Remove any previous custom underline
        let underline = tab.btn.querySelector('.tab-underline');
        if (underline) underline.remove();

        // Create and append the underline element
        underline = document.createElement('span');
        underline.className = 'tab-underline-accent';
        tab.btn.appendChild(underline);

        if (icons[tab.key]) icons[tab.key].src = iconSrc[tab.key][1];
        if (badges[tab.key]) badges[tab.key].style.display = 'none';
    }

    function deactivateTab(tab) {
        tab.btn.classList.remove('border-accent', 'text-accent', 'bg-white');
        tab.btn.style.borderBottom = '';
        tab.btn.style.marginBottom = '';
        // Remove custom underline if present
        const underline = tab.btn.querySelector('.tab-underline-accent');
        if (underline) underline.remove();
        if (icons[tab.key]) icons[tab.key].src = iconSrc[tab.key][0];
        if (badges[tab.key]) {
            const count = parseInt(badges[tab.key].querySelector('span')?.textContent || '0', 10);
            badges[tab.key].style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

function setActiveTab(activeTab) {
        tabs.forEach(tab => {
            if (!tab.btn || !tab.content) return;
            
            if (tab === activeTab) {
                activateTab(tab);
                tab.content.classList.remove('hidden');
                
                // REMOVED: The setTimeout initBorrowersTableControls block.
                // The controls are initialized once on page load by the other file.
                
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

    if (initialActiveTab === 'members-table-tab') {
        setActiveTab(tabs[0]);
    } else if (initialActiveTab === 'active-borrows-tab') {
        setActiveTab(tabs[1]);
    } else if (initialActiveTab === 'unpaid-penalties-tab') {
        setActiveTab(tabs[2]);
    } else if (initialActiveTab === 'reservations-tab') {
        setActiveTab(tabs[3]);
    }

    return { resetTabsToDefault };
}

initializeStaffDashboardTabs();
