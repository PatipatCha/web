@extends('layouts.main')

@section('content')
    @include('cart.step', ['current' => 1])

    <!--================================================== -->

    <div class="container margin-bottom cart-result">

        <div class="col-lg-12 cart-payment-bg in-cart-box step-1">
            @if ($errors->any() && !request()->session()->has('show_popup_error'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="list-style: none;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <cart-container
                    v-bind:cart_data="cart_data"
                    v-bind:cart-summary="cart_data.summary"
                    continue-url="{{ $continue_shopping_url }}"
                    :date-config="cart_data.dateConfig"

            >
            </cart-container>

            <cart-summary v-if="!isCartEmpty" :shipping-rates="{{ json_encode($shipping_rates) }}"></cart-summary>

            <div class="btn-box">
                <div class="row">
                    <div class="col-md-5 col-md-offset-7">
                        <div class="row" v-cloak>
                            <div class="col-sm-12 cart-remark margin-bottom-15" v-if="(!isMinimumCheckoutPassed && !isCartEmpty) || (!isMaximumCheckout && !isCartEmpty)">
                                <p class="remark" v-if="!isMinimumCheckoutPassed && !isCartEmpty">
                                    * {{ trans('frontend.minimum_checkout_warning_message', ['minimum' => number_format($data['minimumCheckout'], 2)]) }}
                                </p>
                                <p class="remark" v-if="!isMaximumCheckout && !isCartEmpty">
                                    * {{ trans('frontend.maximum_product_in_cart', ['items' => $data['maximumCheckout']]) }}
                                </p>
                            </div>
                            <div class="col-xs-12 col-sm-6" :class="isCartEmpty || !isMinimumCheckoutPassed || !isMaximumCheckout ? 'pull-right' : ''">
                                <a v-show="!isCartEmpty" href="{{ $continue_shopping_url }}" class="btn btn-continue-shopping btn-block" id="btn_go_to_shopping">{{ __('frontend.continue_shopping_again') }}</a>
                            </div>
                            <div class="col-sm-6">
                                <span v-bind:class="{'hide': isCartEmpty || !isMinimumCheckoutPassed || !isMaximumCheckout}" >
                                    <cart-continue-button button-text="{{ trans('frontend.continue_checkout_step1') }}" continue-url="{{ route('carts.checkout') }}" class="btn btn-primary btn-block"></cart-continue-button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script-before')
    <script>
        var CART_DATA = {
            data: {!!  json_encode($data['cart']) !!},
            meta: {!! json_encode($meta) !!},
            summary: {!! json_encode($data['summary']) !!},
            minimumCheckout: {!! json_encode($data['minimumCheckout']) !!},
            dateConfig: {!! json_encode($data['dateConfig']) !!},
            maximumCheckout: {!! json_encode($data['maximumCheckout']) !!},
        };
    </script>
@endsection