<template>
    <span>
        <!-- List -->
        <a v-if="buttonType1" href="javascript:;" v-on:click="add">
            <div class="box-bg-icon2 pull-left">
                <div class="box-icon-cart-wishlist">
                    <img v-bind:src="icon" width="25px" v-show="!adding" />
                    <loading v-bind:show="adding"></loading>
                </div>
            </div>
        </a>

        <!-- Detail -->
        <a v-if="buttonType2" href="javascript:;" class="box-bg-icon-cw" v-on:click="add">
            <img v-bind:src="icon" v-show="!adding" />
            <loading v-bind:show="adding"></loading>
            {{ addToWishListButtonName }}
        </a>
    </span>
</template>
<script>
    import { SET_LOGIN_CALLBACK } from '../../store/mutation-types'
    import { SET_PRODUCT_WISH_LIST_REMOVE, SET_ADD_TO_WISH_LIST_SUCCESS, SET_ADD_TO_WISH_LIST_DATA } from '../../store/mutation-types'
    import popup from '../../libraries/popup'
    import GoogleAnalytic from '../../libraries/google_analytic'
    import StringHelper from '../../libraries/StringHelper'

    export default {
        name: 'add-to-wish-list-button',
        props: {
            product: {
                type: String,
                required: true
            },
            buttonType: {
                type: Number,
                default: 1 // 1 = home page, 2 = promotion page
            },
            reloadWhenRemove: {
                type:Boolean,
                default: false
            }
        },
        data: function () {
            return {
                adding: false,
                productObj: {},
                currentItem: null
            }
        },
        computed: {
            icon: function () {
                var icon = this.$store.getters.url + '/assets/images/icon-Wishlist1.png';
                if (this.isAdded()) {
                    icon = this.$store.getters.url + '/assets/images/icon-Wishlist2.png';
                }

                return icon
            },
            buttonType1: function() {
                return this.buttonType == 1
            },
            buttonType2: function() {
                return this.buttonType == 2
            },
            lang: function () {
                return this.$store.getters.lang;
            },
            addToWishListButtonName: function () {
                if (this.isAdded()) {
                    return this.lang.remove_product_to_favorite;
                }

                return this.lang.add_product_to_favorite;

            }
        },
        methods: {
            isAdded: function () {
                if (!this.$store.state.appModule.logged_in) {
                    return false;
                }

                var found = false
                for (var i = 0; i < this.$store.state.wishListModule.items.length; ++i) {
                    if (parseInt(this.$store.state.wishListModule.items[i].content_id) == this.productObj.id) {
                        found = true
                        break
                    }
                }
                return found
            },
            add: function (e) {
                this.currentItem = $(e.target).closest('.box-product');
                if(!this.$store.state.appModule.logged_in){
                    $('#login-modal').modal('show');

                    this.$store.commit(SET_LOGIN_CALLBACK, this.doAdd)
                    
                     return;
                } else {
                    this.doAdd();
                }
            },


            doAdd: function () {
                this.$store.commit(SET_LOGIN_CALLBACK,null)


                var payload = {
                    'product': this.productObj,
                    'callback': this.loadingDone
                };

                if (this.isAdded()) {
                    this.doRemove();
                } else {
                    this.adding = true;
                    
                    this.$store.dispatch('addToWishList', payload)
                    
                }
            },
            onAddSuccess: function (){
                console.log('wl success')
                // this.$store.commit(SET_ADD_TO_WISH_LIST_SUCCESS, true)
                $.toast({
                    heading: this.lang.update_favorite_successful,
                    position: 'top-right',
                    icon: 'success'
                });
                this.$store.commit(SET_ADD_TO_WISH_LIST_DATA, this.productObj)

                const googleAnanlytic = new GoogleAnalytic()
                googleAnanlytic.addToWishList(this.productObj)
            },
            doRemove: function (text) {
                var me = this;
                var message = [
                    '<p>' + this.lang.do_you_want_remove + ' <b>' + this.productTitle() + '</b> ' + this.lang.go_out,
                    ' ' + this.lang.your_favorite + '</p>'
                ] . join('')
                let options = {
                    preventClose: true
                }

                popup.open({
                    message,
                    preventClose: true,
                    type: 'confirm',
                    confirm: (done) => {
                        var payload = {
                            product: me.productObj,
                            callback: function (success) {
                                done()
                                popup.close()
                                if (success === true) {
                                    me.onRemoveWishListSuccess()
                                }
                            }
                        }

                        me.$store.dispatch('removeFormWishList',  payload);
                    }
                })
                // popup.open('', message, 'confirm', true,  function (done) {
                //     //Confirm
                //     var payload = {
                //         product: me.productObj,
                //         callback: function (success) {
                //             done()
                //             popup.close()
                //             if (success === true) {
                //                 me.onRemoveWishListSuccess()
                //             }
                //         }
                //     }
                //
                //     me.$store.dispatch('removeFormWishList',  payload);
                // }, function () {
                //     //Cancel
                // })
            },

            loadingDone: function (duplicate, error) {
                this.adding = false;
                if (duplicate === true) {
                    this.doRemove(this.lang.this_product_already_in_your_wish_list_do_you_want_to_remove);
                }else{
                    if (error) {
                        //On error
                    } else {
                        this.onAddSuccess()
                    }

                }
            },
            productTitle: function () {
                if (typeof this.productObj.name == 'string') {
                    return StringHelper.htmlspecialcharDecode(this.productObj.name)
                } else if (typeof this.productObj.title == 'string') {
                    return StringHelper.htmlspecialcharDecode(this.productObj.title)
                }

                return ''
            },
            onRemoveWishListSuccess: function () {
                const googleAnanlytic = new GoogleAnalytic()
                googleAnanlytic.removeFromWishList(this.productObj)

                if (this.currentItem) {
                    if (window.location.href.match(/members\/wishlist/gi)) {
                        window.location.reload()
                    }
                }
            }
        },

        mounted: function () {
            this.productObj = JSON.parse(this.product)
        }
    }
</script>