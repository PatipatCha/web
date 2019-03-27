<template>
    <div class="modal fade" v-bind:class="customClasses" tabindex="-1" role="dialog">
        <div class="modal-dialog" v-bind:class="modalDialogClass" role="document">
            <div class="modal-content">
                <div class="modal-header" v-if="showHeader">
                    <slot name="header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-right: 0;"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <slot name="title">
                                {{ title }}
                            </slot>
                        </h4>
                    </slot>
                </div>
                <div class="modal-body" v-if="showBody">
                    <slot>
                       Content goes here.....
                    </slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </slot>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</template>
<script>
    export default {
        name: 'modal',
        props: {
            show: {
                type: Boolean,
                default: false
            },
            title: {
                type: String
            },
            additionalCustomClasses: {
                type: String
            },
            showHeader: {
                type: Boolean,
                default: true
            },
            showFooter: {
                type: Boolean,
                default: true
            },
            size: {
                type: String,
                default: ''
            },
            showBody: {
                type: Boolean,
                default: true
            }
        },
        mounted: function () {
            var me = this
            $(this.$el).on('hidden.bs.modal', function () {
                me.$emit('on-close-modal')
            })


            $(this.$el).on('shown.bs.modal', function () {
                me.$emit('on-open-modal')
            })

            $(this.$el).on('show.bs.modal', function () {
                me.$emit('on-before-open-modal')
            })


            if (this.show) {
                this.showModal()
            }
        },
        methods: {
            showModal: function () {
                $(this.$el).modal('show');
            },
            hideModal: function () {
                $(this.$el).modal('hide');
            }
        },
        watch: {
            show: function () {
                if (this.show) {
                    this.showModal()
                } else {
                    this.hideModal()
                }
            }
        },
        computed: {
            customClasses: function () {
                return this.additionalCustomClasses
            },
            modalDialogClass: function () {
                return {
                    'modal-lg': this.size == 'large' ? true : false
                }
            }
        }
    }
</script>