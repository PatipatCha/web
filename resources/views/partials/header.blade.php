
<header class="header-main">
<!-- <div class="box-welcome">
        <div class="box-icon-profile"><div class="clearfix"></div>
            <div class="box-icon-profile2">
                <img src="{{ asset('assets/images/icon-Profile.png') }}" width="25"/>
            </div>
        </div>
    </div> -->

    <div class="box-bar navbar hidden-sm hidden-xs">
        <div class="container">
<?php $user = \App\Bootstrap\Helpers\AuthHelper::user(); ?>
<?php if (!$user) { ?>
            <div class="row">
            <ul class="nav navbar-nav navbar-right" id="user-menu-desktop">
<!-- MKP Section -->
                @if( !empty($p) )
                <li class="navbar-left navbar-text">
                    <b>{{ trans('frontend.mkp') }}</b>
                </li>
                <li class="navbar-left">
                    <select>
                        <option>A</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </li>
                <!-- -->
                <li>
                    @include('partials.user_menu')
                </li>
                <!-- -->
                @else
                <li>
                    <a class="navbar-text" href="{{ route('home.index_mkp') }}"
                        id="lnk_mkp">MKP</a>
                </li>
                <li>
                    <a class="navbar-text" href="{{ route('members.register') }}"
                       id="lnk_register">{{ trans('frontend.register') }}</a>
                </li>
                <li>
                    <a class="navbar-text" href="javascript:void(0)" data-toggle="modal" data-target=".login-modal-sm"
                       id="lnk_sign_in">{{ trans('frontend.sign_in') }}</a>
                </li>
                @endif
<!-- End MKP Section -->
                <!-- <li>
                    <p class="navbar-text">
                        {{ trans('frontend.welcome_to_makroclick_store') }}
                    </p>
                </li> -->
<?php } else { ?>
<?php
                // $token = session()->get('makroclickMember');
                // $key = env('API_JWT_KEY', md5('jwtkey_makroclick_member_authentication_api'));
                // $decrypted = \Firebase\JWT\JWT::decode($token, $key, array('HS256'));
                // $member = json_decode(json_encode($decrypted), TRUE);
                // s($member);
?>
                <li>
                    @include('partials.user_menu')
                </li>
                <?php } ?>
                <li>
                    <store-menu-picker></store-menu-picker>
                </li>
                <li class="box-select-language">
                    <p class="navbar-text">{{ trans('frontend.language') }}</p>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{LaravelLocalization::getLocalizedURL('th', null, [], true) }}"
                           class="btn btn-default navbar-btn {{ LaravelLocalization::getCurrentLocale() == 'th' ? 'active' : '' }}">ไทย</a>
                        <a href="{{LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                           class="btn btn-default navbar-btn {{ LaravelLocalization::getCurrentLocale() == 'en' ? 'active' : '' }}">EN</a>
                    </div>

                <!-- <div class="box-select-language">
                        <div class="btn-group">
                            <button type="button" class="btn2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans('frontend.language') }}
                        <i class="fa fa-caret-down"></i>
{{-- <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10"/> --}}
                        </button>

                        <ul class="dropdown-menu-language">
@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)

                    {{--@if($properties['native'] == LaravelLocalization::getCurrentLocaleNative())--}}
                    {{--@continue--}}
                    {{--@endif--}}

                            <li>
                                <a rel="alternate" hreflang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                            <img src="{{ asset('assets/images/flag-' . $localeCode . '.svg') }}" width="18"> {{ __('frontend.'.$properties['name']) }}
                            </a>
                        </li>
@endforeach
                        </ul>
                    </div>
                </div> -->
                </li>
            </ul>
            </div>

            <div class="box-l hide"></div>

        <!-- <div class="box-select-currency hide">
                <div class="btn-group">
                    <button type="button" class="btn2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ trans('frontend.currency') }} <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10"/>
                    </button>
                    <ul class="dropdown-menu-currency">
                        <li><a href="#">Currency1</a></li>
                        <li><a href="#">Currency2</a></li>
                        <li><a href="#">Currency3</a></li>
                    </ul>
                </div>
            </div> -->
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="mainbar hidden-sm hidden-xs">
        <div class="container">
            <div class="box-makro-logo">
                <a href="{{ route('home.index') }}" title="{{ trans('frontend.website_title') }}">
                    <img src="{{ asset('assets/images/makro_logo.png') }}" width="100%"/>
                    <div class="clearfix"></div>
                </a>
            </div>

            <div class="row">
                <div class="col-sm-10 col-lg-10 widget-box">
                    <div class="col-sm-5 col-lg-6 search">
                        <div class="box-search">
                            <form role="search" action="{{ route('search.index') }}">
                                <div class="input-group add-on">
                                    <search-products value="{{ request()->get('q') }}"
                                                     name-id="srch-term-1"></search-products>
                                    <div class="show-autocomplete">
                                        <div class="v-autocomplete" value="">
                                            <div class="v-autocomplete-input-group"><input type="search"
                                                                                           placeholder="ค้นหา"
                                                                                           required="required"
                                                                                           id="srch-term-1" name="q"
                                                                                           autocomplete="off"
                                                                                           class="form-control"></div>
                                            <!----></div>
                                    </div>
                                <!-- <input class="form-control" placeholder="{{ trans('frontend.search') }}" name="q" id="srch-term" type="text" value="{{ request()->get('q') }}" required="required"> -->
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="select-store pull-left">
                        @if(empty($hide_select_store_button))
                            <select-store-button></select-store-button>
                        @endif
                    </div>

                    <div class="navbar-user pull-left">
                        <ul class="nav navbar-nav">
                            <?php if (!$user) { ?>
                            <li>
                                <a class="navbar-text" href="{{ route('members.register') }}"
                                   id="lnk_register">{{ trans('frontend.register') }}</a>
                            </li>
                            <li>
                                <a class="navbar-text" href="javascript:void(0)" data-toggle="modal"
                                   data-target=".login-modal-sm" id="lnk_sign_in">{{ trans('frontend.sign_in') }}</a>
                            </li>
                        <!-- <li>
                                <p class="navbar-text">
                                    {{ trans('frontend.welcome_to_makroclick_store') }}
                                </p>
                            </li> -->
                            <?php } else { ?>
                            <?php
                            // $token = session()->get('makroclickMember');
                            // $key = env('API_JWT_KEY', md5('jwtkey_makroclick_member_authentication_api'));
                            // $decrypted = \Firebase\JWT\JWT::decode($token, $key, array('HS256'));
                            // $member = json_decode(json_encode($decrypted), TRUE);
                            // s($member);
                            ?>
                            <li>
                                @include('partials.user_menu')
                            </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="pull-left product-action">
                        <?php
                        $user = \App\Bootstrap\Helpers\AuthHelper::user();
                        ?>
                        <div class="box-badge">
                            <span class="box-badge-icon">
                                <wish-list wish-list-url="{{ route('members.wishlist') }}"></wish-list>
                            </span>
                            <span class="box-badge-icon">
                                <cart cart-url="{{ route('carts.index') }}"></cart>
                            </span>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
    </div>

    <div class="hidden-lg hidden-md">
        <nav class="navbar navbar-default navbar-mobile" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="offcanvas-toggle pull-left" data-toggle="offcanvas"
                            data-target="#menu-mobile">
                        <i class="far fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="{{ route('home.index') }}">
                        <img src="{{ asset('assets/images/makro_logo.png') }}" height="40" alt=""/>
                    </a>

                    <div class="col-sm-10 pull-right no-padding">
                        <div class="col-sm-6 hidden-xs">
                            <div class="box-search box-search-md">
                                <form role="search" action="{{ route('search.index') }}">
                                    <div class="input-group add-on">
                                        <search-products value="{{ request()->get('q') }}"
                                                        name-id="srch-term-2"></search-products>
                                        <div class="show-autocomplete">
                                            <div class="v-autocomplete" value="">
                                                <div class="v-autocomplete-input-group"><input type="search"
                                                                                            placeholder="ค้นหา"
                                                                                            required="required"
                                                                                            id="srch-term-2" name="q"
                                                                                            autocomplete="off"
                                                                                            class="form-control"></div>
                                                <!----></div>
                                        </div>
                                    <!-- <input class="form-control" placeholder="{{ trans('frontend.search') }}" name="q" id="srch-term" type="text" value="{{ request()->get('q') }}" required="required"> -->
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="far fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-sm-4 hidden-xs">
                            @if(empty($hide_select_store_button))
                                <select-store-button></select-store-button>
                            @endif
                        </div>

                        <div class="col-sm-3 box-badge">
                            <span class="box-badge-icon hidden-xs hidden-sm">
                                <wish-list wish-list-url="{{ route('members.wishlist') }}"></wish-list>
                            </span>
                            <span class="box-badge-icon">
                                <cart cart-url="{{ route('carts.index') }}"></cart>
                            </span>
                            <div class="clearfix"></div>
                        </div>

                        <button type="button" class="offcanvas-toggle pull-right" data-toggle="offcanvas"
                                data-target="#js-bootstrap-offcanvas">
                            <i class="far fa-user"></i>
                        </button>
                    </div>

                </div>

                <div class="menu-mobile navbar-offcanvas navbar-offcanvas-left navbar-offcanvas-touch" id="menu-mobile">
                    <div class="box-welcome2">
                        <button type="button" id="menu-mobile-btn"
                                class="navbar-toggle offcanvas-toggle pull-left btn btn-default" data-toggle="offcanvas"
                                data-target="#menu-mobile">
                            <i class="far fa-times"></i>
                        </button>
                        <span class="box-select-language">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{LaravelLocalization::getLocalizedURL('th', null, [], true) }}"
                                   class="btn btn-default navbar-btn {{ LaravelLocalization::getCurrentLocale() == 'th' ? 'active' : '' }}">ไทย</a>
                                <a href="{{LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                                   class="btn btn-default navbar-btn {{ LaravelLocalization::getCurrentLocale() == 'en' ? 'active' : '' }}">EN</a>
                            </div>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="scroller">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="{{ route('home.site-map') }}">
                                    หน้าแรกแม็คโครคลิก
                                    <span class="icon">
                                        <i class="far fa-home"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('home.site-map') }}"> {{ __('frontend.see_all') }}</a>
                            </li>
                            <li class="dropdown open">
                                <a href="#" class="dropdown-toggle" id="dropdownMenu2" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    {{ __('frontend.categories') }}
                                    <i class="fa fa-angle-down"></i>
                                    {{-- <img src="{{ asset('assets/images/icon-Dropdown-B.png') }}" width="10"/> --}}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" role="menu">

                                    @if (! empty($mainMobileMenu['left_1']['content']))
                                        <li class="text_title">{{ $mainMobileMenu['left_1']['title'] }}</li>

                                        @foreach($mainMobileMenu['left_1']['content'] as $content)
                                            @php
                                                $link = MakroHelper::getGroupLink($content);
                                            @endphp
                                            <li><a href="{{ $link['url'] }}"
                                                   target="{{ $link['target'] }}">{{ $content['name'] }}</a></li>
                                        @endforeach

                                    @endif

                                    @if (! empty($mainMobileMenu['left_2']['content']))
                                        <li class="text_title">{{ $mainMobileMenu['left_2']['title'] }}</li>

                                        @foreach($mainMobileMenu['left_2']['content'] as $content)
                                            @php
                                                $link = MakroHelper::getGroupLink($content);
                                            @endphp
                                            <li><a href="{{ $link['url'] }}"
                                                   target="{{ $link['target'] }}">{{ $content['name'] }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                        </ul>

                        @yield('nav-mobile-menu')
                    </div>
                </div>

                <div class="navbar-offcanvas navbar-offcanvas-right navbar-offcanvas-touch" id="js-bootstrap-offcanvas">
                    <div class="box-welcome2">
                    <!-- <div class="box-icon-profile-m pull-left">
                        <div class="box-icon-profile2">
                            <img src="{{ asset('assets/images/icon-Profile.png') }}" width="25"/>
                        </div>
                    </div> -->

                        <b class="pull-left">{!! trans('frontend.mobile.welcome_to_makroclick_store') !!}</b>
                        <button type="button" class="navbar-toggle offcanvas-toggle pull-right btn btn-default"
                                data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas">
                            <i class="far fa-times"></i>
                        </button>
                        <div class="clearfix"></div>
                    </div>
                    <ul class="nav navbar-nav">
                        <?php
                        $user = \App\Bootstrap\Helpers\AuthHelper::user();
                        ?>
                        <?php if ( !$user ) { ?>

                        <li>
                            <a href="{{ route('members.register') }}">
                                {{ trans('frontend.register') }}
                                <span class="icon">
                                    <i class="far fa-user"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" data-toggle="modal" data-target=".login-modal-sm">
                                {{ trans('frontend.sign_in') }}
                                <span class="icon">
                                    <i class="far fa-sign-out"></i>
                                </span>
                            </a>
                        </li>
                        <?php } ?>

                        <?php // } else { ?>
                    <!-- <li>
                                @include('partials.user_menu_mobile')
                            </li> -->
                        <?php // } ?>

                        <?php if ($user) { ?>
                        <li>
                            <a href="{{ route('members.profile') }}">
                                {{ trans('frontend.my_profile') }}
                                <span class="icon">
                                    <i class="far fa-user"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('members.taxAddress') }}">
                                {{ trans('frontend.tax_address') }}
                                <span class="icon">
                                    <i class="far fa-money-check-alt"></i>
                                </span>
                            </a>
                        </li>
                    <!-- {{-- <li><a href="{{ route('members.store') }}"><img src="{{ asset('assets/images/icon-my-shipping-17px.png') }}"/> My Store</a></li> --}} -->
                        <li>
                            <a href="{{ route('members.shipping') }}">
                                {{ trans('frontend.my_shipping') }}
                                <span class="icon">
                                    <i class="far fa-map-marker-alt"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('members.wishlist') }}">
                                {{ trans('frontend.my_wishlist') }}
                                <span class="icon">
                                    <i class="far fa-heart"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('members.orders') }}">
                                {{ trans('frontend.my_order') }}
                                <span class="icon">
                                    <i class="far fa-file-alt"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:onLogout('{{ route('members.logout') }}')">
                                {{ trans('frontend.sign_out') }}
                                <span class="icon">
                                    <i class="far fa-sign-out"></i>
                                </span>
                            </a>
                        </li>
                    <!-- <li class="dropdown">
                            <a href="#" class="dropdown-toggle" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans('frontend.my_account') }}
                            <i class="fa fa-angle-down"></i>
{{-- <img src="{{ asset('assets/images/icon-Dropdown-B.png') }}" width="10"/> --}}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" role="menu">
                                {{--<li><a href="{{ route('members.profile') }}">{{ trans('frontend.my_profile') }}</a></li>
                                <li><a href="#">{{ trans('frontend.shipping_address') }}</a></li>
                                <li><a href="#">{{ trans('frontend.my_order') }}</a></li>
                                <li><a href="#">{{ trans('frontend.my_cart') }} <span class="badge2">100</span></a></li>
                                <li><a href="#">{{ trans('frontend.my_wishlist') }} <span class="badge2">100</span></a></li>
                                <li><a href="#">{{ trans('frontend.my_coupons') }}</a></li>
                                <li><a href="#">{{ trans('frontend.my_payment') }}</a></li>--}}

                            <li><a href="{{ route('members.profile') }}"><img src="{{ asset('assets/images/icon-my-profile-17px.png') }}"/>  {{ trans('frontend.my_profile') }}</a></li>
                                <li><a href="{{ route('members.taxAddress') }}"><img src="{{ asset('assets/images/icon-taxinvoice-100px.png') }}" style="max-width: 17px;" />  {{ trans('frontend.tax_address') }}</a></li>
                                {{-- <li><a href="{{ route('members.store') }}"><img src="{{ asset('assets/images/icon-my-shipping-17px.png') }}"/> My Store</a></li> --}}
                            <li><a href="{{ route('members.shipping') }}"><img src="{{ asset('assets/images/icon-my-shipping-17px.png') }}"/>  {{ trans('frontend.my_shipping') }}</a></li>
                                <li><a href="{{ route('members.wishlist') }}"><img src="{{ asset('assets/images/icon-my-wishlist-17px.png') }}"/> {{ trans('frontend.my_wishlist') }}</a></li>
                                <li><a href="{{ route('members.orders') }}"><img src="{{ asset('assets/images/icon-my-order-17px.png') }}"/>  {{ trans('frontend.my_order') }}</a></li>
                                <li class="divider"></li>
                                <li><a href="{{ route('members.logout') }}"><span style="color:#F01616">{{ trans('frontend.sign_out') }}</span></a></li>
                            </ul>
                        </li> -->
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <div class="box-bar box-bar-sm hidden-lg hidden-md">
        <div class="container">
            <store-menu-picker></store-menu-picker>

            <div class="box-select-language hide">
                <div class="btn-group">
                    <button type="button" class="btn2" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        {{ trans('frontend.please_select_language') }}
                        <i class="fa fa-angle-down"></i>
                        {{-- <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10"/> --}}
                    </button>
                    <ul class="dropdown-menu-language">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)

                            {{--@if($properties['native'] == LaravelLocalization::getCurrentLocaleNative())--}}
                            {{--@continue--}}
                            {{--@endif--}}

                            <li>
                                <a rel="alternate" hreflang="{{$localeCode}}"
                                   href="{{LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                    <img src="{{ asset('assets/images/flag-' . $localeCode . '.svg') }}"
                                         width="18"> {{ __('frontend.'.$properties['name']) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>

            {{-- <div class="box-l hide"></div> --}}

            {{--
            <div class="box-select-currency hide">
                <div class="btn-group">
                    <button type="button" class="btn2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ trans('frontend.currency') }}  <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10"/>
                    </button>
                    <ul class="dropdown-menu-currency">
                        <li><a href="#">Currency1</a></li>
                        <li><a href="#">Currency2</a></li>
                        <li><a href="#">Currency3</a></li>
                    </ul>
                </div>
            </div>
            --}}
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="box-search box-search-xs visible-xs">
        <div class="col-xs-12 col-sm-6 padding-right-ad2 search-xs">
            <form role="search" action="{{ route('search.index') }}">
                <div class="input-group add-on">
                    <search-products value="{{ request()->get('q') }}" name-id="srch-term-3"></search-products>
                    <div class="show-autocomplete">
                        <div class="v-autocomplete" value="">
                            <div class="v-autocomplete-input-group"><input type="search" placeholder="ค้นหา"
                                                                           required="required" id="srch-term-3" name="q"
                                                                           autocomplete="off" class="form-control">
                            </div> <!----></div>
                    </div>
                <!-- <input class="form-control" placeholder="{{ trans('frontend.search') }}" name="q" id="srch-term" type="text" value="{{ request()->get('q') }}" required="required"> -->
                    <div class="input-group-btn">
                        <button class="btn btn-primary" type="submit">
                            <i class="far fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xs-12 col-sm-6 padding-left-ad select-store-xs">
            @if(empty($hide_select_store_button))
                <select-store-button></select-store-button>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
</header>

@if (! empty(request()->route()) && request()->route()->getName() != 'home.index')
    <div class="nav-box-menu">
        <div class="container">
            {!! $breadcrumbs !!}
        </div>
    </div>
@endif

<!-- modal Login -->
<div id="login-modal" class="modal fade login-modal-sm login-box" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="form-login-modal" method="post" action="{{ route('members.login') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title" id="Sign in"><b>{{ __('frontend.sign_in') }}</b></h5>
                </div>

                <div class="modal-body">
                    @if (session()->has('required_login') && !session()->has('login_notify_success'))
                        <div class="text-danger">
                            <br/>
                            {{ __('frontend.please_login') }}!
                        </div>
                    @endif


                    @if (session()->has('login_notify_success') )
                        <div class="alert alert-success">
                            {{ session()->get('login_notify_success') }}
                        </div>
                    @endif

                    <label for="">{{ __('frontend.username') }}</label>
                    <input class="form-control" type="text" id="input-username" name="username"
                           placeholder="{{ __('frontend.forgot_password_username_placeholder') }}">

                    <label for="">{{ __('frontend.password') }}</label>
                    <input class="form-control" type="password" id="input-password" name="password"
                           placeholder="{{ __('frontend.password') }}">

                    <div class="forget-pass-text">
                        <a href="{{ route('members.forget-password.index') }}">{{ __('frontend.forgot_your_password') }}</a>
                    </div>

                    <div class="box-link">
                        <input type="checkbox" name="remember_me" id="remember_me" value="1">&nbsp;&nbsp;<label
                                for="remember_me">{{ __('frontend.remember_me') }}</label>
                    </div>

                </div>

                {{--<div class="modal-footer">--}}
                {{--<button type="submit" class="btn btn-primary btn-block">Sign in <span class="login-loader hide"><loading v-bind:show="true"></loading></span></button>--}}
                {{--<div id="login-alert" class="alert alert-danger hide" style="margin:10px auto; padding:10px;">--}}
                {{--&nbsp;--}}
                {{--</div>--}}
                {{--</div>--}}


                <div class="modal-footer">
                    <button type="submit" id="lnk_login" class="btn btn-primary btn-block">{{ __('frontend.sign_in_button') }} <span
                                class="login-loader hide"><loading v-bind:show="true"></loading></span></button>
                    <div id="login-alert" class="alert alert-danger hide" style="margin:10px auto; padding:10px;"></div>

                    <div class="btn-box3">
                        <a class="btn btn-login-with-fb"
                           href="{{ route('members.facebook-login', ['rtt' => urlencode(request()->fullUrl())]) }}">
                            <i class="fab fa-facebook-square"></i> {{ __('frontend.login_with_facebook') }}
                        </a>
                    </div>

                </div>

                <div class="modal-footer2">

                    {{ __('frontend.dont_have_member') }} <br>
                    <div class="btn-box3">
                        <button type="button" class="btn-register"
                                onclick="location='{{ route('members.register') }}'">{{ __('frontend.register') }}</button>
                    </div>

                </div>

                {{ csrf_field() }}

                <input type="hidden" name="redirect_url" id="input_redirect_url"
                       value="{{ session()->has('loggedin_redirect_url') ? urlencode(session()->get('loggedin_redirect_url')) : '' }}">
            </form>
        </div>
    </div>
</div>
