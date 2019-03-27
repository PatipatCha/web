@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="col-lg-12 forget-pass-bg forget-pass-box">
            <div class="forget-pass-area">
                <div class="forget-pass-text-Topic">
                    <b>{{ trans('frontend.register_activate_resend') }}</b>
                </div>
                <form id="activate_by_otp_frm" action="{{ route('members.register.activate.resend.post') }}" method="post">
                    <div class="form-group">
                        <label for="">{{ trans('frontend.username') }}</label>
                        <input type="text" class="form-control" name="username" required="required" value="{{ old('username') }}" placeholder="sample@sample.com / 099xxxxxxx">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-info" type="submit">{{ trans('frontend.confirm') }}</button>
                        {!! csrf_field() !!}
                    </div>
                </form>

                @if (count($errors) > 0)
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    @include('member.partials.register-modal')
@endsection

@section('script')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection