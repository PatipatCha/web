@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container">
        <div class="step-box">
            <ol class="cd-multi-steps text-bottom count">
                <li class="visited"><a href="{{ route('carts.index') }}">In Cart</a></li>
                <li class="current"><em>Payment & PickUp</em></li>
                <li><em>Thank You</em></li>
            </ol>
        </div>
    </div>

    <!--================================================== -->
    @if (!$cart_empty)
        <cart-checkout v-bind:cart="{{ json_encode($cart) }}"
                       v-bind:cart-summary="{{ json_encode($cart_summary) }}"
                       v-bind:current-store="{{ json_encode($store) }}">
        </cart-checkout>
    @else
        <div class="container margin-bottom">
            <div class="row">
                <div class="col-md-4">

                </div>
                <div class="col-md-4">
                    <p>&nbsp;</p>
                    <div class="alert alert-warning">
                        Cart is empty!
                    </div>
                    <p class="text-center">
                        <button class="btn-next" type="button">Continue shopping</button>
                    </p>
                </div>
                <div class="col-md-4">

                </div>
            </div>
        </div>
    @endif
    <!--================================================== -->
@endsection