<template>
    <div class="row">
        <item
                v-bind:item="item"
                v-bind:index="index"
                v-on:on-edit="onEdit"
                v-on:on-remove="onRemove"
                :key="item.id"
                v-for="(item, index) in items"></item>

        <div class="col-md-12" v-if="items.length < 1">
            <div class="alert alert-warning" >
                Shipping address is empty! Please adding address.
            </div>
        </div>

    </div>
</template>

<script>
    import Item from './ListItem'
    export default {
        name: 'shipping-address-list',
        components: {
            Item
        },
        props: {
            items: {
                type: Array,
                default: []
            }
        },
        methods: {
            onEdit: function (index) {
                this.$emit('on-edit', index)
            },
            onRemove: function (index) {
                this.$emit('on-remove', index)
            }
        },
        watch: {
            items: function () {

            }
        }
    }
</script>