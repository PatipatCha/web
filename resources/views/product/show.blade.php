@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom product-detail-show">
        <div class="bg-product-detail padding-bottom15">
            <div class="col-sm-5 no-padding">
                <div class="product-detail-thumb">
                    @if (! empty($product['images']))
                        <div id="carousel-example-generic" class="carousel slide detail-thumb" data-ride="carousel">
                            @if (count($product['images']) > 1)
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    @foreach($product['images'] as $key => $image)
                                        <li data-target="#carousel-example-generic" data-slide-to="{{ $key }}" class="{{ ($key == 0) ? 'active' : '' }}" ><img src="{{ $image['thumbnail'] }}" width="100%"></li>
                                    @endforeach
                                </ol>
                            @endif

                            <!-- Wrapper for slides -->
                            <div class="carousel-inner" role="listbox">
                                @foreach($product['images'] as $key => $image)
                                <div class="item {{ ($key == 0) ? 'active' : '' }}">
                                    <div class="easyzoom eazyzoom--overlay">
                                        <a href="{{ $image['full'] }}">
                                            <img src="{{ $image['large'] }}" class="img-responsive">
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            @if (count($product['images']) > 1)
                                <!-- Controls -->
                                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                    <span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span>
                                    <span class="sr-only">{{ trans('frontend.previous') }}</span>
                                </a>
                                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                    <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                                    <span class="sr-only">{{ trans('frontend.next') }}</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                @if (! empty($product['ribbon']))
                    @if ($product['ribbon']['type'] == 'static')
                        <div class="ribbon-status">
                            <img src="{{ $product['ribbon']['image']['medium'] }}">
                        </div>
                    @endif
                @endif

                {{-- <div class="box-delivery hidden-xs">
                    <div class="box-line padding-top50"></div>
                </div> --}}
            </div>

            <div class="col-sm-7 no-padding">
                <div class="product-detail">
                    <div class="product-detail-name">
                        <b>{{ $product['name'] }}</b>
                    </div>
                    <div class="product-detail-text">
                        {{ trans('frontend.product_code') }} {{$product['item_id']}}
                    </div>
                    {{--<div class="icon-customer">
                        <img src="{{ asset('assets/images/icon-customer.png') }}">
                        <div class="product-detail-textcustomer">
                            <span style="color: #F01616">14</span> customers are considering this item right now!
                        </div>
                    </div><div class="clearfix"></div>--}}

                    <div class="product-detail-text">
                        {{--<b>Ranking</b>
                        <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        <br>
                        <b>Feedback</b>
                        <span style="color:#F01616 ">1,342</span> |
                        <b>Ordered</b>
                        <span style="color:#F01616 ">30,231</span>
                        <br>--}}

                        {{--<p>
                            @if (! empty($product['tags']))
                                @foreach($product['tags'] as $key => $tag)
                                    @if ($key > 0) ,&nbsp; @endif
                                    <a href="{{ route('search.index', ['q' => htmlspecialchars("#{$tag}")]) }}">#{{ $tag }}</a>
                                @endforeach
                            @endif
                        </p>--}}
                    </div>

                    <div class="product-detail-price-red">
                        <b>{{ number_format($product['price'], 2) }} ฿</b>
                    </div>

                    <div class="product-detail-text">
                        @if ($product['price'] < $product['normal_price'])
                        <strike>{{ number_format($product['normal_price'], 2) }} ฿</strike>
                        @endif

                        {{ trans('frontend.avg_price_per_unit', ['unit_name' => $product['unit_name'], 'price' => number_format($product['average_price_per_unit'], 2)]) }}
                    </div>

                    <div class="product-detail-quality">
                        @if ($can_add_to_cart)
                            <div class="product-detail-text">
                                <b>{{ trans('frontend.quantity') }}</b>
                            </div>
                            <stepper v-bind:initial-value="1"></stepper>
                        @else
                            <p class="text-danger padding-top15">{{ __('frontend.coming_soon') }}</p>
                        @endif
                    </div>

                     <div class="product-detail-quality">
                        <div class="product-detail-text">
                            <b>{{ trans('frontend.delivery_service') }}</b>
                        </div>
                        {!! array_get($group_product, '0.product_detail_description') !!}
                    </div>

                    <div class="clearfix"></div>

                    <div class="box-btn-product-detail">
                        <add-to-cart-button product="{!! htmlspecialchars(json_encode($product)) !!}" v-bind:button_type="3" :disabled-button="{{ $can_add_to_cart ? 'false' : 'true' }}"></add-to-cart-button>

                        <add-to-wish-list-button product="{!! htmlspecialchars(json_encode($product)) !!}" v-bind:button-type="2"></add-to-wish-list-button>
                    </div>

                    <div class="addthis_inline_share_toolbox"></div>

                    {{--@if (! empty($product['brand']))--}}
                        {{--<a href="{{ route('brands.show-by-slug', ['slug' => $product['brand']['slug']]) }}" class="item-brand">{{ $product['brand']['name'] }}</a>--}}
                    {{--@endif--}}

                    @if (! empty($product['tags']))
                        @foreach($product['tags'] as $key => $tag)
                            @if ($key > 0)
                                ,&nbsp;
                            @endif
                            <a href="{{ route('search.index', ['q' => htmlspecialchars("#{$tag}")]) }}" class="item-brand">#{{ $tag }}</a>
                        @endforeach
                    @endif
                    <div class="clearfix"></div>

                    <div class="box-line hidden-lg hidden-md hidden-sm"></div>

                    @if ($product['type'] == 'non_assortment_with_install')
                        <div class="product-detail-DeliveryCondition  hidden-lg hidden-md hidden-sm">
                            <div class="product-detail-text">
                                <b>Delivery Condition</b><br>
                                A 3rd party vendor has been contracted to perform your delivery service.
                                <a href="#">(more)</a>
                            </div>
                        </div>
                        <div class="box-line  hidden-lg hidden-md hidden-sm"></div>
                    @endif


                </div>
            </div>

            <div class="tab-product-detail col-sm-12">
                <div class="topic-text-tab-product-detail">
                    <b>{{ __('frontend.product_description') }}</b>
                    <a class="pull-right" data-toggle="collapse" href="#productCollapse" aria-expanded="false" aria-controls="productCollapse">
                        <i class="fa fa-angle-up icon-open"></i>
                        <i class="fa fa-angle-down icon-close"></i>
                        {{-- <img src="{{ asset('assets/images/icon-open.png') }}" class="icon-open">
                        <img src="{{ asset('assets/images/icon-close2.png') }}" class="icon-close"> --}}
                    </a>
                </div>

                <div id="productCollapse" class="text2-tab-product-detail collapse in">
                    {!! $product['description'] !!}
                </div>
            </div>

            <div class="clearfix"></div>
        </div>

    </div>

    <!-- <related-products :product-id="{{ $product['id'] }}"></related-products> -->
    {{-- <recent-products :product-id="{{ $product['id'] }}"></recent-products> --}}
        @widget('App\Widgets\Products\Related', ['product_id' => $product['id'], 'show' => 6])
        @widget('App\Widgets\Products\Recent', ['product_id' => $product['id'], 'show' => 6])
        @widget('App\Widgets\Products\Recommend', ['product_class' => $product['product_class']])

    <?php /*
    @if (! $relateProducts->isEmpty())
    <div class="container margin-bottom">
        <div class="col-lg-12 no-padding">
            <div class="topic-related-product">
                <b>{{ trans('frontend.related_product_view') }}</b>
            </div>

            <div id="releted-pro" class="recent-product owl-carousel">
                @include('partials/product/item-owl-carousel', ['products' => $relateProducts])
            </div>
        </div>

        {{--<div class="box-b-promotion-viewall">
            <a href="#">
                <div class="b-promotion-viewall">
                    View All <img src="{{ asset('assets/images/icon-Slides2.png') }}" width="10"> </div>
            </a>
        </div>--}}
    </div>
    <!--================================================== -->
    @endif
    */ ?>


    <? /*
    @if (! $recentViewProducts->isEmpty())
    <div class="container margin-bottom">
        <div class="col-lg-12 no-padding">
            <div class="topic-related-product">
                <b>{{ trans('frontend.recent_product_view') }}</b>
            </div>
            <div id="recent-pro" class="recent-product owl-carousel">
                @include('partials/product/item-owl-carousel', ['products' => $recentViewProducts])
            </div>
        </div>
    </div>
    @endif
    */ ?>

@endsection


@section('script-before')
    <?php /*
    <script>
        @if (!empty($relateProducts))
            @foreach($relateProducts as $relateProduct)
            CAROUSEL_PRODUCTS['{{ $relateProduct['id'] }}'] = "{!! addslashes(htmlspecialchars(json_encode($relateProduct))) !!}"
            @endforeach
        @endif
        @if (!empty($recentViewProducts))
            @foreach($recentViewProducts as $recentViewProduct)
            CAROUSEL_PRODUCTS['{{ $recentViewProduct['id'] }}'] = "{!! addslashes(htmlspecialchars(json_encode($recentViewProduct))) !!}"
            @endforeach
        @endif

    </script>
    */ ?>
@endsection

@section('script')
    <script>
        GLOBAL_PRUDUCTS['{{ $product['id'] }}'] = {!! json_encode($product) !!}
        GLOBAL_SETTING.is_loaded_product_detail = 1
        gaProductDetailWasViewd()
    </script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/easyzoom.js') }}"></script>

    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={{ env('ADD_THIS_ID') }}"></script>

    @if (app()->environment() == 'production')
        {{--<script src="{{ asset('js/rec_prod.js') }}"></script>--}}
    @elseif (app()->environment() == 'alpha')
        {{--<script src="{{ asset('js/rec_alpha.js') }}"></script>--}}
    @else
       {{--<script src="{{ asset('js/rec.js') }}"></script>--}}
    @endif

    <script>
        $(function() {
            var $easyzoom = $('.easyzoom').easyZoom();
        });
        /*var owlCarousel2 = $('#recent-pro.owl-carousel').owlCarousel({
            loop:false,
            margin:1,
            nav:true,
            responsive:{
                0:{
                    items:2,
                    slideBy: 2,
                },
                600:{
                    items:4,
                    slideBy: 3,
                },
                1000:{
                    items:5,
                    slideBy: 3,
                }
            }
        })

        OWL_OBJECTS.push(owlCarousel2);

        //var owl = $('#recent-pro.owl-carousel');
        //owl.owlCarousel();
        // Go to the next item
        $('.customNextBtn').click(function() {
            owlCarousel2.trigger('next.owl.carousel');
        })
        // Go to the previous item
        $('.customPrevBtn').click(function() {
            // With optional speed parameter
            // Parameters has to be in square bracket '[]'
            owlCarousel2.trigger('prev.owl.carousel', [300]);
        })

        var $easyzoom = $('.easyzoom').easyZoom();*/
    </script>
@endsection

@section('head')
    <?php
        $product_category_lv0 = $productCategories->filter(function ($item) {
            return $item['level'] == 0;
        })->first();

        $product_category_lv1 = $productCategories->filter(function ($item) {
            return $item['level'] == 1;
        })->first();

        $product_category_lv2 = $productCategories->filter(function ($item) {
            return $item['level'] == 2;
        })->first();

        $business_category_lv0 = $businessCategories->filter(function ($item) {
            return $item['level'] == 0;
        })->first();

        $business_category_lv1 = $businessCategories->filter(function ($item) {
            return $item['level'] == 1;
        })->first();

        $business_category_lv2 = $businessCategories->filter(function ($item) {
            return $item['level'] == 2;
        })->first();

        $brands = [];
        if (! empty($product['brands'])) {
            $brands = head($product['brands']);
        }
    ?>

    <meta property="product:id" content="{{ $product['id'] }}" />
    <meta property="product:cat_lv0" content="{{ array_get($product_category_lv0, 'id') }}" />
    <meta property="product:cat_lv1" content="{{ array_get($product_category_lv1, 'id') }}" />
    <meta property="product:cat_lv2" content="{{ array_get($product_category_lv2, 'id') }}" />
    <meta property="business:cat_lv0" content="{{ array_get($business_category_lv0, 'id') }}" />
    <meta property="business:cat_lv1" content="{{ array_get($business_category_lv1, 'id') }}" />
    <meta property="business:cat_lv2" content="{{ array_get($business_category_lv2, 'id') }}" />
    <meta property="product:brand" content="{{ array_get($brands, 'name_original.en') }}" />
    <meta property="product:brand_id" content="{{ array_get($brands, 'id') }}" />
    <meta property="product:brand_name_id" content="{{ array_get($brands, 'id') }}" />
@endsection