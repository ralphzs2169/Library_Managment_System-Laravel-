export const BOOK_FILTERS = {
    page: 1,
    search: '',
    sort: 'newest',
    category: '',
    status: 'all'
}

export const BORROWER_FILTERS = {
    page: 1,
    search: '',
    role: '',
    status: ''
}

export const ACTIVE_BORROW_FILTERS = {
    page: 1,
    search: '',
    role: '',
    status: ''
}

export const UNPAID_PENALTIES_FILTERS = {
    page: 1,
    search: '',
    role: '',
    status: ''
}

export const QUEUE_RESERVATIONS_FILTERS = {
    page: 1,
    search: '',
    status: '',
    expiry: ''
}

export const SEARCH_COLUMN_INDEXES = {
    BOOK_CATALOG: [2, 3], // Title, Author, ISBN
    SEMESTER_CATALOG: [1], // Semester Name
    BORROWERS_LIST: [2, 3], // Borrower Name, ID Number
    ACTIVE_BORROWS: [1, 2, 3, 4], // Borrower Name, Book Title
}

