{{-- filepath: c:\Users\Angela\library_management_system\resources\views\partials\librarian\semesters-table.blade.php --}}
<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg table-fixed">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-12">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs w-40">Name</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-40">Start Date</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-40">End Date</th>
                <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-28">Status</th>
                <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-40">Actions</th>
            </tr>
        </thead>
        <tbody id="semesters-table-body" class="bg-white divide-y divide-gray-100">
            @forelse($semesters as $index => $semester)
            <tr class="hover:bg-gray-50 transition-colors relative {{ $semester->status === 'active' ? 'border-l-4 border-l-accent bg-accent/5' : '' }}">
                <td class="py-3 px-4 text-gray-600 font-medium">{{ ($semesters->currentPage() - 1) * $semesters->perPage() + $index + 1 }}</td>
                <td class="py-3 px-4">
                    <p class="font-semibold text-gray-900">{{ $semester->name }}</p>
                </td>
                <td class="py-3 px-4 text-gray-700 text-sm">{{ \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') }}</td>
                <td class="py-3 px-4 text-gray-700 text-sm">{{ \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') }}</td>
                <td class="py-3 px-4 text-center">
                    @if($semester->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                        <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                        Active
                    </span>
                    @elseif($semester->status === 'inactive')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Inactive
                    </span>
                    @elseif($semester->status === 'ended')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                        <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                        Ended
                    </span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        @if($semester->status === 'ended')
                        <!-- Disabled Edit Button -->
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed opacity-50 transition text-xs font-medium" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>
                            <div class="hidden sm:block absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                                Cannot edit ended semester
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>

                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed opacity-50 transition text-xs font-medium" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Deactivate
                            </button>
                            <div class="hidden sm:block absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                                Cannot modify ended semester
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>

                        @elseif($semester->status === 'inactive')
                        <!-- Active Edit Button -->
                        <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-secondary-light hover:bg-secondary-light/90 text-white rounded-lg cursor-pointer transition text-xs font-medium shadow-sm edit-semester" data-semester-id="{{ $semester->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>

                        @if($activeSemester)
                        <!-- Active semester exists - disable activate -->
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed opacity-50 transition text-xs font-medium" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Activate
                            </button>
                            <div class="hidden sm:block absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                                Deactivate current semester first
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                        @else
                        <!-- No active semester - can activate -->
                        <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg cursor-pointer transition text-xs font-medium shadow-sm activate-semester" data-semester-id="{{ $semester->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Activate
                        </button>
                        @endif
                        @else
                        <!-- Active semester - cannot edit, can deactivate -->
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed opacity-50 transition text-xs font-medium" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>
                            <div class="hidden sm:block absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                                Cannot edit active semester
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>

                        <button class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg cursor-pointer transition text-xs font-medium shadow-sm deactivate-semester" data-semester-id="{{ $semester->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Deactivate
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 font-medium mb-1">No semesters found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($semesters->hasPages())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-gray-600">
        Showing <span class="font-semibold text-gray-800">{{ $semesters->firstItem() }}-{{ $semesters->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $semesters->total() }}</span> semesters
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $semesters->currentPage() - 1 }}" {{ $semesters->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($semesters->getUrlRange(max(1, $semesters->currentPage() - 2), min($semesters->lastPage(), $semesters->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $semesters->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $semesters->currentPage() + 1 }}" {{ $semesters->currentPage() == $semesters->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
