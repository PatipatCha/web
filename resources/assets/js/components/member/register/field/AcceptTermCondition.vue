<template>
    <div class="box-link">
        <input type="checkbox" v-model="accept_term_condition" id="chk_accept_terms_and_conditions">&nbsp;&nbsp;{{ lang.accept }} <a href="#" @click="openModalAccept" id="lnk_terms_and_conditions">{{ lang.term_and_condition }}</a>
        <br/>

        <div id="lbl_accept_terms_and_conditions">
             <span class="remark" v-show="error_term_condition_required">
                {{ lang.accept_and_term_condition_validate }}
            </span>
        </div>

        <modal :show="showAcceptModal" v-bind:title="lang.customer_info" v-on:on-close-modal="onAcceptModalClose">
            <div slot="header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="myModalLabel"><b>{{ lang.term_and_condition }}</b></h5>
            </div>

            <div>
                <div class="scroll-300" v-html="termsConditions">
                </div>

                <div class="clearfix"></div>
            </div>

            <div slot="footer">
                <button type="button" class="btn btn-primary" v-on:click="onAcceptModalClose">{{ lang.btn_confirm }}</button>
            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from '../../../bootstrap/Modal'
    export default {
        name: 'accept-term-condition-field',
        components: {
            'modal': Modal
        },
        props: {
            termsConditions: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                accept_term_condition: false,
                error_term_condition_required: false,
                showAcceptModal: false
            }
        },
        watch: {
            accept_term_condition: function (value) {
                if (value === true) {
                    this.error_term_condition_required = false;
                } else {
                    this.error_term_condition_required = true;
                }
            },
        },
        methods: {
            checkTermCondition: function () {
                var valid = false;
                if (this.accept_term_condition === true) {
                    this.error_term_condition_required = false;
                    valid = true;
                }  else {
                    this.error_term_condition_required = true;
                }

                return valid;
            },

            openModalAccept: function(){
                this.showAcceptModal = true
            },

            onAcceptModalClose: function(){
                this.showAcceptModal = false
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }

</script>