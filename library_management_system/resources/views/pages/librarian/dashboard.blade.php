<x-layout :title="'Librarian Dashboard'">
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        {{-- Header --}}
        <div class="bg-white shadow-sm rounded-xl p-5 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg version="1.0" class="w-9 h-9" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#ffffff">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g>
                                    <path fill="#ffffff" d="M40,64V4c0-2.211-1.789-4-4-4h-8c-2.211,0-4,1.789-4,4v60H40z"></path>
                                    <path fill="#ffffff" d="M20,64V20c0-2.211-1.789-4-4-4H8c-2.211,0-4,1.789-4,4v44H20z"></path>
                                    <path fill="#ffffff" d="M60,36c0-2.211-1.789-4-4-4h-8c-2.211,0-4,1.789-4,4v28h16V36z"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Welcome back, <span class="font-semibold">{{ auth()->user()->firstname }}</span> • {{ now()->format('F d, Y') }}</p>
                    </div>
                </div>

                {{-- Active Semester Display --}}

                <div class="flex flex-col items-end px-5 py-3">
                    <p class="block w-fit text-xs text-gray-500 font-medium uppercase tracking-wide text-right">
                        Active Semester
                    </p>
                    <p class="text-sm font-bold text-accent text-right">
                        {{ isset($activeSemester) ? $activeSemester->name : 'No active semester' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- Total Books --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Books</p>
                    <div class="bg-blue-50 p-2 rounded-lg">
                        {{-- Assuming 'total-books.svg' exists in your assets folder --}}
                        <img src="{{ asset('build/assets/icons/total-books.svg') }}" alt="Total Books" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($totalBooks) }}</p>
                <div class="w-full h-1.5 bg-blue-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: 100%"></div>
                </div>
            </div>

            {{-- Active Borrowers --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Active Borrowers</p>
                    <div class="bg-green-50 p-2 rounded-lg">
                        {{-- Assuming 'active-borrowers.svg' exists in your assets folder --}}
                        <img src="{{ asset('build/assets/icons/active-borrowers.svg') }}" alt="Active Borrowers" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($totalActiveBorrowers) }}</p>
                <div class="w-full h-1.5 bg-green-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: {{ $totalActiveBorrowers > 0 ? min(($totalActiveBorrowers / 100) * 100, 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Books Borrowed --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Books Borrowed</p>
                    <div class="bg-orange-50 p-2 rounded-lg">
                        <svg class="w-5 h-5" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.004 512.004" xml:space="preserve" fill="#000000">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g>
                                    <path style="fill:#EDEBDA;" d="M256,393.144L256,393.144c-72.037-28.809-149.294-39.643-219.429-9.143h-9.143V45.716h9.143 c68.27-24.384,151.159-24.384,219.429,0V393.144z"></path>
                                    <g>
                                        <path style="fill:#CEC9AE;" d="M475.429,45.716c-71.141-24.384-148.663-24.384-219.429,0v347.429 c24.302-9.28,48.859-16.64,73.143-21.403V261.972V29.46c24.283-2.624,48.814-2.615,73.143,0.027V256.44V366.21 c25.582,1.518,50.213,7.067,73.143,17.792h8.997V45.716H475.429z"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,118.859h-82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h82.286c5.047,0,9.143,4.087,9.143,9.143C210.286,114.772,206.19,118.859,201.143,118.859"></path>
                                        <path style="fill:#CEC9AE;" d="M82.286,118.859c-2.469,0-4.754-0.923-6.491-2.651c-1.737-1.737-2.651-4.023-2.651-6.491 c0-2.377,1.006-4.763,2.651-6.491c3.474-3.383,9.509-3.383,12.983,0c1.646,1.728,2.651,4.114,2.651,6.491 c0,1.189-0.183,2.377-0.731,3.474c-0.457,1.097-1.097,2.094-1.92,3.017c-0.914,0.823-1.92,1.463-3.017,1.92 C84.663,118.584,83.474,118.859,82.286,118.859"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,164.573H82.286c-5.047,0-9.143-4.087-9.143-9.143s4.096-9.143,9.143-9.143h118.857 c5.047,0,9.143,4.087,9.143,9.143S206.19,164.573,201.143,164.573"></path>
                                        <path style="fill:#CEC9AE;" d="M164.571,210.287H82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143h82.286 c5.047,0,9.143,4.087,9.143,9.143C173.714,206.2,169.618,210.287,164.571,210.287"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,210.287c-2.469,0-4.754-0.923-6.491-2.651c-0.823-0.923-1.463-1.92-1.92-3.017 c-0.457-1.097-0.731-2.286-0.731-3.474c0-2.377,1.006-4.763,2.651-6.491c3.383-3.383,9.509-3.383,12.983,0 c1.646,1.728,2.651,4.114,2.651,6.491c0,1.189-0.274,2.377-0.731,3.474c-0.457,1.097-1.097,2.094-1.92,3.017 c-0.914,0.823-1.92,1.463-3.017,1.92C203.52,210.013,202.331,210.287,201.143,210.287"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,256.002h-82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h82.286c5.047,0,9.143,4.087,9.143,9.143C210.286,251.915,206.19,256.002,201.143,256.002"></path>
                                        <path style="fill:#CEC9AE;" d="M82.286,256.002c-1.189,0-2.377-0.274-3.474-0.731c-1.097-0.457-2.103-1.097-3.017-1.92 c-1.737-1.737-2.651-4.023-2.651-6.491c0-1.189,0.274-2.377,0.731-3.474c0.457-1.189,1.097-2.103,1.92-3.017 c2.56-2.469,6.583-3.383,9.966-1.92c1.189,0.457,2.103,1.097,3.017,1.92c0.823,0.914,1.463,1.92,1.92,3.017 c0.457,1.097,0.731,2.286,0.731,3.474c0,2.469-0.914,4.754-2.651,6.491C87.04,255.078,84.754,256.002,82.286,256.002"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,301.716H82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h118.857c5.047,0,9.143,4.087,9.143,9.143C210.286,297.629,206.19,301.716,201.143,301.716"></path>
                                    </g>
                                    <g>
                                        <path style="fill:#EDEBDA;" d="M329.143,164.573h-18.286c-5.047,0-9.143-4.087-9.143-9.143s4.096-9.143,9.143-9.143h18.286 c5.047,0,9.143,4.087,9.143,9.143S334.19,164.573,329.143,164.573"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,164.573c-2.478,0-4.763-0.923-6.491-2.651c-1.737-1.737-2.651-4.023-2.651-6.491 c0-2.377,0.997-4.763,2.651-6.491c3.383-3.383,9.6-3.383,12.891,0c1.737,1.728,2.743,4.114,2.743,6.491 c0,1.189-0.274,2.377-0.731,3.474s-1.097,2.094-2.011,3.017C443.611,163.65,441.326,164.573,438.857,164.573"></path>
                                        <path style="fill:#EDEBDA;" d="M329.143,118.859h-18.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h18.286c5.047,0,9.143,4.087,9.143,9.143C338.286,114.772,334.19,118.859,329.143,118.859"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,118.859h-36.571c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h36.571c5.056,0,9.143,4.087,9.143,9.143C448,114.772,443.913,118.859,438.857,118.859"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,301.716h-36.571c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h36.571c5.056,0,9.143,4.087,9.143,9.143C448,297.629,443.913,301.716,438.857,301.716"></path>
                                        <path style="fill:#31087A;" d="M310.857,301.716c-2.469,0-4.754-0.923-6.491-2.651c-1.737-1.737-2.651-4.023-2.651-6.491 c0-1.189,0.274-2.377,0.731-3.474c0.457-1.189,1.097-2.103,1.92-3.017c0.914-0.823,1.92-1.463,3.017-1.92 c2.194-1.006,4.754-1.006,6.949,0c1.097,0.457,2.103,1.097,3.017,1.92c0.823,0.914,1.463,1.829,1.92,3.017 c0.549,1.097,0.731,2.286,0.731,3.474c0,2.377-0.914,4.754-2.651,6.491C315.611,300.792,313.326,301.716,310.857,301.716"></path>
                                        <path style="fill:#EDEBDA;" d="M329.143,256.002h-18.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h18.286c5.047,0,9.143,4.087,9.143,9.143C338.286,251.915,334.19,256.002,329.143,256.002"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,256.002c-1.189,0-2.377-0.274-3.474-0.731c-1.097-0.457-2.103-1.097-3.017-1.92 c-1.737-1.737-2.651-4.023-2.651-6.491c0-1.189,0.183-2.377,0.731-3.474c0.457-1.097,1.097-2.103,1.92-3.017 c3.383-3.383,9.6-3.383,12.983,0c0.823,0.914,1.463,1.92,1.92,3.017c0.457,1.097,0.731,2.286,0.731,3.474 c0,2.377-0.923,4.754-2.651,6.491C443.611,255.078,441.326,256.002,438.857,256.002"></path>
                                        <path style="fill:#EDEBDA;" d="M329.143,210.287h-18.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h18.286c5.047,0,9.143,4.087,9.143,9.143C338.286,206.2,334.19,210.287,329.143,210.287"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,210.287h-36.571c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h36.571c5.056,0,9.143,4.087,9.143,9.143C448,206.2,443.913,210.287,438.857,210.287"></path>
                                    </g>
                                    <g>
                                        <path style="fill:#f97316;" d="M329.143,371.74c-24.283,4.763-48.841,12.123-73.143,21.403 c-72.037-28.809-149.294-39.643-219.429-9.143h-9.143V45.715H0v356.571h329.143V371.74z"></path>
                                        <path style="fill:#f97316;" d="M484.429,45.716v338.286h-8.997c-22.93-10.734-47.561-16.274-73.143-17.792v36.078h109.714V45.716 H484.429z"></path>
                                    </g>
                                    <g>
                                        <path style="fill:#f97316;" d="M0,429.716h329.143v-27.429H0V429.716z"></path>
                                        <path style="fill:#f97316;" d="M402.286,429.716H512v-27.429H402.286V429.716z"></path>
                                    </g>
                                    <path style="fill:#DD342E;" d="M329.143,29.463v232.503v109.778v112.832l36.571-27.429l36.571,27.429V366.203V256.434V29.49 C377.957,26.839,353.426,26.829,329.143,29.463"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($totalBorrowedCopies) }}</p>
                <div class="w-full h-1.5 bg-orange-100 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full transition-all duration-500" style="width: {{ $totalCopies > 0 ? min(($totalBorrowedCopies / $totalCopies) * 100, 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Total Fines --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Fines</p>
                    <div class="bg-red-50 p-2 rounded-lg">
                        {{-- Assuming 'total-fines.svg' exists in your assets folder --}}
                        <img src="{{ asset('build/assets/icons/total-fines.svg') }}" alt="Total Fines" class="w-5 h-5">
                    </div>
                </div>
                {{-- Displaying fines in red and formatting the currency (₱) and decimals --}}
                <p class="text-3xl font-bold text-red-600 mb-3">
                    ₱{{ number_format((int) $totalUnpaidFines, 0) }}
                    <span class="text-xl">.{{ str_pad(round(($totalUnpaidFines - floor($totalUnpaidFines)) * 100), 2, '0', STR_PAD_LEFT) }}</span>
                </p>
                <div class="w-full h-1.5 bg-red-100 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ $totalUnpaidFines > 0 ? min(($totalUnpaidFines / 10000) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Quick Actions & Most Borrowed --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-5">
                    <div class="space-y-3">

                        {{-- Add New Book Quick Action --}}
                        <a href="{{ route('librarian.books.create') }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Add New Book</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('librarian.section.borrowing-records', ['status' => 'borrowed']) }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Process Return</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('librarian.books.index', ['status' => 'pending_issue_review']) }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center group-hover:bg-orange-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Review Book Issues</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($pendingBookReviews > 0)
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">
                                    {{ $pendingBookReviews }}
                                </span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('librarian.section.clearance-management', ['status' => 'pending']) }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center group-hover:bg-yellow-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Process Clearance</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($pendingClearanceRequests > 0)
                                <span class="px-2 py-0.5 bg-yellow-200 text-yellow-600 text-xs font-bold rounded-full">
                                    {{ $pendingClearanceRequests }}
                                </span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('librarian.section.reservation-records', ['status' => 'ready_for_pickup']) }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Check Out Reservations</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($readyForPickupReservations > 0)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                    {{ $readyForPickupReservations }}
                                </span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('librarian.section.penalty-records', ['status' => 'unpaid']) }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Process / Settle Penalties</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($pendingPenaltyPayments > 0)
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                                    {{ $pendingPenaltyPayments }}
                                </span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>


            {{-- Quick Actions End --}}

            {{-- Most Borrowed --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        Top 4 Most Borrowed Books
                    </h2>
                </div>
                <div class="p-5">
                    @if($topBooks->count() > 0)
                    <div class="space-y-2">
                        @foreach($topBooks as $book)
                        <a href="{{ route('librarian.section.borrowing-records', ['search' => $book->title, 'semester' => 'all']) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $book->title }}" class="w-12 h-16 rounded object-cover shadow-sm border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate" title="{{ $book->title }}">{{ $book->title }}</p>
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $book->author->formal_name ?? 'Unknown Author' }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-700">
                                        {{ $book->genre->category->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs font-semibold text-accent">{{ $book->borrow_transactions_count }} borrows</span>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="text-sm text-gray-500">No borrow records yet</p>
                    </div>
                    @endif

                    <a href="{{ route('librarian.books.index') }}" class="w-full mt-4 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        View All Books
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>


        {{-- Recent Activity & Due Today --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Recent Activity --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        Recent Activity
                    </h2>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex-1">
                        @if($recentActivities->count() > 0)
                        <div class="relative">
                            {{-- Timeline Line --}}
                            <div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-accent via-accent/50 to-transparent"></div>

                            @foreach($recentActivities as $activity)
                            <div class="flex gap-4 relative">
                                {{-- Timeline Dot --}}
                                <div class="relative z-10 flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-white border-2 border-accent flex items-center justify-center shadow-sm">
                                        <div class="w-2 h-2 rounded-full bg-accent"></div>
                                    </div>
                                </div>

                                {{-- Activity Content --}}
                                <div class="flex-1 pb-4 min-w-0" title="{{ $activity->details}}">
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <h4 class="text-sm font-bold text-gray-900 mb-1">{{ $activity->action }}</h4>
                                        <p class="text-xs text-gray-600 leading-relaxed mb-2 line-clamp-1">{!! $activity->details !!}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">{{ $activity->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">No recent activity</p>
                            <p class="text-xs text-gray-400 mt-1">Activity will appear here as actions are performed</p>
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('librarian.section.activity-logs') }}" class="w-full mt-5 py-3 border-2 border-gray-200 text-gray-700 hover:border-accent hover:text-accent hover:bg-accent/5 rounded-lg text-sm font-semibold transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View All Activity
                    </a>
                </div>
            </div>

            {{-- Due Today --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        Due Soon
                    </h2>
                    <span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full">{{ $dueSoon->count() }} {{ Str::plural('item', $dueSoon->count()) }}</span>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        @if($dueSoon->count() > 0)
                        <div class="space-y-2">
                            @foreach($dueSoon as $transaction)
                            <a href="{{ route('librarian.section.borrowing-records', ['search' => $transaction->user->fullname, 'filters' => 'due_soon']) }}">
                                @php
                                $dueDate = \Carbon\Carbon::parse($transaction->due_at);
                                $today = \Carbon\Carbon::today();
                                $daysUntilDue = $today->diffInDays($dueDate, false);

                                if ($daysUntilDue < 0) { $dueLabel='Overdue' ; $dueLabelClass='text-red-700 font-semibold' ; } elseif ($daysUntilDue==0) { $dueLabel='Due Today' ; } elseif ($daysUntilDue==1) { $dueLabel='Due in 1 day' ; } else { $dueLabel='Due in ' . $daysUntilDue . ' days' ; } @endphp <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                                        {{ strtoupper(substr($transaction->user->firstname,0,1)) }}{{ strtoupper(substr($transaction->user->lastname,0,1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-900">{{ $transaction->user->firstname }} {{ $transaction->user->lastname }}</p>
                                        <p class="text-xs text-gray-600 truncate">Book: {{ $transaction->bookCopy->book->title ?? 'Unknown Book' }}</p>
                                        <span class="inline-flex items-center text-[11px] rounded-full px-2 py-0.5 bg-orange-200 text-orange-700 border border-orange-100  font-medium mt-1 font-semibold ">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $dueLabel }}</span>
                                    </div>
                                    <img src="{{ $transaction->bookCopy->book->cover_image ? asset('storage/' . $transaction->bookCopy->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $transaction->bookCopy->book->title }}" class="w-12 h-16 rounded object-cover shadow-sm border border-gray-200 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                        </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-gray-500">No books due soon</p>
                    </div>
                    @endif
                </div>

                <a href="{{ route('librarian.section.borrowing-records', ['status' => 'due_soon']) }}" class="w-full mt-4 py-3 bg-accent hover:bg-accent/90 text-white rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
                    View All
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
        </div>

        {{-- Library Statistics --}}
        <div class="bg-white rounded-xl border border-gray-100">
            <div class="p-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                            <path fill="#03abb6" fill-rule="evenodd" d="M34.376 2.155a33.2 33.2 0 0 1 7.795-.63a2.4 2.4 0 0 1 2.305 2.305c.06 1.416.067 4.399-.63 7.795c-.41 2.003-2.819 2.546-4.147 1.217l-1.917-1.916a116 116 0 0 0-2.213 2.055c-1.891 1.801-4.263 4.182-6.264 6.559c-1.702 2.022-4.967 2.008-6.565-.23a94 94 0 0 0-3.148-4.17c-2.234 1.529-6.872 5.048-12.086 11.01q-.486.555-.976 1.138a2 2 0 0 1-3.06-2.577q.516-.61 1.024-1.193c5.59-6.392 10.587-10.152 12.993-11.784c1.683-1.141 3.891-.748 5.126.78a97 97 0 0 1 3.381 4.471a.1.1 0 0 0 .026.023a.2.2 0 0 0 .073.016a.19.19 0 0 0 .152-.06c2.136-2.537 4.626-5.032 6.564-6.879c.833-.793 1.57-1.473 2.141-1.99l-1.79-1.794c-1.329-1.328-.786-3.736 1.217-4.147m-.612 40.57c-.132-1.81-.264-5.039-.264-10.725s.132-8.914.264-10.726c.146-2.01 1.614-3.578 3.702-3.706c.664-.04 1.497-.068 2.534-.068s1.87.028 2.534.068c2.088.128 3.556 1.696 3.702 3.706c.132 1.812.264 5.04.264 10.726s-.132 8.914-.264 10.726c-.146 2.01-1.614 3.578-3.702 3.706c-.664.04-1.497.068-2.534.068s-1.87-.028-2.534-.068c-2.088-.128-3.556-1.696-3.702-3.706M1.67 43.27c-.097-.944-.17-2.304-.17-4.27s.073-3.326.169-4.27c.201-1.99 1.798-3.131 3.547-3.19A84 84 0 0 1 8 31.5c1.187 0 2.097.017 2.784.04c1.749.059 3.346 1.2 3.547 3.19c.096.944.169 2.304.169 4.27s-.073 3.326-.168 4.27c-.202 1.99-1.8 3.131-3.548 3.19c-.687.023-1.597.04-2.784.04s-2.097-.017-2.784-.04c-1.749-.059-3.346-1.2-3.547-3.19M17.5 36c0 3.411.099 5.564.213 6.917c.172 2.048 1.74 3.447 3.72 3.534c.664.029 1.506.049 2.567.049s1.903-.02 2.567-.05c1.98-.086 3.548-1.485 3.72-3.533c.114-1.353.213-3.506.213-6.917s-.099-5.564-.212-6.917c-.173-2.048-1.74-3.447-3.721-3.534c-.664-.029-1.506-.049-2.567-.049s-1.903.02-2.566.05c-1.981.086-3.55 1.485-3.722 3.533C17.6 30.436 17.5 32.589 17.5 36" clip-rule="evenodd" /></svg>
                    </div>
                    Library Statistics
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Total Copies</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCopies }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Available</p>
                        <p class="text-2xl font-bold text-green-600">{{ $totalAvailableCopies }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Reserved</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $totalReservedCopies }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Damaged</p>
                        <p class="text-2xl font-bold text-red-600">{{ $totalDamagedCopies }}</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</x-layout>
