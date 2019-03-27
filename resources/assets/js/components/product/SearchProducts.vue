<template>
    <div>
        <v-autocomplete v-bind:value="value" :auto-select-one-item="false" :placeholder="lang.search" :items="items"
                        v-model="item"
                        :inputAttrs="{class:'form-control',required:'true', id: nameId, name:'q', autocomplete:'off', value:'{{value}}'}"
                        :get-label="getLabel" :component-item='template' @update-items="updateItems">
        </v-autocomplete>
    </div>
</template>


<script>
    import ItemTemplate from './ItemTemplate.vue'
    import Autocomplete from 'v-autocomplete'

    Vue.component('v-autocomplete', Autocomplete)

    export default {
        props: {
            value: {
                type: String,
                defualt: ''
            },
            nameId: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                item: this.value == '' ? null : {name: this.value},
                items: [],
                template: ItemTemplate
            }
        },
        methods: {
            getLabel(item) {
                return item.name
            },
            updateItems(text) {
                var $this = this
                axios.get(this.$store.getters.locale_url + '/autoSearch?q=' + text)
                    .then(function (response) {
                        $this.items = response.data
                    })
                    .catch(function () {

                    });
            }

        },
        mounted() {
            $('.show-autocomplete').remove()
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>