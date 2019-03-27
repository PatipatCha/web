<template>
    <span>
        {{ fullAddress }}
    </span>
</template>

<script>
    export default {
        name: 'full-address',
        props: {
            address: {
                type: Object,
                required: true
            },
            join: {
                type: String,
                default: ', '
            }
        },
        methods: {
            address1: function () {
                if ((typeof this.address.address_line1 == 'string' && this.address.address_line1.length > 0)
                    || (typeof this.address.address == 'string' && this.address.address.length > 0))
                {
                    if ((typeof this.address.address_line1 == 'string' && this.address.address_line1.length > 0)) {
                        return this.address.address_line1;
                    } else {
                        return this.address.address;
                    }
                }

                return '';
            },
            address2: function () {
                if ((typeof this.address.address_line2 == 'string' && this.address.address_line2.length > 0)
                    || (typeof this.address.address2 == 'string' && this.address.address2.length > 0))
                {
                    if ((typeof this.address.address_line2 == 'string' && this.address.address_line2.length > 0)) {
                        return this.address.address_line2;
                    } else {
                        return this.address.address2;
                    }
                }

                return '';
            },
            address3: function () {
                if ((typeof this.address.address_line3 == 'string' && this.address.address_line3.length > 0)
                    || (typeof this.address.address3 == 'string' && this.address.address3.length > 0))
                {
                    if ((typeof this.address.address_line3 == 'string' && this.address.address_line3.length > 0)) {
                        return this.address.address_line3;
                    } else {
                        return this.address.address3;
                    }
                }

                return '';
            },
            subdistrict: function () {
                if ((typeof this.address.township == 'string' && this.address.township.length > 0)
                    || (typeof this.address.subdistrict == 'string' && this.address.subdistrict.length > 0))
                {
                    if ((typeof this.address.township == 'string' && this.address.township.length > 0)) {
                        return this.address.township;
                    } else {
                        return this.address.subdistrict;
                    }
                }

                return '';
            },
            district: function () {
                if ((typeof this.address.city == 'string' && this.address.city.length > 0)
                    || (typeof this.address.district == 'string' && this.address.district.length > 0))
                {
                    if ((typeof this.address.city == 'string' && this.address.city.length > 0)) {
                        return this.address.city;
                    } else {
                        return this.address.district;
                    }
                }

                return '';
            }
        },
        computed: {
            fullAddress: function () {
                var addresses = [];
                if (typeof this.address1() == 'string' && this.address1().length > 0) {
                    addresses.push(this.address1());
                }

                // if (typeof this.address2() == 'string' && this.address2().length > 0) {
                //     addresses.push(this.address2());
                // }

//                if (typeof this.address3() == 'string' && this.address3().length > 0) {
//                    addresses.push(this.address3());
//                }

                if (typeof this.subdistrict() == 'string' && this.subdistrict().length > 0) {
                    addresses.push(this.subdistrict());
                }

                if (typeof this.district() == 'string' && this.district().length > 0) {
                    addresses.push(this.district());
                }

                if (typeof this.address.province == 'string' && this.address.province.length > 0) {
                    addresses.push(this.address.province);
                }

                if (typeof this.address.postcode == 'string' && this.address.postcode.length > 0) {
                    addresses.push(this.address.postcode);
                }


                return addresses.join(this.join);
            }
        }
    }
</script>