<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/users.svg') }}" alt="Personnel Icon" class="w-8 h-8 filter brightness-0 invert">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Personnel Accounts</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage staff and librarian accounts.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button id="open-add-personnel-modal" class="cursor-pointer bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add New Personnel
                    </button>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 text-sm">Total Records (Filtered)</span>
                        <span id="header-total-borrow-records" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $totalPersonnelCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- <!-- Main Content --> --}}
        <div class="bg-white shadow-sm rounded-xl p-6">
            {{-- Improved Table Controls Layout --}}
            <div class="flex flex-col gap-3 mb-6">
                {{-- Top Row: Search (left) and Sort (right) --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                            <input id="borrow-records-search" type="text" placeholder="Search by Name or Email..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <select id="borrow-records-sort-filter" name="sort" class="cursor-pointer w-full sm:w-auto px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                            <option value="" {{ request('sort') === '' ? 'selected' : '' }}>Sort By: Default</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Sort By: Name (A-Z)</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Sort By: Name (Z-A)</option>
                            <option value="role_asc" {{ request('sort') === 'role_asc' ? 'selected' : '' }}>Sort By: Role</option>
                        </select>
                    </div>
                </div>
                {{-- Second Row: Filters --}}
                <div class="flex flex-wrap gap-3">
                    {{-- Role Filter --}}
                    <select id="borrow-records-role-filter" name="role" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Role: All</option>
                        <option value="librarian" {{ request('role') === 'librarian' ? 'selected' : '' }}>Role: Librarian</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Role: Staff</option>
                    </select>

                    {{-- Reset Button --}}
                    <button type="button" id="reset-borrow-records-filters" class="ml-auto cursor-pointer px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                        Reset Filters
                    </button>
                </div>
            </div>
            <div id="personnel-container">
                @include('partials.librarian.user-management.personnel-accounts-table', ['personnel' => $personnel])
            </div>

        </div>
    </section>

    <!-- Add Personnel Modal -->
    <div id="add-personnel-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div id="add-personnel-backdrop" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-out opacity-0 backdrop-blur-sm"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="add-personnel-panel" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all duration-300 ease-out opacity-0 scale-95 sm:my-8 sm:w-full sm:max-w-2xl">
                <!-- Modal Header -->
                <div class="bg-white px-8 pt-6 pb-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-accent/10 rounded-lg">
                                <img src="{{ asset('build/assets/icons/users.svg') }}" alt="Logo" class="w-6 h-6 filter brightness-0 invert sepia saturate-100 hue-rotate-190">
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Add New Personnel</h3>
                        </div>
                        <button type="button" class="close-modal text-gray-400 hover:text-gray-500 transition-colors">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500">Create a new account for a staff member or librarian.</p>
                </div>

                <!-- Form -->
                <div class="px-8 pb-8">
                    <form id="add-personnel-form" class="space-y-4" novalidate>
                        @csrf
                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Personal Information</label>
                            <div class="grid gap-2" style="grid-template-columns: 1fr 1fr 0.5fr;">
                                <!-- First Name -->
                                <div class="field-container flex flex-col">
                                    <input type="text" id="firstname" name="firstname" placeholder="First Name" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="firstname-error-placeholder"></div>
                                </div>
                                <!-- Last Name -->
                                <div class="field-container flex flex-col">
                                    <input type="text" id="lastname" name="lastname" placeholder="Last Name" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="lastname-error-placeholder"></div>
                                </div>
                                <!-- Middle Initial -->
                                <div class="field-container flex flex-col">
                                    <input type="text" id="middle_initial" name="middle_initial" maxlength="1" placeholder="M.I." class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="middle_initial-error-placeholder"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <!-- Email Address -->
                                <div class="field-container flex flex-col">
                                    <input type="email" id="email" name="email" placeholder="Email" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="email-error-placeholder"></div>
                                </div>
                                <!-- Contact Number -->
                                <div class="field-container flex flex-col">
                                    <input type="text" id="contact_number" name="contact_number" placeholder="Contact Number" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="contact_number-error-placeholder"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="form-section pt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Information</label>
                            <!-- Username -->
                            <div class="field-container mb-2">
                                <div class="error-placeholder" id="username-error-placeholder"></div>
                                <input type="text" id="username" name="username" placeholder="Username" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <!-- Password -->
                                <div class="field-container flex flex-col">
                                    <input type="password" id="password" name="password" placeholder="Password" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="password-error-placeholder"></div>
                                </div>
                                <!-- Confirm Password-->
                                <div class="field-container flex flex-col">
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" class="block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <div class="error-placeholder" id="password_confirmation-error-placeholder"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div class="form-section pt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role Assignment</label>
                            <div class="field-container">
                                <div class="error-placeholder" id="role-error-placeholder"></div>
                                <select id="role" name="role" class="cursor-pointer block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <option value="" disabled selected>Select Role</option>
                                    <option value="staff">Staff</option>
                                    <option value="librarian">Librarian</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" class="close-modal px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 bg-accent text-white rounded-lg hover:bg-accent-dark font-medium transition-colors shadow-sm">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/pages/librarian/newPersonnel.js')

</x-layout>
