<template>
    <span>
        <button class="btn btn-primary" type="button" @click="reOrder" :disabled="isLoading">
            <i class="far fa-redo-alt"></i> {{ name }}
            <loading :show="isLoading"></loading>
        </button>
        <modal
                :show="showModal"
                :title="lang.sorry_for_your_inconvenience_all_products_are_unavailable"
                :show-header="false"
                :prevent-close="true"
                :content-size="'wide'"
                :size="'normal'"
                :show-loading="isLoading"
                :show-ok="false"
                @on-close-modal="closeModal"
        >
            <h3>{{ lang.sorry_for_your_inconvenience_all_products_are_unavailable }}</h3>
        </modal>
    </span>
</template>
<script>
    import {SET_RE_ORDER_DATA, SET_RE_ORDER_POPUP} from '../../store/mutation-types'
    import loading from '../loading/Loading'
    import Modal from '../bootstrap/Modal2'
    import popup from "../../libraries/popup";

    export default {
        name: 're-order',
        components: {
            loading,
            Modal
        },
        props: {
            name: {
                type: String,
                required: true
            },
            getProduct: {
                type: Promise,
                required: true
            }
        },
        data() {
            return {
                isLoading: false,
                showModal: false
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        },
        methods: {
            reOrder() {
                this.isLoading = true
                this.getProduct
                    .then(this.sendProducts)
            },

            sendProducts(products) {
                axios.post(this.$store.state.appModule.locale_url + '/carts/re-order', products)
                    .then((response) => {
                        if(response.data.status == 'error') {
                            popup.open('', response.data.message, 'error', true)
                        }
                        let data = _.get(response, 'data.data', null)
                        if (data.items.available.length == 0) {
                            this.isLoading = false
                            this.showModal = true
                        } else {
                            this.$store.commit(SET_RE_ORDER_DATA, _.get(response, 'data.data', null))
                            this.$store.commit(SET_RE_ORDER_POPUP, true)
                        }
                        this.isLoading = false
                    })
                    .catch((error) => {
                        this.$store.commit(SET_RE_ORDER_DATA, null)
                        this.isLoading = false
                    })
            },
            closeModal() {
                this.showModal = false
            }
        },
    }
</script>