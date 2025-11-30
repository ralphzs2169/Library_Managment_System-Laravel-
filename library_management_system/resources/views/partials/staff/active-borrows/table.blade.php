<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Copy No.</th>
                <th class="py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrowed At</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Due Date</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-20">Actions</th>
            </tr>
        </thead>

        <tbody id="active-borrows-real-table-body" class="bg-white divide-y divide-gray-100">
            @foreach ($activeBorrows as $index => $transaction)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $index + 1 + ($activeBorrows->currentPage() - 1) * $activeBorrows->perPage() }}</td>

                <td class="py-3 px-4 whitespace-nowrap text-gray-700 flex items-center gap-3">
                    <img src="{{ $transaction->bookCopy->book->cover_image ? asset('storage/' . $transaction->bookCopy->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $transaction->bookCopy->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div class="flex  flex-col">
                        <div class="font-semibold text-gray-800 truncate max-w-xs">{{ $transaction->bookCopy->book->title }}</div>
                        <div class="text-xs text-gray-500 mt-1">by {{ $transaction->bookCopy->book->author->fullname }}</div>
                    </div>
                </td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">Copy #{{ $transaction->bookCopy->copy_number }}</td>
                <td class="py-2 text-center">
                    <!-- Borrower initials icon -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($transaction->user->firstname,0,1)) }}{{ strtoupper(substr($transaction->user->lastname,0,1)) }}
                    </div>
                </td>
                <td class="py-3 pl-2 pr-4 whitespace-nowrap">
                    <div class="font-medium text-gray-700">{{ $transaction->user->full_name }}
                    </div>


                    <div class="text-gray-500 font-mono text-xs">
                        @if($transaction->user->role === 'teacher')
                        {{ $transaction->user->teachers->employee_number }}
                        @elseif($transaction->user->role === 'student')
                        {{ $transaction->user->students->student_number }}
                        @endif
                    </div>

                    <div class="text-sm text-gray-500">
                        @php
                        if ($transaction->user->role === 'student') {
                        $roleBadgeClass = 'bg-blue-100 text-blue-700 border border-blue-100';
                        $iconHTML = '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">';
                        } elseif ($transaction->user->role === 'teacher') {
                        $roleBadgeClass = 'bg-green-100 text-green-700 border border-green-100';
                        $iconHTML = '<img src="/build/assets/icons/teacher-role-badge.svg" alt="Teacher Badge" class="w-3.5 h-3.5">';
                        } else {
                        $roleBadgeClass = 'bg-gray-100 text-gray-600 border border-gray-200';
                        $iconHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" /></svg>';
                        }
                        @endphp
                        <span class="inline-flex items-center gap-1 px-1 rounded-xl text-xs {!! $roleBadgeClass !!}">
                            {!! $iconHTML !!}
                            {{ ucfirst($transaction->user->role) }}
                        </span></div>

                </td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $transaction->borrowed_at->format('M d, Y') }}</td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $transaction->due_at->format('M d, Y') }}</td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    @if ($transaction->status === 'overdue')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-200 text-red-700 border border-red-100">
                        <!-- Overdue SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $transaction->days_overdue }} day{{ $transaction->days_overdue == 1 ? '' : 's' }} overdue
                    </span>
                    @elseif ($transaction->status === 'borrowed')
                    @php
                    $isDueSoon = $transaction->days_until_due <= config('settings.notifications.reminder_days_before_due'); $badgeBg=$isDueSoon ? 'bg-orange-200 text-orange-700 border border-orange-100' : 'bg-green-200 text-green-700 border border-green-100' ; $svg=$isDueSoon ? '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' ; @endphp <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {!! $badgeBg !!}">
                        {!! $svg !!}
                        Due in {{ $transaction->days_until_due }} day{{ $transaction->days_until_due == 1 ? '' : 's' }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                            </svg>
                            {{ ucfirst($transaction->status) }}
                        </span>
                        @endif
                </td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <div class="relative inline-block text-left w-full">
                        <button type="button" class="kebab-menu-btn cursor-pointer flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-accent transition" data-transaction-id="{{ $transaction->id }}">
                            <!-- Kebab (three dots) icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="5" cy="12" r="1.5" fill="currentColor" />
                                <circle cx="12" cy="12" r="1.5" fill="currentColor" />
                                <circle cx="19" cy="12" r="1.5" fill="currentColor" />
                            </svg>
                        </button>
                        <div class="kebab-menu-dropdown absolute right-0 z-10 w-44 origin-top-right bg-secondary rounded-lg shadow-lg hidden" style="min-width:11rem;">
                            <!-- Arrow indicator -->
                            <div class="kebab-menu-arrow absolute bottom-full right-12 border-4 border-transparent border-b-gray-900"></div>
                            <ul class="py-1">
                                <li>
                                    <button class="kebab-action-btn flex items-center gap-2 px-4 py-2 w-full text-left text-xs text-white cursor-pointer hover:bg-secondary/80 transition" data-action="renew" data-member="{{ $transaction->user }}" data-transaction="{{ $transaction}}">
                                        <!-- Renew icon (same as in JS) -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Renew
                                    </button>
                                </li>
                                <li>
                                    <button class="kebab-action-btn flex items-center gap-2 px-4 py-2 w-full text-left text-xs text-white cursor-pointer hover:bg-secondary/80 transition" data-action="return" data-member="{{ $transaction->user }}" data-transaction="{{ $transaction}}">
                                        <!-- Return icon (same as in JS) -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        Return
                                    </button>
                                </li>
                                <li>
                                    <button class="kebab-action-btn flex items-center gap-2 px-4 py-2 w-full text-left text-xs text-white cursor-pointer hover:bg-secondary/80 transition" data-action="lost" data-transaction-id="{{ $transaction->id }}">
                                        <!-- Lost icon (use report/danger icon) -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Report Lost
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($activeBorrows->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $activeBorrows->firstItem() }}-{{ $activeBorrows->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $activeBorrows->total() }}</span> borrowers
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $activeBorrows->currentPage() - 1 }}" {{ $activeBorrows->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($activeBorrows->getUrlRange(max(1, $activeBorrows->currentPage() - 2), min($activeBorrows->lastPage(), $activeBorrows->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $activeBorrows->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $activeBorrows->currentPage() + 1 }}" {{ $activeBorrows->currentPage() == $activeBorrows->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
