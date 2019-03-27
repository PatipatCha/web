<template>
    <div v-if="getFullAddress(item).length" style="cursor: pointer">
        <b>{{ getFullAddress(item) }}</b>
    </div>
</template>

<script>
    import FullAddress from '../../cart/checkout/address/FullAddress'

    export default {
        name: "AddressItem",
        props: {
            item: {
                required: true
            },
            searchText: {
                required: true,
            }
        },
        components: {
            FullAddress
        },
        methods: {
            getFullAddress(address) {
                let text = ''
                let locale = this.$store.getters.locale
                if (!_.isEmpty(address)) {
                    text += _.get(address.sub_districts.original_name, locale) + ' '
                    text += _.get(address.district.original_name, locale) + ' '
                    text += _.get(address.province.original_name, locale) + ' '
                    text += _.get(address, 'postcode')
                }
                return text
            }
        }
    }
</script>

<style scoped>

</style>