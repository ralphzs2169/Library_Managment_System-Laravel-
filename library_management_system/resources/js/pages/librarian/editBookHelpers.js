export function renderStatusBadge(status) {
	const statusConfig = {
		available: {
			class: 'bg-green-200 text-green-700 border-green-100',
			icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
			</svg>`
		},
		borrowed: {
			class: 'bg-blue-200 text-blue-700 border-blue-100',
			icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
			</svg>`
		},
		damaged: {
			class: 'bg-orange-200 text-orange-700 border-orange-100',
			icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
			</svg>`
		},
		lost: {
			class: 'bg-red-200 text-red-700 border-red-100',
			icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
			</svg>`
		},
		withdrawn: {
			class: 'bg-gray-200 text-gray-700 border-gray-200',
			icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
			</svg>`
		}
	};

	const config = statusConfig[status] || statusConfig['available'];
	return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${config.class}">
		${config.icon}
		${status.charAt(0).toUpperCase() + status.slice(1)}
	</span>`;
}

let nextNewCopyId = -1; // Temporary IDs for new copies (negative to avoid conflicts)
const COPIES_PER_PAGE = 5;
let currentCopiesPage = 1;
let allCopiesData = [];

export function renderCopiesTable(book, editFormContainer) {
	const tbody = editFormContainer.querySelector('#copies-table-body');
	if (!tbody) return;

	const copies = Array.isArray(book.copies) ? book.copies : [];
	allCopiesData = copies; // Store for pagination
	
	if (!copies.length) {
		tbody.innerHTML = `
			<tr>
				<td colspan="4" class="py-20 text-center">
					<div class="flex flex-col items-center justify-center">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
						</svg>
						<p class="text-gray-500 font-medium mb-1">No copies found</p>
						<p class="text-gray-400 text-xs">Click "Add Copy" to add new copies</p>
					</div>
				</td>
			</tr>`;
		return;
	}

	// Reset to first page when reloading data
	currentCopiesPage = 1;
	renderCopiesPage(editFormContainer);
}

function renderCopiesPage(editFormContainer) {
	const tbody = editFormContainer.querySelector('#copies-table-body');
	if (!tbody) return;

	const startIndex = (currentCopiesPage - 1) * COPIES_PER_PAGE;
	const endIndex = startIndex + COPIES_PER_PAGE;
	const pageCopies = allCopiesData.slice(startIndex, endIndex);

	tbody.innerHTML = pageCopies.map(copy => renderCopyRow(copy)).join('');
	attachCopyEventListeners(tbody, editFormContainer);
	renderCopiesPagination(editFormContainer);
}

function renderCopiesPagination(editFormContainer) {
	const paginationContainer = editFormContainer.querySelector('#copies-pagination');
	if (!paginationContainer) return;

	const totalPages = Math.ceil(allCopiesData.length / COPIES_PER_PAGE);
	
	// Hide pagination if only one page or less
	if (totalPages <= 1) {
		paginationContainer.innerHTML = '';
		paginationContainer.classList.add('hidden');
		return;
	}

	paginationContainer.classList.remove('hidden');
	paginationContainer.innerHTML = `
		<div class="flex justify-between items-center mt-4">
			<button ${currentCopiesPage === 1 ? 'disabled' : ''} 
				class="copies-pagination-btn text-sm text-accent font-medium hover:underline ${currentCopiesPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
				data-page="${currentCopiesPage - 1}">
				Prev
			</button>
			<span class="text-sm text-gray-700">Page ${currentCopiesPage} of ${totalPages}</span>
			<button ${currentCopiesPage === totalPages ? 'disabled' : ''} 
				class="copies-pagination-btn text-sm text-accent font-medium hover:underline ${currentCopiesPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
				data-page="${currentCopiesPage + 1}">
				Next
			</button>
		</div>
	`;

	// Attach pagination listeners
	paginationContainer.querySelectorAll('.copies-pagination-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			if (this.disabled) return;
			currentCopiesPage = parseInt(this.dataset.page);
			renderCopiesPage(editFormContainer);
		});
	});
}

function renderCopyRow(copy) {
    const current = copy.status || 'available';
    const isNew = copy.id < 0;

    // Define valid transitions per current status
    const validTransitions = {
        available: ['damaged', 'lost', 'withdrawn'],
        borrowed: [], // cannot manually change any except via return or reports
        damaged: ['withdrawn', 'available'],
        lost: ['available'],
        withdrawn: [] // locked permanently
    };

    // Compute allowed options
    const allowedOptions = ['available', 'borrowed', 'damaged', 'lost', 'withdrawn'].filter(status => {
        return validTransitions[current]?.includes(status) || status === current;
    });

    // Determine if the select should be disabled
    const isDisabled = allowedOptions.length <= 1;

    // Build options dynamically
    const options = allowedOptions
        .map(status => `<option value="${status}" ${current === status ? 'selected' : ''}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`)
        .join('');

    // Tooltip message for disabled select
    const tooltipMessage = (() => {
        switch(current) {
            case 'borrowed':
                return "Cannot manually change a borrowed book. Use the return or report process.";
            case 'withdrawn':
                return "Withdrawn books are permanently removed from circulation and cannot be reactivated.";
            case 'lost':
            case 'damaged':
                return "This book's status can only be changed to withdrawn.";
            default:
                return "No other status available to select.";
        }
    })();

    const selectHtml = isDisabled 
        ? `<div class="relative inline-block w-full">
                <select class="copy-status-select px-3 py-2 text-sm border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed opacity-50 w-full" disabled>
                    ${options}
                </select>
                <div class="tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                    ${tooltipMessage}
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-white"></div>
                </div>
           </div>`
        : `<select name="copies[${copy.id}]" id="copy-status-${copy.id}"
                class="copy-status-select px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition bg-white hover:border-gray-300 cursor-pointer w-full"
                data-original="${current}"
                data-copy-id="${copy.id}"
                data-is-new="${isNew}">
                ${options}
           </select>`;

    const resetButton = isNew
        ? `<button type="button" class="remove-copy-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-md transition font-medium" data-copy-id="${copy.id}" title="Remove this copy">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1 1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Remove
           </button>`
        : `<button type="button" class="reset-copy-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-md transition font-medium" data-copy-id="${copy.id}" title="Reset to original status">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
           </button>`;

    return `
        <tr class="hover:bg-gray-50 transition-colors ${isNew ? 'bg-green-50/30' : ''}" data-copy-id="${copy.id}">
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-accent/10 to-teal-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-bold text-accent">#${copy.copy_number}</span>
                    </div>
                    <span class="text-gray-800 font-medium">Copy ${copy.copy_number}</span>
                    ${isNew ? '<span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">NEW</span>' : ''}
                </div>
            </td>
            <td class="px-4 py-3 current-status-badge" data-copy-id="${copy.id}">${renderStatusBadge(current)}</td>
            <td class="px-4 py-3">
                ${selectHtml}
            </td>
            <td class="px-4 py-3 text-center">
                ${resetButton}
            </td>
        </tr>
    `;
}



function attachCopyEventListeners(tbody, editFormContainer) {
	// Status change listeners
	tbody.querySelectorAll('.copy-status-select').forEach(select => {
		select.addEventListener('change', function() {
			const copyId = this.dataset.copyId;
			const newStatus = this.value;
			const originalStatus = this.dataset.original;
			
			// Update in allCopiesData
			const copyIndex = allCopiesData.findIndex(c => String(c.id) === String(copyId));
			if (copyIndex !== -1) {
				allCopiesData[copyIndex].status = newStatus;
			}
			
			// Update the current status badge
			const statusBadge = tbody.querySelector(`.current-status-badge[data-copy-id="${copyId}"]`);
			if (statusBadge) {
				statusBadge.innerHTML = renderStatusBadge(newStatus);
			}
			
			// Highlight changed selects
			const isChanged = newStatus !== originalStatus;
			if (isChanged) {
				this.classList.add('border-accent', 'bg-accent/5');
				this.classList.remove('border-gray-200');
			} else {
				this.classList.remove('border-accent', 'bg-accent/5');
				this.classList.add('border-gray-200');
			}
			
			updateStatsAfterChange(tbody, editFormContainer);
		});
	});

	// Reset button listeners
	tbody.querySelectorAll('.reset-copy-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			const copyId = this.dataset.copyId;
			const select = tbody.querySelector(`#copy-status-${copyId}`);
			
			if (select) {
				const originalStatus = select.dataset.original;
				select.value = originalStatus;
				
				// Update in allCopiesData
				const copyIndex = allCopiesData.findIndex(c => String(c.id) === String(copyId));
				if (copyIndex !== -1) {
					allCopiesData[copyIndex].status = originalStatus;
				}
				
				const statusBadge = tbody.querySelector(`.current-status-badge[data-copy-id="${copyId}"]`);
				if (statusBadge) {
					statusBadge.innerHTML = renderStatusBadge(originalStatus);
				}
				
				select.classList.remove('border-accent', 'bg-accent/5');
				select.classList.add('border-gray-200');
				
				updateStatsAfterChange(tbody, editFormContainer);
			}
		});
	});

	// Remove button listeners (for new copies)
	tbody.querySelectorAll('.remove-copy-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			const copyId = this.dataset.copyId;
			
			// Remove from allCopiesData
			allCopiesData = allCopiesData.filter(c => String(c.id) !== String(copyId));
			
			// Calculate new total pages after removal
			const totalPages = Math.ceil(allCopiesData.length / COPIES_PER_PAGE);
			
			// If current page is now beyond total pages, go to last page
			if (currentCopiesPage > totalPages) {
				currentCopiesPage = Math.max(1, totalPages);
			}
			
			// Re-render current (or adjusted) page
			renderCopiesPage(editFormContainer);
			updateStatsAfterChange(tbody, editFormContainer);
		});
	});
}

export function addNewCopy(editFormContainer) {
	const tbody = editFormContainer.querySelector('#copies-table-body');
	if (!tbody) return;

	// Get highest copy number from all copies
	const copyNumbers = allCopiesData.map(c => c.copy_number);
	const nextCopyNumber = Math.max(0, ...copyNumbers) + 1;

	const newCopy = {
		id: nextNewCopyId--,
		copy_number: nextCopyNumber,
		status: 'available'
	};

	// Add to data array
	allCopiesData.push(newCopy);
	
	// Go to last page to show new copy
	const totalPages = Math.ceil(allCopiesData.length / COPIES_PER_PAGE);
	currentCopiesPage = totalPages;
	
	// Re-render
	renderCopiesPage(editFormContainer);
	updateStatsAfterChange(tbody, editFormContainer);
}

export function updateStatsAfterChange(tbody, editFormContainer) {
	if (!tbody) return;
	
	const counts = { available: 0, borrowed: 0, damaged: 0, lost: 0, withdrawn: 0 };
	let total = 0;
	
	// Count from all data, not just current page
	allCopiesData.forEach(copy => {
		const status = copy.status;
		if (counts[status] !== undefined) {
			counts[status] += 1;
		}
		total += 1;
	});
	
	const setText = (id, val) => {
		const el = editFormContainer.querySelector('#' + id);
		if (el) el.textContent = val;
	};
	
	setText('stat-available', counts.available);
	setText('stat-borrowed', counts.borrowed);
	setText('stat-damaged', counts.damaged);
	setText('stat-lost', counts.lost);
	setText('stat-total', total);
}

/**
 * Populate summary header
 */
export function populateSummaryHeader(book, editFormContainer) {
	const sTitle = editFormContainer.querySelector('#edit-summary-title');
	const sIsbn = editFormContainer.querySelector('#edit-summary-isbn');
	const sAuthor = editFormContainer.querySelector('#edit-summary-author');
	const sCover = editFormContainer.querySelector('#edit-summary-cover');
	const sCoverPlaceholder = editFormContainer.querySelector('#edit-summary-cover-placeholder');

	if (sTitle) sTitle.textContent = book.title || '—';
	if (sIsbn) sIsbn.textContent = book.isbn || 'N/A';
	if (sAuthor) {
		const parts = [book.author?.firstname, book.author?.middle_initial ? book.author.middle_initial + '.' : '', book.author?.lastname];
		sAuthor.textContent = parts.filter(Boolean).join(' ') || 'N/A';
	}
	
	if (sCover && sCoverPlaceholder) {
		if (book.cover_image) {
			sCover.src = `/storage/${book.cover_image}`;
			sCover.classList.remove('hidden');
			sCoverPlaceholder.classList.add('hidden');
		} else {
			sCover.src = '';
			sCover.classList.add('hidden');
			sCoverPlaceholder.classList.remove('hidden');
		}
	}
}
