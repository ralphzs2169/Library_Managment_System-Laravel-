<x-table-section-layout :title="'Books List'" :total-items="'books->total()'">

    <div id="books-table-container">
        <table class="min-w-full text-sm text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold sticky top-0 z-10">
                <tr>
                    <th class="px-2 py-3 border-b w-10">No.</th>
                    <th class="px-4 py-3 border-b w-20">Cover</th>
                    <th class="px-4 py-3 border-b w-1/3">Title</th>
                    <th class="px-4 py-3 border-b w-1/5">Author</th>
                    <th class="px-4 py-3 border-b w-1/6">Category</th>
                    <th class="px-4 py-3 border-b w-1/6">Copies</th>
                    <th class="px-4 py-3 border-b w-1/5">Status</th>
                    <th class="px-4 py-3 border-b w-1/5">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-800">


                @if ($books->isEmpty())
                <tr>
                    <td colspan="7" class="p-0">
                        <div class="h-80 flex items-center justify-center text-gray-500">
                            No books available.
                        </div>
                    </td>
                </tr>
                @else

                @php
                $rowNumber = ($books->currentPage() - 1) * $books->perPage();
                @endphp

                @foreach ($books as $book)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-600">{{ ++$rowNumber }}</td>
                    <td class="px-4 py-3 text-green-600 font-semibold">
                        <img src="{{ asset('storage/' .  $book->cover_image) }}" class="w-12 h-14" alt="" />
                    </td>
                    <td class="px-4 py-3">{{ $book->title }}</td>
                    <td class="px-4 py-3 text-gray-500">

                        {{ $book->author->firstname }}
                        {{ $book->author->middle_initial ? ' ' . $book->author->middle_initial . '.' : '' }}
                        {{ $book->author->lastname }}

                    </td>
                    <td class="px-4 py-3">{{ $book->genre->category->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $book->copies->where('status', 'available')->count() . '/' . $book->copies->count() }}</td>
                    <td class="px-4 py-3">
                        @php
                        $statuses = $book->copies->groupBy('status')->map->count();
                        @endphp

                        @if($statuses->count() === 1)
                        All {{ ucfirst($statuses->keys()->first()) }}
                        @else

                        @foreach($statuses as $status => $count)
                        {{ $count }} {{ $status }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach

                        @endif
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <button type="button" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs transition edit-book-btn" data-book-id="{{ $book->id }}">
                            Edit
                        </button>
                        <a href="" class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 text-xs transition">
                            View More
                        </a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>


    <div id="edit-book-form-container" data-book-id="" class="hidden">
        @include('pages.librarian.edit-book-form')
    </div>

    @vite('resources/js/pages/librarian/booksList.js')
</x-table-section-layout>
