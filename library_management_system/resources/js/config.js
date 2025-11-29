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
    AVAILABLE_BOOKS: (params) => `/staff/books/available?${params}`,
    BOOK_SELECTION: (transaction_type, member_id, params) => `/staff/books/selection/${transaction_type}/${member_id}?${params}`,
};


export const SEMESTER_ROUTES = {
    INDEX: '/librarian/semester-management',
    CREATE: '/librarian/semester-management/create',
    STORE: '/librarian/semester-management',
    EDIT: (id) => `/librarian/semester-management/${id}/edit`,
    UPDATE: (id) => `/librarian/semester-management/${id}`,
    ACTIVATE: (id) => `/librarian/semester-management/${id}/activate`,
    DEACTIVATE: (id) => `/librarian/semester-management/${id}/deactivate`,
    
    CHECK_ACTIVE: '/staff/check-active-semester',
};

export const TRANSACTION_ROUTES = {
    INDEX: '/staff/transaction',

    VALIDATE_BORROW: '/staff/transaction/borrow/validate',
    PERFORM_BORROW: '/staff/transaction/borrow/perform',

    VALIDATE_RETURN: '/staff/transaction/return/validate',
    PERFORM_RETURN: '/staff/transaction/return/perform',

    VALIDATE_RENEWAL: '/staff/transaction/renewal/validate',
    PERFORM_RENEWAL: '/staff/transaction/renewal/perform',

    VALIDATE_RESERVATION: '/staff/transaction/reservation/validate',
    PERFORM_RESERVATION: '/staff/transaction/reservation/perform',
    AVAILABLE_COPIES_FOR_RESERVATION: (userId, bookId) => `/staff/transaction/reservation/${userId}/book/${bookId}/available-copies`,
    CANCEL_RESERVATION: (reservationId) => `/staff/transaction/reservation/${reservationId}/cancel`,

    UPDATE_PENALTY: (id) => `/staff/transaction/penalty/${id}`,
    CANCEL_PENALTY: (penaltyId, borrowerId) =>  `/staff/transaction/${borrowerId}/penalty/${penaltyId}/cancel`,
}


export const INVALID_INPUT = 422;      // Input format or missing fields
export const AUTHORIZATION_ERROR = 403;   // User not allowed to perform action
export const BUSINESS_RULE_VIOLATION = 400;   // Request violates a business rule
export const NOT_FOUND = 404;   // Request violates a business rule

export const SETTINGS_ROUTES = {
    FETCH: '/settings',
    UPDATE: '/librarian/settings',
};