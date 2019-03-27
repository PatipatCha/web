<template>
    <div v-show="show">
        <div v-if="showLabel">
            <label>{{ lang.makro_id_card }}</label>
        </div>
        <div class="box-MakroCardID pull-left" ref="makro_card_container">
            <div class="form-group has-feedback" v-bind:class="makroIdClass">
                <input :checkcard="is_verify_inprofile" type="text" :name="fieldName" class="form-control form-control-MakroCardID pull-left" placeholder="1019999999999" v-model.trim="makro_card_id" v-on:keyup="inputCardId" id="txt_makro_member_card" @blur="blurInput">
                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" v-show="valid_card_id"></span>

                <div id="lbl_makro_member_card">
                    <span class="remark" v-show="error_card_id_required && !valid_card_id">
                            {{ lang.please_enter_a_makro_card_id }}
                        </span>

                    <span class="remark" v-show="(error_card_id_not_verify || showValidateCardId) && !valid_card_id">
                            {{ lang.please_verify_makro_card_id }}
                        </span>

                    <span class="remark" v-show="has_invalid_card_error && !error_card_id_required && !valid_card_id">
                            {{ lang.invalid_makro_card_id }}
                        </span>

                    <span class="remark" v-show="has_used_card_error && !error_card_id_required && !valid_card_id">
                            {{ lang.makro_card_already_in_use }}
                        </span>
                </div>
            </div>

        </div>
        <div class="box-btn-verify pull-left">
            <button v-if="!showVerifyButton" class="btn-verify" type="button" v-bind:disabled="disabledVerifyButton" v-on:click="checkCardId" id="btn_verify_member">{{ check_card_id_button_name }} <loader :show="showLoading"></loader></button>
        </div>
        <div class="clearfix"></div>

        <!--<div class="box-link">
            <a href="#" data-toggle="modal" data-target="#myModal02">{{ lang.apply_new_makro_membership }}</a>
        </div>-->



        <!-- Verify Makro Card ID Modal  -->
        <modal :show="showVerifyCardIdModal" v-bind:title="lang.customer_info" v-on:on-close-modal="onCloseVerifyCardIdModal" :show-cancel="false" @on-ok="confirmVerifyCardId">
            <div slot="header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="myModalLabel"><b>{{ lang.customer_info }}</b></h5>
            </div>

            <div>
                <div v-if="valid_card_id">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding">
                        <div class="text-customer-info">
                            <b>{{ lang.makro_id_card }} :</b>
                        </div>
                    </div>
                    <div class="text-customer-info2">
                        {{ card_id_response.id }}
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding">
                        <div class="text-customer-info">
                            <b>{{ lang.customer_name }} :</b>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 no-padding">
                        <div class="text-customer-info2">
                            {{ card_id_response.customer_name }}
                        </div>
                    </div>
                </div>

                <div v-if="!valid_card_id">
                    <span class="text-danger">{{ lang.invalid_makro_card_id }}</span>
                </div>

                <div class="clearfix"></div>
            </div>

            <!--<div slot="footer">-->
                <!--<button type="button" class="btn btn-primary" v-on:click="confirmVerifyCardId">{{ verify_card_id_button_name }}</button>-->
            <!--</div>-->
        </modal>
        <!-- Verify Makro Card ID Modal  -->

    </div>
</template>

<script>
    import Modal from '../../../bootstrap/Modal2'
    import Loader from '../../../loading/Loading'
    import popup from '../../../../libraries/popup'
    import {trans} from "../../../../libraries/trans";

    export default {
        name: 'makro-card-id-field',
        components: {
            'modal': Modal,
            'loader': Loader
        },
        props: {
            cardId: {
                type: String,
                default: ''
            },
            disabledButton: {
                type: Boolean,
                default: false
            },
            fieldName: {
                type: String,
                default: ''
            },
            checkCard: {
                type: Boolean,
                default: false
            },
            show: {
                type: Boolean,
                default: false
            },
            showMakroStoreType: {
                type: String,
                default: ''
            },
            showLabel: {
                type: Boolean,
                default: true
            }
        },
        data: function () {
            return {
                makro_card_id: this.cardId,
                showVerifyCardIdModal: false,
                valid_card_id: false,
                card_id_response: {
                    id: '',
                    customer_name: ''
                },
                verify_card_id_button_name: '',
                error_card_id_required: false,
                error_term_condition_required: false,
                check_card_id_button_name: '',
                error_card_id_not_verify: false,
                submitting: false,
                verifyCardIdCallback: null,
                check_card_id_button_name: '',
                showVerifyCardIdModal: false,
                showLoading: false,
                disabledButtonWhenNotNull: this.cardId && this.cardId.length > 0 ? true : false,
                is_verify_inprofile: true,
                showValidateCardId: '',
                has_invalid_card_error: false,
                has_used_card_error: false,
                isSubmitted: false,
                isOnBlur: false
            }
        },
        methods: {
            blurInput() {
                if (this.showMakroStoreType == 'card') {
                    this.isOnBlur = true
                    this.validateRequired()
                }
            },

            inputCardId: function () {
                if(this.checkCard == true){
                    if(this.makro_card_id == this.cardId || this.makro_card_id.length == 0){
                        this.disabledButtonWhenNotNull = true
                        this.is_verify_inprofile = true
                    }else{
                        this.disabledButtonWhenNotNull = false
                        this.is_verify_inprofile = false
                    }
                }



            },

            checkCardId: function () {
                $('.special-char-error', $(this.$refs.makro_card_container)).hide()

                if (this.makro_card_id != '') {
                    if (!this.validateCartIdFormat()) {
                        this.makro_card_id_correct = false;
                        this.error_card_id_required = true;
                    } else {
                        this.makro_card_id_correct = true;
                        this.error_card_id_required = false;

                        this.verifyCardId(this.makro_card_id);
                    }

                } else {
                    this.makro_card_id_correct = false;
                    this.error_card_id_required = true;
                }
            },

            validateCartIdFormat: function () {
                return this.makro_card_id.match(/^\d{13,14}$/);
            },

            checkCardIdRequired: function () {
                var valid = false;
                if (this.makro_card_id != '') {
                    this.makro_card_id_correct = true;
                    this.error_card_id_required = false;
                    valid = true;
                } else {
                    this.makro_card_id_correct = false;
                    this.error_card_id_required = true;
                }

                return valid;
            },

            checkCardIdRequiredAndVerify: function () {
                var valid = this.checkCardIdRequired();
                if (valid) {
                    if (this.valid_card_id === true) {
                        this.error_card_id_not_verify = false;
                    } else {
                        this.error_card_id_not_verify = true;
                    }
                }
            },

            verifyCardId: function (value, callback, done) {
                var me = this;
                this.verifyCardIdCallback = callback;

                this.showLoading = true;
                this.error_card_id_not_verify = false
                this.isSubmitted = true
                axios.get(this.$store.getters.locale_url + '/members/makro-card/' + value)
                    .then(function (response) {
                        if (done) {
                            done()
                        }

                        popup.close()

                        me.showLoading = false;

                        if (response.data.status == 'ok') {
                            me.valid_card_id = true;
                            me.card_id_response.id = response.data.data.card_id;
                            me.card_id_response.customer_name = response.data.data.customer_name;
                            me.verify_card_id_button_name = me.lang.confirm;
                            me.error_card_id_not_verify = false;
                            me.showVerifyCardIdModal = true;
                            me.is_verify_inprofile = true
                        }else if(response.data.status == 'used'){
                            me.valid_card_id = false;
                            me.is_verify_inprofile = true
                            me.verify_card_id_button_name = me.lang.ok;
                            me.inUsesCard();
                        } else {
                          if (parseInt(_.get(response, 'data.errorCode')) == 219001) {
                            popup.open({
                              type: 'error',
                              title: me.lang.could_not_continue_proceeding,
                              message: trans('makro_card_error', { code: 219001 })
                            })

                          } else {
                            me.valid_card_id = false;
                            me.is_verify_inprofile = true
                            me.verify_card_id_button_name = me.lang.ok;
                            me.inValidVerifyCard();
                          }

                        }

                    })
                    .catch(function () {
                        if (done) {
                            done()
                        }

                        me.valid_card_id = false;
                        me.verify_card_id_button_name = me.lang.ok;
                        me.showLoading = false;
                        me.inValidVerifyCard();
                    });
            },

            inValidVerifyCard: function() {
                // this.has_invalid_card_error = true
                // this.makro_card_id = this.cardId;
                // popup.open('', this.lang.invalid_makro_card_id, 'warning', true)
                this.has_invalid_card_error = true
                this.has_used_card_error = false


            },

            inUsesCard: function() {
                this.has_invalid_card_error = false
                this.has_used_card_error = true
                // this.makro_card_id = this.cardId;
                // popup.open('', this.lang.makro_card_already_in_use, 'warning', true)
            },

            onCloseVerifyCardIdModal: function () {
                this.showVerifyCardIdModal = false;
            },

            confirmVerifyCardId: function () {
              console.log('confirmVerifyCardId')
                if (this.valid_card_id === true) {
                    if (typeof this.verifyCardIdCallback == 'function' || typeof this.verifyCardIdCallback == 'object') {
                        this.verifyCardIdCallback.apply(this, []);
                    }
                } else {
                    this.makro_card_id = '';
                }

                this.showVerifyCardIdModal = false;
            },

            recheckCardId: function (callback) {
                var me = this;

                console.log('showMakroStoreType', this.showMakroStoreType)

                if (this.showMakroStoreType == 'store') {
                    console.log('Not recheckCardId')
                    callback.apply(me, []);
                } else {
                    console.log('Yes recheckCardId')
                    if ((this.makro_card_id && this.makro_card_id.length > 0) && this.valid_card_id !== true) {

                        popup.open(this.lang.verify_makro_card_id, this.lang.confirm_verify_makro_card_id, 'confirm', true, function (done) {
                            if (me.validateCartIdFormat()) {
                                me.verifyCardId(me.makro_card_id, function () {
                                    callback.apply(me, [])
                                    popup.close()
                                }, done);
                            } else {
                                done()
                                me.makro_card_id_correct = false;
                                me.error_card_id_required = true;
                            }
                        }, function () {
                            me.makro_card_id = '';
                            callback.apply(me, []);
                        })
                    } else {
                        callback.apply(me, []);
                    }
                }
            },

            onCloseVerifyCardIdModal: function () {
                this.showVerifyCardIdModal = false;
            },

            validate() {
                if (!this.valid_card_id) {
                    this.showValidateCardId = true
                } else {
                    this.showValidateCardId = false

                }
                return this.valid_card_id
            },

            validateRequired() {
                if (this.showMakroStoreType != 'store') {
                    this.has_invalid_card_error = false
                    this.has_used_card_error = false

                    if (!this.valid_card_id) {
                        if (this.validateCartIdFormat() && this.makro_card_id != '') {
                            this.error_card_id_required = false
                            this.error_card_id_not_verify = true
                        } else {
                            this.error_card_id_required = true
                            this.error_card_id_not_verify = false
                        }

                        return false
                    }
                    // if (this.makro_card_id == '') {
                    //     this.error_card_id_required = true
                    //     this.error_card_id_not_verify = false
                    //     return false
                    // } else {
                    //     if (this.validateCartIdFormat()) {
                    //         if (!this.valid_card_id) {
                    //             this.error_card_id_required = false
                    //             this.error_card_id_not_verify = true
                    //         }
                    //     }
                    // }
                }

                this.error_card_id_not_verify = false
                this.error_card_id_required = false
                return true
            }
        },

        watch: {
            makro_card_id: function (newValue, oldValue) {
                this.valid_card_id = false;
                this.has_invalid_card_error = false
                this.has_used_card_error = false

                if (this.isOnBlur && newValue != '') {
                    if (this.validateCartIdFormat()) {
                        this.error_card_id_not_verify = true
                        this.error_card_id_required = false
                    } else {
                        this.error_card_id_not_verify = false
                        this.error_card_id_required = true
                    }
                }

            }
        },

        computed: {
            makroIdClass: function () {
                return {
                    'has-error': this.makro_card_id_correct === false || this.showValidateCardId === true || this.error_card_id_required === true ? true : false,
                    'has-success': this.valid_card_id === true ? true : false
                }
            },
            disabledVerifyButton: function() {
                return this.disabledButtonWhenNotNull
            },
            lang: function () {
                return this.$store.getters.lang;
            },
            showVerifyButton: function() {
                if(this.checkCard){
                    return this.is_verify_inprofile
                }
                return false
            }
        },

        mounted: function () {
            this.verify_card_id_button_name = this.lang.confirm_verify_makro_card_id;
            this.check_card_id_button_name = this.lang.verify;
            this.check_card_id_button_name = this.lang.verify;
        }
    };
</script>