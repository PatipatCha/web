@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="col-lg-12 forget-pass-bg forget-pass-box">
            <div class="forget-pass-area">
                @if ($errors->count())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $message)
                            <p>{{ $message }}</p>
                        @endforeach
                    </div>

                @endif
                <forgot-password csrf="{{ csrf_token() }}" old-username="{{ old('username') }}"></forgot-password>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>

    @include('member.partials.register-modal')
@endsection



@section('script-header')
    <script>
        var MEMBER_DATA = {
            'success_url': '{{ route('members.register.success') }}',
            'activate_email_waiting_url': '{{ route('members.register.email.activate.waiting') }}',
            'activate_otp_url': '{{ route('members.register.activate.otp.get') }}',
            'lang': {
                'verify': '{{ __('frontend.verify') }}',
                'please_wait': '{{ __('Please wait...') }}',
                'confirm': '{{ __('frontend.confirm') }}',
                'ok': '{{ __('frontend.ok') }}',
                'next': '{{ __('frontend.next') }}',
                'required': '{{ __('frontend.required') }}',
                'email_or_mobile': '{{ __('frontend.email_or_mobile') }}',
                'password': '{{ __('frontend.password') }}',
                'confirm_password': '{{ __('frontend.confirm_password') }}',
                'makro_id_card': '{{ __('frontend.makro_card_id') }}',
                'apply_new_makro_membership': '{{ __('frontend.apply_new_makro_membership') }}',
                'accept': '{{ __('frontend.accept') }}',
                'term_and_condition': '{!!  __('frontend.term_and_condition') !!}',
                'password_not_match': '{{ __('frontend.password_not_match') }}',
                'please_enter_a_makro_card_id': '{{ __('frontend.please_enter_a_makro_card_id') }}',
                'please_verify_makro_card_id': '{{ __('frontend.please_verify_makro_card_id') }}',
                'register_submit_please_wait_message': '{{ __('frontend.register_submit_please_wait_message') }}',
                'customer_info': '{{ __('frontend.customer_info') }}',
                'customer_name': '{{ __('frontend.customer_name') }}',
                'confirm_button': '{{ __('frontend.confirm_button')  }}',
                'invalid_phone_format': '{{ __('frontend.invalid_phone_format') }}',
                'invalid_email_format': '{{ __('frontend.invalid_email_format') }}',
                'invalid_makro_card_id': '{{ __('frontend.invalid_makro_card_id') }}',
                'required': '{{ __('frontend.required') }}',
                'register_error_message': '{{ __('frontend.register_error_message')  }}',
                'confirm_verify_makro_card_id': '{{ __('frontend.confirm_verify_makro_card_id') }}',
                'password_requirement': '{!!  __('frontend.password_requirement') !!}',
                'login_with_facebook': '{!!  __('frontend.login_with_facebook') !!}',
                'create_username_and_password_with_facebook_connect': '{{ __('frontend.create_username_and_password_with_facebook_connect') }}',
                'register_with_facebook': '{{ __('frontend.register_with_facebook') }}',
                'register_form': '{{ __('frontend.register_form') }}',
                'please_check_your_username': '{{ __('frontend.please_check_your_username') }}',
                'verifying': '{{ __('frontend.verifying') }}',
                'check_username': '{{ __('frontend.check_username') }}',
                'cloud_not_verify_username': '{{ __('frontend.cloud_not_verify_username') }}',
                'please_try_again': '{{ __('frontend.please_try_again') }}',
                'username_is_not_available': '{{ __('frontend.username_is_not_available') }}',
                'username_is_available': '{{ __('frontend.username_is_available') }}'
            }
        };
    </script>

@endsection

@section('script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection