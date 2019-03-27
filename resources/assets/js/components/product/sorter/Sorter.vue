<template>
    <div class="sorter col-xs-12 col-sm-6 no-padding">
        <form action="" class="form-inline">
            <div class="form-group">
                <label for="">{{ lang.sorter_order_by }}</label>
                <select  v-model="sorter" class="form-control input-sm select2 sorter">
                    <option value="best_match|asc">{{ lang.best_match }}</option>
                    <option value="newest|asc">{{ lang.newest }}</option>
                    <option value="price|asc">{{ lang.price_ascending }}</option>
                    <option value="price|desc">{{ lang.price_descending }}</option>
                </select>
            </div>
        </form>

        <!-- <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 no-padding">
            <div class="search-filter-text4">
                <a href="javascript:;" v-on:click="sort('best_match')">Best Match <span v-html="best_match"></span></a>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 no-padding">
            <div class="search-filter-text4">
                <a href="javascript:;" v-on:click="sort('order')">Order <span v-html="order"></span></a>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 no-padding">
            <div class="search-filter-text4">
                <a href="javascript:;" v-on:click="sort('newest')">Newest <span v-html="newest"></span></a>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 no-padding">
            <div class="search-filter-text4">
                <a href="javascript:;" v-on:click="sort('price')">Price <span v-html="price"></span></a>
            </div>
        </div> -->
    </div>
</template>

<script>
    export default {
        name: 'product-sorter',
        data: function () {
            return {
                sorter: this.getSelectedSorter()
            }
        },
        computed: {
            best_match: function () {
                return this.getActiveSort('best_match')
            },
            order: function () {
                return this.getActiveSort('order')
            },
            newest: function () {
                return this.getActiveSort('newest')
            },
            price: function () {
                return this.getActiveSort('price')
            },
            lang: function () {
                return this.$store.getters.lang
            }
        },
        methods: {
            sort: function(sorter) {
                var sorterArr = sorter.split('|')
                this.$store.dispatch('sortProduct', {
                    'field_name': sorterArr[0],
                    'direction': sorterArr[1]
                })
            },

            getActiveSort(sorter)
            {
                if (!this.$store.state.productSorter.sorters[sorter]) {
                    return '';
                }

                var direction = '';
                switch (this.$store.state.productSorter.sorters[sorter]) {
                    case 'asc':
                        direction = '<i class="fa fa-sort-amount-asc" aria-hidden="true"></i>';
                        break;
                    case 'desc':
                        direction = '<i class="fa fa-sort-amount-desc" aria-hidden="true"></i>';
                        break;
                }

                return direction;
            },
            getSelectedSorter: function () {
                return this.$store.state.productSorter.sorters.field_name + '|' + this.$store.state.productSorter.sorters.direction
            }
        },
        mounted()
        {
            $('.sorter').unbind('change')
            $('.sorter').bind('change', (e) => {
                this.sorter = $(e.target).val()
            })
        },
        watch: {
            sorter: function () {
                this.sort(this.sorter);
            }
        }
    }
</script>