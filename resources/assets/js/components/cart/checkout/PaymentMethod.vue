<template>
    <div class="payment-method-tabs clearfix">
        <h4 class="pay-pickup-text text-bold">
            <b>{{ lang.payment }}</b>
        </h4>
        <label class="error" for="payment_method[payment_method]" id="lbl_select_delivery_address"></label>

        <div class="payment-method-row clearfix">
            <ul class="nav nav-tabs hidden-xs" role="tablist">
                <li
                        v-for="payment in paymentMethods"
                        :key="payment.id.toString()"
                        :id="'payment-' + payment.id"
                        @click="selectPaymentMethod(payment)"
                        :class="payment_id == payment.id ? 'active': '' "
                >
                    <a :href="'#payment-' + payment.id" class="tabs-toggle" :aria-controls="'payment-' + payment.id"
                       role="tab" data-toggle="tab" :id="'opt_select_payment_' + payment.payment_gateway_code">
                        <div class="icon">
                            <img :src=" '/images/' + payment.payment_gateway_driver.toLowerCase() + '.png'" class="img-responsive" alt="Image">
                            <!--<input type="radio" name="payment_method[payment_method]" v-model="payment_id" :value="payment.id.toString()" />-->
                        </div>
                        <div class="title">
                            <h4 class="payment-method-type">{{ payment.title }}</h4>
                            <h5 class="sub-title">{{ payment.subtitle }}</h5>
                        </div>
                    </a>

                    <div class="tab-pane-desktop">
                        <a :href="'#payment-' + payment.id" class="tabs-toggle" aria-controls="true-wallet" role="tab"
                           data-toggle="tab">
                            <div class="icon">
                                <img :src=" '/images/' + payment.payment_gateway_driver.toLowerCase() + '.png'" class="img-responsive" alt="Image">
                            </div>
                            <div class="title">
                                <h4 class="payment-method-type">{{ payment.title }}</h4>
                                <h5 class="sub-title">{{ payment.subtitle }}</h5>
                            </div>
                        </a>
                        <div class="tab-pane-panel">
                            <div class="content" v-if="isSelected(payment)" v-html="payment.detail">

                            </div>
                            <div class="content" v-if="!isMaximum(payment)">
                                <div class="invalid-payment text-center">
                                    <p><img :src="$store.state.appModule.url + '/images/icon-fail.png'" height="64"></p>
                                    <div class="text-danger">
                                        <p>{{ lang.not_use_payment_method }} <span>{{ payment.title }}</span> {{ lang.have
                                            }}</p>
                                        <p>{{ lang.avilable_maximum }} <span>{{ payment.maximum_payment | money }}</span>
                                            {{ lang.baht }}</p>
                                    </div>
                                    <p>{{ lang.please_select_another_payment_options }}</p>
                                </div>
                            </div>
                            <div class="content" v-if="!isMinimum(payment)">
                                <div class="invalid-payment text-center">
                                    <p><img :src="$store.state.appModule.url + '/images/icon-fail.png'" height="64"></p>
                                    <div class="text-danger">
                                        <p>{{ lang.not_use_payment_method }} <span>{{ payment.title }}</span> {{ lang.have
                                            }}</p>
                                        <p>{{ lang.avilable_minimum }} <span>{{ payment.minimum_payment | money }}</span>
                                            {{ lang.baht }}</p>
                                    </div>
                                    <p>{{ lang.please_select_another_payment_options }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="tab-content col-sm-8" :class="payment_id && over? '': 'visible-xs'">
                <!-- default state -->
                <!-- <div role="tabpanel" class="tab-pane active" id="payment-default" v-if="!payment_id && !over">
                    <div class="content" v-html="lang.default_payment_description"></div>
                </div> -->

                <!-- true wallet -->
                <div v-for="payment in paymentMethods" :key="payment.id.toString()" :id="'payment-' + payment.id + '-mobile'" @click="selectPaymentMethod(payment)"
                    role="tabpanel" class="payment-method-group tab-pane visible-xs" :class="payment_select == payment.id ? 'active': ''">
                    <a :href="'#payment-' + payment.id" class="tabs-toggle" aria-controls="true-wallet" role="tab" data-toggle="tab">
                        <div class="icon">
                            <img :src=" '/images/' + payment.payment_gateway_driver.toLowerCase() + '.png'" class="img-responsive" alt="Image">
                        </div>
                        <div class="title">
                            <h4 class="payment-method-type">{{ payment.title }}</h4>
                            <h5 class="sub-title">{{ payment.subtitle }}</h5>
                        </div>
                    </a>
                    <div class="tab-pane-panel">
                        <div class="content" v-if="isSelected(payment)" v-html="payment.detail">
                
                        </div>
                        <div class="content" v-if="!isMaximum(payment)">
                            <div class="invalid-payment text-center">
                                <p><img :src="$store.state.appModule.url + '/images/icon-fail.png'" height="64"></p>
                                <div class="text-danger">
                                    <p>{{ lang.not_use_payment_method }} <span>{{ payment.title }}</span> {{ lang.have }}
                                    </p>
                                    <p>{{ lang.avilable_maximum }} <span>{{ payment.maximum_payment | money }}</span>
                                        {{ lang.baht }}</p>
                                </div>
                                <p>{{ lang.please_select_another_payment_options }}</p>
                            </div>
                        </div>
                        <div class="content" v-if="!isMinimum(payment)">
                            <div class="invalid-payment text-center">
                                <p><img :src="$store.state.appModule.url + '/images/icon-fail.png'" height="64"></p>
                                <div class="text-danger">
                                    <p>{{ lang.not_use_payment_method }} <span>{{ payment.title }}</span> {{ lang.have }}
                                    </p>
                                    <p>{{ lang.avilable_minimum }} <span>{{ payment.minimum_payment | money }}</span>
                                        {{ lang.baht }}</p>
                                </div>
                                <p>{{ lang.please_select_another_payment_options }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="payment_method[payment_method]" class="first-do-not-ignore"
                   :value="payment_id"/>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'cart-checkout-payment-method',
        props: {
            paymentMethods: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                payment_id: '',
                payment_select: '',
                over: false
            }
        },
        methods: {
            selectPaymentMethod(payment) {
                window.$('.payment-method-row .fa-check-circle').css('display', 'none')
                window.$('.payment-method-row .fa-circle').css('display', 'inline-block')
                window.$('.payment-method-row .fa-circle').css('color', '#ddd')

                this.payment_select = payment.id.toString()

                if (this.isMaximum(payment) && this.isMinimum(payment)) {
                    this.payment_id = payment.id.toString()
                    this.over = false


                    window.$('#payment-' + payment.id.toString() + ' .fa-circle').css('display', 'none')
                    window.$('#payment-' + payment.id.toString() + '-mobile .fa-circle').css('display', 'none')
                    window.$('#payment-' + payment.id.toString() + ' .fa-check-circle').css('display', 'inline-block')
                    window.$('#payment-' + payment.id.toString() + '-mobile .fa-check-circle').css('display', 'inline-block')
                } else {
                    this.payment_id = ''
                    this.over = true
                }
            },
            isSelected(payment) {
                if (this.isMaximum(payment) && this.isMinimum(payment)) {
                    return true
                }
                return false
            },
            isMaximum(payment) {
                if (payment.maximum_payment == null) {
                    return true
                }
                if (parseFloat(payment.maximum_payment) < this.grandTotal) {
                    console.log(parseFloat(payment.maximum_payment) + ',' + this.grandTotal)
                    return false
                }
                return true
            },
            isMinimum(payment) {
                if (payment.minimum_payment == null) {
                    return true
                }
                if (parseFloat(payment.minimum_payment) > this.grandTotal) {
                    return false
                }
                return true
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            grandTotal: function () {
                return parseFloat(this.$store.getters.grandTotal)
            }
        }
    }
</script>