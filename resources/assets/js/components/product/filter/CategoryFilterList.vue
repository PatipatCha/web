<template>
    <div>

    </div>
</template>

<script>
    export default {
        name: 'product-category-filter-list',
        props: {
            item: {
                type: Object,
                required: true
            },
            level: {
                type: Number,
                default: 0
            }
        }
    }
</script>