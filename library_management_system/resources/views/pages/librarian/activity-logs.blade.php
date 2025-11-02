<x-table-section-layout :title="'Books List'" :total-items="'books->total()'">
    <table class="min-w-full text-sm text-left border-collapse">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold sticky top-0 z-10">
            <tr>
                <th class="px-4 py-3 border-b">No.</th>
                <th class="px-4 py-3 border-b">Action</th>
                <th class="px-4 py-3 border-b">Entity</th>
                <th class="px-4 py-3 border-b">Timestamp</th>
                <th class="px-4 py-3 border-b">Details</th>
                <th class="px-4 py-3 border-b">Performed by</th>
                <th class="px-4 py-3 border-b">Role</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-800">


            @if ($activity_logs->isEmpty())
            <tr>
                <td colspan="7" class="p-0">
                    <div class="h-80 flex items-center justify-center text-gray-500">
                        No activity logs available.
                    </div>
                </td>
            </tr>
            @else
            @php
            $rowNumber = ($activity_logs->currentPage() - 1) * $activity_logs->perPage();
            @endphp
            @foreach ($activity_logs as $log)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-600">{{ ++$rowNumber }}</td>
                <td class="px-4 py-3 text-green-600 font-semibold">{{ $log->action }}</td>
                <td class="px-4 py-3">{{ $log->entity_type }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $log->created_at }}</td>
                <td class="px-4 py-3">{{ $log->details }}</td>
                <td class="px-4 py-3">{{ optional($log->user)->fullname ?? 'System' }}</td>
                <td class="px-4 py-3 capitalize">{{ optional($log->user)->role ?? 'none' }}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</x-table-section-layout>
