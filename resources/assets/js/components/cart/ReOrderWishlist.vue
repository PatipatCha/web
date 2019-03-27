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
            name: {
                type: String,
                default: 'เพิ่มสินค้าทั้งหมดลงตระกร้า'
            }
        },
        computed: {
            getProducts() {
                return new Promise((resolve, reject) => {
                    axios.get(this.$store.state.appModule.locale_url + '/members/list-wish-list')
                        .then((result => {
                            const items = _.get(result, 'data.data.items')
                            const products = items.map((item) => {
                                return {
                                    content_id: _.get(item, 'content.id'),
                                    quantity: 1
                                }
                            })

                            resolve(products)
                        }))
                        .catch((error) => {
                            reject(error)
                        })
                })
            }
        }
    }
</script>