<template>
    <div class="search-filter-menu-box search-filter-Package">
        <div class="search-filter-text">
            <b>{{ lang.brand }}</b>
        </div>
        <div class="search-filter-text2">
            <div class="search-filter-Package-checkbox" v-for="(item, index) in items">
                <label class="checkbox-inline">
                    <input type="checkbox" v-model="selectedBrands" v-bind:id="getId(item)" v-bind:value="item.value" v-on:change="select(item.value)" :key="getId(item)"> {{ item.name }}
                </label>
            </div>
        </div>
        <div class="more-btn">
            {{ lang.show_all }} <i class="fa fa-angle-down"></i>
            {{ lang.show_less }} <i class="fa fa-angle-up"></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'product-brand-filter',
        props: {
            items: {
                type: Array
            }
        },
        data: function () {
            return {
                selectedBrands: this.$store.state.productFilterModule.brands
            }
        },
        methods: {
            getId: function (item) {
                return 'filter_brand_' + encodeURIComponent(item.value)
            },
            select: function(name) {
                var payload = {
                    'name': name
                };

                this.$store.dispatch('filterSelectBrand', payload);
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    };
</script>
