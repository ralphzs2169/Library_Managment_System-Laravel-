{{-- filepath: c:\Users\Angela\library_management_system\resources\views\partials\librarian\activity-logs-table.blade.php --}}
<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="w-full text-sm rounded-lg table-fixed">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-12">ID</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-28">Action</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-32">Entity</th>
                <th class="py-3 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-10"></th>
                <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-48">Performed By</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-36">Timestamp</th>
                <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-24">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @forelse($activityLogs as $index => $log)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">{{ $log->id }}</td>
                <td class="py-3 px-4">
                    @php
                    $actionConfig = match($log->action) {
                    'created' => ['class' => 'bg-green-100 text-green-700 border-green-200', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>'],
                    'updated' => ['class' => 'bg-blue-100 text-blue-700 border-blue-200', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>'],
                    'deleted' => ['class' => 'bg-red-100 text-red-700 border-red-200', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>'],
                    default => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" /></svg>']
                    };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $actionConfig['class'] }}">
                        {!! $actionConfig['icon'] !!}
                        {{ ucfirst($log->action) }}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800">{{ $log->entity_type }}</span>
                        <span class="text-xs text-gray-500">ID: {{ $log->entity_id }}</span>
                    </div>
                </td>
                <td class="py-2 text-center">
                    <!-- User initials icon -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($log->user->firstname, 0, 1)) }}{{ strtoupper(substr($log->user->lastname, 0, 1)) }}
                    </div>
                </td>
                <td class="py-3 pl-2 pr-4">
                    <div class="font-medium text-gray-700">{{ $log->user->full_name }}</div>
                    <div class="text-gray-500 font-mono text-xs">
                        @if($log->user->role === 'teacher')
                        {{ $log->user->teachers->employee_number ?? 'N/A' }}
                        @elseif($log->user->role === 'student')
                        {{ $log->user->students->student_number ?? 'N/A' }}
                        @else
                        {{ ucfirst($log->user->role) }}
                        @endif
                    </div>
                    <div class="text-sm">
                        @php
                        $roleBadgeClass = match($log->user->role) {
                        'student' => 'bg-blue-100 text-blue-700 border-blue-100',
                        'teacher' => 'bg-green-100 text-green-700 border-green-100',
                        'librarian' => 'bg-purple-100 text-purple-700 border-purple-100',
                        'staff' => 'bg-orange-100 text-orange-700 border-orange-100',
                        default => 'bg-gray-100 text-gray-600 border-gray-200'
                        };
                        $iconHTML = match($log->user->role) {
                        'student' => '<img src="/build/assets/icons/student-role-badge.svg" alt="Student Badge" class="w-3.5 h-3.5">',
                        'teacher' => '<img src="/build/assets/icons/teacher-role-badge.svg" alt="Teacher Badge" class="w-3.5 h-3.5">',
                        default => '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" /></svg>'
                        };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-1 rounded-xl text-xs {{ $roleBadgeClass }}">
                            {!! $iconHTML !!}
                            {{ ucfirst($log->user->role) }}
                        </span>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-600 text-xs">
                    <div class="font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                    <div class="text-gray-500">{{ $log->created_at->format('h:i A') }}</div>
                </td>
                <td class="py-3 px-4">
                    <button class="view-activity-details cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-medium transition shadow-sm" data-activity-log='@json($log)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Details
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 font-medium mb-1">No activity logs found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($activityLogs->hasPages())
<div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-gray-600">
        Showing <span class="font-semibold text-gray-800">{{ $activityLogs->firstItem() }}-{{ $activityLogs->lastItem() }}</span>
        of <span class="font-semibold text-gray-800">{{ $activityLogs->total() }}</span> logs
    </p>

    <div class="flex items-center gap-2">
        <!-- Previous -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $activityLogs->currentPage() - 1 }}" {{ $activityLogs->onFirstPage() ? 'disabled' : '' }}>
            Previous
        </button>

        <!-- Page Numbers -->
        @foreach ($activityLogs->getUrlRange(max(1, $activityLogs->currentPage() - 2), min($activityLogs->lastPage(), $activityLogs->currentPage() + 2)) as $page => $url)
        <button class="pagination-btn px-4 py-2 text-sm font-medium rounded-lg border transition {{ $activityLogs->currentPage() == $page ? 'bg-accent text-white border-accent shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}" data-page="{{ $page }}">
            {{ $page }}
        </button>
        @endforeach

        <!-- Next -->
        <button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" data-page="{{ $activityLogs->currentPage() + 1 }}" {{ $activityLogs->currentPage() == $activityLogs->lastPage() ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
@endif
