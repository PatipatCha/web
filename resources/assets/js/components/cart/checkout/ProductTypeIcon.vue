<template>
    <span class="icon" v-html="icon">
    </span>
</template>

<script>
    export default {
        name: 'cart-product-type-icon',
        props: {
            type: {
                type: String,
                required: true
            },
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/'
            }
        },
        computed: {
            icon: function () {
                var image = '';
                switch (this.type) {
                    case 'foobar':
                        image = 'icon-pick&pack2.png';
                        break;
                    case 'preorder':
                        image = '<i class="far fa-clock"></i>';
                        break;
                    case 'stocked':
                        image = '<i class="far fa-truck-container"></i>';
                        break;
                    case 'pickup-stocked':
                        image = '<i class="far fa-hand-holding-box"></i>';
                        break;
                    case 'pickup-preorder':
                        image = '<i class="far fa-hand-holding-box"></i>';
                        break;
                }
                return image;
            }
        }
    }
</script>