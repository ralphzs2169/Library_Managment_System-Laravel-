{{-- Table for queue reservation --}}

<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Queue Position</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Pickup Deadline</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Reserved at</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Actions</th>
            </tr>
        </thead>
        <tbody id="queue-reservations-real-table-body" class="bg-white divide-y divide-gray-100">
            @forelse($queueReservations as $index => $reservation)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $index + 1 + ($queueReservations->currentPage() - 1) * $queueReservations->perPage() }}</td>

                {{-- Borrower --}}
                <td class="py-2 text-center">
                    <!-- Borrower initials icon -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($reservation->borrower->firstname,0,1)) }}{{ strtoupper(substr($reservation->borrower->lastname,0,1)) }}
                    </div>
                </td>
                <td class="py-3 pl-2 pr-4 whitespace-nowrap">
                    <div class="font-medium text-gray-700">{{ $reservation->borrower->full_name }}
                    </div>


                    <div class="text-gray-500 font-mono text-xs">
                        @if($reservation->borrower->role === 'teacher')
                        {{ $reservation->borrower->teachers->employee_number }}
                        @elseif($reservation->borrower->role === 'student')
                        {{ $reservation->borrower->students->student_number }}
                        @endif
                    </div>

                    <div class="text-sm text-gray-500">
                        @php
                        if ($reservation->borrower->role === 'student') {
                        $roleBadgeClass = 'bg-blue-100 text-blue-700 border border-blue-100';
                        $iconHTML = '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">';
                        } elseif ($reservation->borrower->role === 'teacher') {
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
                            {{ ucfirst($reservation->borrower->role) }}
                        </span></div>

                </td>
                <td class="py-3 px-4 text-gray-700 flex items-center gap-3">
                    <img src="{{ $reservation->book->cover_image ? asset('storage/' . $reservation->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $reservation->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div>
                        <div class="font-semibold text-gray-800 truncate max-w-xs">{{ $reservation->book->title }}</div>
                        <div class="text-xs text-gray-500 mt-1">by {{ $reservation->book->author->fullname ?? '' }}</div>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-700">
                    {{-- Status badge (match tableReservations.js design) --}}
                    @if($reservation->status === 'pending')
                    <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold bg-yellow-200 text-yellow-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pending
                    </span>
                    @elseif($reservation->status === 'ready_for_pickup')
                    <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 384 512">
                            <path fill="#1D4ED8" d="M368 48h4c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h4c0 80.564 32.188 165.807 97.18 208C47.899 298.381 16 383.9 16 464h-4c-6.627 0-12 5.373-12 12v24c0 6.627 5.373 12 12 12h360c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12h-4c0-80.564-32.188-165.807-97.18-208C336.102 213.619 368 128.1 368 48zM64 48h256c0 101.62-57.307 184-128 184S64 149.621 64 48zm256 416H64c0-101.62 57.308-184 128-184s128 82.38 128 184z" /></svg>
                        Ready for Pickup
                    </span>
                    @else
                    <span class="text-gray-500 text-xs">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-gray-700">
                    @if($reservation->status === 'pending')
                    @php
                    $pos = $reservation->queue_position;
                    $suffix = 'th';
                    if ($pos == 1) $suffix = 'st';
                    elseif ($pos == 2) $suffix = 'nd';
                    elseif ($pos == 3) $suffix = 'rd';
                    @endphp
                    <span class="font-semibold text-gray-700">
                        {{ $pos }}{{ $suffix }}
                    </span>
                    @else
                    —
                    @endif
                </td>

                {{-- Expiry Date Column  --}}
                <td class="py-3 px-4 text-gray-700">
                    {{ $reservation->pickup_deadline_date ? \Carbon\Carbon::parse($reservation->pickup_deadline_date)->format('M d, Y') : 'N/A' }}
                </td>

                {{-- Reserved at Column --}}
                <td class="py-3 px-4 text-gray-700">
                    {{ $reservation->created_at ? \Carbon\Carbon::parse($reservation->created_at)->format('M d, Y') : 'N/A' }}
                </td>

                {{-- Actions Column --}}
                {{-- <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <div class="relative inline-block text-left w-full">

                        <button type="button" class="kebab-menu-btn cursor-pointer flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-accent transition" data-reservation-id="{{ $reservation->id }}">
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
                            @php
                            if($reservation->status !== 'ready_for_pickup') {
                            $disabledClass = 'opacity-50 cursor-not-allowed pointer-events-none';
                            } else {
                            $disabledClass = 'cursor-pointer';
                            }
                            @endphp
                            <button class="kebab-action-btn flex items-center tracking-wide gap-2 px-4 py-2 w-full text-left text-xs text-white hover:bg-secondary/80 transition {{ $disabledClass }}" data-action="fulfill" data-book='@json($reservation->book)' data-book-copy='@json($reservation->bookCopy)' data-member='@json($reservation->borrower)'>
                                <!-- Settle icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Fulfill Reservation
                            </button>
                        </li>
                        <li>
                            <button class="kebab-action-btn flex items-center tracking-wide gap-2 px-4 py-2 w-full text-left text-xs text-white cursor-pointer hover:bg-secondary/80 transition" data-action="cancelReservation" data-reservation-id="{{ $reservation->id }}">
                                <!-- Cancel icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel Reservation
                            </button>
                        </li>
                    </ul>
                </div>
</div>
</td> --}}
<td class="py-3 px-4 whitespace-nowrap text-gray-700">
    <button class="open-reservation-record-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-reservation-record='@json($reservation)' data-action-by="staff">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Manage
    </button>
</td>
</tr>
@empty
<tr>
    <td colspan="8" class="py-10 text-center text-gray-500 text-sm">
        No queue reservation found.
    </td>
</tr>
@endforelse
</tbody>
</table>
@include('modals.librarian.manage-reservation-record')
</div>

@if($queueReservations->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $queueReservations->firstItem() }}-{{ $queueReservations->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $queueReservations->total() }}</span> reservation
    </p>
    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $queueReservations->currentPage() - 1 }}" {{ $queueReservations->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>
        <!-- Page Numbers -->
        @foreach ($queueReservations->getUrlRange(max(1, $queueReservations->currentPage() - 2), min($queueReservations->lastPage(), $queueReservations->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $queueReservations->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach
        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $queueReservations->currentPage() + 1 }}" {{ $queueReservations->currentPage() == $queueReservations->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif

@vite('resources/js/pages/librarian/reservationRecords/reservationRecordDetails.js')
