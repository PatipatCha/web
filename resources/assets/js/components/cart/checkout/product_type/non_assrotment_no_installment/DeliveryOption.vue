<template>
    <div>
        <div class="product-delivery-box">
            <label class="radio-inline">
                <input type="radio" v-bind:name="inputName" value="pickup_at_store" v-model="deliveryMethod">
                รับสินค้าที่ Store Name
            </label>
        </div>

        <div class="product-delivery-box">
            <label class="radio-inline">
                <input type="radio" v-bind:name="inputName" value="same_as_buyer_information" v-model="deliveryMethod">
                รับสินค้าที่อยู่เดียวกับข้อมูลผู้สั่งซื้อ
            </label>
        </div>

        <div class="product-delivery-box">
            <label class="radio-inline">
                <input type="radio" v-bind:name="inputName" value="other_address" v-model="deliveryMethod">
                รับสินค้าที่อื่น ๆ
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'cart-checkout-non-assortment-delivery-option',
        props: {
            inputName: {
                type: String,
                required: true
            }
        },
        data: function () {
            return {
                deliveryMethod: null
            }
        },
        watch: {
            deliveryMethod: function () {
                this.$emit('change', this.deliveryMethod)
            }
        }
    }
</script>