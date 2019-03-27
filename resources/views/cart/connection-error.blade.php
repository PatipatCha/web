@extends('layouts.main')

@section('content')
    @include('cart.step', ['current' => $step])

    <!--================================================== -->

    <div class="container margin-bottom cart-result">

        <div class="col-lg-12 cart-payment-bg in-cart-box">
            <div class="error-box">
                <div class="icon-error-box">
                    <img class="icon-error" src="{{ asset('assets/images/icon-server-error.png') }}">
                </div>

                <div class="text-404error">
                    {{--{!! trans('frontend.there_was_a_connection_problem_please_retry_again') !!}--}}
                </div>

                <div class="text-404error2">
                    {!! trans('frontend.there_was_a_connection_problem_please_retry_again', ['code' => $code]) !!}
                </div>

                <div class="error-btn-box">
                    <a href="javascript:window.location.reload()"><button class="btn-back-to-home" type="">{{ trans('frontend.reload_page') }}</button></a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script-before')

@endsection