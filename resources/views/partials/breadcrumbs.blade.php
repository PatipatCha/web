@if ($breadcrumbs)
    @foreach ($breadcrumbs as $breadcrumb)
        @if (! $breadcrumb->last)
            <a href="{{ $breadcrumb->url }}" title="{{ $breadcrumb->title }}">{{ $breadcrumb->title }}</a>
            <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
        @else
            <span style="color: #333333;">{{ $breadcrumb->title }}</span>
        @endif
    @endforeach
@endif