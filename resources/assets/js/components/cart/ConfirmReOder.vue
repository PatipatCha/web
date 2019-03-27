<template>
    <div>
        <modal
                :show="show"
                :title="title"
                v-bind:show-header="false"
                v-on:on-ok="conformOrder"
                v-on:on-close-modal="closeModal"
                :prevent-close="true"
                :content-size="'wide'"
                :size="modalSize"
                :show-loading="isLoading"
        >
            <h3 v-if="rejectItems.length == 0">{{ title }}</h3>
            <reorder-item :items="rejectItems" v-if="!passed && items.length > 0"></reorder-item>
        </modal>
    </div>
</template>

<script>
    import Modal from '../bootstrap/Modal2'
    import {SET_RE_ORDER_DATA, SET_CART_ITEMS, SET_RE_ORDER_POPUP} from '../../store/mutation-types'
    import ReorderItem from './ReorderItem'
    import popup from '../../libraries/popup'

    export default {
        name: "confirm-re-order",
        components: {
            Modal,
            ReorderItem
        },
        data() {
            return {
                isLoading: false
            }
        },
        computed: {
            show: function () {
                return this.$store.state.cartModule.reOrderPopup
            },
            reOrderData: function () {
                return this.$store.getters.reOrder
            },
            items: function () {
                return _.get(this.reOrderData, 'items.available', [])
            },
            passed: function () {
                return _.get(this.reOrderData, 'passed', true)
            },
            modalSize: function () {
                return !this.passed && this.items.length > 0 ? 'large' : 'normal'
            },
            rejectItems: function () {
                return _.get(this.reOrderData, 'items.reject', [])
            },
            lang: function () {
                return this.$store.getters.lang;
            },
            title: function () {
                let text = _.replace(this.lang.please_confirm_to_add_items_to_the_cart, '{items}', this.items.length)
                if (this.items.length == 1) {
                    text = _.replace(this.lang.please_confirm_to_add_item_to_the_cart, '{items}', this.items.length)
                }
                return text
            }
        },
        methods: {
            conformOrder() {
                this.isLoading = true
                const availableItems = _.get(this.reOrderData, 'items.available')
                const data = _.map(availableItems, (item) => {
                    return {
                        content_id: item.content_id,
                        quantity: item.available_quantity
                    }
                })

                axios.post(this.$store.state.appModule.locale_url + '/carts/add-items', {items: data}).then(response => {
                    if (response.data.status === 'ok') {
                        this.$store.commit(SET_CART_ITEMS, response.data.data);
                        $.toast({
                            heading: this.lang.updated_cart_successful,
                            position: 'top-right',
                            icon: 'success'
                        });
                        this.closeModal()
                        setTimeout(() => {
                            this.$store.commit(SET_RE_ORDER_DATA, null)
                        }, 500)
                    } else {
                        this.closeModal()
                        setTimeout(() => {
                            this.$nextTick(() => {
                                popup.open(this.lang.could_not_add_product_to_cart, response.data.message, 'error');
                            })
                            this.$store.commit(SET_RE_ORDER_DATA, null)
                        }, 500)
                    }

                    this.isLoading = false
                }).catch(error => {
                    this.closeModal()
                    setTimeout(() => {
                        this.$nextTick(() => {
                            popup.open(this.lang.could_not_add_product_to_cart, response.data.message, 'error');
                        })
                        this.$store.commit(SET_RE_ORDER_DATA, null)
                    }, 500)
                    this.isLoading = false
                })
            },
            closeModal() {
                this.$store.commit(SET_RE_ORDER_POPUP, false)
                // this.$store.commit(SET_RE_ORDER_DATA, null)
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>