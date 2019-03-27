<template>
    <span class="hidden-print">
        <a @click="openModal" style="cursor:pointer">({{ trans('view_map') }})</a>
        <modal 
            :show="showModal"
            content-size="full"
            :prevent-close="true"
            class="modal-shopping-option "
            @on-open-modal="openModal"
            @on-close-modal="closeModal"
            :show-body="true"
            :show-footer="true"
        >
        <div slot="header" class="text-left">
            <h4 class="modal-title pull-left">{{ storeName }}</h4>
        </div>
        <div>
            <view-map ref="viewMap" :store="storeData"></view-map>
        </div>
        <div slot="footer">
            <div class="address-modal-box new-address-modal-box">
                <div class="cta">

                    <div class="text-center">
                        <button type="button"
                                class="btn btn-primary"
                                @click="closeModal"
                        >
                            {{ trans('ok') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </modal>
    </span>
</template>

<script>
import Modal from '../../components/bootstrap/Modal2'
import ViewMap from './ViewMap'
import { trans } from '../../libraries/trans'

export default {
    name: 'view-map-buttom',
    components: {
        Modal,
        ViewMap
    },
    props: {
        storeData: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            showModal: false
        }
    },
    computed: {
        locale() {
            return this.$store.getters.locale
        },
        storeName() {
            return _.get(this.storeData, 'name')
        }
    },
    methods: {
        openModal() {
            this.$refs.viewMap.initMap()
            this.showModal = true
        },
        closeModal() {
            this.showModal = false
        }
    }
}
</script>
