<template>
    <a href="javascript:;" v-on:click.prevent="increment" id="btn_increase_item">
        <!-- <img class="pull-right" v-bind:src="url+'/assets/images/plus.png'" width="20"> -->
        <i class="fa fa-plus"></i>
    </a>
</template>

<script>
    export default {
        name: 'cart-increment',
        data: function(){
            return {
                url: this.$store.getters.url
            };
        },
        props: ['item'],
        methods:{
            increment: function () {
                this.$emit('loading')

                var payload = {
                    item: this.item,
                    callback: this.loaded
                };
                this.$store.dispatch('incrementCartItem', payload);
            },

            loaded: function () {
                this.$emit('loaded')
            }
        }
    }
</script>