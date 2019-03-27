<template>
    <div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 no-padding subscribe-container">
        <div class="footer-text-newsletter">
            {{ lang.newsletter }}
        </div>
        <div class="footer-form-newsletter pull-left">
            <input v-on:keyup.enter="addSubscribe" type="text" :data-rule-email="true" class="form-control-newsletter email-sub" :placeholder="lang.enter_your_email_here" v-model="email">
        </div>
        <div class="footer-btn-newsletter pull-left">
            <a href="javascript:;" v-on:click="addSubscribe">
                <div class="btn-newsletter">
                    {{ lang.subscribe }} <loader v-bind:show="showLoader"></loader>
                </div>
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
</template>
<script>

    import Loader from '../loading/Loading'
    import popup from '../../libraries/popup'

    export default {
        name: 'add-to-wish-list-button',
        components: {
            'loader': Loader
        },
        methods: {
            addSubscribe : function(e){
                $('.special-char-error', $('.subscribe-container')).hide()

                var $this = this;
                var check_mail = $this.isValidEmail(this.email);
                if(check_mail == true){
                    this.showLoader = true;
                    axios.post(this.$store.getters.locale_url + '/subscribe',{
                         email:this.email
                    })
                    .then(function (response) {
                        if(response.data.status == "active"){
                            // $this.showModal = true;
                            $this.onSubscribeSuccess()
                            $this.email = '';
                            $this.showLoader = false;
                        }else{
                            $this.onSubscribeError(response.data.message);
                            $this.showLoader = false;
                        }
                        
                    })
                    .catch(function (response) {
                        $this.onSubscribeError(response.data.message);
                        $this.showLoader = false;
                    });
                }else{
                    $this.onSubscribeError($this.lang.invalid_email_format);
                    $this.showLoader = false;
                }
                // setTimeout(function () {
                //     $this.showModal = true;
                //     $this.showLoader = false;
                // }, 2000);
                //this.showModal = true;
            },
            isValidEmail: function (value) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(value);
            },
            onCloseModal: function () {
                this.closeModal();
            },
            closeModal: function () {
                var $this = this;
                this.showModal = false;
                this.showModalError = false;
            },
            onSubscribeSuccess: function () {
                var url_img = this.url+'assets/images/icon-newsletter.png'
                var message = [
                    '<div class="modal-letter-box">',
                        '<img class="icon-newletter" style="color:red;" src="'+ url_img + '">',
                    '</div>',
                    '<div class="text-modal-letter">',
                        '<b>',
                            '<span style="color:#7ED321;">' + this.lang.thank_you_for_subscribe + '</span><br>',
                            '<span style="color:#333;">www.makroclick.com</span>',
                        '</b>',
                    '</div>',
                ] . join('')

                popup.open('', message, 'success')
            },
            onSubscribeError: function (response) {
                this.Errormessage = response;
                this.email = '';

                var message = [
                    '<div class="modal-letter-box">',
                        '<i class="fa fa-exclamation-circle fa-5x" aria-hidden="true" style="color: #F01616;"></i>',
                    '</div>',
                    '<div class="text-modal-letter">',
                        '<b>',
                            '<span style="color:#F01616;">' + this.Errormessage + '</span><br>',
                            '<span style="color:#333;">www.makroclick.com</span>',
                        '</b>',
                    '</div>',
                ] . join('')

                popup.open('', message, 'error')
                /*
                var $this = this;
                this.showModalError = true;
                this.email = '';
                this.Errormessage = response;*/
                
            }
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                showModal: false,
                showLoader: false,
                email: '',
                SuccessMessage:'Thank you for the news and follow up special results',
                showModalError: false,
                Errormessage: '',
                btnColse: 'Close'
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>
