<template>
    <div class="panel panel-default">
        <a class="collapsed" role="button" data-toggle="collapse" href="#product-assortment-tab" aria-expanded="false" aria-controls="product-assortment-tab">
            <div class="panel-heading" role="tab" id="heading-product-assortment-tab">
                <h4 class="panel-title">
                    <product-type-icon type="assortment"></product-type-icon>{{ lang.assortment }} <!--2 days-->
                    <span class="remark">({{ groupData.items.length | number_comma}} item)</span>
                    <product-type-icon-information type="assortment" trigger="hover"></product-type-icon-information>
                </h4>
            </div>
        </a>
        <div id="product-assortment-tab" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="'heading-product-assortment-tab">
            <div class="panel-body">
                <div class="topic-table1">
                    <b>{{ lang.pickup_store_location }}</b>
                </div>
                <div class="message-box">
                    <p>{{ lang.makro_store }} : {{ currentStore.name }}</p>
                    <p>{{ maskroStoreAddress }}</p>
                    <p><b>{{ lang.tel }}</b> {{ getStoreDisplayData(currentStore.contact_phone) }}</p>
                    <p><b>{{ lang.fax }}</b> {{ getStoreDisplayData(currentStore.contact_fax) }}</p>
                </div>


                <div class="row">
                    <div class="col-sm-6">
                        <label for="">{{ lang.choose_pickup_date }}</label>
                        <date-picker-field input-name="product_assortment[pickup_date]" v-bind:start-date="groupData.group_pickup_start_date" v-bind:end-date="groupData.group_pickup_end_date" v-bind:default-date="groupData.group_pickup_start_date"></date-picker-field>
                    </div>
                    <div class="col-sm-6">
                        <label for="">{{ lang.choose_pickup_time }}</label>
                        <picker-time input-name="product_assortment[pickup_time]"></picker-time>
                    </div>
                    <div class="col-sm-12">
                        <small class="remark">{{ lang.pickup_information }}</small>
                    </div>
                </div>



                <cart-checkout-product-list v-bind:items="groupData.items"></cart-checkout-product-list>

            </div>
        </div>
    </div>
</template>

<script>
    import DatePicker from '../../../input_fields/DatePicker'
    import PickerTime from '../fields/PickerTime'
    import ProductList from '../product_type/list/List'
    import ProductTypeIcon from '../ProductTypeIcon'
    import ProductTypeIconInformation from './../ProductTypeIconInformation'

    export default {
        name: 'cart-checkpot-product-assortment',
        props: ['groupData', 'currentStore'],
        components: {
            'date-picker-field': DatePicker,
            'picker-time': PickerTime,
            'cart-checkout-product-list': ProductList,
            'product-type-icon': ProductTypeIcon,
            'product-type-icon-information': ProductTypeIconInformation
        },
        data: function () {
            return {
                 url: this.$store.getters.url + '/'
            }
        },
        methods: {
            getStoreDisplayData: function (value) {
                if (typeof value == 'string' && value.length > 0) {
                    return value;
                }

                return '-';
            }
        },
        computed: {
            maskroStoreAddress: function () {
                var addresses = [];
                if (typeof this.currentStore.address.address == 'string' && this.currentStore.address.address.length > 0) {
                    addresses.push(this.currentStore.address.address);
                }

                if (typeof this.currentStore.address.address2 == 'string' && this.currentStore.address.address2.length > 0) {
                    addresses.push(this.currentStore.address.address2);
                }

                if (typeof this.currentStore.address.address3 == 'string' && this.currentStore.address.address3.length > 0) {
                    addresses.push(this.currentStore.address.address3);
                }

                if (typeof this.currentStore.address.subdistrict == 'string' && this.currentStore.address.subdistrict.length > 0) {
                    addresses.push(this.currentStore.address.subdistrict);
                }

                if (typeof this.currentStore.address.district == 'string' && this.currentStore.address.district.length > 0) {
                    addresses.push(this.currentStore.address.district);
                }

                if (typeof this.currentStore.address.province == 'string' && this.currentStore.address.province.length > 0) {
                    addresses.push(this.currentStore.address.province);
                }

                if (typeof this.currentStore.address.postcode == 'string' && this.currentStore.address.postcode.length > 0) {
                    addresses.push(this.currentStore.address.postcode);
                }


                return addresses.join(', ');
            },
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>