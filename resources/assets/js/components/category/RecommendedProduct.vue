<template>
    <div ref="recommendedProducts" v-bind:class="divClass"></div>
</template>

<script>
    import axios from 'axios'
    import _ from 'lodash'

    export default {
        name: 'recommended-products',
        props: {
            categorySlug: {
                type: String
            },
            categoryId: {
                type: Number
            },
            categoryType: {
                type: String
            },
            categoryLevel: {
                type: String
            },
            hasBanner: {
                type: Number,
                default: false
            },
            apiType: {
                type: String,
                default: 'recommend'
            },
            divClass: {
                type: String,
                default: ''
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

                var owlCarousel = $(this.$refs.recommendedProducts).owlCarousel({
                    loop:this.loop,
                    margin:0,
                    nav:false,
                    responsive:{
                        0:{
                            items:2
                        },
                        600:{
                            items:3
                        },
                        1000:{
                            items:4
                        }
                    },
                    onInitialized: function (event) {
                        if (event.item.count <= event.page.size) {
                            $('.owl-nav', $(event.target)).hide()
                        }
                    }
                })

                OWL_OBJECTS.push(owlCarousel);
                this.$store.dispatch('initCarousel', $(this.$refs.recommendedProducts))
            },
            shouldLoop(products) {
                const width = getViewportDimension()[0]
                let responsiveItems = 2
                if (width < 600) {
                    responsiveItems = 2
                } else if (width < 1000) {
                    responsiveItems = 4
                } else {
                    responsiveItems = 5
                }

                var len = _.size(products)
                if (len <= responsiveItems) {
                    this.loop = false
                } else {
                    this.loop = true
                }
            }
        },
        mounted()
        {
            axios.get(this.$store.state.appModule.locale_url + '/category/recommended-products/' + this.categorySlug + '?categoryId=' + this.categoryId + '&categoryType=' + this.categoryType + '&hasBanner=' + this.hasBanner + '&apiType=' + this.apiType + '&categoryLevel=' + this.categoryLevel)
            .then((response) => {
                $(this.$refs.recommendedProducts).html(response.data.content);

                IMPRESSION_PRODUCTS.push({
                    'name': GLOBAL_SETTING.ga_recommended_product_list,
                    'items': {}
                })

                for (let i in response.data.products) {
                    CAROUSEL_PRODUCTS[i] = response.data.products[i]
                    IMPRESSION_PRODUCTS[0]['items'][i] = {
                        'refer_object': 'CAROUSEL_PRODUCTS',
                        'id': i
                    }
                }
                gaTriggerImpression()

                if (!this.hasBanner && _.isEmpty(response.data.products)) {
                    $('#recommended-products-wrapper').remove()
                }

                this.shouldLoop(response.data.products)
                this.initialCarousel()
            })
            .catch((error) => {

            })
        }
    }
</script>