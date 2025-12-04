{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\books-list.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/currently-borrowed-white.svg') }}" alt="Book Catalog Icon" class="w-8 h-8">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Borrowing Records</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Review the complete history of all book borrowing and return transactions.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Records (Filtered)</span>
                    <span id="header-total-borrow-records" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $totalBorrowRecords }}</span>
                </div>
            </div>
        </div>

        {{-- <!-- Main Content --2 --}}
        <div class="bg-white shadow-sm rounded-xl p-6">
            {{-- Improved Table Controls Layout --}}
            <div class="flex flex-col gap-3 mb-6">
                {{-- Top Row: Search (left) and Sort (right) --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                            <input id="borrow-records-search" type="text" placeholder="Search by Borrower Name, Book Title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <select id="borrow-records-sort-filter" name="sort" class="cursor-pointer w-full sm:w-auto px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                            <option value="" {{ request('sort') === '' ? 'selected' : '' }}>Sort By: Priority (Overdue First)</option>
                            <option value="due_asc" {{ request('sort') === 'due_asc' ? 'selected' : '' }}>Sort By: Due Date (Asc)</option>
                            <option value="due_desc" {{ request('sort') === 'due_desc' ? 'selected' : '' }}>Sort By: Due Date (Desc)</option>
                            <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Sort By: Book Title (A-Z)</option>
                            <option value="borrower_asc" {{ request('sort') === 'borrower_asc' ? 'selected' : '' }}>Sort By: Borrower Name (A-Z)</option>
                        </select>
                    </div>
                </div>
                {{-- Second Row: Filters --}}
                <div class="flex flex-wrap gap-3">
                    {{-- Role Filter --}}
                    <select id="borrow-records-role-filter" name="role" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Role: All</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
                    </select>
                    {{-- Status Filter --}}
                    <select id="borrow-records-status-filter" name="status" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Status: All</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Status: Overdue</option>
                        <option value="due_soon" {{ request('status') === 'due_soon' ? 'selected' : '' }}>Status: Due Soon</option>
                        <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Status: Borrowed (On Time)</option>
                        <option value="returned_late" {{ request('status') === 'returned_late' ? 'selected' : '' }}>Status: Returned Late</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Status: Returned (On Time)</option>
                    </select>
                    {{-- Semester Filter --}}
                    <select id="borrow-records-semester-filter" name="semester" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Semester: All</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ request('semester') == $semester->id ? 'selected' : '' }} {{ $semester->id == $activeSemesterId ? 'selected' : '' }}>
                            {{ $semester->name }}{{ $semester->id == $activeSemesterId ? ' (active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    {{-- Reset Button --}}
                    <button type="button" id="reset-borrow-records-filters" class="ml-auto cursor-pointer px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                        Reset Filters
                    </button>
                </div>
            </div>
            <div id="borrow-records-container">
                @include('partials.librarian.circulation-records.borrowing-records-table', ['borrowTransactions' => $borrowTransactions])
            </div>

            @include('modals.librarian.borrow-record-details')
        </div>

        @vite('resources/js/pages/librarian/utils/popupDetailsModal.js')
        @vite('resources/js/pages/librarian/borrowingRecords/borrowRecordDetails.js')
        @vite('resources/js/ajax/librarianSectionsHandler.js')
        @vite('resources/js/pages/librarian/borrowingRecords/borrowRecordsTableControls.js')
    </section>
</x-layout>
