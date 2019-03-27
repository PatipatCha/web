@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <!--================================================== -->
    <div class="container margin-bottom">
        <div class="col-lg-12 cart-payment-bg pay-thank-box">

            <div class="thank-box">
                <img class="icon-finish-thank" src="{{ asset('assets/images/icon-fail.png') }}">
                <div class="text-thank" id="lbl_payment_fail">
                    <b>
                        <span style="color: #ff0000;">{{ __('frontend.your_order_has_been_fail')  }}</span><br>
                        {{ __('frontend.an_error_occurred_during_payment_please_pay_again') }}
                    </b>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ route('home.index') }}" class="btn btn-block btn-continue-shopping" id="btn_go_to_shopping_popup">{{ __('frontend.go_to_shopping') }}</a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('carts.index') }}" class="btn btn-block btn-continue" id="btn_retry_payment">{{ __('frontend.retry_payment') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--================================================== -->
@endsection

@section('script-before')
    <script>
        setTimeout(function(){
            //location.href = '{{ route('carts.index') }}';
        }, 3000);
    </script>
@endsection