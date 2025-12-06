export const BASE_URL = "https://library_management_system.test/";

export const API_ROUTES ={
    LOGIN: BASE_URL + 'login',
    SIGNUP: BASE_URL + 'signup',
    LOGOUT: BASE_URL + 'logout',

    GET_CATEGORIES: BASE_URL + 'librarian/categories',
    ADD_CATEGORY: BASE_URL + 'librarian/category-management',
    UPDATE_CATEGORY: BASE_URL + 'librarian/category-management',
    DELETE_CATEGORY: BASE_URL + 'librarian/category-management', 

    GET_GENRES: BASE_URL + 'librarian/category-management/genres',
    ADD_GENRE: BASE_URL + 'librarian/category-management/genres',
    UPDATE_GENRE: BASE_URL + 'librarian/category-management/genres',
    DELETE_GENRE: BASE_URL + 'librarian/category-management/genres',

    // Add other API routes as needed
};

export const BOOK_ROUTES = {
    INDEX: '/librarian/books/index',
    CREATE: '/librarian/books/create',
    STORE: '/librarian/books/store',
    EDIT: (id) => `/librarian/books/${id}/edit`,
    UPDATE: (id) => `/librarian/books/${id}`,
};


export const SEMESTER_ROUTES = {
    INDEX: '/librarian/semester-management',
    CREATE: '/librarian/semester-management/create',
    STORE: '/librarian/semester-management',
    EDIT: (id) => `/librarian/semester-management/${id}/edit`,
    UPDATE: (id) => `/librarian/semester-management/${id}`,
    ACTIVATE: (id) => `/librarian/semester-management/${id}/activate`,
    DEACTIVATE: (id) => `/librarian/semester-management/${id}/deactivate`,

};

export const STAFF_DASHBOARD_ROUTES = {
    MEMBERS: '/staff/dashboard/members',
    ACTIVE_BORROWS: '/staff/dashboard/active-borrows',
    UNPAID_PENALTIES: '/staff/dashboard/unpaid-penalties',
    QUEUE_RESERVATIONS: '/staff/dashboard/queue-reservations',
};

export const LIBRARIAN_SECTION_ROUTES = {
    BORROW_RECORDS: '/librarian/section/borrowing-records',
    RESERVATION_RECORDS: '/librarian/section/reservation-records',
    PENALTY_RECORDS: '/librarian/section/penalty-records'
};

export const TRANSACTION_ROUTES = {
    INDEX: '/staff/transaction',

    VALIDATE_BORROW: '/transaction/borrow/validate',
    PERFORM_BORROW: '/transaction/borrow/perform',
    
    VALIDATE_RETURN: '/transaction/return/validate',
    PERFORM_RETURN: '/transaction/return/perform',

    VALIDATE_RENEWAL: '/transaction/renewal/validate',
    PERFORM_RENEWAL: '/transaction/renewal/perform',

    VALIDATE_RESERVATION: '/transaction/reservation/validate',
    PERFORM_RESERVATION: '/transaction/reservation/perform',
   
    // AVAILABLE_COPIES_FOR_RESERVATION: (memberId, bookId) => `/staff/transaction/${memberId}/book/${bookId}/available-copies`,
    CANCEL_RESERVATION: (reservationId) => `/transaction/reservation/${reservationId}/cancel`,

    PROCESS_PENALTY: (id) => `/transaction/penalty/${id}`,
    CANCEL_PENALTY: (penaltyId, borrowerId) =>  `/transaction/${borrowerId}/penalty/${penaltyId}/cancel`,

    // Helpers for fetching books
    FETCH_BORROWER: (borrowerId) => `/transaction/borrower/${borrowerId}`,
    AVAILABLE_BOOKS: (params) => `/transaction/books/available?${params}`,
    BOOK_SELECTION: (transaction_type, member_id, params) => `/transaction/books/selection/${transaction_type}/${member_id}?${params}`,
    CHECK_ACTIVE: '/transaction/check-active-semester',
}

export const CLEARANCE_ROUTES = {
    VALIDATE_CLEARANCE_REQUEST: (targetUserId, requestorId) => `/transaction/clearance/${targetUserId}/validate-request/${requestorId}`,
    PERFORM_CLEARANCE_REQUEST: (targetUserId, requestorId) => `/transaction/clearance/${targetUserId}/perform-request/${requestorId}`,
    
    MARK_AS_CLEARED: (userId) => `/transaction/clearance/${userId}/mark-as-cleared`,
};


export const INVALID_INPUT = 422;      // Input format or missing fields
export const AUTHORIZATION_ERROR = 403;   // User not allowed to perform action
export const BUSINESS_RULE_VIOLATION = 400;   // Request violates a business rule
export const NOT_FOUND = 404;   // Request violates a business rule

export const SETTINGS_ROUTES = {
    FETCH: '/settings',
    UPDATE: '/librarian/settings',
};