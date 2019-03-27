@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">

        <div class="col-lg-12 register-bg register-box">
            <div class="register-area2">
                <div class="thank-you-register-box">
                    <img class="icon-newletter" src="{{ asset('assets/images/icon-newsletter.png') }}">
                </div>

                <div class="text-thank-you-register">
                    {!! trans('frontend.activate_email_waiting_text') !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

    </div>

    @include('member.partials.register-modal')
@endsection

@section('script')

@endsection