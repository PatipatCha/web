<template>
    <div class="google-map" :id="mapName"></div>
</template>

<script>
export default {
    name: 'view-map',
    props: {
        store: {
            type: Object,
            default: null
        }
    },
    computed: {
        element() {
            return document.getElementById(this.mapName)
        },
        latLng() {
            return _.get(this.store, 'address.location')
        }
    },
    data() {
        return {
            mapName: 'preview-map',
            position: null,
            markers: [],
            info: null,
            infowindow: '',
            map: null
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
            this.info = new google.maps.InfoWindow({
                content: this.getAddress(this.store),
            });
            this.info = new google.maps.InfoWindow({
                content: this.getAddress(this.store),
            });

            let pointer = new google.maps.Marker({
                position: this.latLng,
                map: map,
                icon: icon,
                title: this.store.name
            })
            pointer.addListener('click', () => {
                this.info.open(map, pointer)
            });
            this.info.open(map, pointer)
        },
        getAddress(store) {
            let btn = ''
            return '<div class="store-location text-center">' +
                '<div class="icon-pin"><img src="/images/makro-store-pin.png"/></div>' +
                '<div class="title">'+ store.name +'</div>' +
                btn +
                '</div>'
        },
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