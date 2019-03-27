@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <!--================================================== -->

    <div class="container margin-bottom">
        <div class="col-lg-12 cart-payment-bg pay-pickup-box">
            <div class="text-center">
                {{ __('frontend.connecting') }}
                {!! $paymentConnecting !!}
            </div>
        </div>
    </div>

    <!--================================================== -->
@endsection

@section('script-before')
    <script>
        document.getElementById('form-gateway').submit()
    </script>
@endsection