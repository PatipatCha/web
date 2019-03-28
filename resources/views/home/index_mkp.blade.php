@extends('layouts.main')

@section('nav-mobile-menu')
    <ul class="nav navbar-nav">
        @if (! empty($homeGroupMenu['top']['content']))
            @foreach($homeGroupMenu['top']['content'] as $content)
                @php
                    $link = MakroHelper::getGroupLink($content);
                @endphp
                <li><a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $content['name'] }}</a></li>
            @endforeach
        @endif
    </ul>
@endsection

@section('content')
    <div id="home" class="clearfix">
        <div class="box-bar2 hidden-sm hidden-xs">
            <div class="container">
                <div class="box-catagories-seeall col-lg-3 col-md-3">
                    <div class="box-catagories-seeall2">
                        {{ __('frontend.categories') }}
                    </div>
                    <div class="box-catagories-seeall3">
                        <a href="{{ route('home.site-map') }}">{{ __('frontend.see_all') }}</a>
                    </div>
                </div>

                <div class="box-menu-highlight col-lg-9 col-md-9">
                    @if (! empty($homeGroupMenu['top']['content']))
                        @foreach($homeGroupMenu['top']['content'] as $content)
                            @php
                                $link = MakroHelper::getGroupLink($content);
                            @endphp
                            <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">
                                <div class="box-menu-highlight2">
                                    <b>{{ $content['name'] }}</b>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div><div class="clearfix"></div>
        </div>

        <div class="container margin-bottom box-categories-container">
            <div class="box-categories-scroll">
                <div class="box-menu-catagories">
                    <div class="box-menu-catagories2 col-lg-3 col-md-3 no-padding hidden-sm hidden-xs">

                        @if (! empty($homeGroupMenu['left_1']['content']))
                            <div class="menu-catagories-list">
                                {{ $homeGroupMenu['left_1']['title'] }}
                            </div>
                            @foreach($homeGroupMenu['left_1']['content'] as $content)
                                @php
                                    $link = MakroHelper::getGroupLink($content);
                                @endphp

                                <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">
                                    <div class="menu-catagories-list2">
                                        {{ $content['name'] }}
                                    </div>
                                </a>
                            @endforeach

                            <div class="box-line1"></div>
                        @endif

                        @if (! empty($homeGroupMenu['left_2']['content']))
                            <div class="menu-catagories-list">
                                {{ $homeGroupMenu['left_2']['title'] }}
                            </div>
                            @foreach($homeGroupMenu['left_2']['content'] as $content)
                                @php
                                    $link = MakroHelper::getGroupLink($content);
                                @endphp

                                <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">
                                    <div class="menu-catagories-list2">
                                        {{ $content['name'] }}
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>

                    @include('partials.banner', ['banners' => $banner1, 'id' => 'carousel-example-generic'])
                </div>
            </div>
            <div class="control visible-xs">
                <div class="arrow prev hide">
                    <i class="fa fa-chevron-left"></i>
                </div>
                <div class="arrow next hide">
                    <i class="fa fa-chevron-right"></i>
                </div>
            </div>
        </div>
        @widget('App\Widgets\Products\Recent', ['minimum' => 5])
        <!--================================================== -->

        @include('partials.home.campaign-block', ['campaigns' => $homeCampaigns])
        <!--================================================== -->

        <div class="container margin-bottom hidden-lg hidden-md">
            <div class="dropdown">
                @if (! empty($homeGroupMenu['left_3']['content']))
                    <button class="dropbtn-by-product">
                        {{ $homeGroupMenu['left_3']['title'] }}&nbsp;&nbsp;&nbsp;
                        <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10">
                    </button>

                    <div class="dropdown-content">
                        @foreach($homeGroupMenu['left_3']['content'] as $content)
                            @php
                                $link = MakroHelper::getGroupLink($content);
                            @endphp

                            <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $content['name'] }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="container margin-bottom box-categories-container">
            <div class="box-categories-scroll">
                <div class="box-menu-catagories">
                    <div class="box-menu-catagories2 col-lg-3 col-md-3 no-padding hidden-sm hidden-xs">
                        @if (! empty($homeGroupMenu['left_3']['content']))
                            <div class="menu-catagories-list">
                                {{ $homeGroupMenu['left_3']['title'] }}
                            </div>
                            <div class="box-line1"></div>

                            @foreach($homeGroupMenu['left_3']['content'] as $content)
                                @php
                                    $link = MakroHelper::getGroupLink($content);
                                @endphp
                                <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">
                                    <div class="menu-catagories-list2">
                                        {{ $content['name'] }}
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>

                    @include('partials.banner', ['banners' => $banner2, 'id' => 'carousel-example-generic2'])
                </div>
            </div>
            <div class="control visible-xs">
                <div class="arrow prev hide">
                    <i class="fa fa-chevron-left"></i>
                </div>
                <div class="arrow next hide">
                    <i class="fa fa-chevron-right"></i>
                </div>
            </div>
        </div>

        <!--================================================== -->

        <?php /*
        <div class="container margin-bottom">
            <div class="box-brand">
                <div class="col-lg-3 col-md-3 no-padding hidden-sm hidden-xs">
                    @if(! empty($brandLeftBanners))
                        <div id="carousel_home_left_banner" class="carousel slide" data-ride="carousel">

                            @if (count($brandLeftBanners) > 1)
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    @foreach($brandLeftBanners as $key => $brandLeftBanner)
                                        <li data-target="#carousel_home_left_banner" data-slide-to="{{ $key }}" @if($key == 0) class="active" @endif></li>
                                    @endforeach
                                </ol>
                            @endif

                            @if (count($brandLeftBanners) > 0)
                            <!-- Wrapper for slides -->
                            <div class="carousel-inner" role="listbox">
                                @foreach($brandLeftBanners as $key => $brandLeftBanner)
                                    <div class="item  @if($key == 0) active @endif">
                                        @if(! empty($brandLeftBanner['url']))
                                            <a href="{{ $brandLeftBanner['url'] }}" target="{{ empty(array_get($brandLeftBanner, 'target')) ? '_self' : array_get($brandLeftBanner, 'target') }}">
                                        @endif

                                        <img src="{{ $brandLeftBanner['image'] }}" class="img-responsive">

                                        @if(! empty($brandLeftBanner['url']))
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 no-padding box-brand-wrap">
                @foreach($brands as $brand)
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 box-brand-column">
                        
                            <?php
                                $logo = url('assets/images/products/Brand1.png');

                                if (! empty($brand['images'])) {
                                    $brandImages = collect($brand['images']);
                                    $brandImage = $brandImages->where('position', 'thumb')->first();

                                    if ($brandImage) {
                                        $logo = $brandImage['image'];
                                    }
                                }
                            ?>
                        <div class="box-logo-brand">
                            <a href="{{ route('brands.show-by-slug', ['slug' => $brand['slug']]) }}"><img src="{{ $logo }}" class="img-responsive"></a>
                        </div>
                    </div>
                    @endforeach
                    
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        */ ?>
        <!--================================================== -->

        @if (!empty($banner3))
        <div class="container margin-bottom">

            <img src="{{ $banner3 }}" class="img-responsive">

        </div>
        @endif

        <!--================================================== -->

        <div class="container margin-bottom hidden-lg hidden-md">
            <div class="dropdown">
                @if (! empty($homeGroupMenu['left_4']['content']))
                    <button class="dropbtn-by-product">
                        {{ $homeGroupMenu['left_4']['title'] }}&nbsp;&nbsp;&nbsp;
                        <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10">
                    </button>

                    <div class="dropdown-content">
                        @foreach($homeGroupMenu['left_4']['content'] as $content)
                            @php
                                $link = MakroHelper::getGroupLink($content);
                            @endphp

                            <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $content['name'] }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="container margin-bottom box-categories-container">
            <div class="box-categories-scroll">
                <div class="box-menu-catagories">
                    <div class="box-menu-catagories2 col-lg-3 col-md-3 no-padding hidden-sm hidden-xs">
                        @if (! empty($homeGroupMenu['left_4']['content']))
                            <div class="menu-catagories-list">
                                {{ $homeGroupMenu['left_4']['title'] }}
                            </div>
                            <div class="box-line1"></div>

                            @foreach($homeGroupMenu['left_4']['content'] as $content)
                                @php
                                    $link = MakroHelper::getGroupLink($content);
                                @endphp
                                <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">
                                    <div class="menu-catagories-list2">
                                        {{ $content['name'] }}
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>

                    @include('partials.banner', ['banners' => $banner4, 'id' => 'carousel-example-generic4'])
                </div>
            </div>
            <div class="control visible-xs">
                <div class="arrow prev hide">
                    <i class="fa fa-chevron-left"></i>
                </div>
                <div class="arrow next hide">
                    <i class="fa fa-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
    <!--================================================== -->
@endsection

@section('script-header')
    <script>
        var HOME_DATA = {
            'login_success': '{{ $login_success }}'
        };
    </script>
@endsection

@section('script')
    
    <script>
        $(window).ready(function() {
            var owl = null;

            var width = getViewportDimension()[0];
            var responsiveItems = 2;
            if (width < 600) {
                responsiveItems = 2;
            } else {
                responsiveItems = 4;
            }

            IMPRESSION_PRODUCTS.push({
                'name': '{{ env('GA_HOME_CAMPAIGN_LIST', 'Home campaign list') }}',
                'items': {}
            })

            @foreach($homeCampaigns as $homeCampaign)
                @foreach($homeCampaign['products'] as $data)
                    GLOBAL_PRUDUCTS['{{ $data['id'] }}'] = {
                            'refer_object': 'CAROUSEL_PRODUCTS',
                            'id': '{{ $data['id'] }}'
                        };
                    CAROUSEL_PRODUCTS['{{ $data['id'] }}'] = {!! json_encode($data) !!}
                    IMPRESSION_PRODUCTS[0]['items']['{{ $data['id'] }}'] = {
                        'refer_object': 'CAROUSEL_PRODUCTS',
                        'id': '{{ $data['id'] }}'
                    }
                @endforeach

                var len = {{ sizeof($homeCampaign['products']) }};
                var loop = false
                if (len <= responsiveItems) {
                    loop = false
                } else {
                    loop = true
                }

                owl = $('#home-campaign-' + '{{ $homeCampaign['id'] }}')
                owl.owlCarousel({
                    loop: loop,
                    nav: true,
                    responsive: {
                        0: {
                            items: 2
                        },
                        600: {
                            items: 4
                        },
                        1900: {
                            items: 4
                        }
                    },
                    onInitialized: function (event) {
                        if (event.item.count <= event.page.size) {
                            $('.owl-nav', $(event.target)).hide()
                        }
                    }
                });
            @endforeach

            gaTriggerImpression();
        });
    </script>
@endsection