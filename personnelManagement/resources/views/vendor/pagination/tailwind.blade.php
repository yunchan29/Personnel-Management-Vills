@if ($paginator->hasPages())
    <div class="flex justify-end items-center gap-2 mt-6">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button class="px-3 py-1 bg-gray-200 text-gray-400 rounded cursor-not-allowed" disabled>Previous</button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 bg-[#BD9168] text-white rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>
        @else
            <button class="px-3 py-1 bg-gray-200 text-gray-400 rounded cursor-not-allowed" disabled>Next</button>
        @endif

    </div>
@endif
