<div class="overflow-x-auto rounded-lg border border-gray-200">

    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                <th class="py-3 text-center font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-1/6">Borrower Name</th>
                <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-1/6">Email</th>
                {{-- <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-1/6">Contact</th> --}}
                {{-- <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">ID Number</th> --}}
                {{-- <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Role</th> --}}
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Dept/Year</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-20">Active Borrows</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-46 ">Reservations</th>
                <th class="py-3 px-4 text-left text-gray-700 uppercase tracking-wider whitespace-nowrap w-46 ">Actions</th>
            </tr>
        </thead>
        <tbody id="members-skeleton-body" class="bg-white divide-y divide-gray-100">
            <tr>
                @for ($i = 0; $i < 8; $i++) <tr>
                    <td class="px-4 py-3">
                        <div class="h-5 w-8 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="py-2 text-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse mx-auto"></div>
                    </td>
                    <td class="py-3 pl-2 pr-4">
                        <div class="h-5 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                        <div class="h-4 w-20 bg-gray-200 rounded animate-pulse mb-1"></div>
                        <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="pl-2 pr-4 px-4">
                        <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="h-5 w-16 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="h-4 w-12 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="h-8 w-24 bg-gray-200 rounded-lg animate-pulse mx-auto"></div>
                    </td>
                    @endfor
            </tr>
        </tbody>

        <tbody id="members-real-table-body" class="bg-white divide-y divide-gray-100">
            </tr>
            @if($users->isEmpty())
            <tr>
                <td colspan="9" class="py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <img src="{{ asset('build/assets/icons/no-members-found.svg') }}" alt="No Members Found">
                        <p class="text-gray-500 text-lg font-medium mb-2">No members found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @else
            @foreach($users as $index => $user)
            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                <td class="px-4 py-3 text-black">
                    {{ $users->firstItem() + $index }}
                </td>
                <td class="py-2 text-center">
                    <!-- Borrower initials icon -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($user->firstname,0,1)) }}{{ strtoupper(substr($user->lastname,0,1)) }}
                    </div>
                </td>

                {{-- Borrower Name Column --}}
                <td class="py-3 pl-2 pr-4 whitespace-nowrap ">
                    <div class="font-medium text-gray-700">{{ $user->full_name }}
                    </div>


                    <div class="text-gray-500 font-mono text-xs">
                        @if($user->role === 'teacher')
                        {{ $user->teachers->employee_number }}
                        @elseif($user->role === 'student')
                        {{ $user->students->student_number }}
                        @endif
                    </div>

                    <div class="text-sm text-gray-500">
                        @php
                        if ($user->role === 'student') {
                        $roleBadgeClass = 'bg-blue-100 text-blue-700 border border-blue-100';
                        $iconHTML = '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">';
                        } elseif ($user->role === 'teacher') {
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
                            {{ ucfirst($user->role) }}
                        </span></div>

                </td>
                {{-- Email Column --}}
                <td class="pl-2 pr-4 px-4 text-black ">
                    {{ $user->email }}
                </td>


                {{-- Department/Year Level Column --}}
                <td class=" py-3 px-4 text-black whitespace-nowrap text-xs">
                    @if($user->role === 'teacher')
                    {{ $user->teachers->department->name }}
                    @elseif($user->role === 'student')
                    {{ $user->students->department->name }} - {{ $user->students->year_level }}
                    @endif
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
                    @elseif ($user->library_status === 'suspended')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-semibold bg-red-100 text-red-700 border w-full border-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Suspended
                    </span>
                    @elseif ($user->library_status === 'cleared')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-semibold bg-gray-100 text-black border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Cleared
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

                {{-- Active Borrowings Column --}}
                <td class="py-3 px-4">
                    @if ($user->active_borrowings_count === 1) <span class="text-gray-700 font-medium text-xs">
                        {{ $user->active_borrowings_count }} book
                    </span>
                    @else
                    <span class="text-gray-700 font-medium text-xs">
                        {{ $user->active_borrowings_count }} books
                    </span>
                    @endif
                </td>

                {{-- Pending Reservation Column Count --}}
                <td class="py-3 px-4 align-middle">
                    @php
                    $pendingReservations = $user->pendingReservations()->count();
                    $readyReservations = $user->readyReservations ? $user->readyReservations()->count() : 0;
                    @endphp

                    <div class="flex flex-col gap-1 min-h-[48px] justify-center">
                        @if ($pendingReservations > 0)
                        @php
                        $pendingBadgeConfig = [
                        'class' => 'bg-yellow-200 text-yellow-800',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>'
                        ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full w-full text-xs {{ $pendingBadgeConfig['class'] }}">
                            {!! $pendingBadgeConfig['icon'] !!}
                            {{ $pendingReservations }} Pending
                        </span>
                        @endif

                        @if ($readyReservations > 0)
                        @php
                        $readyBadgeConfig = [
                        'class' => 'bg-green-200 text-green-700',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>'
                        ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full w-full text-xs {{ $readyBadgeConfig['class'] }}">
                            {!! $readyBadgeConfig['icon'] !!}
                            {{ $readyReservations }} Ready for Pickup
                        </span>
                        @endif

                        @if ($pendingReservations === 0 && $readyReservations === 0)
                        <span class="text-gray-500 text-xs">None</span>
                        @endif
                    </div>
                </td>

                {{-- Actions Column --}}
                <td class="py-3 px-4 align-middle">
                    <button data-user-id="{{ $user->id }}" class="open-borrower-modal  transition-all flex items-center justify-center gap-2 w-30 py-1 rounded-lg bg-accent text-white shadow-sm hover:bg-accent/90 cursor-pointer">
                        <span class="flex items-center justify-center">
                            <!-- Modern profile icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" fill="none" />
                                <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-3.314 3.134-6 7-6s7 2.686 7 6" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold">View Profile</span>
                    </button>
                </td>

            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

@if($users->isNotEmpty())
<div class=" flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-black">
        Showing <span class="font-semibold text-gray-800">{{ $users->firstItem() }}-{{ $users->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $users->total() }}</span> borrowers
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $users->currentPage() - 1 }}" {{ $users->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $users->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $users->currentPage() + 1 }}" {{ $users->currentPage() == $users->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
