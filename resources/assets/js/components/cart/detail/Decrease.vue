<template>
    <a href="javascript:;" v-on:click.prevent="decrement" id="btn_decrease_item">
        <!-- <img class="pull-left" v-bind:src="url+'/assets/images/minus.png'" width="20"> -->
        <i class="fa fa-minus"></i>
    </a>
</template>

<script>
    export default{
        name: 'cart-decrease',
        data: function(){
            return {
                url: this.$store.getters.url,
                currentQuantity: this.item.quantity
            }
        },
        props: ['item'],
        methods: {
            decrement: function () {
                // If user decrement quantity until 0
                // Ask user "Do you want to delete this item?"
                if(this.item.quantity == 1){
                    this.$store.dispatch('removeCartItem', this.item)
                }else{


                    this.doDecrement();
                }
            },
            doDecrement: function () {
                var payload = {
                    item: this.item,
                    callback: this.loaded
                };

                this.$store.dispatch('decrementCartItem', payload);
                this.$emit('loading')
            },
            loaded: function () {
                this.$emit('loaded')
            }
        }
    }
</script>