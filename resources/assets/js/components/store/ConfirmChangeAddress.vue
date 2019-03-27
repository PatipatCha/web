<template>
    <div class="address-modal-box new-address-modal-box">
        <div class="title text-pd" v-html="message"></div>
        <div class="cta">
            <div class="pull-left">
                <button
                        type="button"
                        class="btn btn-default"
                        @click="() => { onCancel() }"
                        id="btn_popup_cancel_change_shipping_address"
                >
                    <i class="fas fa-arrow-left"></i> {{ trans('cancel')}}
                </button>
            </div>

            <div class="pull-right">
                <button type="button"
                        class="btn btn-primary"
                        @click="submit"
                        id="btn_popup_confirm_change_shipping_address"
                >
                    <i class="fas fa-check" v-if="!submitting"></i>
                    <loading :show="submitting"></loading>
                    {{ trans('ok') }}
                </button>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="text-right">
                        <div class="error" v-if="submitError && submitError.length > 0">
                            {{ submitError }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Loading from '../loading/Loading'

    export default {
        name: 'confirm-change-address',
        components: {
            Loading
        },
        props: {
            onCancel: {
                type: Function,
                default: () => {}
            },
            onSubmit: {
                type: Function,
                default: () => {}
            },
            submitting: {
                type: Boolean,
                default: false
            },
            submitError: {
                type: String,
                default: ''
            }
        },
        methods: {
            submit() {
                this.onSubmit()
            }
        },
        computed: {
            message() {
                return this.trans('confirm_change_delivery_address_message')
            }
        }
    }
</script>