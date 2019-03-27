import store from '../store'
import AddToCartButton from '../components/cart/AddToCartButton'
import Cart from '../components/cart/Cart'
import CartContainer from '../components/cart/detail/CartContainner'
import CartSummary from '../components/cart/detail/Summary'
import Stepper from '../components/cart/Stepper'
import TotalPrice from '../components/cart/TotalPrice'
import CartContinueButton from '../components/cart/detail/ContinueButton'
import Shipping from '../components/cart/checkout/Shipping'
import AddToCartSuccess from '../components/cart/AddToCartSuccess'
import CartSuccess from '../components/cart/Success'
import RetryPaymentButton from '../components/cart/RetryPaymentButton'
import AddToCartErrorLowStock from '../components/cart/AddToCartErrorLowStock'
import ReOrderHistory from '../components/cart/ReOrderHistory'
import ReOrderWishList from '../components/cart/ReOrderWishlist'
import ConfirmReOrder from  '../components/cart/ConfirmReOder'

Vue.component('add-to-cart-button', AddToCartButton)
Vue.component('cart', Cart)
Vue.component('cart-container', CartContainer)
Vue.component('cart-summary', CartSummary)
Vue.component('stepper', Stepper)
Vue.component('total-price', TotalPrice)
Vue.component('cart-continue-button', CartContinueButton)
Vue.component('cart-shipping', Shipping)
Vue.component('add-to-cart-success', AddToCartSuccess)
Vue.component('cart-success', CartSuccess)
Vue.component('retry-payment-button', RetryPaymentButton)
Vue.component('add-to-cart-error-low-stock', AddToCartErrorLowStock)
Vue.component('re-order-history', ReOrderHistory)
Vue.component('re-order-wish-list', ReOrderWishList)
Vue.component('confirm-re-order', ConfirmReOrder)

//Get cart from api

store.dispatch('receiveCartFromApi');