@extends('layouts.main')

@section('nav-mobile-menu')
    @if (! empty($allCampaigns))
        <ul class="nav navbar-nav dropdown-open">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" id="dropDownMobileMenuPromotion" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ trans('frontend.campaign') }}
                    <i class="fa fa-angle-down"></i>
                </a>

                <ul class="dropdown-menu open" aria-labelledby="dropDownMobileMenuPromotion" role="menu">
                    @foreach($allCampaigns as $allCampaign)
                        <li class="@if($allCampaign['id'] == $campaign['id']) active @endif">
                            <a href="{{ route('campaigns.show', ['slug' => $allCampaign['slug']]) }}">{{ $allCampaign['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    @endif
@endsection

@section('content')
    @if (! empty($campaign['bannerA']))
        <div class="container margin-bottom">
            <img src="{{ $campaign['bannerA'] }}" class="img-responsive">
        </div>
    @endif

    <!--================================================== -->

    {{--<div class="container margin-bottom hidden-lg hidden-md">
        <div class="dropdown-product-list-menu">
            <button class="dropbtn-product-list-menu">
                <b>Fresh Food</b>&nbsp;&nbsp;&nbsp;
                <img src="{{ asset('assets/images/icon-Dropdown-B.png') }}" width="10"></button>
            <div class="dropdown-content-product-list-menu">
                <a href="#">Meat</a>
                <a href="#">Fruit</a>
                <a href="#">Pork</a>
                <a href="#">Meatball</a>
                <a href="#">Noodle</a>
            </div>
        </div>
    </div>--}}

    <div class="container margin-bottom">
        <div class="col-lg-3 col-md-3 padding-right-15 hidden-sm hidden-xs">
            @if (! empty($campaign['bannerB']))
                <img src="{{ $campaign['bannerB'] }}" class="img-responsive">
            @endif

            @include('partials.product.sidebar_filter', ['filters' => $filters ? $filters : []])
        </div>

        <a name="product-list"></a>

        @include('partials.product.list', ['title' => $campaign['name'], 'products' => $products, 'listName' => env('GA_CAMPAIGN_LIST')])
    </div>

    <!--================================================== -->
@endsection

@section('script')
    <script>
        @if (! $products->isEmpty())
                    IMPRESSION_PRODUCTS.push({
                name: '{{ env('GA_CAMPAIGN_LIST', 'Campaign list') }}',
                items: {}
            })
            @foreach($products as $product)
                GLOBAL_PRUDUCTS['{{ $product['id'] }}'] = {!! json_encode($product) !!}
                IMPRESSION_PRODUCTS[0].items['{{ $product['id'] }}'] = {
                'refer_object': 'GLOBAL_PRUDUCTS',
                'id': '{{ $product['id'] }}'
            }
            @endforeach
        @endif

        gaTriggerImpression()
    </script>
@endsection