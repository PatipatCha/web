@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="nav-box-menu2"></div>

    <div class="container">
        <div class="error-box">
            <div class="icon-error-box">
                <img class="icon-error" src="{{ asset('assets/images/icon-error.png') }}">
            </div>

            <div class="text-404error">
                <b>{{ trans('frontend.search_not_found', ['keyword' => $keyword]) }}</b>
            </div>
            <div class="text-center">
                <?php
                    $suggest_keywords = '';

                    if (! empty($suggestKeywords)) {
                        if (! is_array($suggestKeywords)) {
                            $suggestKeywords = [$suggestKeywords];
                        }

                        $suggestKeywords = array_slice($suggestKeywords, 0, 5);

                        foreach ($suggestKeywords as $suggestKeyword) {
                            $suggest_keywords .= '<a href="' . route('search.index', ['q' => $suggestKeyword]) . '" class="text-suggestion">' . $suggestKeyword . '</a>, ';
                        }

                        $suggest_keywords = rtrim($suggest_keywords, ', ');
                    }
                ?>

                @if(empty($suggest_keywords))
                    {{ trans('frontend.please_check_search_keyword') }}
                @else
                    {!! trans('frontend.search_empty_keyword_suggestion', ['suggest_keywords' => $suggest_keywords]) !!}
                @endif
            </div>

            <div class="error-btn-box">
                <a href="{{ route('home.index') }}"><button class="btn-back-to-home" type="">{{ trans('frontend.back_to_home') }}</button></a>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection