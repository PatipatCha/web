<template>
    <div class="search-filter-menu-box">
        <div class="search-filter-text"><b>{{ lang.price_range }}</b></div>
        <div class="search-filter-text2">
            <div class="search-filter-Weight-Price">
                <input type="text" class="form-control-search-filter-Weight-Price pull-left min-input not-prevent-special-chars" :placeholder="lang.price_min" v-model.trim="min" v-on:keyup.enter="onSubmitPrice('min')">
                <div style="padding: 5px 5px 0px 5px;" class="pull-left"> - </div>
                <input type="text" class="form-control-search-filter-Weight-Price pull-left max-input not-prevent-special-chars" :placeholder="lang.price_max" v-model.trim="max" v-on:keyup.enter="onSubmitPrice('max')">
                <button type="button" class="btn btn-link" v-on:click.prevent="submitPrice">
                    <i class="fa fa-search"></i>
                </button>
                <div class="clearfix"></div>
            </div>
            <label class="error" v-if="isError">{{ error }}</label>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'product-price-filter',
        data: function () {
            return {
                min: this.$store.getters.productFilterMinPrice,
                max: this.$store.getters.productFilterMaxPrice,
                isError: false,
                error: '',
                currentMinPrice: this.$store.getters.productFilterMinPrice,
                currentMaxPrice: this.$store.getters.productFilterMaxPrice
            }
        },
        methods: {
            onSubmitPrice: function (input) {
                var minPrice = parseFloat(this.min);
                var maxPrice = parseFloat(this.max);

                if (input == 'min') {
                    if (!isNaN(minPrice)) {
                        if (isNaN(maxPrice)) {
                            $('.max-input').focus();
                        } else {
                            this.submitPrice();
                        }
                    } else {
                        this.submitPrice();
                    }

                } else if (input == 'max') {
                    if (!isNaN(maxPrice)) {
                        if (isNaN(minPrice)) {
                            $('.min-input').focus();
                        } else {
                            this.submitPrice();
                        }
                    } else {
                        this.submitPrice();
                    }
                }
            },
            submitPrice: function () {
                this.isError = false
                if (this.validatePrice(this.min) && this.validatePrice(this.max)) {
                    var minPrice = parseFloat(this.min)
                    var maxPrice = parseFloat(this.max)

                    var tempMinPrice = isNaN(minPrice) ? 0 : minPrice
                    var tempMaxPrice = isNaN(maxPrice) ? 0 : maxPrice

                    if (this.min != null && this.min != '' && this.max != null && this.max != '' && tempMaxPrice < tempMinPrice) {
                        this.error = this.lang.filter_max_price_must_not_less_than_min_price
                        this.isError = true
                    } else {
                        if (minPrice < 0 || maxPrice < 0) {
                            this.error = this.lang.filter_price_must_greater_than_zero
                            this.isError = true
                        } else {
                            this.$store.dispatch('filterPricesChange', {
                                min: minPrice,
                                max: maxPrice
                            })
                        }

                    }
                } else {
                    this.error = this.lang.filter_price_must_be_number_only
                    this.isError = true
                }
            },
            validatePrice: function (price) {
                if (price == null || price == '') {
                    return true
                }

                price = parseFloat(price)
                if (!isNaN(price)) {
                    return true
                }

                return false
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang
            }
        }
    }
</script>