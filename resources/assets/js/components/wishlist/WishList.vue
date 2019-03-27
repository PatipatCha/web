<template>
    <div class="box-badge2 wishlist-badge">
        <a v-bind:href="wishListUrl">
            <i class="far fa-heart"></i>
            <!-- <div class="box-bg-icon">
                <div class="box-icon-cart">
                    <img v-bind:src="wishListIcon" width="25px"/>
                </div>
            </div> -->
            <div v-if="count > 0">
                <span class="badge">{{ count | number_comma }}</span>
            </div>
        </a>
    </div>
</template>

<script>
    export default {
        name: 'wish-list',
        props: {
            wishListUrl: {
                type: String
            },
        },
        data: function () {
            return {
                wishListIcon: this.$store.getters.url + '/assets/images/icon-Wishlist2.png'
            }
        },
        computed: {
            count: function () {
                if (!this.$store.state.appModule.logged_in) {
                    return 0;
                }
                return this.$store.getters.countWishList
            }
        },
        beforeCreate: function () {
            //Load wish list only logged in
            if (this.$store.state.appModule.logged_in == 1) {
                this.$store.dispatch('getWishLists');
            }

        }
    }
</script>
