<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower Name</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">ID Number</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Role</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Dept/Year</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrowed</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Penalties</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Reservations</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
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
            <tr type="button" data-user-id="{{ $user->id }}" class="open-borrower-modal hover:bg-accent/5 transition-colors cursor-pointer active:hover:bg-accent/10 active:scale-[0.99]">
                <td class="px-4 py-3 text-gray-600">
                    {{ $users->firstItem() + $index }}
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 whitespace-nowrap">{{ $user->getFullnameAttribute() }}</span>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-600 font-mono text-xs">
                    @if($user->role === 'teacher')
                    {{ $user->teachers->employee_number }}
                    @elseif($user->role === 'student')
                    {{ $user->students->student_number }}
                    @endif
                </td>
                <td class="py-3 px-4">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->role === 'student' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap text-xs">
                    @if($user->role === 'teacher')
                    {{ $user->teachers->department->name }}
                    @elseif($user->role === 'student')
                    {{ $user->students->department->name }} - {{ $user->students->year_level }}
                    @endif
                </td>
                <td class="py-3 px-4">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                        {{$user->library_status}}
                    </span>
                </td>
                <td class="py-3 px-4">
                    @if ($user->borrowTransactions->count() === 1) <span class="text-gray-700 font-medium text-xs">
                        {{ $user->borrowTransactions->count() }} book
                    </span>
                    @else
                    <span class="text-gray-700 font-medium text-xs">
                        {{ $user->borrowTransactions->count() }} books
                    </span>
                    @endif
                </td>
                <td class="py-3 px-4 text-gray-500 text-xs">—</td>
                <td class="py-3 px-4 text-gray-500 text-xs">None</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

@if($users->isNotEmpty())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-gray-600">
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
