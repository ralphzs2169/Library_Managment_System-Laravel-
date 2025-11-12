{{-- filepath: c:\Users\Angela\library_management_system\resources\views\partials\librarian\books-table.blade.php --}}
<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Cover</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Title & ISBN</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Author</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Category</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Copies</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Status</th>
                <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Actions</th>
            </tr>
        </thead>
        <tbody id="books-table-body" class="bg-white divide-y divide-gray-100">
            @if ($books->isEmpty())
            <tr>
                <td colspan="8" class="py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <img src="{{ asset('build/assets/icons/no-books-found.svg') }}" alt="No Books Found" class="w-16 h-16 mb-3 opacity-50">
                        <p class="text-gray-500 text-lg font-medium mb-2">No books found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @else
            @php
            $rowNumber = ($books->currentPage() - 1) * $books->perPage();
            @endphp

            @foreach ($books as $book)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-gray-600 font-medium">{{ ++$rowNumber }}</td>
                <td class="px-4 py-3">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $book->title }}" class="w-12 h-16 rounded-md object-cover shadow-sm border border-gray-200">
                </td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800 truncate max-w-xs">{{ $book->title }}</p>
                    <p class="text-xs text-gray-500 font-mono mt-1">ISBN: {{ $book->isbn ?? 'N/A' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">
                    {{ $book->author->formal_name }}
                </td>
                <td class="px-4 py-3 text-gray-700 text-center">
                    <span class="inline-flex w-full px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                        {{ $book->genre->category->name ?? 'N/A' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-gray-800 text-sm">
                        {{ $book->copies->count() }} {{ $book->copies->count() === 1 ? 'copy' : 'copies' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @php
                    $statuses = $book->copies->groupBy('status')->map->count();
                    $filteredStatus = request('status'); // Get the current filter
                    @endphp

                    @if($filteredStatus && $filteredStatus !== 'all')
                    {{-- Show only the filtered status --}}
                    @php
                    $count = $statuses->get($filteredStatus, 0);
                    $badgeConfig = match($filteredStatus) {
                    'available' => [
                    'class' => 'bg-green-200 text-green-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    ],
                    'borrowed' => [
                    'class' => 'bg-blue-200 text-blue-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'
                    ],
                    'damaged' => [
                    'class' => 'bg-orange-200 text-orange-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>'
                    ],
                    'lost' => [
                    'class' => 'bg-red-200 text-red-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'
                    ],
                    'withdrawn' => [
                    'class' => 'bg-gray-200 text-gray-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>'
                    ],
                    default => [
                    'class' => 'bg-gray-200 text-gray-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    ],
                    };

                    @endphp

                    @if($count > 0)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeConfig['class'] }} w-full">
                        {!! $badgeConfig['icon'] !!}
                        {{ $count }} {{ ucfirst($filteredStatus) }}
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        No {{ ucfirst($filteredStatus) }}
                    </span>
                    @endif
                    @elseif($statuses->count() === 1)
                    {{-- Original logic: Single status --}}
                    @php
                    $status = $statuses->keys()->first();
                    $badgeConfig = match($status) {
                    'available' => [
                    'class' => 'bg-green-200 text-green-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    ],
                    'borrowed' => [
                    'class' => 'bg-blue-200 text-blue-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'
                    ],
                    'damaged' => [
                    'class' => 'bg-orange-200 text-orange-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>'
                    ],
                    'lost' => [
                    'class' => 'bg-red-200 text-red-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'
                    ],
                    default => [
                    'class' => 'bg-gray-200 text-gray-700',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    ],
                    };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeConfig['class'] }} w-full">
                        {!! $badgeConfig['icon'] !!}
                        All {{ ucfirst($status) }}
                    </span>
                    @else
                    {{-- Original logic: Multiple statuses --}}
                    <div class="flex flex-col gap-1.5">
                        @foreach($statuses as $status => $count)
                        @php
                        $badgeConfig = match($status) {
                        'available' => [
                        'class' => 'bg-green-200 text-green-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                        ],
                        'borrowed' => [
                        'class' => 'bg-blue-200 text-blue-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'
                        ],
                        'damaged' => [
                        'class' => 'bg-orange-200 text-orange-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>'
                        ],
                        'lost' => [
                        'class' => 'bg-red-200 text-red-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'
                        ],
                        'withdrawn' => [
                        'class' => 'bg-gray-200 text-gray-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>'
                        ],
                        default => [
                        'class' => 'bg-gray-200 text-gray-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                        ]
                        };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeConfig['class'] }}">
                            {!! $badgeConfig['icon'] !!}
                            {{ $count }} {{ ucfirst($status) }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" class="edit-book-btn cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-book-id="{{ $book->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>

                    </div>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

@if($books->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-gray-600">
        Showing <span class="font-semibold text-gray-800">{{ $books->firstItem() }}-{{ $books->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $books->total() }}</span> books
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $books->currentPage() - 1 }}" {{ $books->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($books->getUrlRange(max(1, $books->currentPage() - 2), min($books->lastPage(), $books->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $books->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $books->currentPage() + 1 }}" {{ $books->currentPage() == $books->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
