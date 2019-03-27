<template>
    <div class="flex-item" ref="residenceAddress">
        <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
            <label :for="getAddressId">{{ lang.address_line1 }}<span style="color:#F01616;"> *</span></label>
            <input type="text" class="form-control" :placeholder="lang.home_moo_soi" :data-rule-no-special-char="true" data-rule-maxlength="45" data-rule-required="true" required  v-bind:name="getInputName('address_line1')" v-model="address_line1" :id="getAddressId" :data-msg-required="lang.please_enter_address">
            <label class="error" :for="addressId"  :id="labelAddress"></label>
        </div>

        <!-- <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad"> -->
        <!-- <label for="">{{ lang.address_line2 }}</label>
        <input type="text" class="form-control" :placeholder="lang.address" v-bind:name="getInputName('address_line2')" data-rule-maxlength="70" v-model="address_line2"> -->
        <!-- </div> -->


        <div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
            <label :for="getProvinceIdName">{{ lang.province }}<span style="color:#F01616;"> *</span> <loading :show="loadingProvince"></loading></label>
            <div class="btn-group">
                <select class="form-control province" ref="province"  v-bind:name="getInputName('province_id')" :id="getProvinceIdName">
                </select>
                <input type="hidden" :name="'required_province_id' + getProvinceIdName" v-model="province_id" :id="'required_province_id' + getProvinceIdName" data-rule-requirednozero="true" v-bind:class="ignoreValidateClass" :data-msg-requirednozero="lang.please_select_province"/>
            </div>
            <label class="error" :for="'required_province_id' + getProvinceIdName"  :id="labelProvince"></label>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
            <label for="">{{ lang.city }}<span style="color:#F01616;"> *</span> <loading :show="loadingDistrict"></loading></label>
            <div class="btn-group">
                <select class="form-control district" ref="district" v-bind:name="getInputName('district_id')" :id="districtId">
                </select>
                <input type="hidden" :name="'required_district_id' + districtId" v-model="district_id" data-rule-requirednozero="true" v-bind:class="ignoreValidateClass" :data-msg-requirednozero="lang.please_select_district"/>
            </div>
            <label class="error" :for="'required_district_id' + districtId"  :id="labelDistrict"></label>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
            <label for="">{{ lang.sub_district }}<span style="color:#F01616;"> *</span> <loading :show="loadingSubDistrict"></loading></label>
            <div class="btn-group">
                <select class="form-control subdistrict" ref="subdistrict" v-bind:name="getInputName('subdistrict_id')" :id="subDistrictId">
                </select>
                <input type="hidden" :name="'required_subdistrict_id' + subDistrictId" v-model="subdistrict_id" data-rule-requirednozero="true" v-bind:class="ignoreValidateClass" :data-msg-requirednozero="lang.please_select_subdistrict"/>
            </div>
            <label class="error" :for="'required_subdistrict_id' + subDistrictId"  :id="labelSubDistrict"></label>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
            <label :for="getPostcodeIdName">{{ lang.postcode }}<span style="color:#F01616;"> *</span></label>
            <input type="text" class="form-control"  v-bind:placeholder="lang.postcode" v-model="postcode" v-bind:name="getInputName('postcode')" :readonly="true" :id="getPostcodeIdName">
            <label class="error" :for="getInputName('postcode')"  :id="labelPostcode"></label>
        </div>

    </div>
</template>

<script>
    import Loading from '../../../loading/Loading'
    export default {
        name: 'residence-address',
        components: {
            Loading
        },
        props: {
            inputName: {
                type: String,
                default: ''
            },
            initData: {
                type: Object
            },
            address3: {
                type: Boolean,
                default: false
            },
            ignoreValidateClass: {
                type: String,
                default: 'do-not-ignore'
            },
            useDeliveryResidenceAddress: {
                type: Boolean,
                default: false
            },
            addressId: {
                type: String,
                default: ''
            },
            provinceId: {
                type: String,
                default: ''
            },
            districtId: {
                type: String,
                default: ''
            },
            subDistrictId: {
                type: String,
                default: ''
            },
            labelAddress: {
                type: String,
                default: ''
            },
            labelProvince: {
                type: String,
                default: ''
            },
            labelDistrict: {
                type: String,
                default: ''
            },
            labelSubDistrict: {
                type: String,
                default: ''
            },
            labelPostcode: {
                type: String,
                default: ''
            },
            postcodeIdName: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                provinces: [],
                province_id: '',
                districts: [],
                district_id:  '',
                subdistricts: [],
                subdistrict_id: '',
                loadingDistrict: false,
                loadingSubDistrict: false,
                loadingProvince: false,
                postcode: '',
                address_line1: this.getAddressLine1(),
                address_line2: this.getAddressLine2(),
                first_set_province: true,
                first_set_district: true,
                first_set_subdistrict: true,
                first_set_postcode: true,
                isSetDistrict: false,
                isSetSubDistrict: false
            }
        },
        methods: {
            getProvinces: function () {
                var $this = this;
                $this.loadingProvince = true;

                axios.get(this.getProvinceUrl)
                    .then(function(response) {
                        $this.provinces = response.data;

                        $this.province_id = $this.getProvinceId();
                        $this.loadingProvince = false;
                        $this.first_set_province = false
                    })
                    .catch(function() {
                        $this.loadingProvince = false;
                    });
            },
            getDistricts: function () {
                var $this = this;
                $this.loadingDistrict = true;

                axios.get(this.getDistrictUrl)
                    .then(function(response) {
                        $this.loadingDistrict = false;
                        $this.districts = response.data;

                        if ($this.first_set_district) {
                            $this.district_id = $this.getDistrictId();
                            $this.first_set_district = false;
                        }

                    })
                    .catch(function() {
                        $this.loadingDistrict = false;
                    });
            },
            getSubDistricts: function () {
                var $this = this;
                $this.loadingSubDistrict = true;

                axios.get(this.getSubDistrictUrl)
                    .then(function(response) {
                        $this.loadingSubDistrict = false;
                        $this.subdistricts = response.data;

                        if ($this.first_set_subdistrict) {
                            $this.subdistrict_id = $this.getSubDistrictId();
                            $this.postcode = $this.getPostCode();
                            $this.first_set_subdistrict = false;
                            $this.first_set_postcode = false;
                        }

                    })
                    .catch(function() {
                        $this.loadingSubDistrict = false;
                    });
            },
            onChangeProvince: function () {
                console.log('onChangeProvince')
                //Set district to empty
                this.districts = [];
                this.district_id = '';

                //Set sub district to empty
                this.subdistricts = [];
                this.subdistrict_id = '';

                //Set postcode to empty
                this.postcode = '';

            },
            onChangeDistrict: function () {
                //Set sub district to empty
                this.subdistricts = [];
                this.subdistrict_id = '';

                //Set postcode to empty
                this.postcode = '';
            },
            getInputName: function (name) {
                if (this.inputName.length > 0) {
                    return this.inputName + '[' + name + ']';
                }

                return name;
            },
            getAddressLine1: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.address == 'string' || typeof this.initData.address_line1 == 'string') {
                        if (typeof this.initData.address == 'string') {
                            return this.initData.address;
                        }

                        return this.initData.address_line1;
                    }

                }

                return ''
            },
            getAddressLine2: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.address2 == 'string' || typeof this.initData.address_line2 == 'string') {
                        if (typeof this.initData.address2 == 'string') {
                            return this.initData.address2;
                        }

                        return this.initData.address_line2;
                    }
                }

                return ''
            },
            getProvinceId: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.original_province == 'object' || typeof this.initData.province_id == 'string') {
                        if (typeof this.initData.original_province == 'object') {
                            return this.initData.original_province.id + '';
                        }

                        return this.province_id;
                    }
                }

                return ''
            },

            getDistrictId: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.original_district == 'object' || typeof this.initData.district_id == 'string') {
                        if (typeof this.initData.original_district == 'object') {
                            return this.initData.original_district.id + '';
                        }

                        return this.district_id;
                    }
                }

                return ''
            },
            getSubDistrictId: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.original_subdistrict == 'object' || typeof this.initData.subdistrict_id == 'string') {
                        if (typeof this.initData.original_subdistrict == 'object') {
                            return this.initData.original_subdistrict.id + '';
                        }

                        return this.subdistrict_id;
                    }

                }
                return ''
            },
            getPostCode: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.postcode == 'string') {
                        return this.initData.postcode;
                    }
                }

                return ''
            },

            setSelectedProvince() {
                setTimeout(() => {
                    if (this.province_id && this.province_id != '') {
                        console.log('Set selected province', this.province_id)
                        $(this.$refs.province).val(this.province_id).trigger('change')
                    } else {
                        console.log('Unset selected province', this.province_id)
                        $(this.$refs.province).val('').trigger('change')
                        this.setProvinceSelect()
                    }
                }, 100)
            },

            setSelectedDistrict() {
                setTimeout(() => {
                    if (this.district_id && this.district_id != '') {
                        console.log('Set selected district', this.district_id)
                        $(this.$refs.district).val(this.district_id).trigger('change')
                    } else {
                        console.log('Unset selected district', this.district_id)
                        $(this.$refs.district).val('').trigger('change')
                        this.setDistrictSelect()
                    }
                }, 100)
            },

            setSelectedSubdistrict() {
                setTimeout(() => {
                    if (this.subdistrict_id && this.subdistrict_id != '') {
                        this.isSetSubDistrict = true
                        console.log('Set selected subdistrict', this.subdistrict_id)
                        $(this.$refs.subdistrict).val(this.subdistrict_id).trigger('change')
                    } else {
                        console.log('Unset selected subdistrict', this.subdistrict_id)
                        $(this.$refs.subdistrict).val('').trigger('change')
                        this.setSubDistrictSelect()
                    }
                }, 100)
            },

            setSubDistrictSelect() {
                let subdistricts = []
                subdistricts.push({
                    id: '0',
                    text: this.$store.state.appModule.lang.select_subdistrict
                })

                if (this.subdistricts && this.subdistricts.length) {
                    for (let i = 0; i < this.subdistricts.length; ++i) {
                        subdistricts.push({
                            id: this.subdistricts[i].sub_district_id,
                            text: this.subdistricts[i].name
                        })
                    }
                }



                $(this.$refs.subdistrict).html('').select2()
                $(this.$refs.subdistrict).select2({
                    theme: 'bootstrap',
                    width: 'auto',
                    minimumResultsForSearch: -1,
                    data: subdistricts,
                    allowClear: true,
                });


                $(this.$refs.subdistrict).unbind('select2:select')
                $(this.$refs.subdistrict).on("select2:select", (e) => {
                    if (parseInt(e.target.value) != 0) {
                        $('label[for="required_subdistrict_id' + this.subDistrictId + '"]', $(this.$refs.residenceAddress)).hide()
                    } else {
                        $('label[for="required_subdistrict_id' + this.subDistrictId + '"]', $(this.$refs.residenceAddress)).show()
                    }
                });
            },

            setDistrictSelect() {
                let districts = []
                districts.push({
                    id: '0',
                    text: this.$store.state.appModule.lang.select_district
                })

                if (this.districts && this.districts.length) {
                    for (let i = 0; i < this.districts.length; ++i) {
                        districts.push({
                            id: this.districts[i].district_id,
                            text: this.districts[i].name
                        })
                    }
                }

                $(this.$refs.district).html('').select2()
                $(this.$refs.district).select2({
                    theme: 'bootstrap',
                    width: 'auto',
                    minimumResultsForSearch: -1,
                    data: districts,
                    allowClear: true,
                })

                $(this.$refs.district).unbind('select2:select')
                $(this.$refs.district).on("select2:select", (e) => {
                    if (parseInt(e.target.value) != 0) {
                        $('label[for="required_district_id' + this.districtId + '"]', $(this.$refs.residenceAddress)).hide()
                    } else {
                        $('label[for="required_district_id' + this.districtId + '"]', $(this.$refs.residenceAddress)).show()
                    }
                });
            },

            setProvinceSelect() {
                let provinces = []
                provinces.push({
                    id: '0',
                    text: this.$store.state.appModule.lang.select_province
                })
                if (this.provinces && this.provinces.length) {
                    for (let i = 0; i < this.provinces.length; ++i) {
                        if (this.provinces[i]) {
                            provinces.push({
                                id: this.provinces[i].province_id,
                                text: this.provinces[i].name
                            })
                        }
                    }
                }

                $(this.$refs.province).html('').select2()
                $(this.$refs.province).select2({
                    theme: 'bootstrap',
                    width: 'auto',
                    minimumResultsForSearch: -1,
                    data: provinces,
                    allowClear: true,
                });

                $(this.$refs.province).unbind('select2:select')
                $(this.$refs.province).on("select2:select", (e) => {
                    if (parseInt(e.target.value) != 0) {
                        $('label[for="required_province_id' + this.provinceId + '"]', $(this.$refs.residenceAddress)).hide()
                    } else {
                        $('label[for="required_province_id' + this.provinceId + '"]', $(this.$refs.residenceAddress)).show()
                    }
                });
            }

        },
        mounted () {
            this.getProvinces()
            this.setDistrictSelect()
            this.setSubDistrictSelect()

            $(this.$refs.province).on('change', (e) => {
                console.log('Province change', e.target.value)

                this.province_id = e.target.value
            })

            $(this.$refs.district).on('change', (e) => {
                console.log('District change', e.target.value)

                this.district_id = e.target.value
            })

            $(this.$refs.subdistrict).on('change', (e) => {
                console.log('SubDistrict change', e.target.value)
                this.subdistrict_id = e.target.value
            })
        },
        watch: {
            province_id: function () {
                this.onChangeProvince();

                if (this.province_id != '') {

                    let int = setInterval(() => {
                        if (!this.loadingProvince) {
                            this.getDistricts();
                            clearInterval(int)
                        }
                    }, 200)

                    $('#required_province_id-error').hide()

                }

                this.setSelectedProvince()

            },
            district_id: function () {
                this.onChangeDistrict();

                if (this.district_id != '') {
                    let int = setInterval(() => {
                        if (!this.loadingDistrict) {
                            this.getSubDistricts();
                            clearInterval(int)
                        }
                    }, 200)

                    $('#required_district_id-error').hide()

                }

                this.setSelectedDistrict()
            },
            subdistrict_id: function () {
                console.log('setSelectedSubdistrict')
                if (this.subdistrict_id != '') {
                    $('#required_subdistrict_id-error').hide()

                    for (let i = 0; i < this.subdistricts.length; ++i) {
                        if (this.subdistricts[i].sub_district_id == this.subdistrict_id) {
                            this.postcode = this.subdistricts[i].postcode
                            break
                        }
                    }
                }

                this.setSelectedSubdistrict()
            },
            initData: {
                deep: true,
                handler: function() {
                    console.log('Init data')
                    this.isSetDistrict = false
                    this.isSetSubDistrict = false

                    this.first_set_district =  true;
                    this.first_set_subdistrict = true;
                    this.first_set_postcode =  true;
                    this.first_set_province =  true;

                    if (this.initData) {
                        this.address_line1 = this.getAddressLine1();
                        this.address_line2 = this.getAddressLine2();
                        this.province_id = this.getProvinceId();
                        this.district_id =  this.getDistrictId();
                        this.subdistrict_id = this.getSubDistrictId();
                        this.postcode = this.getPostCode();
                    }
                }
            },
            provinces() {
                this.setProvinceSelect()
                this.setSelectedProvince()
            },
            districts() {
                this.setDistrictSelect()
                this.setSelectedDistrict()
            },
            subdistricts() {

                this.setSubDistrictSelect()
                this.setSelectedSubdistrict()
            }
        },
        computed: {
            selectProvinceText: function () {
                return this.$store.state.appModule.lang.select_province
            },
            selectDistrictText: function () {
                return this.$store.state.appModule.lang.select_district
            },
            selectSubDistrictText: function () {
                return this.$store.state.appModule.lang.select_subdistrict
            },
            postCodePlaceHolder: function () {
                return this.$store.state.appModule.lang.postcode_placeholder
            },
            lang: function () {
                return this.$store.getters.lang;
            },
            getProvinceUrl() {
                let url = this.$store.getters.locale_url + '/address/provinces'
                if (this.useDeliveryResidenceAddress === true) {
                    url = this.$store.getters.locale_url + '/address/delivery-provinces'
                }

                console.log(url)
                return url
            },
            getDistrictUrl() {
                let url = this.$store.getters.locale_url + '/address/getCityById?province_id=' + this.province_id
                if (this.useDeliveryResidenceAddress === true) {
                    url = this.$store.getters.locale_url + '/address/delivery-cities?province_id=' + this.province_id
                }

                return url
            },
            getSubDistrictUrl() {
                let url = this.$store.getters.locale_url + '/address/SubDistrictById?district_id=' + this.district_id
                if (this.useDeliveryResidenceAddress === true) {
                    url = this.$store.getters.locale_url + '/address/delivery-sub-districts?district_id=' + this.district_id
                }

                return url
            },
            getAddressId() {
                if (_.isEmpty(this.addressId)) {
                    return false
                }

                return this.addressId
            },
            getProvinceIdName() {
                if (_.isEmpty(this.provinceId)) {
                    return false
                }

                return this.provinceId
            },
            getPostcodeIdName() {
                if (_.isEmpty(this.postcodeIdName)) {
                    return false
                }

                return this.postcodeIdName
            }
        }
    }
</script>