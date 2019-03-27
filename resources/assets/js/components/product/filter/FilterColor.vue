<template>
    <div class="search-filter-menu-box search-filter-Package">
        <div class="search-filter-text">
            <b>{{ title }}</b>
        </div>
        <div class="search-filter-text2">
            <div class="search-filter-Package-checkbox filter-color">
                <ul>
                    <li v-for="(item, index) in displayItems" v-bind:title="item.name"  v-bind:id="getId(item)"  v-on:click="select(item.value)" :key="getId(item)" v-bind:class="{ active: isActiveClass(item)}" v-if="item.image && item.image.length > 0" ><span v-if="item.image && item.image.length > 0" v-html="image(item)"></span></li>
                </ul>
                <!--<label class="checkbox-inline">
                    <input type="checkbox" v-model="selected"  v-bind:id="getId(item)" v-bind:value="item.value"  v-on:change="select(item.value)" :key="getId(item)">
                    <span>{{ item.name }}</span><!-- ใส่ v-if="! (item.image && item.image.length > 0)" เพื่อ ถ้ามีรูปแล้วไม่ต้องการให้แสดง text -->
                    <!--<span v-if="item.image && item.image.length > 0" v-html="image(item)"></span>-->
                    <!--({{ item.count | number_comma }})-->
                <!--</label>-->
            </div>
        </div>
        <div class="more-btn" v-if="isLimit">
            <a href="javascript:;" v-on:click="doShowAll" v-if="!isShowingAll">{{ lang.show_all }} <i class="fa fa-angle-down"></i></a>
            <a href="javascript:;" v-on:click="doShowLess" v-if="isShowingAll">{{ lang.show_less }} <i class="fa fa-angle-up"></i></a>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'product-filter-color',
        props: {
            items: {
                type: Array,
                required: true
            },
            idName: {
                type: String,
                required: true
            },
            dispatch: {
                type: String,
                required: true
            },
            initShowItems: {
                type: Number,
                default: 6
            },
            initShowAll: {
                type: Boolean,
                default: false
            },
            title: {
                type: String,
                required: true
            },
            selectedState: {
                type: String,
                required: true
            }
        },
        data: function () {
            return {
                selected: this.$store.state.productFilterModule[this.selectedState],
                showAll: this.initShowAll,
                isMore: false,
                isLimit: false,
                isShowingAll: false
            }
        },
        methods: {
            getId: function (item) {
                return this.idName + encodeURIComponent(item.value)
            },
            select: function(name) {
                var payload = {
                    'name': encodeURIComponent(name)
                };

                this.$store.dispatch(this.dispatch, payload);
            },
            doShowAll: function () {
                this.showAll = true;
            },
            doShowLess: function () {
                this.showAll = false;
            },
            image: function (item) {
                if (item.image && item.image.length > 0) {
                    return '<img src="' + item.image + '" title="' + item.name + '" class="filter-image-icon">'
                }

                return ''
            },
            isActiveClass:function(item){
                // console.log(this.selected)
                var index = this.selected.indexOf(item.name)
                if(index != -1){
                    return true
                }else{
                    return false
                }
                
            }
        },
        computed: {
            displayItems: function () {
                if (!this.showAll && this.items.length > this.initShowItems) {
                    this.isLimit = true
                    this.isShowingAll = false
                    return this.items.slice(0, this.initShowItems);
                }

                this.isShowingAll = true
                return this.items
            },
            lang: function () {
                return this.$store.getters.lang
            }
        }
    }
</script>