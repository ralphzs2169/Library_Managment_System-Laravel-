<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg table-fixed">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-12">ID</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10"></th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider w-50">Borrower</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider w-40">Requested By</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Semester</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-32">Date Requested</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-30">Status</th>
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
                        <div class="w-10 h-10 bg-gray-200 rounded-full animate-pulse"></div>
                        <div class="flex flex-col gap-1">
                            <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-3 w-20 bg-gray-100 rounded animate-pulse"></div>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-3 w-16 bg-gray-100 rounded animate-pulse mt-1"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-6 w-20 bg-gray-200 rounded-full animate-pulse"></div>
                </td>
                <td class="py-3 px-4">
                    <div class="h-8 w-20 bg-gray-200 rounded-lg animate-pulse"></div>
                </td>
                </tr>
                @endfor
        </tbody>

        {{-- Real Table --}}
        <tbody id="borrowing-records-real-table-body" class="bg-white divide-y divide-gray-100">
            @forelse ($clearances as $index => $clearance)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">{{ $clearance->id }}</td>

                <td>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($clearance->user->firstname,0,1)) }}{{ strtoupper(substr($clearance->user->lastname,0,1)) }}
                    </div>
                </td>
                {{-- Borrower Column --}}
                <td class="py-3 px-4 whitespace-nowrap">
                    <div class="flex items-center gap-3">

                        <div>
                            <div class="font-medium text-gray-700">{{ $clearance->user->fullname }}</div>
                            <div class="text-gray-500 font-mono text-xs">
                                @if($clearance->user->role === 'teacher')
                                {{ $clearance->user->teachers->employee_number ?? 'N/A' }}
                                @elseif($clearance->user->role === 'student')
                                {{ $clearance->user->students->student_number ?? 'N/A' }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                @php
                                if ($clearance->user->role === 'student') {
                                $roleBadgeClass = 'bg-blue-100 text-blue-700 border border-blue-100';
                                $iconHTML = '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">';
                                } elseif ($clearance->user->role === 'teacher') {
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
                                    {{ ucfirst($clearance->user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Requested By Column --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    @if($clearance->requestedBy)
                    @if($clearance->requestedBy->id === $clearance->user->id)
                    <span class="text-gray-500 italic text-xs">Self-Requested</span>
                    @else
                    <div class="font-medium text-sm">{{ $clearance->requestedBy->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ ucfirst($clearance->requestedBy->role) }}</div>
                    @endif
                    @else
                    <span class="text-gray-400 text-xs">N/A</span>
                    @endif
                </td>

                {{-- Semester Column --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{ $clearance->semester ? $clearance->semester->name : 'N/A' }}
                </td>

                {{-- Date Requested Column --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    {{ $clearance->created_at->format('M d, Y') }}
                    <div class="text-xs text-gray-500">{{ $clearance->created_at->format('h:i A') }}</div>
                </td>

                {{-- Status Column --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    @if ($clearance->status === 'approved')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700 border border-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Approved
                    </span>
                    @elseif ($clearance->status === 'rejected')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-200 text-red-700 border border-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Rejected
                    </span>
                    @elseif ($clearance->status === 'pending')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pending
                    </span>
                    @endif
                </td>

                {{-- Actions Column --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <button class="open-clearance-record-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-clearance-record='@json($clearance)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manage

                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-20 px-4 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <img class="w-24 h-24" src="{{ asset('build/assets/icons/no-results-found.svg') }}" alt="No Results Found">
                        <p class="text-gray-500 text-lg font-medium mb-2">No clearance records found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- @if($clearances->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $clearances->firstItem() }}-{{ $clearances->lastItem() }}</span>
of <span class="font-semibold text-gray-800">{{ $clearances->total() }}</span> records
</p>

<div class="flex items-center gap-2">
    <!-- Previous -->
    <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $clearances->currentPage() - 1 }}" {{ $clearances->onFirstPage() ? 'disabled' : '' }}>
        Previous
    </button>

    <!-- Page Numbers -->
    @foreach ($clearances->getUrlRange(max(1, $clearances->currentPage() - 2), min($clearances->lastPage(), $clearances->currentPage() + 2)) as $page => $url)
    <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $clearances->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
        {{ $page }}
    </button>
    @endforeach

    <!-- Next -->
    <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $clearances->currentPage() + 1 }}" {{ $clearances->currentPage() == $clearances->lastPage() ? 'disabled' : '' }}>
        Next
    </button>
</div>
</div>
@endif --}}
