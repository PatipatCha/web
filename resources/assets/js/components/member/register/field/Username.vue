<template>
    <div>
        <div>
            <label for="input_username">{{ label }} <span style="color:#F01616;"> *</span></label>
        </div>
        <div v-bind:class="boxClasses" ref="username_container">
            <div class="has-feedback" v-bind:class="usernameClass">
                <input type="text" class="form-control" v-bind:placeholder="placeHolder" v-model.trim="username"
                       v-on:blur="checkUsername" name="username" id="txt_username">
                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" v-show="available"></span>

                <div id="lbl_username">
                    <div class="remark" v-show="show_username_error">
                        {{ username_error_message }}
                    </div>
                    <div class="remark" v-show="facebook_not_pull_email">
                        {{ lang.facebook_not_pull_email }}
                    </div>
                    <div class="remark" v-show="show_username_checking && !username_not_available">
                        {{ lang.please_check_your_username }}
                    </div>
                    <div class="remark" v-show="username_not_available && !checking && !show_username_error">
                        {{ lang.username_is_not_available }}
                    </div>
                    <div class="remark" v-show="custom_error !== false && !checking && !show_username_error">
                        {{ custom_error }}
                    </div>
                    <div class="text-success" v-show="available && !checking">
                        {{ lang.username_is_available }}
                    </div>
                </div>
            </div>

        </div>
        <div class="box-btn-verify pull-left" v-if="showCheckUsername">
            <button class="btn-verify" type="button" v-on:click="verifyUsername" id="btn_verify_email">{{ lang.verify }}
                <loader :show="checking"></loader>
            </button>
        </div>
        <div class="clearfix"></div>
    </div>
</template>

<script>
  import axios from 'axios'
  import Loader from '../../../loading/Loading'
  import popup from '../../../../libraries/popup'

  export default {
    name: 'username-field',
    components: {
      'loader': Loader
    },
    props: {
      initUsername: {
        type: String,
        default: ''
      },
      directAvailable: {
        type: Number,
        default: false
      },
      directAvailable: {
        type: Number,
        default: 0
      },
      directUsernameNotAvailable: {
        type: Number,
        default: 0
      },
      facebookNotPullEmail: {
        type: Number,
        default: 0
      },
      label: {
        type: String,
        default: 'Email or Mobile'
      },
      showCheckUsername: {
        type: Boolean,
        default: true
      },
      emptyBoxClass: {
        type: Boolean,
        default: false
      },
      placeHolder: {
        type: String,
        default: 'sample@sample.com / 099xxxxxxx'
      },
      needVerifyUsername: {
        type: Boolean,
        default: true
      }
    },
    data: function () {
      return {
        username: this.initUsername,
        number_of_username_blur: 0,
        username_correct: false,
        show_username_error: false,
        username_error_message: '',
        username_type: '',
        card_id_response: {
          id: '',
          customer_name: ''
        },
        available: this.directAvailable == 1 ? true : false,
        checking: false,
        show_username_checking: false,
        checking_username_message: '',
        username_not_available: this.directUsernameNotAvailable == 1 ? true : false,
        facebook_not_pull_email: this.facebookNotPullEmail == 1 ? true : false,
        isBlur: false,
        preventCheck: false,
        custom_error: false
      }
    },
    methods: {
      checkUsername: function () {
        this.custom_error = false
        this.isBlur = true
        var valid = false;
        //Check username only it's not empty
        ++this.number_of_username_blur;

        if (this.username != '') {
          var result = false;
          var type = '';
          //If user input number only

          let username = this.username.replace(/\-/g, '')
          console.log(username)

          let isNumber = !isNaN(username)
          if (isNumber) {
            //Validate phone format
            result = this.isValidPhone(this.username);
            type = 'phone';
            console.log('phone')
            this.username_type = 'phone'
          } else {
            //Validate email format
            result = this.isValidEmail(this.username);
            type = 'email';
            this.username_type = 'email'
            console.log('email')
          }

          if (result === true) {
            this.username_correct = true;
            valid = true;
          } else {
            this.username_correct = false;
            this.checking = false;
            this.available = false;

            switch (type) {
              case 'phone':
                this.username_error_message = this.lang.invalid_phone;
                break;
              case 'email':
                this.username_error_message = this.lang.invalid_email_format;
                break;
            }
          }
        } else {
          this.username_error_message = this.lang.register_username_required;
          this.username_correct = false;
          this.show_username_error = true;
          this.username_not_available = false;
        }

        return valid;
      },

      isValidPhone: function (value) {
        value = value.replace(/\-/g, '')
        if (value.match(/^(06|08|09)[0-9]{8}$/i)) {
          return true;
        }

        return false;
      },

      isValidEmail: function (value) {
        // var re = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        // let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        // let re = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        // let re  = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
        // let re = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        let re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return re.test(value);
      },

      checkUsernameIsChecked: function () {
        if (this.needVerifyUsername) {
          if (this.username_correct && !this.available && this.checking == false) {
            this.show_username_checking = true;
          } else {
            this.show_username_checking = false;
          }
        } else {
          this.show_username_checking = false;
        }

      },
      verifyUsername: function () {
        $('.special-char-error', $(this.$refs.username_container)).hide()

        if (this.username_correct) {
          this.checking = true;
          this.sendVerifyUsername();
        } else {
          this.checkUsername();
        }

      },
      sendVerifyUsername: function () {
        var me = this;
        me.username_not_available = false;
        me.available = false;

        let username = this.username
        if (this.username_type == 'phone') {
          username = username.replace(/\-/g, '')
        }
        axios.get(this.$store.getters.locale_url + '/members/verify-username?username=' + encodeURIComponent(username))
          .then(function (response) {
            me.checking = false;
            if (response.data.status == 'ok') {
              if (!response.data.data[0].exists) {
                me.preventCheck = true
                me.username = me.username.toLowerCase()
                if (me.username_type == 'phone') {
                  me.username = me.username.replace(/\-/g, '')
                }
                me.available = true;
              } else {
                me.username_not_available = true;
              }
            } else {
              if (_.get(response, 'data.show_inline') == 1) {
                me.$nextTick(() => {
                  me.show_username_checking = false
                  me.custom_error = _.get(response, 'data.message')
                })

              } else {
                me.verifyUsernameError(response);
              }

            }
          })
          .catch(function (error) {
            me.verifyUsernameError(response);
          });
      },
      verifyUsernameError: function (response) {
        popup.open('', response.data.message, 'error')
      },
      isUsernameVerified: function () {
        return this.available
      }
    },
    computed: {
      usernameClass: function () {
        var hasSuccess = false;
        var hasFeedback = false;
        var hasError = false;
        if (this.number_of_username_blur > 0) {
          this.facebook_not_pull_email = false;

          if (this.username_correct) {
            if (this.available) {
              hasError = false;
              hasSuccess = true;
              hasFeedback = true;
              this.show_username_error = false;
            } else {
              hasError = false;
              hasSuccess = false;
              hasFeedback = false;
              this.show_username_error = false;
            }
          } else {
            hasError = true;
            hasSuccess = false;
            hasFeedback = false;
            this.show_username_error = true;
          }
        }
        return {
          'has-success': hasSuccess,
          'has-feedback': hasFeedback,
          'has-error': hasError
        }
      },
      boxClasses: function () {
        var bool = true;
        if (this.emptyBoxClass === true) {
          bool = false;
        }
        return {
          'box-MakroCardID': bool,
          'pull-left': bool
        }
      },
      lang: function () {
        return this.$store.getters.lang;
      }
    },
    watch: {
      username: function (newValue, oldValue) {
        if (newValue != oldValue && !this.preventCheck) {
          this.available = false;
        }

        this.preventCheck = false

        if (this.isBlur) {
          this.checkUsername()
        }
      },
      username_correct: function () {
        this.checkUsernameIsChecked();
      },
      available: function () {
        this.checkUsernameIsChecked();
      },
      checking: function () {
        this.checkUsernameIsChecked();
      }
    },
    mounted: function () {
      this.checking_username_message = this.lang.please_check_your_username;
    }
  };
</script>