<template>
    <div v-show="show">
        <!--<label for="">{{ lang.makro_store }}<span style="color:#F01616;"> *</span></label>-->
        <div class="has-feedback" :class="feedBackClass">
            <select class="form-control makro-store" id="cbo_makro_member_store">
                <option value="">{{ lang.select_a_makro_store }}</option>
                <option
                        v-for="(store, index) in makroStores"
                        :key="index.toString()"
                        :value="store.id"
                >{{ store.name }}</option>
            </select>
            <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" v-if="hasError === false"></span>

            <div id="lbl_makro_member_store">
                 <span class="remark" v-show="hasError === true">
                    {{ lang.please_select_a_makro_store }}
                </span>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: 'makro-store-field',
        props: {
            makroStores: {
                type: Array,
                required: true,
            },
            show: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                makroStoreId: '',
                hasError: ''
            }
        },
        methods: {
            initSelect2() {

                $(".makro-store").select2('destroy');

                setTimeout(() => {
                    $( ".makro-store" ).select2({
                        theme: "bootstrap"
                    });

                    $(".makro-store").unbind('select2:select')
                    $(".makro-store").unbind('select2:close')

                    $(".makro-store").on("select2:select", (e) => {
                        this.makroStoreId = e.target.value

                        console.log(this.makroStoreId)
                    });

                    $(".makro-store").on("select2:close", (e) => {
                        this.validate()
                    });

                }, 50)

            },
            validate() {
                if (parseInt(this.makroStoreId) > 0) {
                    this.hasError = false
                    return true
                } else {
                    this.hasError = true
                    return false
                }
            }
        },
        mounted() {
            $(() => {
                this.initSelect2()
            })
        },

        watch: {
            makroStoreId: function (newValue) {
                this.validate()
            },
            show: function (newValue) {
                if (newValue === true) {
                    this.initSelect2()
                }
            }
        },

        computed: {
            feedBackClass() {
                let hasError = false
                let hasSuccess = false

                if (this.hasError === false) {
                    hasError = false
                    hasSuccess = true
                } else if (this.hasError === true) {
                    hasError = true
                    hasSuccess = false
                }

                return {
                    'has-error': hasError,
                    'has-success': hasSuccess
                }
            },
            lang() {
                return this.$store.getters.lang;
            }
        }
    }
</script>