@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="sitemap-box">
            <div class="sitemap-item">
                {{-- Business --}}
                <div class="topic-text">
                    <b>{{ trans('frontend.all_business') }}</b>
                </div>
                @foreach ($business as $category)
                <div class="sitemap-box-list">
                    <div class="sitemap-topic">
                        <a href="{{ route('categories.show', ['slug' => $category['slug']]) }}"><b>{{ $category['name'] }}</b></a>
                    </div>

                    @if (! empty($category['children']['data']))
                        <div class="sitemap-text-box">
                            @foreach ($category['children']['data'] as $child)
                                <div class="col-lg-3 col-md-3 col-sm-3 sitemap-text no-padding">
                                    <a href="{{ route('categories.show', ['slug' => $child['slug']]) }}">{{ $child['name'] }}</a><br>
                                </div>
                            @endforeach
                            <div class="clearfix"></div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="padding-top15"></div>

            <div class="sitemap-item">
                {{-- Category --}}
                <div class="topic-text">
                    <b>{{ trans('frontend.all_categories') }}</b>
                </div>
                @foreach ($categories as $category)
                <div class="sitemap-box-list">
                    <div class="sitemap-topic">
                        <a href="{{ route('categories.show', ['slug' => $category['slug']]) }}"><b>{{ $category['name'] }}</b></a>
                    </div>

                    @if (! empty($category['children']['data']))
                        <div class="sitemap-text-box">
                            @foreach ($category['children']['data'] as $child)
                                <div class="col-lg-3 col-md-3 col-sm-3 sitemap-text no-padding">
                                    <a href="{{ route('categories.show', ['slug' => $child['slug']]) }}">{{ $child['name'] }}</a><br>
                                </div>
                            @endforeach
                            <div class="clearfix"></div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection