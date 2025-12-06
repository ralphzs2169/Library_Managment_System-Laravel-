<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-8">No.</th>
                {{-- <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Copy No.</th> --}}
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider w-70">Book</th>
                <th class="py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-12"></th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Borrowed At</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Due Date</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Times Renewed</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Actions</th>
            </tr>
        </thead>

        <tbody id="active-borrows-real-table-body" class="bg-white divide-y divide-gray-100">
            @forelse ($activeBorrows as $index => $transaction)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $index + 1 + ($activeBorrows->currentPage() - 1) * $activeBorrows->perPage() }}</td>

                {{-- <td class="py-3 px-4 whitespace-nowrap text-gray-700">Copy #{{ $transaction->bookCopy->copy_number }}</td> --}}
                <td class="py-3 px-4 text-gray-700 flex items-center gap-3" title="{{ $transaction->bookCopy->book->title }}">
                    <img src="{{ $transaction->bookCopy->book->cover_image ? asset('storage/' . $transaction->bookCopy->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $transaction->bookCopy->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div class="flex  flex-col">
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
                    $isDueSoon = $transaction->days_until_due <= config('settings.notifications.reminder_days_before_due'); $badgeBg=$isDueSoon ? 'bg-orange-200 text-orange-700 border border-orange-100' : 'bg-blue-200 text-blue-700' ; $svg=$isDueSoon ? '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' ; @endphp <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {!! $badgeBg !!}">
                        {!! $svg !!}

                        @if ((int) $transaction->days_until_due === 0)
                        Due Today
                        @else
                        Due in {{ $transaction->days_until_due }} day{{ $transaction->days_until_due == 1 ? '' : 's' }}
                        @endif
                        </span>
                        @endif
                </td>
                @php

                $studentMaxRenewals = (int) config('settings.renewing.student_renewal_limit');
                $teacherMaxRenewals = (int) config('settings.renewing.teacher_renewal_limit');

                @endphp
                <td class="py-3 px-4 whitespace-nowrap font-semibold text-gray-700">{{ $transaction->times_renewed }} / {{ $transaction->user->role === 'teacher' ? $teacherMaxRenewals : $studentMaxRenewals }}</td>
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <button class="open-borrow-record-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-borrow-record='@json($transaction)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manage
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-20 px-4 text-center text-gray-500">
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
    @include('modals.librarian.borrow-record-details')
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
@vite('resources/js/pages/manage-record-details/borrowRecordDetails.js')
