<template>
    <div>
        <div class="g-recaptcha" v-bind:data-sitekey="dataSiteKey" data-callback="reCaptchaCallback">
            
        </div>

        <div id="lbl_identify_robot">
             <span class="remark" v-if="isError && !recaptchaChecked" >
            {{ lang.please_verify_you_are_not_robot }}
        </span>
        </div>

        <div class="clearfix"></div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
     export default {
         name: 'recaptcha-input',
         data: function () {
             return{
                 dataSiteKey: this.$store.state.appModule.reCaptchaSiteKey,
                 isError: false
             }
            
         },
         methods: {
             validateReCaptcha: function () {
                 var passed = reCaptchaValid === false ? false : true;
                 if (!passed) {
                    this.isError = true;
                 } else {
                      this.isError = false;
                 }

                 return passed;
             }
         },
         computed: {
             ...mapGetters([
                 'recaptchaChecked'
             ]),
             lang: function () {
                 return this.$store.getters.lang;
             }
         }
     }
 </script>