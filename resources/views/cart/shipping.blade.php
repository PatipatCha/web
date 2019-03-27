@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    @include('cart.step', ['current' => 3])



    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (count($errors) > 0)
                    <br />
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!--================================================== -->
    @if (!$cart_empty)
        <cart-shipping
                v-bind:cart="{{ htmlspecialchars(json_encode($cart)) }}"
                v-bind:cart-arr="{{ htmlspecialchars(json_encode($cart_arr)) }}"
                v-bind:cart-summary="{{ json_encode($cart_summary) }}"
                v-bind:current-store="{{ json_encode($store) }}"
                v-bind:promotions="{{ htmlspecialchars(json_encode($cart_promotions)) }}"
                submit-url="{{ route('carts.shipping.post') }}"
                v-bind:member-address="{{ json_encode($member_address) }}"
                v-bind:shipping-addresses="{{ json_encode($shipping_addresses) }}"
                shipping-form-url="{{ route('carts.shipping') }}"
                :delivery-dates="{{ json_encode($delivery_dates) }}"
                v-bind:show-item-status="false"
                v-bind:config-date="{!! htmlspecialchars(json_encode($dateConfig)) !!}"
        ></cart-shipping>
    @endif

    <!--================================================== -->
@endsection

