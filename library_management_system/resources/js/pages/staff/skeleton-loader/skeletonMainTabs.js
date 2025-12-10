export function borrowersSkeletonLoader() {
    return `
        <table class="w-full text-sm rounded-lg table-fixed">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-14">No.</th>
                    <th class="py-3 text-center font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-10"></th>
                    <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-50">Borrower Name</th>
                    <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-60">Email</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Dept/Year</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-36">Status</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-40">Active Borrows</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-46">Reservations</th>
                    <th class="py-3 px-4 text-left text-gray-700 uppercase tracking-wider whitespace-nowrap w-36">Actions</th>
                </tr>
            </thead>

            <tbody id="members-skeleton-body" class="bg-white divide-y divide-gray-100">
                ${Array.from({ length: 8 }).map(() => `
                    <tr>
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
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}


export function activeBorrowsSkeletonLoader() {
    return  `
        <table class="w-full text-sm rounded-lg">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Copy No.</th>
                        <th class="py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                        <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrowed At</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Due Date</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-20">Actions</th>
                    </tr>
                </thead>
                <tbody id="active-borrows-skeleton-body" class="bg-white divide-y divide-gray-100">
                    ${Array.from({ length: 8 }).map(() => `
                        <tr>
                            <td class="py-3 px-4"><div class="h-5 w-8 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4 flex items-center gap-3">
                                <div class="w-12 h-16 rounded-md bg-gray-200 animate-pulse"></div>
                                <div class="flex flex-col gap-1">
                                    <div class="h-5 w-32 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </td>
                            <td class="py-3 px-4"><div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-2 text-center"><div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse mx-auto"></div></td>
                            <td class="py-3 pl-2 pr-4">
                                <div class="h-5 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                                <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                            </td>
                            <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4"><div class="h-5 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4"><div class="h-8 w-24 bg-gray-200 rounded-lg animate-pulse mx-auto"></div></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
}

export function unpaidPenaltiesSkeletonLoader() {
    return `
        <table class="w-full text-sm rounded-lg">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Reason</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Amount</th>
                    <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Issued at</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Returned at</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-20">Actions</th>
                </tr>
            </thead>
            <tbody id="unpaid-penalties-skeleton-body" class="bg-white divide-y divide-gray-100">
                ${Array.from({ length: 8 }).map(() => `
                    <tr>
                        <td class="py-3 px-4"><div class="h-5 w-8 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-2 text-center"><div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse mx-auto"></div></td>
                        <td class="py-3 pl-2 pr-4">
                            <div class="h-5 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                        </td>
                        <td class="py-3 px-4"><div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-5 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 pl-2 pr-4"><div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4 flex items-center gap-3">
                            <div class="w-12 h-16 rounded-md bg-gray-200 animate-pulse"></div>
                            <div class="flex flex-col gap-1">
                                <div class="h-5 w-32 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </td>
                        <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-8 w-24 bg-gray-200 rounded-lg animate-pulse mx-auto"></div></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

export function queueReservationsSkeletonLoader() {
    return `
        <table class="w-full text-sm rounded-lg">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Queue Position</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Pickup Deadline</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Reserved at</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody id="queue-reservations-skeleton-body" class="bg-white divide-y divide-gray-100">
                ${Array.from({ length: 8 }).map(() => `
                    <tr>
                        <td class="py-3 px-4"><div class="h-5 w-8 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-2 text-center"><div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse mx-auto"></div></td>
                        <td class="py-3 pl-2 pr-4">
                            <div class="h-5 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                        </td>
                        <td class="py-3 px-4 flex items-center gap-3">
                            <div class="w-12 h-16 rounded-md bg-gray-200 animate-pulse"></div>
                            <div class="flex flex-col gap-1">
                                <div class="h-5 w-32 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </td>
                        <td class="py-3 px-4"><div class="h-5 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                        <td class="py-3 px-4"><div class="h-8 w-24 bg-gray-200 rounded-lg animate-pulse mx-auto"></div></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}