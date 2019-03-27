@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="col-lg-12 forget-pass-bg forget-pass-box">
            <div class="forget-pass-area">
                <div class="forget-pass-text-Topic">
                    <b>{{ trans('frontend.register_activate_phone') }}</b>
                </div>
                <div>
                    <br />
                    @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-ban"></i> {{ __('frontend.an_error_occurred')  }}</h4>
                            <ul style="list-style: none">
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{ trans('frontend.send_otp_already_information') }}
                </div>
                <form id="activate_by_otp_frm" action="{{ route('members.register.activate.otp.post') }}" method="post">
                    <div class="form-group">
                        <label for="">{{ trans('frontend.phone') }}</label>
                        <p class="form-control">
                            {{ $username }}
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ trans('frontend.otp') }} ({{ __('frontend.otp_ref') }}: {{ $otp_ref }})</label>
                        <input type="text" class="form-control" name="otp" required="required" placeholder="{{ trans('frontend.otp_field_placeholder') }}">
                    </div>
                    <div class="form-group">
                        {{ __('frontend.not_receive_otp') }} <a href="javascript:void(0)" id="resend_otp_btn"><i class="fa fa-refresh" aria-hidden="true"></i> <span class="text-danger">{{ __('frontend.resend_otp_again') }}</span></a>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" style="width: 100%">{{ trans('frontend.confirm') }}</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
            <div class="clearfix"></div>
        </div>

        <form id="resend_otp" action="{{ route('members.register.activate.resend.post') }}" method="post">
            <input type="hidden" name="username" value="{{ $username }}" />
            {!! csrf_field() !!}
        </form>
    </div>

    @include('member.partials.register-modal')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#activate_by_otp_frm').on('click', '#resend_otp_btn', function () {
                $('#resend_otp').submit();
            });
        });
    </script>
@endsection