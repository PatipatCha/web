<template>
    <div>
        <h3 v-if="!isEmpty">{{ lang.my_shopping_cart }}</h3>
        <div v-if="!isEmpty">
            <group-items
                    v-if="data.items.length > 0"
                    v-bind:title="data.title"
                    v-bind:alias="data.alias"
                    v-bind:items="data.items"
                    v-for="data in datas"
                    :key="data.id"
                    :can-edit="canEdit"
                    :description="data.description"
                    :btn-id="data.id_btn"
                    :config-date="dateConfig"
            ></group-items>
        </div>
        <div v-if="isEmpty" class="cart-empty">
            <img :src="url+'assets/images/icon-cart2.png'" alt="">
            <p id="lbl_no_item_in_cart">{{ lang.your_cart_is_empty }}</p>
            <a v-bind:href="continueUrl" class="btn btn-primary" id="btn_go_to_shopping">{{ lang.choose_product }}</a>
        </div>
    </div>
</template>

<script>
    import GroupItems from './GroupItems'
    import { SET_CART_ITEMS, SET_CART_SUMMARY } from '../../../store/mutation-types'
    import popup from '../../../libraries/popup'

    export default{
        name: 'cart-container',
        data: function () {
            return {
                url: this.$store.getters.url + '/'
            }
        },
        components: {
            'group-items': GroupItems
        },
        props: {
            cart_data: {
                type: Object
            },
            cartSummary: {
                type: Object
            },
            continueUrl: {
                type: String
            },
            canEdit: {
                type: Boolean,
                default: true
            },
            dateConfig: {
                type: Object
            }
        },
        beforeCreate: function () {
            if (typeof CART_DATA.data == 'object' || typeof CART_DATA.data == 'function') {
                this.$store.commit(SET_CART_ITEMS, CART_DATA.data);
            }

        },
        mounted: function () {
            this.$store.commit(SET_CART_SUMMARY, this.cartSummary)
        },
        computed: {
            datas: function () {
                return this.$store.state.cartModule.items
            },
            isEmpty: function () {
                return this.$store.getters.cartCount < 1 ? true : false
            },
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>