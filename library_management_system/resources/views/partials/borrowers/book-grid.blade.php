{{-- filepath: c:\Users\Angela\library_management_system\resources\views\partials\borrowers\book-grid.blade.php --}}
@if(count($books) > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
    @foreach($books as $book)
    @php
    $availableCopies = $book->copies->where('status', \App\Enums\BookCopyStatus::AVAILABLE)->count();
    $totalCopies = $book->copies->count();
    $userReservation = $book->reservations->first();
    $userBorrow = $book->borrowTransactions->first();
    @endphp
    <a href="{{ route('borrowers.book-info', $book->id) }}" class="book-card bg-white shadow-sm hover:shadow-md hover:shadow-accent transition-shadow duration-200 overflow-hidden cursor-pointer group">
        {{-- Book Cover --}}
        <div class="relative aspect-[4/5.2] bg-white overflow-hidden">
            @if($book->cover_image)
            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-cyan-400 to-blue-500 text-white p-6">
                <span class="font-semibold text-center text-sm">{{ Str::limit($book->title, 40) }}</span>
            </div>
            @endif

            {{-- Availability Badge --}}
            <div class="absolute top-2 right-2">
                @if($userBorrow)
                <span class="flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Currently Borrowed
                </span>
                @elseif($userReservation)
                @if($userReservation->status === \App\Enums\ReservationStatus::PENDING)
                <span class="flex items-center gap-1 px-2 py-1 bg-yellow-200 text-yellow-800 text-xs font-semibold rounded-full shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pending Reservation
                </span>
                @elseif($userReservation->status === \App\Enums\ReservationStatus::READY_FOR_PICKUP)
                <span class="flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 384 512">
                        <path fill="#1D4ED8" d="M368 48h4c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h4c0 80.564 32.188 165.807 97.18 208C47.899 298.381 16 383.9 16 464h-4c-6.627 0-12 5.373-12 12v24c0 6.627 5.373 12 12 12h360c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12h-4c0-80.564-32.188-165.807-97.18-208C336.102 213.619 368 128.1 368 48zM64 48h256c0 101.62-57.307 184-128 184S64 149.621 64 48zm256 416H64c0-101.62 57.308-184 128-184s128 82.38 128 184z" />
                    </svg>
                    Ready for Pickup
                </span>
                @endif
                @elseif($availableCopies > 0)
                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full shadow-lg">
                    Available
                </span>
                @else
                <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded-full shadow-lg">
                    Unavailable
                </span>
                @endif
            </div>
        </div>

        {{-- Book Details --}}
        <div class="p-4">
            {{-- Title --}}
            <h3 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-3 min-h-[3rem]" title="{{ $book->title }}">
                {{ $book->title }}
            </h3>

            {{-- Author --}}
            <p class="text-xs text-gray-600 mb-2 truncate">
                by {{ $book->author->firstname }} {{ $book->author->lastname }}
            </p>

            {{-- Copies Info --}}
            <div class="pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">
                        {{ $availableCopies }}/{{ $totalCopies }} available
                    </span>
                    <span class="flex items-center text-xs text-gray-500 gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $book->publication_year ?? 'N/A' }}
                    </span>

                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>
@else
<div class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center">
    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    </svg>
    <h3 class="text-lg font-medium text-gray-900 mb-2">No books found</h3>
    <p class="text-gray-500">Try adjusting your search or filters to find what you're looking for.</p>
</div>
@endif
