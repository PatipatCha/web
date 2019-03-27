<template>
    <div class=" form-calendar has-feedback">
        <input class="form-control" ref="datePickerInput" v-bind:name="inputName" readonly="readonly" v-bind:value="getDefaultDate()" style="background-color: #ffffff;" v-bind:required="required" v-bind:data-rule-required="required">
        <span class="form-control-calendar" aria-hidden="true" v-on:click="showDatePicker"></span>
    </div>
</template>

<script>
    import datepicker from 'bootstrap-datepicker';
    import 'bootstrap-datepicker/dist/locales/bootstrap-datepicker.th.min'
    import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'

    export default {
        name: 'date-picker-field',
        props: {
            startDate: {
                type: String,
                default: ''
            },
            endDate: {
                type: String,
                default: ''
            },
            defaultDate: {
                type: String,
                default: ''
            },
            inputName: {
                type: String,
                default: ''
            },
            required: {
                type: Boolean,
                default: false
            },
            preventFuture: {
                type: Boolean,
                default: false
            }
        },
        mounted: function () {
            var datePickerInput = $(this.$refs.datePickerInput);

            var options = {
                format: 'dd/mm/yyyy'
            };

            if (this.startDate != '') {
                options['startDate'] = this.dateStringToFormat(this.startDate);
            }

            if (this.endDate != '') {
                options['endDate'] = this.dateStringToFormat(this.endDate);
            }

            options['language'] = this.$store.state.appModule.locale;


            //Check prevent future date
            if (this.preventFuture === true) {
                options['endDate'] = 'today'
            }

            datePickerInput.datepicker(options)
                .on('changeDate', function () {
                    datePickerInput.datepicker('hide');
                });
        },
        methods: {
            showDatePicker: function () {
                var datePickerInput = $(this.$refs.datePickerInput);
                datePickerInput.datepicker('show');
            },

            dateStringToFormat: function (dateStr) {
                if(dateStr.indexOf('-') > -1){
                    var date = dateStr.split('-');
                    return date[2] + '/' + date[1] + '/' + date[0];
                }else{
                    var date = dateStr.split('/');
                    return date[0] + '/' + date[1] + '/' + date[2];
                }
                
            },

            getDefaultDate: function () {
                if (this.defaultDate != '') {
                    if(this.defaultDate.indexOf('-') > -1){
                        var date = this.defaultDate.split('-');
                        return date[2] + '/' + date[1] + '/' + date[0];
                    }else{
                        var date = this.defaultDate.split('/');
                        return date[0] + '/' + date[1] + '/' + date[2];
                    }
                }

                return '';
            }
        }
    }
</script>