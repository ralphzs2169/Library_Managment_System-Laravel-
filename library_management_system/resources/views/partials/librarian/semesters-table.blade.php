<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">No.</th>
                <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">Name</th>
                <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">Start Date</th>
                <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">End Date</th>
                <th class="text-center py-4 px-4 uppercase font-semibold text-gray-700">Status</th>
                <th class="text-center py-4 px-4 uppercase font-semibold text-gray-700">Actions</th>
            </tr>
        </thead>
        <tbody id="semesters-table-body">
            @forelse($semesters as $index => $semester)
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="py-4 px-4 text-gray-600">{{ ($semesters->currentPage() - 1) * $semesters->perPage() + $index + 1 }}</td>
                <td class="py-4 px-4">
                    <div class="font-semibold text-gray-900">{{ $semester->name }}</div>
                </td>
                <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') }}</td>
                <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') }}</td>
                <td class="py-4 px-4 text-center">
                    @if($semester->status === 'active')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                        Active
                    </span>
                    @elseif($semester->status === 'inactive')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                        Inactive
                    </span>
                    @elseif($semester->status === 'ended')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                        Ended
                    </span>
                    @endif
                </td>
                <td class="py-4 px-4">
                    <div class="flex items-center justify-center gap-2">
                        @if($semester->status === 'ended')
                        {{-- Ended semester - all actions disabled --}}
                        <div class="relative inline-block">
                            <button class="p-2 bg-gray-400 rounded-lg transition-colors group opacity-50 cursor-not-allowed" disabled>
                                <img src="{{ asset('build/assets/icons/edit.svg') }}" alt="Edit" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Cannot edit ended semester
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        <div class="relative inline-block">
                            <button class="p-2 bg-gray-400 rounded-lg transition-colors group opacity-50 cursor-not-allowed" disabled>
                                <img src="{{ asset('build/assets/icons/activate.svg') }}" alt="Activate" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Cannot activate ended semester
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        @elseif($semester->status === 'inactive')
                        {{-- Inactive semester - can edit and activate --}}
                        <div class="relative inline-block">
                            <button class="p-2 bg-blue-500 hover:bg-blue-600 rounded-lg transition-colors group edit-semester" data-semester-id="{{ $semester->id }}">
                                <img src="{{ asset('build/assets/icons/edit.svg') }}" alt="Edit" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Edit
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>

                        @if($activeSemester)
                        {{-- Active semester exists - disable activate --}}
                        <div class="relative inline-block">
                            <button class="p-2 bg-gray-400 rounded-lg transition-colors group opacity-50 cursor-not-allowed" disabled>
                                <img src="{{ asset('build/assets/icons/activate.svg') }}" alt="Activate" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Deactivate current semester first
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        @else
                        {{-- No active semester - can activate --}}
                        <div class="relative inline-block">
                            <button class="p-2 bg-green-500 hover:bg-green-600 rounded-lg transition-colors group activate-semester cursor-pointer" data-semester-id="{{ $semester->id }}">
                                <img src="{{ asset('build/assets/icons/activate.svg') }}" alt="Activate" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Activate
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        @endif
                        @else
                        {{-- Active semester - cannot edit, can deactivate --}}
                        <div class="relative inline-block">
                            <button class="p-2 bg-gray-400 rounded-lg transition-colors group opacity-50 cursor-not-allowed" disabled>
                                <img src="{{ asset('build/assets/icons/edit.svg') }}" alt="Edit" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Cannot edit active semester
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        <div class="relative inline-block">
                            <button class="p-2 bg-gray-600 hover:bg-gray-700 rounded-lg transition-colors group deactivate-semester cursor-pointer" data-semester-id="{{ $semester->id }}">
                                <img src="{{ asset('build/assets/icons/ban.svg') }}" alt="Deactivate" class="w-5 h-5">
                            </button>
                            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Deactivate
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <img src="{{ asset('build/assets/icons/no-semester.svg') }}" alt="No Semesters" class="w-8 h-8">
                    </div>
                    <p class="text-gray-600 font-medium mb-1">No semesters found</p>
                    <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
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
