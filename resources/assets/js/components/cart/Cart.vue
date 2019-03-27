<template>
    <div>
        <div class="box-badge2 cart-summary" v-if="!mobile">
            <a v-bind:href="cartUrl">
                <i class="far fa-shopping-cart"></i>
                <!-- <div class="box-bg-icon">
                    <div class="box-icon-cart">
                        <img v-bind:src="iconCartImage" width="25px"/>
                    </div>
                </div> -->
                <div v-if="count > 0">
                    <span class="badge">{{ count | number_comma }}</span>
                </div>
            </a>
        </div>
        <div class="box-badge3" v-if="mobile">
		    <a v-bind:href="cartUrl">
                <i class="far fa-shopping-cart"></i>
                <!-- <div class="box-bg-icon">
                    <div class="box-icon-cart">
                        <img v-bind:src="iconCartImage" width="25px" />
                    </div>
				</div> -->
				<div v-if="count > 0">
				    <span class="badge">{{ count | number_comma }}</span>
				</div>
            </a>
		</div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import { ADD_TO_CART, UPDATE_CART_ITEM_QTY} from '../../store/mutation-types'

    export default {
        name: 'cart',
        props: {
            cartUrl: {
                type: String
            },
            mobile: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            count: function() {
                return this.$store.getters.cartCount;
            }
        },
        data: function()
        {
            return {
                iconCartImage: this.$store.getters.url + '/assets/images/icon-Cart.png'
            }
        },
        methods: {
            clearCart: function () {
                if (confirm('Clear cart?')) {
                    this.$store.dispatch('clearCart')
                }
            }
        }

    }
</script>