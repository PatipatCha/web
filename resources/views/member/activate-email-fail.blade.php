@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">

        <div class="col-lg-12 register-bg register-box">
            <div class="register-area2">
                <div class="thank-you-register-box">
                    <img class="icon-finish-thank" src="{{ asset('assets/images/icon-fail.png') }}">
                </div>

                <div class="text-thank-you-register">
                    @if (! empty($errorCode) && $errorCode == 200006)
                        {!! trans('frontend.activation_code_was_expire') !!}

                        <br>
                        <a href="{{ route('members.register.activate.resend.get') }}" title="{{ __('frontend.go_to_activation_resend_page') }}" class="btn btn-default">{{ __('frontend.go_to_activation_resend_page') }}</a>
                    @else
                        {!! trans('frontend.activate_email_fail_text') !!}
                    @endif
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

    </div>

    @include('member.partials.register-modal')
@endsection

@section('script')

@endsection