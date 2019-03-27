@if ($paginator->hasPages())
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <a href="#" class="disabled">
            <img src="{{ url('assets/images/icon-Slides5.png') }}">
        </a>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev">
            <img src="{{ url('assets/images/icon-Slides5.png') }}">
        </a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <a href="#" class="disabled">{{ $element }}</a>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <a href="#" class="active">{{ $page }}</a>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next">
            <img src="{{ url('assets/images/icon-Slides6.png') }}">
        </a>
    @else
        <a href="#" class="disabled">
           <img src="{{ url('assets/images/icon-Slides6.png') }}">
        </a>
    @endif
@endif
