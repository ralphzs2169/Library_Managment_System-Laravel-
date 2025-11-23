import { showError, showWarning, showConfirmation, showToast } from "../utils.js";
import { PENALTY_ROUTES } from "../config.js";
import { VALIDATION_ERROR } from "../config.js";
import { displayInputErrors } from "../helpers.js";
import { BORROWER_FILTERS } from "../utils/tableFilters.js";
import { highlightSearchMatches } from "../tableControls.js";
import { SEARCH_COLUMN_INDEXES } from "../utils/tableFilters.js";

export async function loadBorrowers(page = BORROWER_FILTERS.page, scrollUp = true) {
    try {
        BORROWER_FILTERS.page = page; // Update current page in filters

        const searchInput = document.querySelector('#borrowers-search');
        const borrowerRoleFilter = document.querySelector('#borrower-role-filter');
        const borrowerStatusFilter = document.querySelector('#borrower-status-filter');

        if (searchInput) BORROWER_FILTERS.search = searchInput.value || '';
        if (borrowerRoleFilter) BORROWER_FILTERS.role = borrowerRoleFilter.value;
        if (borrowerStatusFilter) BORROWER_FILTERS.status = borrowerStatusFilter.value;

        const params = new URLSearchParams(BORROWER_FILTERS);

        const response = await fetch(`?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        const html = await response.text();

        if(!response.ok) {
            showError('Something went wrong', 'Unable to load members. Please try again.');
            return;
        }
            
        const container = document.querySelector('#borrowers-table-container');
        if(container) {
            container.innerHTML = html;
        }

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#borrowers-table-container', SEARCH_COLUMN_INDEXES.BORROWERS_LIST);
        }

        if( scrollUp ) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (error) {
        showError('Something went wrong',  error.message ||  'Unable to load members. Please try again.');
    }
}

export async function fetchBorrowerDetails(userId) {
     try {
            const response = await fetch(`/staff/borrower/${userId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (!response.ok) {
                showError('Something went wrong', 'Failed to load borrower details.');
                return;
           }
           
            return data.borrower;
        } catch (error) {
            showError('Network Error', 'Unable to load borrower details. Please try again later.');
        }
}

export async function updatePenalty(paymentDetails, form) {
      // Make sure validate_only is set after all other fields
        paymentDetails.set('validate_only', 1);
        const penaltyId = paymentDetails.get('penalty_id');
        
        // Add CSRF token and method spoofing for PUT request
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        paymentDetails.append('_token', csrfToken);
        paymentDetails.append('_method', 'PUT');
    
        let response = await fetch(PENALTY_ROUTES.UPDATE(penaltyId), {
            method: 'POST', // Use POST but spoof to PUT with _method
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: paymentDetails
        });
    
        let result = await response.json();
    
        if (!response.ok) {
            if (response.status === VALIDATION_ERROR) {
                // Check for invalid status (business rule violation)
                displayInputErrors(result.errors, form, false);
                return false;
            }
            showWarning('Something went wrong', 'Please try again.');
            return false;
        }
    
        // Step 2: Show confirmation
        const isConfirmed = await showConfirmation(
            'Save Changes?',
            'Are you sure you want to save changes to this book?',
            'Yes, save'
        );
        
        if (!isConfirmed) return false;
        
        paymentDetails.delete('validate_only');
    
        // Step 3: Submit update
        response = await fetch(PENALTY_ROUTES.UPDATE(penaltyId), {
            method: 'POST', // Use POST but spoof to PUT with _method
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: paymentDetails
        });
        result = await response.json();
    
        if (!response.ok) {
            showWarning('Something went wrong', 'Failed to update book. Please try again later.');
            return false;
        }
    
        if (result.status === 'invalid' || result.status === 'error') {
            showWarning('Something went wrong', result.message || 'Failed to update book. Please try again later.');
            return false;
        }
        
        showToast('Payment processed successfully!', 'success');
        loadBorrowers(undefined, false);
        return true;
        // showSuccessWithRedirect('Success', 'Book updated successfully!', window.location.href);
    }