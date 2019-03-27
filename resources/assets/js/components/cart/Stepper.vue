<template>
    <div class="box-quality">
        <div class="cart-qty">
            <a v-on:click.prevent="stepDown" href="javascript:void(0)">
                <i class="fa fa-minus"></i>
                <!-- <img class="pull-left" v-bind:src="iconMinus" width="20"> -->
            </a>
            <input type="text" v-model.number="stepperNum" class="no-spinners not-prevent-special-chars" v-on:blur="onBlurInput" />
            <a v-on:click.prevent="stepUp" href="javascript:void(0)">
                <i class="fa fa-plus"></i>
                <!-- <img class="pull-right" v-bind:src="iconPlus" width="20"> -->
            </a>
            <div class="clearfix"></div>
        </div>
    </div>
</template>

<script>
    import ManualInput from './detail/Manual'

    export default{
        name: "stepper",
        components: {
            'manual-input': ManualInput
        },
        props: ["initialValue"],
        data: function() {
            return {
                // iconMinus: this.$store.getters.url + "/assets/images/minus.png",
                // iconPlus: this.$store.getters.url + "/assets/images/plus.png",
                stepperNum: this.initialValue
            }
        },
        methods: {
            stepUp: function(){
                return this.$store.dispatch("stepUp", ++this.stepperNum)
            },
            stepDown: function(){
                if(this.stepperNum <= 1){
                    return ;
                }else{
                    return this.$store.dispatch("stepDown", --this.stepperNum)
                }
            },
            onBlurInput: function () {
                this.stepperNum = parseInt(this.stepperNum);
            }
        },
        mounted: function(){
            var me = this;
            this.$store.dispatch("setQuantity", this.initialValue);
        },
        watch: {
            stepperNum: function () {
                if (this.stepperNum != '') {
                    this.stepperNum = parseInt(this.stepperNum)

                    if (isNaN(this.stepperNum) || !this.stepperNum || this.stepperNum < 1) {
                        this.stepperNum = 1;
                    }
                }

                this.$store.dispatch("stepUp", this.stepperNum);
            }
        }
    }
</script>
