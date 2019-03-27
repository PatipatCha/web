<template>
    <button type="button" v-on:click="onClick" id="btn_continue_checkout">{{ buttonText }}</button>
</template>

<script>
    import { SET_LOGIN_CALLBACK } from '../../../store/mutation-types'
    import popup from '../../../libraries/popup'

    export default {
        name: 'cart-continue-button',
        props: {
            buttonText: {
                type: String
            },
            continueUrl: {
                type: String
            }
        },
        methods: {
            onClick: function () {
                if (!this.$store.state.appModule.logged_in) {
                    this.$store.commit(SET_LOGIN_CALLBACK, this.afterLogin);
                    $('#login-modal').modal('show');
                } else {
                    this.continueAction()
                }
            },
            afterLogin: function () {
                this.continueAction()
            },
            continueAction() {
                if (!this.checkCartAmount()) {
                    popup.open({
                        type: 'error',
                        message: '<div id="lbl_warning_product_max_min_order">' + this.lang.please_check_order_amount + '</div>',
                        confirmText: this.lang.okay,
                        btnId: 'btn_warning_product_max_min_order'
                    })
                } else {
                    window.location = this.continueUrl;
                }
            },
            checkCartAmount()
            {
                var items = this.$store.state.cartModule.items
                var passed = true
                for (var i = 0; i < items.length; ++i) {
                    for (var j = 0; j < items[i].items.length; ++j) {
                        let item = items[i].items[j];

                        let min = null
                        if (typeof item.content == 'object'
                            && typeof item.content.data == 'object'
                            && typeof item.content.data.minimum_order_limit == 'number'
                        )
                        {
                            min = item.content.data.minimum_order_limit
                        }

                        let max = null
                        if (typeof item.content == 'object'
                            && typeof item.content.data == 'object'
                            && typeof item.content.data.maximum_order_limit == 'number'
                        )
                        {
                            max = item.content.data.maximum_order_limit
                        }

                        let stock = _.get(item, 'content.data.stock')
                        if (typeof stock == 'number'
                            && stock < max
                        )
                        {
                            max = stock
                        }

                        if (min != null) {
                            if (item.quantity < min) {
                               return false
                            }
                        }

                        if (max != null) {
                            if (item.quantity > max) {
                                return false
                            }
                        }
                    }

                }

                return passed
            }
        },
        computed: {
            lang()
            {
                return this.$store.getters.lang
            }
        },
        mounted() {
            this.checkCartAmount()
        }
    }
</script>