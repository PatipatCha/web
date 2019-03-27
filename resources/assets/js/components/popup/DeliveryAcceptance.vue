<template>
    <div>
        <modal v-bind:show="true"
               :show-header="false"
               :prevent-close="true"
               content-size="full"
               :show-cancel="false"
               @on-ok="onConfirm"
               :center="false"
            
        >
            <div class="text-center">
                <h3 class="f-600 mgb-0 text-suggestion">ยินดีต้อนรับสู่แม็คโครคลิกโฉมใหม่</h3>

                <!--<h3 class="f-600 mgb-0 text-suggestion">New Makroclick - more simpler - more convenient<br>Bangkok metropolitan area home delivery</h3>-->
                <p>
                    ระหว่างวันที่ 31 มี.ค. - 31 ส.ค. 61<br />
                    สั่งออนไลน์ พร้อมบริการจัดส่ง ทั่วกรุงเทพฯ และปริมณฑลเท่านั้น<br />
                    ต่างจังหวัดแล้วพบกันเร็วๆ นี้
                </p>

                <h3 class="f-600 mgb-0 text-suggestion">Welcome to the new Makroclick.com</h3>

                <!--<h3 class="f-600 mgb-0 text-suggestion">New Makroclick - more simpler - more convenient<br>Bangkok metropolitan area home delivery</h3>-->
                <p>
                    During 31 Mar - 31 Aug 2018<br />
                    Makroclick will offer online shopping with delivery service for customer<br />
                    within Bangkok and vicinity only.<br />
                    You will be notified when we extend the service and delivery areas.

                </p>

                <hr>
            </div>
            
            <div class="col-sm-9 col-sm-offset-2 no-padding">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1" v-model="accept" id="chk_accept_delivery_condition">
                        <span>
                            กรุณายืนยันข้อมูล และเงื่อนไขการให้บริการข้างต้น
                            <br/>/ Please confirm to accept on the above service and delivery condition

                        </span>
                        <br>
                        <span v-if="showError" class="text-suggestion">
                            กรุณายืนยันข้อมูล และเงื่อนไขการให้บริการข้างต้น
                            <br />/ Please confirm to accept on the above service and delivery condition
                        </span>
                    </label>
                </div>
            </div>
            <div class="form-group has-error" v-if="submitError && !showError">
                <span class="help-block">{{ lang['400_error_title'] }}</span>
            </div>

            <div slot="footer">
                <div class="btn-wrap">
                    <button  type="button" class="btn btn-block btn-primary" @click="onConfirm" id="btn_continue_shopping"><loading v-bind:show="showLoading"></loading> เลือกซื้อสินค้าต่อ / Continue</button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from '../bootstrap/Modal2'
    import popup from '../../libraries/popup'

    export default {
        name: 'delivery-acceptance-popup',
        components: {
            Modal
        },
        props: {
            submitUrl: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                accept: false,
                showError: false,
                showLoading: false,
                submitError: false
            }
        },
        methods: {
            onConfirm() {
                this.submitError = false
                if (this.accept) {
                    this.showError = false
                    this.submit()
                } else {
                    this.showError = true
                }
            },

            submit() {
                this.showLoading = true
                axios.post(this.submitUrl)
                    .then((response) => {
                        window.location.reload()
                    })
                    .catch((error) => {
                        this.submitError = true
                        this.showLoading = false
                    })
            }
        },
        watch: {
            accept: function () {
                if (this.accept && this.showError) {
                    this.showError = false
                }
            }
        },
        computed: {
            lang() {
                return this.$store.state.appModule.lang
            }
        }
    }
</script>