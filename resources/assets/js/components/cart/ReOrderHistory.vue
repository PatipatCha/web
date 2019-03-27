<template>
    <re-order :get-product="getProducts" :name="name"></re-order>
</template>

<script>
    import ReOrder from './ReOrder'

    export default {
      name: 're-order-history',
      components: {
        ReOrder
      },
      props: {
        products: {
          type: Array,
          required: true
        },
        name: {
          type: String,
          default: 'สั่งซื้อซ้ำอีกครั้ง'
        }
      },
      computed: {
        getProducts() {
          return new Promise((resolve, reject) => {
              resolve(this.products)
          })
        }
      }
    }
</script>