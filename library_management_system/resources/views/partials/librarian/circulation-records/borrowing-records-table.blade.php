<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg table-fixed">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-12">ID</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider w-70">Book</th>
                <th class="py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10"></th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Borrower</th>
                {{-- <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Borrowed At</th> --}}
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Due Date</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-36">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Actions</th>
            </tr>
        </thead>

        {{-- Skeleton Loader --}}
        <tbody id="borrowing-records-skeleton" class="bg-white divide-y divide-gray-100 hidden">
            @for ($i = 0; $i < 8; $i++) <tr>
                <td class="py-3 px-4">
                    <div class="h-4 w-8 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-16 bg-gray-200 rounded-md animate-pulse"></div>
                        <div class="flex flex-col gap-1">
                            <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-3 w-20 bg-gray-100 rounded animate-pulse"></div>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-2 text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full mx-auto animate-pulse"></div>
                </td>
                <td class="py-3 pl-2 pr-4">
                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse mb-1"></div>
                    <div class="h-3 w-16 bg-gray-100 rounded animate-pulse mb-1"></div>
                    <div class="h-3 w-14 bg-gray-100 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-8 w-20 bg-gray-200 rounded-lg animate-pulse mx-auto"></div>
                </td>
                </tr>
                @endfor
        </tbody>

        {{-- Real Table --}}
        <tbody id="borrowing-records-real-table-body" class="bg-white divide-y divide-gray-100">
            @forelse ($borrowTransactions as $index => $transaction)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $transaction->id }}</td>

                <td class=" py-3 px-4 text-gray-700 flex items-center gap-3" title="{{ $transaction->bookCopy->book->title }}">
                    <img src=" {{ $transaction->bookCopy->book->cover_image ? asset('storage/' . $transaction->bookCopy->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $transaction->bookCopy->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div class="flex flex-col">
                        <div class="font-semibold text-gray-800 max-w-xs line-clamp-2">{{ $transaction->bookCopy->book->title }}</div>
                        <div class="text-xs text-gray-700 max-w-xs">Copy #{{ $transaction->bookCopy->copy_number }}</div>
                        <div class="text-xs text-gray-500 mt-1">by {{ $transaction->bookCopy->book->author->fullname }}</div>
                    </div>
                </td>
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
                {{-- <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $transaction->borrowed_at->format('M d, Y') }}</td> --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $transaction->due_at->format('M d, Y') }}</td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{-- REFACTORED STATUS LOGIC --}}
                    @if ($transaction->returned_at)
                    @if ($transaction->returned_at->lte($transaction->due_at))
                    {{-- Returned On Time --}}
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700 border border-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Returned On Time
                    </span>
                    @else
                    {{-- Returned Late --}}
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700 border border-orange-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Returned Late
                    </span>
                    <div class="mt-1 text-[11px] text-red-600">Was {{ $transaction->days_overdue }} days late</div>
                    @endif

                    @elseif ($transaction->status === 'overdue')
                    {{-- Currently Overdue --}}
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-200 text-red-700 border border-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $transaction->days_overdue }} day{{ $transaction->days_overdue == 1 ? '' : 's' }} overdue
                    </span>

                    @elseif ($transaction->status === 'borrowed')
                    {{-- Currently Borrowed (On Time / Due Soon) --}}
                    @php
                    $isDueSoon = $transaction->days_until_due <= (int) config('settings.notifications.reminder_days_before_due'); if ($isDueSoon) { $badgeBg='bg-orange-200 text-orange-700 border border-orange-100' ; $svg='<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>' ; if ($transaction->days_until_due == 0) {
                        $label = 'Due Today';
                        } elseif ($transaction->days_until_due == 1) {
                        $label = 'Due in 1 day';
                        } else {
                        $label = 'Due in ' . $transaction->days_until_due . ' days';
                        }
                        } else {
                        $badgeBg = 'bg-blue-200 text-blue-700 border border-blue-100';
                        $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>';
                        $label = 'Due in ' . $transaction->days_until_due . ' days';
                        }
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {!! $badgeBg !!}">
                            {!! $svg !!}
                            {{ $label }}
                        </span>

                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" />
                            </svg>
                            {{ ucfirst($transaction->status) }}
                        </span>
                        @endif

                </td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">

                    <button class="open-borrow-record-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-borrow-record='@json($transaction)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Details
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-20 px-4 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <img class="w-24 h-24" src="{{ asset('build/assets/icons/no-results-found.svg') }}" alt="No Results Found">
                        <p class="text-gray-500 text-lg font-medium mb-2">No results found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($borrowTransactions->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $borrowTransactions->firstItem() }}-{{ $borrowTransactions->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $borrowTransactions->total() }}</span> borrowers
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $borrowTransactions->currentPage() - 1 }}" {{ $borrowTransactions->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($borrowTransactions->getUrlRange(max(1, $borrowTransactions->currentPage() - 2), min($borrowTransactions->lastPage(), $borrowTransactions->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $borrowTransactions->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $borrowTransactions->currentPage() + 1 }}" {{ $borrowTransactions->currentPage() == $borrowTransactions->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
