<x-layout :title="'Book Information'">
    <div class="flex">
        <x-user-sidebar />

        <div class="flex-1 md:ml-60 p-6 bg-background min-h-screen">
            <div>


                {{-- Main Content --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Header Section --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-300">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">Book Information</h1>
                                <p class="text-xs text-gray-600 mt-0.5">Complete details about this book</p>
                            </div>
                        </div>
                        {{-- Back Button --}}
                        <div>
                            <a href="{{ route('users.book-catalog') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Catalog
                            </a>
                        </div>
                    </div>

                    {{-- Book Details --}}
                    <div class="p-5">
                        {{-- Status Banners --}}
                        @if(isset($activeBorrow))
                        <div class="mb-6 rounded-lg border-2 border-blue-600/30 bg-blue-600/10 px-4 py-3 flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="text-sm text-blue-600 leading-snug">
                                <p class="font-bold text-blue-600">Currently Borrowed</p>
                                <p>You have a copy of this book. Due: <span class="font-bold">{{ \Carbon\Carbon::parse($activeBorrow->due_at)->format('M d, Y') }}</span>.</p>
                            </div>
                        </div>
                        @elseif(isset($activeReservation))
                        @if($activeReservation->status === \App\Enums\ReservationStatus::PENDING)
                        <div class="mb-6 rounded-lg border-2 border-amber-400 bg-amber-50 px-4 py-3 flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-sm text-amber-800 leading-snug">
                                <p class="font-bold text-amber-900">Reservation Pending</p>
                                <p>You are currently in the queue for this book. Your queue position is <span class="font-bold">#{{ $queuePosition }}</span>.</p>
                            </div>
                        </div>
                        @elseif($activeReservation->status === \App\Enums\ReservationStatus::READY_FOR_PICKUP)
                        <div class="mb-6 rounded-lg border-2 border-blue-400 bg-blue-50 px-4 py-3 flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-sm text-blue-800 leading-snug">
                                <p class="font-bold text-blue-900">Ready for Pickup</p>
                                <p>Your copy is ready! Please proceed to the library counter to claim your book by <span class="font-bold">{{ \Carbon\Carbon::parse($activeReservation->pickup_deadline)->format('M d, Y') }}</span>.</p>
                            </div>
                        </div>
                        @endif
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            {{-- Left Column: Cover Image --}}
                            <div class="lg:col-span-1">
                                <div class="sticky top-6">
                                    <div class="relative aspect-[3/4] bg-gray-100 rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-cyan-400 to-blue-500 text-white p-6">
                                            <span class="font-bold text-center text-sm">{{ Str::limit($book->title, 40) }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Quick Stats --}}
                                    <div class="mt-4 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                        <h3 class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Availability</h3>
                                        <div class="space-y-1.5">
                                            @php
                                            $availableCopies = $book->copies->where('status', \App\Enums\BookCopyStatus::AVAILABLE)->count();
                                            $totalCopies = $book->copies->count();
                                            @endphp
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-600">Total Copies:</span>
                                                <span class="text-xs font-bold text-gray-900">{{ $totalCopies }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-600">Available:</span>
                                                <span class="text-xs font-bold text-green-600">{{ $availableCopies }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-600">Borrowed:</span>
                                                <span class="text-xs font-bold text-red-600">{{ $book->copies->where('status', \App\Enums\BookCopyStatus::BORROWED)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Book Information --}}
                            <div class="lg:col-span-3 space-y-5">
                                {{-- Title & Basic Info --}}
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1.5">{{ $book->title }}</h2>
                                    <p class="text-sm text-gray-600">
                                        by <span class="font-semibold text-gray-800">{{ $book->author->firstname }} {{ $book->author->middle_initial ? $book->author->middle_initial . '. ' : '' }}{{ $book->author->lastname }}</span>
                                    </p>
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            {{ $book->genre->category->name ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            {{ $book->genre->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Description --}}
                                @if($book->description)
                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-sm font-bold text-gray-900 mb-2">Description</h3>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $book->description }}</p>
                                </div>
                                @endif

                                {{-- Simple Book Details List --}}
                                <div class="border-t border-gray-200 pt-4">


                                    <div class="text-sm text-gray-900 space-y-2">
                                        <div class="flex">
                                            <span class="w-26 font-bold">Publisher</span>
                                            <span>{{ $book->publisher ?? '-' }}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-26 font-bold">Year</span>
                                            <span>{{ $book->publication_year ?? '-' }}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-26 font-bold">Category</span>
                                            <span>{{ $book->genre->category->name ?? '-' }}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-26 font-bold">Genre</span>
                                            <span>{{ $book->genre->name ?? '-' }}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-26 font-bold">Date Added</span>
                                            <span>{{ $book->created_at->format('F d, Y') ?? '-' }}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-26 font-bold">Language</span>
                                            <span>{{ $book->language ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>



                                {{-- Copies Status --}}
                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-sm font-bold text-gray-900 mb-3">Book Copies Status</h3>
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-xs">
                                                <thead class="bg-gray-100 border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-3 py-2 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">Copy No.</th>
                                                        <th class="px-3 py-2 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">Status</th>
                                                        <th class="px-3 py-2 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">Date Added</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-100">
                                                    @forelse($book->copies as $copy)
                                                    @php
                                                    $isBorrowedByUser = isset($activeBorrow) && $activeBorrow->book_copy_id === $copy->id;
                                                    @endphp
                                                    <tr class="transition {{ $isBorrowedByUser ? 'bg-blue-50 border-l-4 border-blue-500' : 'hover:bg-gray-50' }}">
                                                        <td class="px-3 py-2 text-gray-900 font-medium">
                                                            {{ $copy->copy_number }}
                                                            @if($isBorrowedByUser)
                                                            <span class="ml-2 text-[10px] uppercase tracking-wider font-bold text-blue-600">(Your Copy)</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @php
                                                            $status = strtolower($copy->status);
                                                            $badgeConfig = match($status) {
                                                            'available' => ['class' => 'bg-green-200 text-green-700', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
                                                            'borrowed' => ['class' => 'bg-blue-200 text-blue-700', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'],
                                                            'damaged' => ['class' => 'bg-orange-200 text-orange-700', 'icon' => '<img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3 h-3">'],
                                                            'lost' => ['class' => 'bg-red-200 text-red-700', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'],
                                                            'withdrawn' => ['class' => 'bg-gray-200 text-gray-700', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>'],
                                                            'pending_issue_review' => ['class' => 'bg-amber-300 text-amber-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
                                                            'on_hold_for_pickup' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
                                                            default => ['class' => 'bg-gray-200 text-gray-700', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
                                                            };
                                                            @endphp
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeConfig['class'] }}">
                                                                {!! $badgeConfig['icon'] !!}
                                                                {{ $status === 'pending_issue_review' ? 'Pending Review' : ($status === 'on_hold_for_pickup' ? 'On Hold For Pickup' : ucfirst(str_replace('_', ' ', $status))) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600">{{ $copy->created_at->format('M d, Y') }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="3" class="px-3 py-6 text-center text-gray-500 text-xs">
                                                            No copies available for this book.
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="bg-gray-50 border-t border-gray-200 px-5 py-4">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                            <a href="{{ route('users.book-catalog') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Catalog
                            </a>

                            @php
                            $availableCopies = $book->copies->where('status', \App\Enums\BookCopyStatus::AVAILABLE)->count();
                            @endphp

                            @if(isset($activeBorrow))
                            <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-100 border border-blue-200 text-blue-700 rounded-lg text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Currently Borrowed</span>
                            </div>
                            @elseif(isset($activeReservation))
                            {{-- If user has active reservation, show status button instead of reserve --}}
                            <div class="flex flex-col sm:flex-row items-stretch gap-2">
                                <!-- Status Box -->
                                <div class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 border border-gray-200 text-gray-600 rounded-lg text-sm font-medium whitespace-nowrap">
                                    @if ($activeReservation->status === \App\Enums\ReservationStatus::PENDING)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Reservation Pending (#{{ $queuePosition }})</span>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Ready for Pickup</span>
                                    @endif
                                </div>

                                <!-- Cancel Button -->
                                <button id="cancel-reservation-button" class="cursor-pointer flex-1  inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-all shadow-sm" data-reservation-id="{{ $activeReservation->id }}" data-borrower-id="{{ auth()->id() }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel
                                </button>
                            </div>



                            @elseif(isset($canReserve) && $canReserve['result'] !== 'success')
                            <div class="relative inline-block group w-full sm:w-auto">
                                <button disabled class="w-full sm:w-auto cursor-not-allowed inline-flex justify-center items-center gap-2 px-5 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-semibold shadow-sm opacity-75">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Reserve a Copy
                                </button>
                                <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                    {{ str_ireplace("user's", "Your", $canReserve['message']) }}
                                    <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </div>
                            @elseif($availableCopies > 0)
                            <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Available for borrowing at the library</span>
                            </div>
                            @else
                            <button id="reserve-book-button" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2 bg-yellow-500 cursor-pointer text-white rounded-lg hover:bg-yellow-600 transition text-sm font-semibold shadow-md hover:shadow-lg" data-book='@json($book)' data-reserver='@json($user)' data-queue-position='{{ $queuePosition ?? null }}'>
                                <img src={{ asset('/build/assets/icons/reservation-white.svg')}} alt="Reserve Book Icon" class="w-5 h-5">
                                Reserve a Copy
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.confirm-reservation')
</x-layout>

@vite('resources/js/pages/borrower/reserveBook.js')
@vite('resources/js/pages/borrower/userSidebar.js')
