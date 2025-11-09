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

    ADD_BOOK: BASE_URL + 'librarian/books/store',
    UPDATE_BOOK: BASE_URL + 'librarian/books/update',
    // Add other API routes as needed
};

export const SEMESTER_ROUTES = {
    CREATE: '/librarian/semester-management/create',
    STORE: '/librarian/semester-management',
    EDIT: (id) => `/librarian/semester-management/${id}/edit`,
    UPDATE: (id) => `/librarian/semester-management/${id}`,
    ACTIVATE: (id) => `/librarian/semester-management/${id}/activate`,
};

export const VALIDATION_ERROR = 422;