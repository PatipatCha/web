<template>
    <div class="search-filter-menu-box">
        <div class="search-filter-text">
            <b>{{ lang.categories }}</b>
        </div>
        <div class="search-filter-text2">
            <div class="menu-related-categories">

                <ul class="category-level-0">
                    <li v-for="level0 in categories" class="category-level-0" v-bind:class="categoryLevelClass(level0)">
                        <a v-bind:href="getLink(level0)" v-on:click.prevent="selectCategory(level0, $event, 'product_category_lv0_id')">{{ level0.name }}</a>
                        <ul v-if="level0.children.length > 0" class="category-level-1">
                            <li v-for="level1 in level0.children" class="category-level-1" v-bind:class="categoryLevelClass(level1)">
                                <a v-bind:href="getLink(level1)" v-on:click.prevent="selectCategory(level1, $event, 'product_category_lv1_id')">{{ level1.name }}</a>
                                <ul v-if="level1.children.length > 0" class="category-level-2">
                                    <li v-for="level2 in level1.children" class="category-level-2" v-bind:class="categoryLevelClass(level2)">
                                        <a v-bind:href="getLink(level2)" v-on:click.prevent="selectCategory(level2, $event, 'product_category_lv2_id')">{{ level2.name }}</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'product-category-filter',
        props: {
            categories: {
                type: Array
            },
            filter: {
                type: Boolean,
                default: true
            }
        },
        methods: {
            select: function (item) {
                if (item.id == this.$store.state.productFilterModule.category_id) {
                    this.$store.dispatch('selectFilterCategory', null);
                } else {
                    this.$store.dispatch('selectFilterCategory', item.id);
                }

            },

            getUrl: function (item) {
                var productListUrl = GLOBAL_SETTING.product_list_url;
                if (productListUrl.match(/\?/gi)) {
                    productListUrl = '&'
                } else {
                    productListUrl = '?'
                }

                productListUrl = productListUrl + 'product_category_lv0_id=' + item.id;

                return productListUrl;
            },
            activeCLass: function (item) {
                return {
                    active: item.id == this.$store.state.productFilterModule.category_id ? true : false
                }
            },
            categoryLevelClass: function (item) {
                var classes = {
                    'have-children': item.children.length > 0,
                    'no-children': item.children.length < 1,
                    'category-item': true
                }

                classes['category-id-' + item.id] = true
                return classes
            },
            getLink: function (item) {
                if (item.children.length < 1) {
                    //return 'http://google.com'
                }

                return 'javascript:;'
            },
            selectCategory: function (item, event, name) {
                if (this.filter) {
                    //Filter
                    if (item.children.length > 0 && name == 'product_category_lv0_id') {
                        this.toggleCollapse(event)
                    } else {
                        var payload = {
                            id: item.id,
                            name: name,
                            clearFilter: true
                        }
                        this.$store.dispatch('selectFilterCategory', payload);
                    }
                } else {
                    //Category
                    if (item.children.length > 0 && name == 'product_category_lv0_id') {
                        this.toggleCollapse(event)
                    } else {
                        window.location = this.$store.getters.locale_url + '/category/' + item.slug
                    }
                }

                /*if (item.children.length > 0 && name == 'product_category_lv0_id') {
                    var a = $(event.target)
                    var li = a.closest('li')
                    if (li.hasClass('active')) {
                        li.removeClass('active')
                        li.find('li').removeClass('active')
                    } else {
                        li.addClass('active')
                    }
                } else {
                    if (this.filter) {
                        var payload = {
                            id: item.id,
                            name: name
                        }
                        this.$store.dispatch('selectFilterCategory', payload);
                    } else {
                        window.location = this.$store.getters.locale_url + '/category/' + item.slug
                    }
                }*/

            },
            toggleCollapse: function (event) {
                var a = $(event.target)
                var li = a.closest('li')
                if (li.hasClass('active')) {
                    li.removeClass('active')
                    li.find('li').removeClass('active')
                } else {
                    li.addClass('active')
                }
            }
        },
        mounted: function () {
            var item = $('.category-id-' + this.$store.state.productFilterModule.category_id)
            item.addClass('active');
            var closest1 = item.parent().closest('.category-item').addClass('active')
            closest1.parent().closest('.category-item').addClass('active')
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>