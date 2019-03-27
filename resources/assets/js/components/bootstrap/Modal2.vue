<template>
	<div ref="modal" class="modal fade mk-modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document" v-bind:class="modalDialogClass">
			<div class="modal-content">
				<button type="button" class="close" v-on:click="onCancel"  aria-label="Close" style="margin-right: 0;" v-if="preventClose === false"><span aria-hidden="true">&times;</span></button>

				<div class="modal-header" v-if="showHeader">
					<slot name="header">
						<h4 class="modal-title text-center">
							<slot name="title">
								<div v-html="title"></div>
							</slot>
						</h4>
					</slot>
				</div>

				<div class="modal-body" v-if="showBody" :class="showFooter?'':'disable'">
					<div class="row">
						<div v-bind:class="contentClass">
							<slot>
								Content goes here...
							</slot>
						</div>
					</div>
				</div>

				<div class="modal-footer" v-if="showFooter">
					<div class="row">
						<div :class="footerClass">
							<slot name="footer">
								<div class="btn-wrap" v-if="reverseButtonAlign">
									<button ref="cancelButton" type="button" class="btn btn-default" v-if="showCancel" v-on:click="onCancel" id="btn_pop_up_continue_shopping">
										<i class="far fa-times"></i> {{ cancelText }}
									</button>
									<button ref="okButton" type="button" class="btn btn-primary" :disabled="showLoading" v-if="showOk" v-on:click="onOk" :id="getBtnId">
										<i class="far fa-check"></i>
										<loading v-bind:show="showLoading"></loading> {{ okText }}
									</button>
								</div>
								<div class="btn-wrap" v-else>
									<button ref="cancelButton" type="button" class="btn btn-default" v-if="showCancel" v-on:click="onCancel" id="btn_pop_up_continue_shopping">
										<i class="far fa-times"></i> {{ cancelText }}
									</button>
										<button ref="okButton" type="button" class="btn btn-primary" :disabled="showLoading" v-if="showOk" v-on:click="onOk"
										:id="getBtnId">
										<i class="far fa-check"></i>
										<loading v-bind:show="showLoading"></loading> {{ okText }}
									</button>
								</div>
							</slot>
						</div>
					</div>

					<div class="error" v-if="errorText && errorText.length > 0" style="text-align: center; margin-top: 10px;">
						{{ errorText }}
					</div>
				</div>

			</div>
		</div>
	</div>
</template>
<script>
	import Loading from '../loading/Loading'
	import store from '../../store'
	import { mapGetters } from 'vuex'

	export default {
		name: 'modal-2',
		component: {
            Loading
		},
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
			},
			contentSize: {
			    type: String,
				default: 'normal'  //'normal', 'wide'
			},
			showOk: {
			    type: Boolean,
				default: true
			},
            showCancel: {
                type: Boolean,
                default: true
            },
			okText: {
                type: String,
				default: store.getters.lang.ok
			},
            cancelText: {
                type: String,
                default: store.getters.lang.cancel
            },
			showLoading: {
			    type: Boolean,
				default: false
			},
			closeOnBlur: {
			    type: Boolean,
				default: true
			},
			center: {
                type: Boolean,
                default: true
			},
			closeOnBlur: {
			    type: Boolean,
				default: false
			},
			reverseButtonAlign: {
                type: Boolean,
                default: false
			},
            preventClose: {
                type: Boolean,
                default: false
			},
            btnId: {
                type: String,
                default: ''
			},
			errorText: {
			    type: String,
				default: ''
			}
		},
		data: function () {
			return {
			    okButtonText: this.okText,
				cancelButtonText: this.cancelText
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

			//Set default lang
			//Ok button text
			if (!this.okButtonText) {
                this.okButtonText = this.lang.ok
			}

			//Cancel button text
            if (!this.cancelButtonText) {
                this.cancelButtonText = this.lang.cancel
            }
		},
		methods: {
			showModal: function () {
				$(this.$el).modal('show');
			},
			hideModal: function () {
				$(this.$el).modal('hide');
			},
            onOk: function () {
				this.$emit('on-ok');
            },
            onCancel: function () {
			    if (!this.showLoading) {
                    this.hideModal()
                    this.$emit('on-cancel');
				}
            }
		},
		watch: {
			show: function () {
				if (this.show) {
					this.showModal()
				} else {
					this.hideModal()
				}
			},
//            closeOnBlur: function () {
//                if (!this.closeOnBlur) {
//                    $(this.$el).data('bs.modal').options.keyboard = false
//                    $(this.$el).data('bs.modal').options.backdrop = 'static'
//                } else {
//                    $(this.$el).data('bs.modal').options.keyboard = true
//                    $(this.$el).data('bs.modal').options.backdrop = 'true'
//                }
//            }
		},
		computed: {
			...mapGetters([
			    'popupOption'
			]),
			customClasses: function () {
				return this.additionalCustomClasses
			},
			modalDialogClass: function () {
				return {
					'modal-lg': this.size == 'large' ? true : false
				}
			},
			contentClass: function () {
				return {
				    'col-sm-8 col-sm-offset-2': (this.contentSize == 'normal') ? true : false,
					'col-sm-10 col-sm-offset-1': (this.contentSize == 'wide') ? true : false,
                    'col-sm-12': (this.contentSize == 'full') ? true : false,
					'text-center': this.center
				}
            },
			footerClass: function() {
                return this.size == 'large' ? 'col-sm-4 col-sm-offset-4' : 'col-sm-8 col-sm-offset-2'

			},
			lang: function () {
				return this.$store.getters.lang
            },
            getBtnId: function () {
                if (!this.btnId) {
                    return false;
                }

                return this.btnId
            }
		}
	}
</script>