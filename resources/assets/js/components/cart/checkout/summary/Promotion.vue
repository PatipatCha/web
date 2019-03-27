<template>
    <div class="cart-pd-box" v-if="promotions.length" id="lbl_special_promotion">
        <div class="detail">
            <div class="icon">
                <img :src="url+'assets/images/icon-tag.png'" alt="">
                <span v-if="showTitle">{{ lang.promotion_title }}</span>
            </div>
            <div class="box">
                <div class="title" v-bind:class="itemClass(index)" :key="index" v-for="(promotion, index) in promotions">{{ promotion.title }}</div>
            </div>
        </div>
        <a href="javascript:;" class="more-detail" v-show="!isShowMore && promotions.length > 2" v-on:click="showMore" id="lnk_view_all_promotion">{{ lang.show_more }}</a>
        <a href="javascript:;" class="more-detail" v-show="isShowMore && promotions.length > 2" v-on:click="showLess">{{ lang.show_less }}</a>
    </div>
</template>

<script>
    export default {
        name: 'cart-promotion',
        props: {
            promotions: {
                type: Array
            },
            showTitle: {
                type: Boolean,
                default: false
            }
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                isShowMore: false
            }
        },
        methods: {
            itemClass: function (index) {
                var textOverflow = false
                if (!this.isShowMore && index < 2 && this.promotions.length > 2) {
                    textOverflow = true
                }

                var hide = false
                if (!this.isShowMore && index > 1) {
                    hide = true
                }
                return {
                    'text-overflow': textOverflow,
                    'hide': hide
                }
            },
            showMore: function () {
                this.isShowMore = true
            },
            showLess: function () {
                this.isShowMore = false
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang
            }
        }
    }
</script>