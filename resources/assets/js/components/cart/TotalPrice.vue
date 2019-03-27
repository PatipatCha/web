<template>
    <div class="product-detail-totalprice">
        Total Price&nbsp;
        <span style="color:#F01616;"><b>{{ totalPrice | money }} ฿</b></span>
    </div>
</template>

<script>
    export default{
        name: "totalPrice",
        props: ["price"],
        computed: {
            totalPrice: function(){
                return this.price * this.$store.state.ProductDetail.quantity
                return 0;
            }
        }
    }
</script>