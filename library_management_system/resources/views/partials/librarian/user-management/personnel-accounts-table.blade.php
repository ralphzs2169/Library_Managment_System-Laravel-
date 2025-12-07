<div class="overflow-x-auto rounded-lg border border-gray-200">

    <table class="w-full text-sm rounded-lg table-fixed">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10">ID</th>
                <th class="py-3 text-center font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10"></th>
                <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Full Name</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-34">Contact Number</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Email</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Role</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-24">Status</th>
                <th class="py-3 px-4 text-left text-gray-700 uppercase tracking-wider whitespace-nowrap w-26 ">Actions</th>
            </tr>
        </thead>

        <tbody id="members-real-table-body" class="bg-white divide-y divide-gray-100">
            @foreach($personnel as $index => $user)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="px-4 py-3 text-black">
                    {{ $user->id}}
                </td>
                <td class="py-2 text-center">
                    <!-- Initials icon -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($user->firstname,0,1)) }}{{ strtoupper(substr($user->lastname,0,1)) }}
                    </div>
                </td>

                {{-- Name Column --}}
                <td class="py-3 pl-2 pr-4 whitespace-nowrap ">
                    <div class="font-medium text-gray-700">{{ $user->firstname }} {{ $user->lastname }}</div>
                </td>

                {{-- Contact Number --}}
                <td class="py-3 px-4 text-black whitespace-nowrap">
                    <div class="text-gray-500">{{ $user->contact_number ?? 'N/A' }}</div>
                </td>

                {{-- Email --}}
                <td class="py-3 px-4 text-black whitespace-nowrap">
                    <div class="text-gray-500">{{ $user->email }}</div>
                </td>

                {{-- Role --}}
                <td class="py-3 px-4">
                    @php
                    if ($user->role === 'librarian') {
                    $roleBadgeClass = 'bg-purple-100 text-purple-700 border border-purple-100';
                    } elseif ($user->role === 'staff') {
                    $roleBadgeClass = 'bg-blue-100 text-blue-700 border border-blue-100';
                    } else {
                    $roleBadgeClass = 'bg-gray-100 text-gray-600 border border-gray-200';
                    }
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-semibold {{ $roleBadgeClass }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>

                {{-- Library Status Column --}}
                <td class="py-3 px-4">
                    @if ($user->library_status === 'active')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-semibold bg-green-200 text-green-700 border w-full border-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Active
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-semibold bg-gray-100 text-black border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                        </svg>
                        {{ ucfirst($user->library_status) }}
                    </span>
                    @endif
                </td>

                {{-- Actions Column --}}
                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                    <button data-user-id="{{ $user->id }}" class="open-personnel-modal cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manage
                    </button>
                </td>

            </tr>
            @endforeach

        </tbody>
    </table>
</div>

@if($personnel->isNotEmpty())
<div class=" flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $personnel->firstItem() }}-{{ $personnel->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $personnel->total() }}</span> personnel
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $personnel->currentPage() - 1 }}" {{ $personnel->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($personnel->getUrlRange(max(1, $personnel->currentPage() - 2), min($personnel->lastPage(), $personnel->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $personnel->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $personnel->currentPage() + 1 }}" {{ $personnel->currentPage() == $personnel->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
