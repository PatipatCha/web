<div class="container">
    <div class="step-box">
        <ol class="cd-multi-steps text-bottom count">
            <li id="lbl_in_cart" class="{{ \App\Bootstrap\Helpers\CartHelper::getCartStepClass($current, 1) }}">{!!  \App\Bootstrap\Helpers\CartHelper::getCartStepLink($current, 1, trans('frontend.cart_step_1'), route('carts.index')) !!}</li>
            <li id="lbl_address" class="{{ \App\Bootstrap\Helpers\CartHelper::getCartStepClass($current, 2) }}">{!!  \App\Bootstrap\Helpers\CartHelper::getCartStepLink($current, 2, trans('frontend.cart_step_2'),  route('carts.checkout')) !!}</li>
            <li id="lbl_review" class="{{ \App\Bootstrap\Helpers\CartHelper::getCartStepClass($current, 3) }}">{!!  \App\Bootstrap\Helpers\CartHelper::getCartStepLink($current, 3, trans('frontend.cart_step_3'),  route('carts.shipping')) !!}</li>
            <li id="lbl_success" class="{{ \App\Bootstrap\Helpers\CartHelper::getCartStepClass($current, 4) }}">{!!  \App\Bootstrap\Helpers\CartHelper::getCartStepLink($current, 4, trans('frontend.cart_step_4'), '') !!}</li>
        </ol>
    </div>
</div>