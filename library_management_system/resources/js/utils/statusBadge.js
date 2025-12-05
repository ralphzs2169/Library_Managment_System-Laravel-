
export function getBorrowingStatusBadge(transaction) {
    const status = transaction.status;
    const daysUntilDue = transaction.days_until_due;
    const daysOverdue = transaction.days_overdue;
    // Assuming dueReminderThreshold is available in the transaction object or a global setting
    const dueReminderThreshold = transaction.due_reminder_threshold ?? 3; 

    let badgeBg = '';
    let svg = '';
    let label = '';

    // Helper to format labels (e.g., 'borrowed' -> 'Borrowed')

    // --- 1. Returned Statuses (Final States) ---
    if (transaction.returned_at) {
        const returnedDate = new Date(transaction.returned_at);
        const dueDate = new Date(transaction.due_at);
        
        if (returnedDate <= dueDate) {
            // Returned On Time (Green Check)
            badgeBg = 'bg-green-200 text-green-700 border border-green-100';
            svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
            label = 'Returned On Time';
        } else {
            // Returned Late (Orange Alert Icon)
            badgeBg = 'bg-orange-200 text-orange-700 border border-orange-100';
            svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            label = 'Returned Late';
            
            if (typeof daysOverdue === 'number' && daysOverdue > 0) {
                const dayText = daysOverdue === 1 ? 'day' : 'days';
                // Note: The inner HTML must be escaped/formatted carefully in JS string
                label += `<div class="mt-1 text-[11px] text-orange-700 font-semibold">(${daysOverdue} ${dayText} late)</div>`;
            }
        }
    } 
    // --- 2. Overdue Status ---
    else if (status === 'overdue') {
        badgeBg = 'bg-red-200 text-red-700 border border-red-100';
        svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        
        const dayText = daysOverdue === 1 ? 'day' : 'days';
        label = `${daysOverdue} ${dayText} overdue`;
    } 
    // --- 3. Borrowed Status (Active Loans) ---
    else if (status === 'borrowed') {
        const isDueSoon = typeof daysUntilDue === 'number' && daysUntilDue <= dueReminderThreshold;
        
        const timerSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

        if (isDueSoon) {
            // Due Soon (Orange Alert Icon, Timer icon)
            badgeBg = 'bg-orange-200 text-orange-700 border border-orange-100';
            svg = timerSvg;
            
            if (daysUntilDue === 0) {
                label = 'Due Today';
            } else if (daysUntilDue === 1) {
                label = 'Due in 1 day';
            } else {
                label = 'Due in ' + daysUntilDue + ' days';
            }
        } else {
            // Borrowed (Standard Blue, Timer icon)
            badgeBg = 'bg-blue-200 text-blue-700 border border-blue-100';
            svg = timerSvg;
            label = 'Due in ' + daysUntilDue + ' days';
        }
    } 
    // --- 4. Default/Other Statuses ---
    else {
        // Fallback for unexpected statuses
        badgeBg = 'bg-gray-100 text-gray-700 border border-gray-200';
        svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" /></svg>`;
        label = formatLabel(status);
    }

    // Construct the final HTML badge
    const badgeHtml =  `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${badgeBg}">
        ${svg}
        ${label}
    </span>`;

    return badgeHtml;
}

export function getReservationStatusBadge(status) {

    let badgeHtml = '';
    let badgeIcon = '';
    let badgeClass = '';
    let label = formatLabel(status);
    
    switch (label) {
        case 'pending':
            badgeClass = 'bg-yellow-200 text-yellow-800';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            break;

        case 'ready_for_pickup':
            badgeClass = 'bg-blue-100 text-blue-700'; 
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 384 512">
                    <path fill="#1D4ED8" d="M368 48h4c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h4c0 80.564 32.188 165.807 97.18 208C47.899 298.381 16 383.9 16 464h-4c-6.627 0-12 5.373-12 12v24c0 6.627 5.373 12 12 12h360c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12h-4c0-80.564-32.188-165.807-97.18-208C336.102 213.619 368 128.1 368 48zM64 48h256c0 101.62-57.307 184-128 184S64 149.621 64 48zm256 416H64c0-101.62 57.308-184 128-184s128 82.38 128 184z" />
                </svg>`;
            break;

        case 'completed':
            badgeClass = 'bg-green-200 text-green-700';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            break;

        case 'cancelled':
        case 'expired': 
            badgeClass = 'bg-gray-200 text-gray-700';
            badgeIcon = `
               <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
            break;

        default:
            // Fallback for any other unexpected status
            return `<span class="text-gray-500 text-xs">${label}</span>`;
    }

    // Construct the final HTML badge
    badgeHtml = `
        <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">
            ${badgeIcon}
            ${label}
        </span>
    `;

    return badgeHtml;
}

export function getPenaltyStatusBadge(status) {
    const formattedStatus = formatLabel(status);
    let badgeBg = '';
    let svgOrImg = '';
    let label = '';

    switch (formattedStatus) {
        case 'unpaid':
            badgeBg = 'bg-red-100 text-red-700';
            // Alert/Warning Icon SVG for Unpaid
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>`;
            label = 'Unpaid';
            break;

        case 'partially paid':
            badgeBg = 'bg-orange-200 text-orange-700';
            // Placeholder for the partially-paid SVG/Image
            svgOrImg = `<img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">`;
            label = 'Partially Paid';
            break;

        case 'paid':
            badgeBg = 'bg-green-200 text-green-700';
            // Checkmark Icon SVG for Paid
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`;
            label = 'Paid';
            break;

        case 'cancelled':
            badgeBg = 'bg-gray-200 text-gray-700';
            // X / Cancel Icon SVG
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>`;
            label = 'Cancelled'; // Override default formatting to match original Blade logic
            break;
            
        default:
            // Fallback for UNKNOWN status
            badgeBg = 'bg-gray-100 text-gray-500';
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" /></svg>`;
            label = 'Unknown';
            break;
    }

    // Construct the final HTML badge
    return `<span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${badgeBg} whitespace-nowrap">
        ${svgOrImg}
        ${label}
    </span>`;
}

export function getPenaltyTypeBadge(type) {
    
    // Standardize key for comparison
    const formattedType = formatLabel(type);
    console.log(formattedType);
    let badgeClass = '';
    let iconHtml = '';
    let label = '';

    switch (formattedType) {
        case 'late return':
            badgeClass = 'text-red-700';
            label = 'Late Return';
            iconHtml = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>`;
            break;

        case 'lost book':
            badgeClass = 'text-purple-700';
            label = 'Lost';
            iconHtml = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>`;
            break;

        case 'damaged book':
            badgeClass = 'text-orange-700';
            label = 'Damaged';
            iconHtml = `<img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">`;
            break;

        default:
            // Fallback for unknown type
            return `<span class="text-gray-500">${formatLabel(type)}</span>`;
    }

    return `<span class="w-full inline-flex items-center gap-1 rounded-full text-xs font-semibold ${badgeClass}">
        ${iconHtml}
        ${label}
    </span>`;
}

function formatLabel(s) {
    if (!s) {
        return 'Unknown';
    }
    // Convert to lowercase, split by underscores, , and rejoin
    return s.toLowerCase()
        .split('_')
        .map(word => word.charAt(0).toLowerCase() + word.slice(1))
        .join(' ');
}