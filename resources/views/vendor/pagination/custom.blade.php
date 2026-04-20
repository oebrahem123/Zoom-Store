@if ($paginator->hasPages())
<div class="flex-c-m flex-w w-full p-t-38">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
    <span class="flex-c-m how-pagination1 trans-04 m-all-7 opacity-50">‹</span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="flex-c-m how-pagination1 trans-04 m-all-7">‹</a>
    @endif


    {{-- Page Numbers --}}
    @foreach ($elements as $element)

    {{-- "..." --}}
    @if (is_string($element))
    <span class="m-all-7">{{ $element }}</span>
    @endif

    {{-- Array --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)

    @if ($page == $paginator->currentPage())
    <span class="flex-c-m how-pagination1 trans-04 m-all-7 active-pagination1">
        {{ $page }}
    </span>
    @else
    <a href="{{ $url }}" class="flex-c-m how-pagination1 trans-04 m-all-7">
        {{ $page }}
    </a>
    @endif

    @endforeach
    @endif

    @endforeach


    {{-- Next --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="flex-c-m how-pagination1 trans-04 m-all-7">›</a>
    @else
    <span class="flex-c-m how-pagination1 trans-04 m-all-7 opacity-50">›</span>
    @endif

</div>
@endif