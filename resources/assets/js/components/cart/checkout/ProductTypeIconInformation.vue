<template>
    <a tabindex="0" role="button"
       data-toggle="popover"
       v-bind:data-trigger="trigger"
       v-bind:data-content="information_message"
       v-bind:data-placement="getPlacement"
       :id="btnId"
    >
        <i class="fas fa-info-circle"></i>
    </a>
</template>

<script>
    export default {
        name: 'cart-product-type-icon-information',
        props: {
            type: {
                type: String,
                required: true
            },
            placement: {
                type: String,
                default: 'auto bottom'
            },
            trigger: {
                type: String,
                default: 'focus hover'
            },
            directContent: {
                type: String,
                default: ''
            },
            btnId: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            information_message: function () {
                if (this.directContent != '') {
                    return this.directContent
                }
                var message = '';
                switch (this.type) {
                    case 'assortment':
                        message = this.lang.assortment_information;
                        break;
                    case 'non_assortment_without_installment':
                        message = this.lang.non_assortment_without_installment_information;
                        break;
                    case 'non_assortment_with_installment':
                        message = this.lang.non_assortment_with_installment_information;
                        break;
                }
                return message;
            },
            getPlacement() {
                if ($( window ).width() <= 600) {
                    return 'top'
                }
                return this.placement
            }
        },
        mounted() {
            // Init Popover
            setTimeout(() => {
                $('[data-toggle="popover"]').popover({
                    html: true,
                    container: 'body'
                });
            })

        }
    }
</script>