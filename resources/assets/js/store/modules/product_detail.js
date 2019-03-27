import * as mutationTypes from '../mutation-types'
import AddToCartButton from '../../components/cart/AddToCartButton'
import store from '../'
import AddToWishListButton from '../../components/wishlist/AddToWishListButton'

const state = {
    quantity: 1
}

const mutations = {
    [mutationTypes.PRODUCT_DETAIL_UPDATE_STEPPER_NUMBER](state, number){
        state.quantity = number;
    }
}

const actions = {
    stepUp: function({state, commit, getters, rootState}, number){
        commit(mutationTypes.PRODUCT_DETAIL_UPDATE_STEPPER_NUMBER, number)
    },
    stepDown: function({state, commit, getters, rootState}, number){
        commit(mutationTypes.PRODUCT_DETAIL_UPDATE_STEPPER_NUMBER, number)
    },
    setQuantity: function({state, commit, getters, rootState}, number) {
        commit(mutationTypes.PRODUCT_DETAIL_UPDATE_STEPPER_NUMBER, number)
    },
    initCarousel: function ({state}, wrapper) {
        //console.log(CAROUSEL_PRODUCTS)
        const reCreateAddButtons = () => {
            //Add to cart button
            var els = $('.owl-carousel .add-to-cart-wrapper');
            els.each(function () {
                var productId = $(this).attr('data-id');
                var data = CAROUSEL_PRODUCTS[productId]

                var addToCartWrapper= {
                    'template': '<div data-id="' + productId + '" class="add-to-cart-wrapper"><div class="box-b-cart pull-left"><add-to-cart-button product=\'' +  data + '\' v-bind:button_type="2"></add-to-cart-button></div></div>'
                };

                new Vue({
                    store,
                    el: this,
                    components: {
                        'add-to-cart-button': AddToCartButton
                    },
                    render: h => h(addToCartWrapper)
                });
            });


            //Add to wish list button
            var wish_list_els = $('.owl-carousel .add-to-wish-list-wrapper');
            wish_list_els.each(function () {
                var productId = $(this).attr('data-id');
                var data = CAROUSEL_PRODUCTS[productId]
                var addToWishListWrapper= {
                    'template': '<div data-id="' + productId + '" class="add-to-wish-list-wrapper"><div class="box-b-wishlist pull-right"><add-to-wish-list-button product=\'' + data + '\'></add-to-wish-list-button></div></div>'
                };
                new Vue({
                    store,
                    el: this,
                    components: {
                        'add-to-wish-list-button': AddToWishListButton
                    },
                    render: h => h(addToWishListWrapper)
                });
            });
        }

        setTimeout(() => {
            reCreateAddButtons();
        }, 600)


        $('.owl-carousel').on('changed.owl.carousel', function() {
            setTimeout(() => {
                reCreateAddButtons();
            }, 600)
        })

        $('.owl-carousel').on('translated.owl.carousel', function() {
            setTimeout(() => {
                reCreateAddButtons();
            }, 600)
        })
    }
}

export default {
    state,
    mutations,
    actions
}