<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-8">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10"></th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider w-70">Book</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-34 ">Reason</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Amount</th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-30">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Returned at</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-30">Actions</th>
            </tr>
        </thead>

        <tbody id="unpaid-penalties-real-table-body" class="bg-white divide-y divide-gray-100">
            @forelse($unpaidPenalties as $index => $unpaidPenalty)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $index + 1 + ($unpaidPenalties->currentPage() - 1) * $unpaidPenalties->perPage() }}</td>

                {{-- Borrower  --}}
                <td class="py-2 text-center">
                    <!-- Borrower initials icon -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($unpaidPenalty->user->firstname,0,1)) }}{{ strtoupper(substr($unpaidPenalty->user->lastname,0,1)) }}
                    </div>
                </td>
                <td class="py-3 pl-2 pr-4 whitespace-nowrap">
                    <div class="font-medium text-gray-700">{{ $unpaidPenalty->user->full_name }}
                    </div>


                    <div class="text-gray-500 font-mono text-xs">
                        @if($unpaidPenalty->user->role === 'teacher')
                        {{ $unpaidPenalty->user->teachers->employee_number }}
                        @elseif($unpaidPenalty->user->role === 'student')
                        {{ $unpaidPenalty->user->students->student_number }}
                        @endif
                    </div>

                    <div class="text-sm text-gray-500">
                        @php
                        if ($unpaidPenalty->user->role === 'student') {
                        $roleBadgeClass = 'bg-blue-100 text-blue-700 border border-blue-100';
                        $iconHTML = '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">';
                        } elseif ($unpaidPenalty->user->role === 'teacher') {
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
                            {{ ucfirst($unpaidPenalty->user->role) }}
                        </span></div>

                </td>
                {{-- Book column  --}}
                <td class="py-3 px-4 text-gray-700 flex items-center gap-3">
                    <img src="{{ $unpaidPenalty->bookCopy->book->cover_image ? asset('storage/' . $unpaidPenalty->bookCopy->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $unpaidPenalty->bookCopy->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div class="flex  flex-col">
                        <div class="font-semibold text-gray-800 max-w-xs line-clamp-2">{{ $unpaidPenalty->bookCopy->book->title }}</div>
                        <div class="text-xs text-gray-600 truncate max-w-xs">Copy #{{ $unpaidPenalty->bookCopy->copy_number }}</div>
                        <div class="text-xs text-gray-500 mt-1">by {{ $unpaidPenalty->bookCopy->book->author->fullname }}</div>
                    </div>
                </td>
                <td class="py-3 text-gray-700">
                    {{-- Reason badge --}}
                    @if($unpaidPenalty->penalty->type === 'late_return')
                    <span class="w-full inline-flex items-center gap-1 px-2 rounded-full text-xs font-semibold text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Late Return
                    </span>
                    @elseif($unpaidPenalty->penalty->type === 'lost_book')
                    <span class="w-full inline-flex items-center font-semibold gap-1 px-2 rounded-xs text-xs   text-purple-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Lost
                    </span>
                    @elseif($unpaidPenalty->penalty->type === 'damaged_book')
                    <span class="w-full inline-flex items-center font-semibold gap-1 px-2 rounded-xs text-xs    text-orange-700">
                        <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">
                        Damaged
                    </span>
                    @else
                    <span class="text-gray-500">{{ ucfirst($unpaidPenalty->penalty->type) }}</span>
                    @endif
                    <span class="text-xs pl-2 text-gray-500">Issued at: {{ \Carbon\Carbon::parse($unpaidPenalty->penalty->created_at)->format('M d, Y') }}</span>

                </td>
                <td class="py-3 px-4 whitespace-nowrap font-bold">
                    {{-- Display the total penalty amount first --}}
                    <div class="text-base text-gray-800 font-bold">
                        ₱ {{ number_format($unpaidPenalty->penalty->amount, 2) }}
                    </div>

                    @if ($unpaidPenalty->penalty->status === \App\Enums\PenaltyStatus::PARTIALLY_PAID)

                    {{-- Show the remaining balance as a smaller sub-detail --}}
                    <div class="text-xs text-red-700 mt-1 whitespace-nowrap">
                        (₱ {{ number_format($unpaidPenalty->penalty->remaining_amount, 2) }} Left)
                    </div>
                    @endif
                </td>

                <td class="py-3 pl-2 pr-4 whitespace-nowrap text-gray-700">
                    {{-- Status badge --}}
                    @if ($unpaidPenalty->penalty->status === \App\Enums\PenaltyStatus::UNPAID)
                    <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                        Unpaid
                    </span>
                    @elseif ($unpaidPenalty->penalty->status === \App\Enums\PenaltyStatus::PARTIALLY_PAID)
                    <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700 whitespace-nowrap">
                        <img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">
                        Partially Paid
                    </span>
                    @elseif ($unpaidPenalty->penalty->status === \App\Enums\PenaltyStatus::PAID)
                    <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Paid
                        @elseif ($unpaidPenalty->penalty->status === \App\Enums\PenaltyStatus::CANCELLED)
                        <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Cancelled </span>
                        @endif
                </td>


                {{-- Returned at column  --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{ $unpaidPenalty->returned_at ? \Carbon\Carbon::parse($unpaidPenalty->penalty->returned_at)->format('M d, Y') : 'N/A' }}
                </td>

                {{-- Actions column  --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <button class="open-penalty-record-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-penalty-record='@json($unpaidPenalty)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manage
                    </button>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="10" class="py-20 px-4 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <img class="w-24 h-24" src="{{ asset('build/assets/icons/no-results-found.svg') }}" alt="No Results Found">
                        <p class="text-gray-500 text-lg font-medium mb-2">No penalty results found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @include('modals.librarian.penalty-record-details')
</div>

@if($unpaidPenalties->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $unpaidPenalties->firstItem() }}-{{ $unpaidPenalties->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $unpaidPenalties->total() }}</span> borrowers
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $unpaidPenalties->currentPage() - 1 }}" {{ $unpaidPenalties->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($unpaidPenalties->getUrlRange(max(1, $unpaidPenalties->currentPage() - 2), min($unpaidPenalties->lastPage(), $unpaidPenalties->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $unpaidPenalties->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $unpaidPenalties->currentPage() + 1 }}" {{ $unpaidPenalties->currentPage() == $unpaidPenalties->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif

@vite('resources/js/pages/manage-record-details/penaltyRecordDetails.js')
