{{-- Table for queue reservation --}}

<div class="overflow-x-auto rounded-lg border border-gray-200 table-fixed">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-12">ID</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10"></th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider w-70">Book</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-36">Reserved At</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-26">Pickup Deadline</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Actions</th>
            </tr>
        </thead>
        {{-- Skeleton Loader for Reservation Records Table --}}

        <tbody id="reservation-records-skeleton" class="bg-white divide-y divide-gray-100 hidden">
            @for ($i = 0; $i < 8; $i++) <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                <td class="py-3 px-4">
                    <div class="h-4 w-10 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-2 text-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse mx-auto"></div>
                </td>
                <td class="py-3 pl-2 pr-4">
                    <div class="h-4 w-24 bg-gray-200 rounded mb-2 animate-pulse"></div>
                    <div class="h-3 w-16 bg-gray-100 rounded mb-1 animate-pulse"></div>
                    <div class="h-3 w-14 bg-gray-100 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4 flex items-center gap-3">
                    <div class="w-12 h-16 rounded-md bg-gray-200 animate-pulse"></div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2 animate-pulse"></div>
                        <div class="h-3 w-20 bg-gray-100 rounded animate-pulse"></div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-24 bg-gray-200 rounded mb-2 animate-pulse"></div>
                    <div class="h-3 w-20 bg-gray-100 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-8 w-16 bg-gray-200 rounded-lg mx-auto animate-pulse"></div>
                </td>
                </tr>
                @endfor
        </tbody>

        <tbody id="reservation-records-real-table-body" class="bg-white divide-y divide-gray-100">
            @forelse($reservations as $index => $reservation)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $reservation->id }}</td>

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
                <td class="py-3 px-4 text-gray-700 flex items-center gap-3" title="{{ $reservation->book->title }}">
                    <img src="{{ $reservation->book->cover_image ? asset('storage/' . $reservation->book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $reservation->book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                    <div>
                        <div class="font-semibold text-gray-800 line-clamp-2 max-w-xs">{{ $reservation->book->title }}</div>
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
                        {{-- Hourglass Icon (Using the standard `clock` or `hourglass` path) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 384 512">
                            <path fill="#1D4ED8" d="M368 48h4c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h4c0 80.564 32.188 165.807 97.18 208C47.899 298.381 16 383.9 16 464h-4c-6.627 0-12 5.373-12 12v24c0 6.627 5.373 12 12 12h360c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12h-4c0-80.564-32.188-165.807-97.18-208C336.102 213.619 368 128.1 368 48zM64 48h256c0 101.62-57.307 184-128 184S64 149.621 64 48zm256 416H64c0-101.62 57.308-184 128-184s128 82.38 128 184z" /></svg>
                        Ready for Pickup
                    </span>
                    @elseif($reservation->status === 'completed')
                    <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Completed
                    </span>

                    @elseif($reservation->status === 'cancelled')
                    <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cancelled
                    </span>
                    @else
                    <span class="text-gray-500 text-xs">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @endif
                    {{-- 2. Queue Position (Secondary Info, only if pending) --}}
                    @if($reservation->status === 'pending')
                    @php

                    $pos = $reservation->queue_position;
                    $suffix = 'th';
                    if ($pos % 100 >= 11 && $pos % 100 <= 13) { $suffix='th' ; } else { switch ($pos % 10) { case 1: $suffix='st' ; break; case 2: $suffix='nd' ; break; case 3: $suffix='rd' ; break; } } @endphp <div class="flex items-center gap-1 pl-2 mt-0.5 text-xs text-indigo-600 font-semibold py-0.5 w-fit  ">

                        Queue: {{ $pos }}{{ $suffix }}
</div>

@elseif($reservation->status === 'completed')
<span class="text-xs pl-2 text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($reservation->completed_at)->format('M d, Y') }}</span>
@else
{{-- Placeholder for non-pending items --}}
<span class="text-xs pl-2 text-gray-500 mt-0.5">— Not in Queue</span>
@endif
</td>
<td class="py-3 px-4 text-gray-700">
    <div class="flex flex-col">
        {{-- 1. Reservation Date (Primary Info) --}}
        <span class="font-medium text-gray-800 whitespace-nowrap">
            {{ \Carbon\Carbon::parse($reservation->reserved_at)->format('M d, Y') }}
        </span>

    </div>
</td>

{{-- Expiry Date Column  --}}
<td class="py-3 px-4 text-gray-700">
    {{ $reservation->pickup_deadline_date ? \Carbon\Carbon::parse($reservation->pickup_deadline_date)->format('M d, Y') : 'N/A' }}
</td>

{{-- Actions Column --}}
<td class="py-3 px-4 whitespace-nowrap text-gray-700">
    <button class="open-reservation-record-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-reservation-record='@json($reservation)'>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Manage
    </button>
</td>
</tr>
@empty
<tr>
    <td colspan="8" class="py-20 text-center text-gray-500 text-sm">
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

@if($reservations->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $reservations->firstItem() }}-{{ $reservations->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $reservations->total() }}</span> reservation
    </p>
    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $reservations->currentPage() - 1 }}" {{ $reservations->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>
        <!-- Page Numbers -->
        @foreach ($reservations->getUrlRange(max(1, $reservations->currentPage() - 2), min($reservations->lastPage(), $reservations->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $reservations->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach
        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $reservations->currentPage() + 1 }}" {{ $reservations->currentPage() == $reservations->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
