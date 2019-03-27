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
                <b>{{ trans('frontend.404_error_title') }}</b>
            </div>
            <div class="text-404error2">
                {!! $exception->getMessage() ? $exception->getMessage() : trans('frontend.404_error_description') !!}
            </div>

            <div class="error-btn-box">
                <a href="{{ route('home.index') }}"><button class="btn-back-to-home" type="">{{ trans('frontend.back_to_home') }}</button></a>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection