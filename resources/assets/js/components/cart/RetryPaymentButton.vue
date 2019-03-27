<template>
    <a href="javascript:;" class="btn btn-block btn-continue" id="btn_retry_payment" onclick="onClick">{{ lang.retry_payment }}</a>
</template>

<script>
    export default {
        name: 'retry-payment-button',
        computed: {
            lang() {
                return this.$store.getters.lang
            }
        },
        methods: {
            onClick() {
                axios.post(this.$store.getters.locale_url + '/carts/shipping')
                    .then((response) => {

                    })
                    .catch((error) => {

                    })
            }
        }
    }
</script>