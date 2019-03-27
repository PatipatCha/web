<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ empty($headerTitle) ? trans('frontend.website_title') : $headerTitle }}</title>
        @include('partials.seo')

        @yield('head')

        <meta name="viewport" content="width=device-width, initial-scale=1">

        @if(env('ALLOW_BOT', true) != true)
            <meta name="robots" content="noindex">
        @endif

        <link href="{{ asset('favicon.png')}}" rel="shortcut icon" type="image/png">

        <link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/font-awesome/css/all.min.css') }}" rel="stylesheet">

        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
        <script src="{{ asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        
        <script src="https://cdn.jsdelivr.net/npm/object-assign-polyfill@0.1.0/index.min.js"></script>

        <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">

        <STYLE TYPE="TEXT/CSS"><!--
            A:link	{
                text-decoration:none;
            }
            A:visited	{
                text-decoration:none;
            }
            //--></STYLE>

        @if (! empty($msStyle))
            <style>
                {!! $msStyle !!}
            </style>
        @endif

        @yield('script-header')

        <script>
            var routeName = '{{ ! empty(request()->route()) ? request()->route()->getName() : '' }}'

            if (routeName === 'home.index'
                || routeName === 'members.register.facebook'
            ) {
                history.pushState('', document.title, window.location.href.replace(/#_=_/, ''))
            }

        </script>

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(

                {'gtm.start': new Date().getTime(),event:'gtm.js'}
            );var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ env('GA_TRACKING_ID') }}');</script>
        <!-- End Google Tag Manager -->
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id={{ env('GA_TRACKING_ID') }}"
                    height="0" width="0" style="display:none;visibility:hidden">
            </iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->

        <script>
            window.IMPRESSION_PRODUCTS = [];
            window.GLOBAL_PRUDUCTS = {};
            window.OWL_CAROUSEL_LOOP = true;
            window.OWL_OBJECTS = [];
            window.CAROUSEL_PRODUCTS = {};
            window.GLOBAL_SETTING = {
                url: '{{ url('') }}',
                locale_url: '{{ url(LaravelLocalization::getCurrentLocale())  }}',
                product_list_url: '{{ isset($list_url) ? $list_url : '' }}',
                modules: {
                    product: {
                        inputs: {!! isset($product_inputs) ? $product_inputs : json_encode([]) !!}
                    }
                },
                current_locale: '{{ LaravelLocalization::getCurrentLocale() }}',
                lang: <?php echo json_encode(__('frontend')) ?>,
                required_login: {{ session()->has('required_login') ? 1 : 0  }},
                logged_in: {{ App\Bootstrap\Helpers\AuthHelper::user() ? 1 : 0 }},
                pickup_times: '{!! env('MAKRO_PICKUP_TIME') !!}',
                reCaptchaSiteKey: '{!! env('NOCAPTCHA_SITEKEY') !!}',
                login_success: {{ session()->has('login_success') ? 1 : 0 }},
                current_store: {{ !\App\Bootstrap\Helpers\StoreHelper::getCurrentStore() ? 0 : \App\Bootstrap\Helpers\StoreHelper::getCurrentStore() }},
                confirm_change_store: {{ (isset($confirm_change_store) && $confirm_change_store) ? 1 : 0 }},
                locale: '{{ \App::getLocale() }}',
                direct_category_id: {{ isset($direct_category_id) ? $direct_category_id : 0 }},
                route_name: '{{ ! empty(request()->route()) ? request()->route()->getName() : '' }}',
                product_id: '{{isset($product_id)?$product_id:''}}',
                ga_recommended_product_list: '{{ env('GA_RECOMMENDED_PRODUCT_LIST', 'Recommended products') }}',
                ga_related_product_list: '{{ env('GA_RELATED_PRODUCT_LIST', 'Related products') }}',
                ga_recent_product_list: '{{ env('GA_RECENT_PRODUCT_LIST', 'Recent products') }}',
                is_loaded_related_products: 0,
                is_loaded_recent_products: 0,
                is_loaded_product_detail: 0,
                GA_CUSTOM_FIELD_ITEM_TYPE: '{{ env('GA_CUSTOM_FIELD_ITEM_TYPE') }}',
                GA_CUSTOM_FIELD_BUYER_ID: '{{ env('GA_CUSTOM_FIELD_BUYER_ID') }}',
                GA_CUSTOM_FIELD_BUYER_NAME: '{{ env('GA_CUSTOM_FIELD_BUYER_NAME') }}',
                GA_CUSTOM_FIELD_SUPPLIER_ID: '{{ env('GA_CUSTOM_FIELD_SUPPLIER_ID') }}',
                GA_CUSTOM_FIELD_SUPPLIER_NAME: '{{ env('GA_CUSTOM_FIELD_SUPPLIER_NAME') }}',
                GA_CUSTOM_FIELD_STORE_ID: '{{ env('GA_CUSTOM_FIELD_STORE_ID') }}',
                GA_CUSTOM_FIELD_STORE_AREA: '{{ env('GA_CUSTOM_FIELD_STORE_AREA') }}',
                GA_CUSTOM_FIELD_PAYMENT_TYPE: '{{ env('GA_CUSTOM_FIELD_PAYMENT_TYPE') }}',
                GA_CUSTOM_FIELD_SHIPPING_TYPE: '{{ env('GA_CUSTOM_FIELD_SHIPPING_TYPE') }}',
                GA_CUSTOM_FIELD_COST: '{{ env('GA_CUSTOM_FIELD_COST') }}',
                GA_CUSTOM_FIELD_MAKRO_MEMBER_ID: '{{ env('GA_CUSTOM_FIELD_MAKRO_MEMBER_ID') }}',
                GA_CUSTOM_FIELD_MAKRO_CUSTOMER_GROUP_ID: '{{ env('GA_CUSTOM_FIELD_MAKRO_CUSTOMER_GROUP_ID') }}',
                show_popup_error: {{ request()->session()->has('show_popup_error') ? 1 : 0 }},
                errors: {!! $errors->any() ? json_encode($errors->all()) : json_encode([]) !!},
                show_store_selector: {{ !env('ENABLE_SELECT_STORE', false) ? 0 : 1 }},
                shipping_title_message: '{{ $shippingTitleMessage }}',
                pickup_title_message: '{{ $pickupTitleMessage }}'
            };
        </script>

        <div id="makro-app">
            @include('partials.header')

            @yield('content')

            @include('partials.footer')
        </div>

        @yield('script-before')

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', '{{ env('GOOGLE_ANALYTIC_TRACKING_ID') }}', 'auto');
            ga('require', 'ec');
        </script>

        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=geometry"></script>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <script src="{{ asset(mix('/js/manifest.js')) }}"></script>
        <script src="{{ asset(mix('/js/vendor.js')) }}"></script>
        <script src="{{ asset(mix('/js/app.js')) }}"></script>

        <script src="{{ asset('assets/js/bootstrap.offcanvas.min.js') }}"></script>
        {{--<script type="text/javascript">--}}
            {{--var _gaq = _gaq || [];--}}
            {{--_gaq.push(['_setAccount', '{{ env('GOOGLE_ANALYTIC_TRACKING_ID') }}']);--}}
            {{--_gaq.push(['_setDomainName', 'jqueryscript.net']);--}}
            {{--_gaq.push(['_trackPageview']);--}}

            {{--(function() {--}}
                {{--var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;--}}
                {{--ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';--}}
                {{--var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);--}}
            {{--})();--}}
        {{--</script>--}}

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTIC_TRACKING_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '{{ env('GOOGLE_ANALYTIC_TRACKING_ID') }}');
        </script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="{{ asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>
        <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
        @yield('script')
        
        @if (! empty($msScript))
            <script>
                {!! $msScript !!}
            </script>
        @endif
        @stack('custom-scripts')
    </body>
</html>
