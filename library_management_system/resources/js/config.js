export const BASE_URL = "https://library_management_system.test/";

export const API_ROUTES ={
    LOGIN: BASE_URL + 'login',
    SIGNUP: BASE_URL + 'signup',
    LOGOUT: BASE_URL + 'logout',

    ADD_CATEGORY: BASE_URL + 'app/routes/api.php/category/create',
    ADD_GENRE: BASE_URL + 'app/routes/api.php/genre/create',

    GET_CATEGORIES: BASE_URL + 'app/routes/api.php/categories',
    GET_GENRES: BASE_URL + 'app/routes/api.php/genres',

    UPDATE_CATEGORY: BASE_URL + 'app/routes/api.php/category/update',
    UPDATE_GENRE: BASE_URL + 'app/routes/api.php/genre/update',

    DELETE_CATEGORY: BASE_URL + 'app/routes/api.php/category/delete', 
    DELETE_GENRE: BASE_URL + 'app/routes/api.php/genre/delete'
    // Add other API routes as needed
};