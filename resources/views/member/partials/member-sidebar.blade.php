<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">
    <div class="menu-my-account-box">
        <div class="menu-my-account">
            <?php $routeName = request()->route()->getName(); ?>
            <ul>
                <li class="{{ $routeName == 'members.profile' ? 'active' : '' }}">
                    <a href="{{ route('members.profile') }}">
                        <!-- <img src="{{ asset('assets/images/icon-my-profile-17px.png') }}"/> -->
                        <i class="far fa-user"></i> {{ trans('frontend.my_profile') }}
                    </a>
                </li>
                <li class="{{ $routeName == 'members.taxAddress' ? 'active' : '' }}">
                    <a href="{{ route('members.taxAddress') }}">
                        <!-- <img src="{{ asset('assets/images/icon-taxinvoice-100px.png') }}" style="max-width: 17px;" /> -->
                        <i class="far fa-money-check-alt"></i> {{ trans('frontend.tax_address') }}
                    </a>
                </li>
                {{-- <li><a href="{{ route('members.store') }}"><img src="{{ asset('assets/images/icon-my-shipping-17px.png') }}"/> My Store</a></li> --}}
                <li class="{{ $routeName == 'members.shipping' ? 'active' : '' }}">
                    <a href="{{ route('members.shipping') }}">
                        <!-- <img src="{{ asset('assets/images/icon-my-shipping-17px.png') }}"/> -->
                        <i class="far fa-map-marker-alt"></i> {{ trans('frontend.my_shipping') }}
                    </a>
                </li>
                <li class="{{ $routeName == 'members.wishlist' ? 'active' : '' }}">
                    <a href="{{ route('members.wishlist') }}">
                        <!-- <img src="{{ asset('assets/images/icon-my-wishlist-17px.png') }}"/> -->
                        <i class="far fa-heart"></i> {{ trans('frontend.my_wishlist') }}
                    </a>
                </li>
                <li class="{{ $routeName == 'members.orders' ? 'active' : '' }}">
                    <a href="{{ route('members.orders') }}">
                        <!-- <img src="{{ asset('assets/images/icon-my-order-17px.png') }}"/> -->
                        <i class="far fa-file-alt"></i> {{ trans('frontend.my_order') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>