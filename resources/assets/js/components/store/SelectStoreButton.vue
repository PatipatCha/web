<template>
    <button @click="getAddress" type="button" class="btn btn-default navbar-btn pickup-btn btn-block">
        <span v-if="!selected_address" class="text-ellipsis">
            <i class="far fa-box"></i>
            {{ trans('choose_way_to_get_product') }}
        </span>
        <div v-else class="pickup-btn-group">
            <span class="icon">
                <i class="far fa-box"></i>
            </span>
            <div :title="FullAddress()" class="label" v-if="selected_address.type == 'address'">
                <span class="title">{{ trans('delivery_to') }}</span>
                <span class="value">{{ FullAddress() }}</span>
            </div>
            <div :title="FullAddress()" class="label" v-if="selected_address.type == 'store'">
                <span class="title">{{ trans('pick_up_at_your_own_branch') }}</span>
                <span class="value">{{ FullAddress() }}</span>
            </div>

            <span class="edit visible-xs"> {{ trans('change') }}</span>
        </div>
    </button>
</template>

<script>
    import {SET_OPEN_POPUP_ADDRESS} from '../../store/mutation-types'
    import {mapState} from 'vuex'

    export default {
        name: "SelectStoreButton",
        data() {
            return {}
        },
        methods: {
            getAddress() {
                this.$store.commit(SET_OPEN_POPUP_ADDRESS, true)
            },
            FullAddress() {
                let text = ''
                let locale = this.$store.getters.locale
                if(_.get(this.selected_address, 'type') == 'address') {
                    text += _.get(this.selected_address, 'postcode') + ' '
                }
                let branch_name = _.get(this.selected_address.name, locale)
                if(locale === 'th') {
                    branch_name = _.split(branch_name, 'สาขา', 2)
                    branch_name = 'สาขา'+branch_name[1]
                }else {
                    branch_name = _.split(branch_name, 'Branch', 2)
                    branch_name = 'Branch '+branch_name[1]
                }
                text += _.get(this.selected_address.province.original_name, locale, branch_name)
                return text
            }
        },
        computed: {
            ...mapState({
                selected_address: state => state.storeModule.selected_address,
                current_store_id: state => state.storeModule.current_store_id
            })
        }
    }
</script>