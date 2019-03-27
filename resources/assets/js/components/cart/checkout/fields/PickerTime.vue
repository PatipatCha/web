<template>
    <div class="btn-group">
        <select class="form-control" v-bind:name="inputName">
            <option v-bind:value="time" v-for="time in times">{{ time }} {{ lang.clock }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: 'cart-checkout-picker-time',
        props: ['inputName'],
        data: function () {
            return {
                times: this.$store.state.appModule.pickup_times
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>