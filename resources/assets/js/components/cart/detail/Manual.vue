<template>
    <!--<input-->
            <!--v-on:blur="update"-->
            <!--v-on:keyup.enter="update"-->
            <!--type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'-->
            <!--v-model.number="amount" class="no-spinners"-->
            <!--id="txt_quantity"-->
    <!--/>-->
    <input
            v-on:blur="update"
            v-on:keyup.enter="update"
            type="text"
            v-model="amount"
            class="no-spinners not-prevent-special-chars txt_quantity"
    />
</template>

<script>
    import popup from '../../../libraries/popup'

    export default{
        name: 'cart-manual',
        props: ['item'],
        data: function () {
            return {
                amount: this.item.quantity,
              loading: false
            }
        },
        methods: {
            update: function(){
                this.amount = parseInt(this.amount)
                if (isNaN(this.amount)) {
                    this.amount = 1
                }

                this.formatNumber(this.amount);

                if(this.amount <= 0){
                    this.removeCartItem();
                }else{
                    if (this.amount != parseInt(this.item.quantity)) {
                      if (!this.loading) {
                        this.loading = true
                        this.$emit('loading');
                        var payload = {
                          item: this.item,
                          amount: this.amount,
                          callback: this.loaded,
                          error: this.onError
                        }

                        // this.item.quantity = this.amount;
                        this.$store.dispatch('updateCartItem', payload)
                      }

                    }
                }
            },

            removeCartItem: function () {
                var payload = {
                    item: this.item,
                    cancel_callback: this.onCancelRemove
                };

                this.$store.dispatch('removeCartItem', payload)
            },

            loaded: function (isError) {
              this.loading = false
                this.$emit('loaded')
            },

            onError: function () {
                console.log('onError')
                this.amount = this.item.quantity;
            },

            onCancelRemove: function () {
                this.amount = this.item.quantity;
            },

            formatNumber: function (number) {
                var amount = parseInt(this.amount);
                if (isNaN(amount)) {
                    this.amount = this.item.quantity;
                    popup.open('', this.lang.quantity_must_be_number_only, 'info')
                } else if (amount < 1) {
                    this.removeCartItem();
                }
            }
        },

        watch: {
            item: function () {
                this.amount = this.item.quantity
            },
            amount: function () {
                if (this.amount != '') {
                    this.amount = parseInt(this.amount)

                    if (isNaN(this.amount) || !this.amount || this.amount < 1) {
                        this.amount = 1;
                    }
                }
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang
            }
        }
    }
</script>