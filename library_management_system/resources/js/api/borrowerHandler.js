import { showError } from "../utils.js";

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

            return data.user;
        } catch (error) {
            showError('Network Error', 'Unable to load borrower details. Please try again later.');
        }
}