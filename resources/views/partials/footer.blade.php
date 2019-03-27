<!--=========================FOOTER========================= -->
<div class="box-footer">
    <div class="container">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            <div class="box-footer-guarantee">
                <div class="footer-icon-guarantee">
                    <img src="{{ asset('assets/images/icon-BestValues.png') }}">
                </div>
                <div class="footer-text-guarantee">
                    BEST VALUES
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            <div class="box-footer-guarantee">
                <div class="footer-icon-guarantee">
                    <img src="{{ asset('assets/images/icon-BrandGuarantee.png') }}">
                </div>
                <div class="footer-text-guarantee">
                    BRAND GUARANTEE
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            <div class="box-footer-guarantee">
                <div class="footer-icon-guarantee">
                    <img src="{{ asset('assets/images/icon-delivery3.png') }}">
                </div>
                <div class="footer-text-guarantee">
                    FAST DELIVERY
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            <div class="box-footer-guarantee2">
                <div class="footer-icon-guarantee">
                    <img src="{{ asset('assets/images/icon-SecurePayment.png') }}">
                </div>
                <div class="footer-text-guarantee">
                    SECURE PAYMENT
                </div>
            </div>
        </div>

        <subscribe></subscribe>
        <div class="col-lg-6 col-md-6 col-sm-7 col-xs-12 no-padding">
            <div class="footer-box-store-about-contact">
                <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-4 no-padding">
                    <a href="#">
                        <div class="footer-store-icon pull-left hidden-xs">
                            <img src="{{ asset('assets/images/icon-StorLocation.png') }}" class="img-responsive">
                        </div>
                        <a href="{{ route('stores.location') }}">
                            <div class="footer-store-text pull-left">
                                Store Location
                            </div>
                        </a>
                    </a>
                </div>
                <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-4 no-padding">
                    <a href="{{ route('contents.show', ['slug' => env('CONTENT_ABOUT_US_SLUG')]) }}">
                        <div class="footer-store-icon pull-left hidden-xs">
                            <img src="{{ asset('assets/images/icon-AboutMakro.png') }}" class="img-responsive">
                        </div>
                        <div class="footer-store-text pull-left">
                            About Makro
                        </div>
                    </a>
                </div>
                <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-4 no-padding">
                    <a href="{{ route('home.contact-us') }}">
                        <div class="footer-store-icon pull-left hidden-xs">
                            <img src="{{ asset('assets/images/icon-ContactUs.png') }}" class="img-responsive">
                        </div>
                        <div class="footer-store-text2 pull-left">
                            02-335-5300<br>
                            <span>{{ __('frontend.contact_available_time') }}</span>
                        </div>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="footer-box-line"></div>

        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            @if (! empty($footerMenu['menu_1']['content']))
                <div class="footer-sitemap-text">
                    <b>{{ $footerMenu['menu_1']['title'] }}</b>
                </div>
                <div class="footer-sitemap-text-list">
                    @foreach($footerMenu['menu_1']['content'] as $content)
                        @php
                            $link = MakroHelper::getGroupLink($content);
                        @endphp
                        <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $content['name'] }}</a><br>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            @if (! empty($footerMenu['menu_2']['content']))
                <div class="footer-sitemap-text">
                    <b>{{ $footerMenu['menu_2']['title'] }}</b>
                </div>
                <div class="footer-sitemap-text-list">
                    @foreach($footerMenu['menu_2']['content'] as $content)
                        @php
                            $link = MakroHelper::getGroupLink($content);
                        @endphp
                        <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $content['name'] }}</a><br>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            @if (! empty($footerMenu['menu_3']['content']))
                <div class="footer-sitemap-text">
                    <b>{{ $footerMenu['menu_3']['title'] }}</b>
                </div>
                <div class="footer-sitemap-text-list">
                    @foreach($footerMenu['menu_3']['content'] as $content)
                        @php
                            $link = MakroHelper::getGroupLink($content);
                        @endphp
                        <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $content['name'] }}</a><br>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
            <div class="footer-sitemap-text">
                <b>{{ trans('frontend.my_account') }}</b>
            </div>
            <div class="footer-sitemap-text-list">
                <a href="{{ route('members.profile') }}" class="need-login">{{ trans('frontend.my_profile') }}</a><br>
                <a href="{{ route('members.taxAddress') }}" class="need-login">{{ trans('frontend.tax_address') }}</a><br>
                <a href="{{ route('members.shipping') }}" class="need-login">{{ trans('frontend.my_shipping') }}</a><br>
                <a href="{{ route('members.wishlist') }}" class="need-login">{{ trans('frontend.my_wishlist') }}</a><br>
                <a href="{{ route('members.orders') }}" class="need-login">{{ trans('frontend.my_order') }}</a><br>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="footer-box-line"></div>

        <div class="footer-text-copyright">
            {{ trans('frontend.footer_copyright') }}
        </div>

    </div>
</div>


<store-picker></store-picker>
<select-address-modal></select-address-modal>


<!-- Modal Newsletter-->
<div class="modal fade register-box" id="myModal04" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="modal-letter-box">
                    <img class="icon-newletter" src="{{ asset('assets/images/icon-newsletter.png') }}">
                </div>
                <div class="text-modal-letter">
                    <b>
                        <span style="color:#7ED321;">Thank you for the news and follow up special results.</span><br>
                        www.makroclick.com
                    </b>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
<?php
    $addToCartSuccessLang = [
        'item' => __('frontend.item'),
        'add_product' => __('frontend.add_product'),
        'to_your_cart' => __('frontend.to_your_cart'),
        'continue_choose_product' => __('frontend.continue_choose_product'),
        'add_to_cart_success_checkout' => __('frontend.add_to_cart_success_checkout'),
    ];
?>
<add-to-cart-success v-bind:lang="{{ json_encode($addToCartSuccessLang) }}"></add-to-cart-success>
<add-to-cart-error-low-stock v-bind:lang="{{ json_encode($addToCartSuccessLang) }}"></add-to-cart-error-low-stock>
<add-to-wish-list-success></add-to-wish-list-success>
<store-picker-product-not-in-store></store-picker-product-not-in-store>
<default-popup></default-popup>
<confirm-re-order></confirm-re-order>

{{--@if (!\App\Bootstrap\Helpers\MakroHelper::isAcceptedDelivery())--}}
    {{--<delivery-acceptance-popup--}}
        {{--submit-url="{{ route('accept-delivery') }}"--}}
    {{--></delivery-acceptance-popup>--}}
{{--@endif--}}