<template>
    <div>
        <modal v-bind:show="show"
               v-bind:title="title"
               v-on:on-ok="onConfirm"
               v-on:on-cancel="onCancel"
               v-bind:show-header="showHeader"
               v-on:on-close-modal="onClose"
               v-bind:show-cancel="showCancel"
               v-bind:center="center"
               v-bind:show-loading="showLoading"
               v-bind:close-on-blur="closeOnBlur"
               v-bind:content-size="size"
               v-bind:ok-text="confirmText"
               v-bind:cancel-text="cancelText"
               :btn-id="btnId"
               :prevent-close="preventClose"
               :error-text="errorText"
        >
            <div v-html="message">

            </div>
        </modal>
    </div>
</template>

<script>
  import Modal from '../bootstrap/Modal2'

  export default {
    name: 'default-popup',
    components: {
      Modal
    },
    data: function () {
      return {
        showLoading: false
      }
    },
    methods: {
      onConfirm: function () {
        var me = this
        var callback = this.$store.state.appModule.popup.confirm
        if (callback) {
          this.showLoading = true
          callback(function () {
            me.onLoadingDone()
          })
        } else {
          this.onClose()
        }
      },
      onCancel: function () {
        var callback = this.$store.state.appModule.popup.cancel
        if (callback) {
          callback()
        }

        this.onClose()
      },
      onClose: function () {
        this.$store.state.appModule.popup.show = false
        var callback = this.$store.state.appModule.popup.onClose
        if (callback) {
          callback()
        }
      },
      onLoadingDone: function () {
        this.showLoading = false
      }
    },
    computed: {
      lang: function () {
        return this.$store.getters.lang
      },
      showHeader: function () {
        if (!this.$store.state.appModule.popup.title || this.$store.state.appModule.popup.title == '') {
          return false
        }

        return true
      },
      title: function () {
        return this.$store.state.appModule.popup.title
      },
      show: function () {
        return this.$store.state.appModule.popup.show === true ? true : false
      },
      message: function () {
        return this.$store.state.appModule.popup.message
      },
      showCancel: function () {
        return this.$store.state.appModule.popup.type == 'confirm' ? true : false
      },
      center: function () {
        return this.$store.state.appModule.popup.center === true ? true : false
      },
      closeOnBlur: function () {
        return this.$store.state.appModule.popup.type == 'confirm' ? false : true
      },
      size: function () {
        return this.$store.state.appModule.popup.size
      },
      confirmText: function () {
        return this.$store.state.appModule.popup.confirmText
      },
      cancelText: function () {
        return this.$store.state.appModule.popup.cancelText
      },
      btnId: function () {
        return this.$store.state.appModule.popup.btnId
      },
      preventClose: function () {
        return this.$store.state.appModule.popup.preventClose
      },
      errorText() {
        return this.$store.state.appModule.popup.errorText
      }
    }
  }
</script>