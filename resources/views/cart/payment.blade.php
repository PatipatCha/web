@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    @include('cart.step', ['current' => 2])

    <!--================================================== -->
    <cart-checkout
            v-bind:cart="{{ htmlspecialchars(json_encode($cart)) }}"
            v-bind:cart-summary="{{ json_encode($cart_summary) }}"
            v-bind:promotions="{{ json_encode($cart_promotions) }}"
            submit-url="{{ route('carts.checkout.post') }}"
            v-bind:member-addresses="{!! htmlspecialchars(json_encode($member_addresses)) !!}"
            v-bind:billing-address="{!! htmlspecialchars(json_encode($billing_address)) !!}"
            v-bind:tax-address="{!! htmlspecialchars(json_encode($tax_address)) !!}"
            shipping-form-url="{{ route('carts.shipping') }}"
            v-bind:member-profile="{!! htmlspecialchars(json_encode($profile)) !!}"
            payment-method="{{ $payment_method }}"
            v-bind:payment-methods="{{ json_encode($payment_methods) }}"
            makro-card-info="{!! htmlspecialchars(json_encode($makro_card_info)) !!}"
            v-bind:has-makro-card="{{ $has_makro_card }}"
            v-bind:is-created-siebel="{{ $is_created_siebel }}"
            v-bind:member-was-ordered="{{ $member_was_ordered }}"
            v-bind:shipping-addresses="{!! htmlspecialchars(json_encode($shipping_addresses)) !!}"
            v-bind:show-item-status="false"
            v-bind:config-date="{!! htmlspecialchars(json_encode($dateConfig)) !!}"
    >
    </cart-checkout>
    <!--================================================== -->
@endsection