@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">

        <div class="col-lg-12 register-bg register-box">

            <facebook-register
                v-bind:lang="{{ json_encode([
                    'register_error_message' => __('frontend.register_error_message'),
                    'register_submit_please_wait_message' => __('frontend.register_submit_please_wait_message'),
                    'next' => __('frontend.next'),
                    'verify' => __('frontend.verify'),
                    'required' => __('frontend.required'),
                    'email_or_mobile' => __('frontend.email_or_mobile'),
                    'invalid_phone_format' => __('frontend.invalid_phone_format'),
                    'invalid_email_format' => __('frontend.invalid_email_format'),
                    'confirm' => __('frontend.confirm'),
                    'could_not_registered' => __('frontend.could_not_registered'),
                    'makro_id_card' => __('frontend.makro_id_card'),
                    'please_enter_a_makro_card_id' => __('frontend.please_enter_a_makro_card_id'),
                    'please_verify_makro_card_id' => __('frontend.please_verify_makro_card_id'),
                    'please_wait' => __('Please wait...'),
                    'customer_info' => __('frontend.customer_info'),
                    'customer_name' => __('frontend.customer_name'),
                    'invalid_makro_card_id' => __('frontend.invalid_makro_card_id'),
                    'apply_new_makro_membership' =>__('frontend.apply_new_makro_membership'),
                    'ok' => __('OK'),
                    'confirm_verify_makro_card_id' => __('frontend.confirm_verify_makro_card_id'),
                    'accept' => __('frontend.accept'),
                    'term_and_condition' => __('frontend.term_and_condition'),
                    'please_check_your_username' =>  __('frontend.please_check_your_username'),
                    'verifying' =>  __('frontend.verifying'),
                    'check_username' => __('frontend.check_username'),
                    'cloud_not_verify_username' => __('frontend.cloud_not_verify_username'),
                    'please_try_again'=> __('frontend.please_try_again'),
                    'username_is_not_available' => __('frontend.username_is_not_available'),
                    'username_is_available' => __('frontend.username_is_available'),
                    'facebook_not_pull_email' => __('frontend.facebook_not_pull_email')
                ]) }}"
                v-bind:direct-available="{{ $available ? 1 : 0 }}"
                v-bind:direct-username-not-available="{{ $username_not_available ? 1 : 0 }}"
                v-bind:facebook-not-pull-email="{{ empty($facebook_user_email) ? 0 : 0 }}"
                success-url="{{ route('members.register.facebook.success') }}"
                terms-conditions="{{ $terms_conditions }}"
                makro-stores="{{ json_encode($available_register_stores) }}"
            ></facebook-register>

        </div>
        <div class="clearfix"></div>

    </div>

    @include('member.partials.register-modal')
@endsection


@section('script-header')
    <script>
        var MEMBER_REGISTER_FACEBOOK_DATA = {
            'facebook_user_id': '{{ $facebook_user_id }}',
            'facebook_user_email': '{{ $facebook_user_email }}',
            'success_url': '{{ route('members.register.success') }}',
            'lang': {
                'register_error_message': '{{ __('frontend.register_error_message')  }}',
                'register_submit_please_wait_message': '{{ __('frontend.register_submit_please_wait_message') }}',
                'next': '{{ __('Next') }}',
            }
        }
    </script>
@endsection

@section('script')

@endsection