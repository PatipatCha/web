<template>
    <div class="container margin-bottom" ref="wrapper">
        <div class="col-lg-12 no-padding">
            <div class="topic-related-product">
                <b>{{ related_product_view }}</b>
            </div>

            <div class="recent-product owl-carousel" ref="reletedProducts">

            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import _ from 'lodash'

    export default {
        name: 'related-products',
        props: {
            productId: {
                type: Number,
                required: true
            }
        },
        data() {
            return {
                loop: false
            }
        },
        methods: {
            initialCarousel()
            {
                $('.owl-carousel').carousel({
                    interval: false
                })

                var owlCarousel = $(this.$refs.reletedProducts).owlCarousel({
                    loop:this.loop,
                    margin:0,
                    nav:true,
                    responsive:{
                        0:{
                            items:2,
                            slideBy: 1,
                        },
                        600:{
                            items:4,
                            slideBy: 1,
                        },
                        1000:{
                            items: 6,
                            slideBy: 1,
                        }
                    },
                    onInitialized: function (event) {
                        console.log(event)
                        if (event.item.count <= event.page.size) {
                            $('.owl-nav', $(event.target)).hide()
                        }
                    }
                })

                OWL_OBJECTS.push(owlCarousel);

                this.$store.dispatch('initCarousel', $(this.$refs.wrapper))
            },

            shouldLoop(products) {
                const width = getViewportDimension()[0]
                let responsiveItems = 2
                if (width < 600) {
                    responsiveItems = 2
                } else if (width < 1000) {
                    responsiveItems = 4
                } else {
                    responsiveItems = 6
                }

                var len = _.size(products)
                if (len <= responsiveItems) {
                    this.loop = false
                } else {
                    this.loop = true
                }
            }
        },
        computed: {
            related_product_view()
            {
                return this.$store.state.appModule.lang.related_product_view
            },
            products()
            {
                return []
            }
        },
        mounted() {
            axios.get(this.$store.state.appModule.locale_url + '/product/' + this.productId + '/related')
                .then((response) => {
                    $(this.$refs.reletedProducts).html(response.data.content)

                    const index = IMPRESSION_PRODUCTS.length
                    IMPRESSION_PRODUCTS.push({
                        name: GLOBAL_SETTING.ga_related_product_list,
                        items: {}
                    })
                    for (let i in response.data.products) {
                        CAROUSEL_PRODUCTS[i] = response.data.products[i]
                        IMPRESSION_PRODUCTS[index]['items'][i] = {
                            'refer_object': 'CAROUSEL_PRODUCTS',
                            'id': i
                        }
                    }

                    GLOBAL_SETTING.is_loaded_related_products = 1

                    gaProductDetailWasViewd()

                    this.shouldLoop(response.data.products)
                    this.initialCarousel()
                })
                .catch((error) => {
                    GLOBAL_SETTING.is_loaded_related_products = 1
                })
        }
    }
</script>