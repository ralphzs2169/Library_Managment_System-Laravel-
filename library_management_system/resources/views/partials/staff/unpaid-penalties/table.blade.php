<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Reason</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Amount</th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Issued at</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Returned at</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-20">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">

            @foreach($unpaidPenalties as $index => $unpaidPenalty)
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
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{-- Reason badge --}}
                    @if($unpaidPenalty->penalty->type === 'late_return')
                    <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Late Return
                    </span>
                    @elseif($unpaidPenalty->penalty->type === 'lost_book')
                    <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Lost
                    </span>
                    @elseif($unpaidPenalty->penalty->type === 'damaged_book')
                    <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
                        <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">
                        Damaged
                    </span>
                    @else
                    <span class="text-gray-500">{{ ucfirst($unpaidPenalty->penalty->type) }}</span>
                    @endif
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
                    @endif
                </td>

                {{-- Issued at column  --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{ $unpaidPenalty->penalty->created_at->format('M d, Y') }}
                </td>

                {{-- Book column  --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700 flex items-center gap-3">
                    <img src="{{ $unpaidPenalty->bookCopy->book->cover_image ? asset('storage/' . $unpaidPenalty->bookCopy->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $unpaidPenalty->bookCopy->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div class="flex  flex-col">
                        <div class="font-semibold text-gray-800 truncate max-w-xs">{{ $unpaidPenalty->bookCopy->book->title }}</div>
                        <div class="text-xs text-gray-600 truncate max-w-xs">Copy #{{ $unpaidPenalty->bookCopy->copy_number }}</div>
                        <div class="text-xs text-gray-500 mt-1">by {{ $unpaidPenalty->bookCopy->book->author->fullname }}</div>
                    </div>
                </td>

                {{-- Returned at column  --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{ $unpaidPenalty->returned_at ? $unpaidPenalty->returned_at->format('M d, Y') : 'N/A' }}
                </td>

                {{-- Actions column  --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <div class="relative inline-block text-left w-full">

                        <button type="button" class="kebab-menu-btn cursor-pointer flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-accent transition" data-penalty-id="{{ $unpaidPenalty->penalty->id }}">
                            <!-- Kebab (three dots) icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="5" cy="12" r="1.5" fill="currentColor" />
                                <circle cx="12" cy="12" r="1.5" fill="currentColor" />
                                <circle cx="19" cy="12" r="1.5" fill="currentColor" />
                            </svg>
                        </button>
                        <div class="kebab-menu-dropdown absolute right-0 z-10 w-44 origin-top-right bg-secondary rounded-lg shadow-lg hidden" style="min-width:11rem;">
                            <div class="kebab-menu-arrow absolute bottom-full right-12 border-4 border-transparent border-b-gray-900"></div>
                            <ul class="py-1">
                                <li>
                                    <button class="kebab-action-btn flex items-center gap-2 px-4 py-2 w-full text-left text-xs text-white cursor-pointer hover:bg-secondary/80 transition" data-action="settle" data-penalty="{{ $unpaidPenalty }}" data-member="{{ $unpaidPenalty->user }}">
                                        <!-- Settle icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Settle Penalty
                                    </button>
                                </li>
                                <li>
                                    <button class="kebab-action-btn flex items-center gap-2 px-4 py-2 w-full text-left text-xs text-white cursor-pointer hover:bg-secondary/80 transition" data-action="cancelPenalty" data-penalty-id="{{ $unpaidPenalty->penalty->id }}" data-member-id="{{ $unpaidPenalty->user->id }}">
                                        <!-- Cancel icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancel Penalty
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

{{-- @if($penalties->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $penalties->firstItem() }}-{{ $penalties->lastItem() }}</span>
of <span class="font-semibold text-gray-800">{{ $penalties->total() }}</span> borrowers
</p>

<div class="flex items-center gap-2">
    <!-- Previous -->
    <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $penalties->currentPage() - 1 }}" {{ $penalties->onFirstPage() ? 'disabled' : '' }}>
        Previous
    </button>

    <!-- Page Numbers -->
    @foreach ($penalties->getUrlRange(max(1, $penalties->currentPage() - 2), min($penalties->lastPage(), $penalties->currentPage() + 2)) as $page => $url)
    <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $penalties->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
        {{ $page }}
    </button>
    @endforeach

    <!-- Next -->
    <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $penalties->currentPage() + 1 }}" {{ $penalties->currentPage() == $penalties->lastPage() ? 'disabled' : '' }}>
        Next
    </button>
</div>
</div>
@endif --}}
