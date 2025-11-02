<x-layout>
    <!-- Top Stats -->
    <div class="px-12 min-h-screen bg-background">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 py-4 px-20">
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
                    <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">189</span>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                        <input type="text" placeholder="Search by name or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option>Sort: Alphabetical</option>
                        <option>Sort: Newest</option>
                        <option>Sort: Oldest</option>
                    </select>
                    <select class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option>Status: All</option>
                        <option>Active</option>
                        <option>Penalty</option>
                        <option>Cleared</option>
                    </select>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower Name</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">ID Number</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Role</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Dept/Year</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrowed</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Penalties</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Reservations</th>

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)

                        <tr type="button" data-user-id="{{ $user->id }}" class="open-borrower-modal hover:bg-accent/5 transition-colors cursor-pointer active:hover:bg-accent/10 active:scale-[0.99]">
                            <td class="py-3 px-4 text-gray-600 font-medium">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-800 whitespace-nowrap">{{ $user->getFullnameAttribute() }}</span>
                                </div>
                            </td>

                            <td class="py-3 px-4 text-gray-600 font-mono text-xs">
                                @if($user->role === 'teacher')
                                {{ $user->teachers->employee_number }}
                                @elseif($user->role === 'student')
                                {{ $user->students->student_number }}
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->role === 'student' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 whitespace-nowrap text-xs">
                                @if($user->role === 'teacher')
                                {{ $user->teachers->department->name }}
                                @elseif($user->role === 'student')
                                {{ $user->students->department->name }} - {{ $user->students->year_level }}
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                                    {{$user->library_status}}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-gray-700 font-medium text-xs">3 books</span>
                            </td>
                            <td class="py-3 px-4 text-gray-500 text-xs">—</td>
                            <td class="py-3 px-4 text-gray-500 text-xs">None</td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-800">1-3</span> of <span class="font-semibold text-gray-800">189</span> borrowers
                </p>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Previous
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-accent border border-accent rounded-lg shadow-sm">
                        1
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        2
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        3
                    </button>
                    <span class="px-2 text-gray-500">...</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        19
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('modals.borrowers');
</x-layout>

@vite('resources/js/pages/staff/borrowers.js')
@vite('resources/js/pages/staff/borrowBook.js')
