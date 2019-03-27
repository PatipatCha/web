{{--
<div class="box-text-welcome2 box-welcome-member">
    <b>{{ trans('frontend.welcome_to_makroclick_store') }}</b>
</div>    
--}}
<?php
$user = \App\Bootstrap\Helpers\AuthHelper::user();
$first_name = array_get($user, 'profile.first_name');
if (empty($first_name)) {
    $first_name = array_get($user, 'username');
}
?>
<div class="box-text-welcome2 box-welcome-member">
    <span>{{ trans('frontend.hello') }}</span>
    <div class="dropdown">
        <a class="text_welcome" id="member-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true"
           aria-expanded="false" href="{{ route('members.profile') }}">
            <span class="name">{{ $first_name }}</span>
            <i class="far fa-user"></i>
            <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="member-dropdown">
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
        </ul>
    </div>
</div>