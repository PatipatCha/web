@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container">
        <div class="step-box">
            <ol class="cd-multi-steps text-bottom count">
                <li class="visited"><a href="in_cart_eng.html">In Cart</a></li>
                <li class="visited"><em>Payment & PickUp</em></li>
                <li class="current"><em>Thank You</em></li>
            </ol>
        </div>
    </div>

    <!--================================================== -->

    <div class="container margin-bottom">
        <div class="col-lg-12 cart-payment-bg pay-pickup-box">
            <div class="text-center">
                Connecting...
                <form action="" method="" id="form-gateway">
                    <input type="hidden" name="payment_id" value="{{ $order['order_no'] }}" />
                </form>
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