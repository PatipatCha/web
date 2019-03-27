<template>
    <div>
        <modal
                v-bind:show="show"
                additional-custom-classes="cs-modal"
                v-on:on-close-modal="onModalClose"
                v-bind:show-header="false"
                v-bind:ok-text="lang.ok"
                v-bind:center="false"
                v-bind:show-ok="true"
                v-bind:show-cancel="false"
                v-on:on-ok="onSuccessOk"
        >
            <div>
                <div class="text-center">
                    <p class="text-success">
                        <img :src="url+'assets/images/icon-cheak-20px.png'" alt="">
                        {{ lang.added_to_favorite }}
                    </p>
                </div>
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2">
                        <div class="product">
                            <div class="row">
                                <div class="no-padding col-xs-4 col-sm-4">
                                    <img v-bind:src="image" class="img-responsive">
                                </div>
                                <div class="cart-text-detail col-xs-8 col-sm-8">
                                    <b>{{ title }}</b><br>
                                    <b><span class="item-price">{{ price | money }} ฿ </span></b> {{lang.per}} {{ price_per_key }}<br>
                                    <s v-if="price < normal_price">{{ normal_price | money }} ฿</s>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>


<script>
    import Modal from '../bootstrap/Modal2'
    import { SET_ADD_TO_WISH_LIST_SUCCESS, SET_ADD_TO_WISH_LIST_DATA} from '../../store/mutation-types'
    import StringHelper from '../../libraries/StringHelper'

    export default {
        name: 'add-to-wish-list-success',
        components: {
            Modal
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                locale_url: this.$store.getters.locale_url + '/'
            }
        },
        methods: {
            onModalClose: function () {
                this.$store.commit(SET_ADD_TO_WISH_LIST_SUCCESS, {'success': false, 'data': null})
            },
            getDataValue: function (fieldName) {
                var data = this.$store.state.storeModule.add_to_wish_list_data;
                if (data) {
                    if (typeof data[fieldName] == 'string' || typeof data[fieldName] == 'number') {
                        return data[fieldName];
                    }
                }

                return '';
            },

            onModalClose: function (){
                this.$store.commit(SET_ADD_TO_WISH_LIST_SUCCESS, false)
                this.$store.commit(SET_ADD_TO_WISH_LIST_DATA, null)
            },
            onSuccessOk: function () {
                this.onModalClose()
            }
        },
        computed: {
            show: function() {
                return this.$store.state.storeModule.add_to_wish_list_success;
            },
            title: function() {
                var name = this.getDataValue('name');

                if (typeof name == 'string' && name.length > 0) {
                    return StringHelper.htmlspecialcharDecode(name);
                }


                var title = this.getDataValue('title');
                return StringHelper.htmlspecialcharDecode(title);
            },
            normal_price: function() {
                return this.getDataValue('normal_price');
            },
            price: function () {
                return this.getDataValue('price');
            },
            price_per_key: function () {
                return this.getDataValue('price_per_key');
            },
            image: function () {
                if (this.$store.state.storeModule.add_to_wish_list_data) {
                    var image = this.$store.state.storeModule.add_to_wish_list_data.thumbnail;

                    if ((typeof image == 'object' || typeof image == 'array') && image.length > 0) {
                        return image[0];
                    } else if (typeof image == 'string') {
                        return image;
                    }
                }

                return '';
            },
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>