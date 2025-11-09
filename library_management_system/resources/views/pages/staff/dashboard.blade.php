<x-layout>
    <div class="px-12 py-6 min-h-screen bg-background">
        <!-- Top Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-4 px-20">
            {{-- Total Books --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Books</p>
                    <div class="bg-blue-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/total-books.svg') }}" alt="Total Books" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">1,128</p>
            </div>

            {{-- Active Borrowers --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Active Borrowers</p>
                    <div class="bg-green-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/active-borrowers.svg') }}" alt="Active Borrowers" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">256</p>
            </div>

            {{-- Books Borrowed --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Books Borrowed</p>
                    <div class="bg-orange-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/books-borrowed.svg') }}" alt="Books Borrowed" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">342</p>
            </div>

            {{-- Total Fines --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Fines</p>
                    <div class="bg-red-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/total-fines.svg') }}" alt="Total Fines" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-red-600">₱4,215<span class="text-xl">.50</span></p>
            </div>
        </div>

        <!-- Borrowers Table Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('build/assets/icons/borrowers-black.svg') }}" alt="Borrowers" class="w-8 h-8">
                    <h2 class="text-xl font-semibold text-gray-800">Borrowers</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Members</span>
                    <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $users->total() }}</span>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                        <input type="text" placeholder="Search by name or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select name="role" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Role: All</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
                    </select>
                    <select name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Status: All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Status: Active</option>
                        <option value="penalty" {{ request('status') === 'penalty' ? 'selected' : '' }}>Status: Penalty</option>
                        <option value="cleared" {{ request('status') === 'cleared' ? 'selected' : '' }}>Status: Cleared</option>
                    </select>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div id="members-table-container">
                @include('partials.staff.members-table', ['users' => $users])
            </div>
        </div>
    </div>

    @include('modals.confirm-borrow')
    @include('modals.borrower-profile')
</x-layout>

@vite('resources/js/pages/staff/borrowers.js')
@vite('resources/js/pages/staff/borrowBook.js')
@vite('resources/js/pages/staff/confirmBorrow.js')
@vite('resources/js/pages/staff/borrowersPagination.js')
