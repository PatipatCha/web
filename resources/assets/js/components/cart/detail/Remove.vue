<template>
    <button class="btn-remove" data-toggle="modal" v-on:click="remove" id="btn_delete_item">{{ lang.remove }}</button>
</template>

<script>

    export default{
        name: "cart-remove-item",
        data: function () {
            return {
                url: this.$store.getters.url
            }
        },
        props: ['item', 'lang'],
        methods: {
            remove: function () {
                this.$store.dispatch('removeCartItem', this.item)
            }
        }
    }
</script>