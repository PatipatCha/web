<template>
    <div class="google-map" :id="mapName"></div>
</template>

<script>
    import {trans} from "../../../libraries/trans"

    let instance = null
    window.selectStore = (event, id) => {
        // console.log(event)
        console.log(id)
        event.target.innerHTML = '<i class="fa fa-spinner fa-spin"></i> '+trans('select')
        instance.selectStore(id)
    }
    export default {
        name: "GoogleMap",
        props: {
            stores: {
                request: true
            },
            latLng: {
                request: true
            },
            onClickStoreFromMap: {
                type: Function,
                default: () => {}
            },
            storeSelected: {
                request: false
            },
            allStore: {
                request: true
            }
        },
        data() {
            return {
                mapName: 'store-map',
                position: null,
                markers: [],
                infoes: [],
                infowindow: '',
                map: null
            }
        },
        computed: {
            element: function () {
                return document.getElementById(this.mapName)
            },
            selected_address: function () {
                return this.$store.state.storeModule.selected_address
            }
        },
        methods: {
            initMap() {
                let icon = '/images/makro-store-pin.png'
                this.position = new google.maps.LatLng(this.latLng)
                this.infowindow = ''
                // console.log(obAddress)
                const options = {
                    zoom: 10,
                    center: this.position,
                    title: 'Hello',
                    mapTypeControl: false,
                    fullscreenControl: false,
                    streetViewControl: false
                }

                this.map = new google.maps.Map(this.element, options);
                let map = this.map
                this.markers = []
                this.infoes = []
                _.forEach(this.allStore, (item) => {
                    if(_.has(item.data, 'address')) {
                        let marker = {
                            lat: _.get(item.data, 'address.location.lat'),
                            lng: _.get(item.data, 'address.location.lng')
                        }
                        this.infoes[item.data.address.id] = new google.maps.InfoWindow({
                            content: this.getAddress(item.data),
                        });

                        let pointer = new google.maps.Marker({
                            position: marker,
                            map: map,
                            icon: icon,
                            title: item.data.name
                        })
                        this.markers[item.data.address.id] = pointer

                        pointer.addListener('click', () => {
                            if (this.infowindow) {
                                this.infoes[this.infowindow].close()
                            }
                            let id = item.data.address.id
                            console.log(item)
                            this.infowindow = id
                            this.infoes[item.data.address.id].open(map, this.markers[item.data.address.id])
                            this.selectStore(id)
                            this.map.panTo(item.data.address.location);
                        });
                    }
                })
                if(this.storeSelected) {
                    let id = _.get(this.storeSelected, 'data.address.id')
                    if (id) {
                        this.openInfoWindow(id)
                    }
                }
            },
            openInfoWindow(id) {
                if(this.infowindow) {
                    this.infoes[this.infowindow].close()
                }
                this.infowindow = id
                if(_.has(this.infoes, id)) {
                    this.infoes[this.infowindow].open(this.map, this.markers[this.infowindow])
                }
            },
            getAddress(item) {
                let btn = ''
                if(item.address.id == this.storeSelected.data.address.id) {
                     btn = '<i class="far fa-check"></i>'
                }
                let locale = this.$store.getters.locale
                let branch_name = item.name
                if(locale === 'th') {
                    branch_name = _.split(branch_name, 'สาขา', 2)
                    branch_name = 'สาขา'+branch_name[1]
                }else {
                    branch_name = _.split(branch_name, 'Branch', 2)
                    branch_name = 'Branch '+branch_name[1]
                }
                return '<div class="store-location text-center">' +
                    '<div class="icon-pin"><img src="/images/makro-store-pin.png"/></div>' +
                    '<div class="title">'+ branch_name +'</div>' +
                    btn +
                    '</div>'
            },
            selectStore(id) {
                this.onClickStoreFromMap(id)
                // this.infoes[id] = new google.maps.InfoWindow({
                //     content: this.getAddress(this.storeSelected.data),
                // });
            }
        },
        mounted() {
            this.initMap()
            instance = this
        },
        watch: {
            // stores: function () {
            //     this.initMap()
            // },
            latLng: function () {
                console.log(this.latLng)
                // this.initMap()
                this.openInfoWindow(_.get(this.storeSelected, 'data.address.id'))
                this.map.panTo(this.latLng);

            }
        }
    }
</script>

<style scoped>
    .google-map {
        width: auto;
        height: calc(50vh - 50px);
        margin: 0 auto;
        background: gray;
    }
</style>