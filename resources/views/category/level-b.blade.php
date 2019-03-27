<?php
    $categoryChildren = null;
    if (! empty($category['children']) && ! empty($category['children']['data'])) {
        $categoryChildren = collect($category['children']['data']);
        $categoryChildren = $categoryChildren->sortBy('priority')->slice(0, 10);
    }
?>

@extends('layouts.main')

@section('nav-mobile-menu')
    <ul class="nav navbar-nav dropdown-open">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" id="dropDownMobileMenuCategoryChildren" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $category['name'] }}
                <i class="fa fa-angle-down"></i>
            </a>

            <ul class="dropdown-menu" aria-labelledby="dropDownMobileMenuCategoryChildren" role="menu">
                @if (! empty($categoryChildren))
                    @foreach($categoryChildren as $child)
                        <li><a href="{{ route('categories.show', ['slug' => $child['slug']]) }}" title="{{ $child['name'] }}">{{ $child['name'] }}</a></li>
                    @endforeach
                @endif
            </ul>
        </li>
    </ul>
@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="box-menu-catagories">
            <div class="box-menu-catagories2 col-lg-3 col-md-3 no-padding hidden-sm hidden-xs">
                <div class="menu-catagories-list">
                    {{ $category['name'] }}
                </div>

                @if (! empty($categoryChildren))
                    @foreach($categoryChildren as $child)
                        <a href="{{ route('categories.show', ['slug' => $child['slug']]) }}" title="{{ $child['name'] }}">
                            <div class="menu-catagories-list2">
                                {{ $child['name'] }}
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>

            <div class="col-lg-9 col-md-9 no-padding">
                @if (! empty($topBanners))
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        @if (count($topBanners) > 1)
                        <ol class="carousel-indicators">
                            @foreach($topBanners as $key => $banner)
                                <li data-target="#carousel-example-generic" data-slide-to="{{ $key }}" class="{{ ($key == 0) ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                        @endif

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            @foreach($topBanners as $key => $banner)
                                <div class="item {{ ($key == 0) ? 'active' : '' }}">
                                    <a href="{{ $banner['url'] }}" target="{{ array_get($banner, 'target', '_self') }}"><img src="{{ $banner['image'] }}" target="{{ array_get($banner, 'target', '_self') }}" class="img-responsive"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <!--================================================== -->

    <div class="container margin-bottom" id="recommended-products-wrapper">
        @if (! empty($bannerB))
        <div class="col-lg-3 col-md-3  no-padding hidden-md hidden-sm hidden-xs">
            <div class="promotion-BN">
                <a href="{{ $bannerB['url'] }}" target="{{ array_get($bannerB, 'target', '_self') }}">
                    <img src="{{ $bannerB['image'] }}" class="img-responsive">
                </a>
            </div>
        </div>
        @endif

        @if (! empty($bannerB))
            <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 no-padding">
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        @endif

            <recommended-products
                    :has-banner="{{ empty($bannerB) ? 0 : 1 }}"
                    :category-id="{{ $category['id'] }}"
                    api-type="search"
                    div-class="owl-carousel"
                    category-type="{{ $category['type'] }}"
                    category-level="{{ $category['level'] }}"
                    category-slug="{{ $category['slug'] }}">
            </recommended-products>
        </div>
        <div class="clearfix"></div>

        <!-- <div class="box-b-promotion-viewall hidden-lg">
            <a href="#">
                <div class="b-promotion-viewall">
                     <img src="{{ asset('assets/images/icon-Slides2.png') }}" width="10"> </div>
            </a>
        </div> -->
    </div>

    <!--================================================== -->
    @include('partials.category.children-block', ['children' => $children])
    <!--================================================== -->
@endsection

@section('script')
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script>
        $('.offcanvas-toggle').click(function(e) {
            e.stopPropagation();
            $('.navbar-offcanvas .dropdown-open').find('[data-toggle="dropdown"]').trigger('click');
        })
        $('.navbar-offcanvas').on('hidden.bs.offcanvas', function() {
            $('.navbar-offcanvas .dropdown').removeClass('active');
        })
    </script>
@endsection