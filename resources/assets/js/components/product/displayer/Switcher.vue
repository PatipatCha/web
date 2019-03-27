<template>
    <div class="box-icon-layout">
        <span class="pull-left">
            <b>{{ lang.sorter_order_by }}</b>
        </span>
        <a href="javascript:;" class="icon-layout-thumb pull-left" v-bind:class="gridActive" v-on:click.prevent="changeType('grid')"></a>
        <a href="#" class="icon-layout-list pull-left"  v-bind:class="listActive" v-on:click.prevent="changeType('list')"></a>
        <div class="clearfix"></div>
    </div>
</template>

<script>
    export default {
        name: 'product-displayer-switcher',
        computed: {
            gridActive: function () {
                return {
                    'active-icon-layout-thumb': this.$store.state.productDisplayer.type == 'grid'
                }
            },
            listActive: function () {
                return {
                    'active-icon-layout-list': this.$store.state.productDisplayer.type == 'list'
                }
            },
            lang: function () {
                return this.$store.getters.lang
            }
        },
        methods: {
            changeType: function (type) {
                this.$store.dispatch('changeProductDisplayer', type)
            }
        }

    }
</script>