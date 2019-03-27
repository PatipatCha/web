<template>
    <div class="row">
        <div class="col-sm-12">
            <i>
                <h4 class="text-muted">{{ lang.enter_makro_id_or_select_makro_store_notice }}</h4>
            </i>
        </div>
        <div class="col-lg-5">
            <label class="radio-inline">
                <input type="radio" value="card" v-model="type" id="opt_makro_member_card"> {{ lang.have_makro_member }}
            </label>
        </div>
        <div class="col-lg-6">
            <label class="radio-inline">
                <input type="radio" value="store" v-model="type" id="opt_makro_member_store"> {{ lang.dont_have_makro_member }}
            </label>
        </div>
    </div>
    <!--<div class="has-feedback" :class="{'has-error': hasError === true}">-->
        <!--<label></label>-->
        <!--<select class="form-control makro-store-type" v-model="type">-->
            <!--<option value="">{{ lang.select_makro_store_or_enter_your_makro_card_id }}</option>-->
            <!--<option value="card">{{ lang.makro_card_id }}</option>-->
            <!--<option value="store">{{ lang.select_makro_store }}</option>-->
        <!--</select>-->

        <!--<span class="remark" v-if="hasError">-->
            <!--*{{ lang.please_select_makro_store_or_enter_your_makro_card_id }}-->
		<!--</span>-->
    <!--</div>-->
</template>

<script>
    export default {
        name: 'select-makro-type',
        data() {
            return {
                type: 'card',
                hasError: ''
            }
        },
        watch: {
            type: function (newValue) {
                this.$emit('select', newValue)
                this.validate()
            }
        },
        mounted() {
            $(() => {
                $( ".makro-store-type" ).select2({
                    theme: "bootstrap",
                    minimumResultsForSearch: -1
                });

                $(".makro-store-type").on("select2:select", (e) => {
                    this.type = e.target.value
                });

                $(".makro-store-type").on("select2:close", (e) => {
                    this.validate()
                });
            })
        },
        methods: {
            validate() {
                if (this.type == '') {
                    this.hasError = true
                    return false
                } else {
                    this.hasError = false
                    return true
                }
            }
        },
        computed: {
            lang() {
                return this.$store.getters.lang;
            }
        }
    }
</script>
