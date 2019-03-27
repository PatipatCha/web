@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <!--================================================== -->

    <div class="container margin-bottom">
        <div class="location-box">
            <div class="topic-text">
                <b>{{ trans('frontend.store_location') }}</b>
            </div>

            <div class="deta-box2">

                <div class="col-lg-3 col-md-3 col-sm-3 margin-bottom-15 padding-left-ad">
                    <div class="btn-group-ad">
                        <select name="stores" id="stores">
                            <option value="">{{ trans('frontend.store_location') }}</option>
                            @foreach($lists as $key => $list)
                                <optgroup label="{{ trim($key) }}" title="{{ trim($key) }}">
                                    @foreach($list as $key2 => $item)
                                        <option value="{{ $item['id'] }}" title="{{ $item['name'] }} {{ trim($key) }}">{{ trim($item['name']) }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div id="map" style="width:100%;height:500px;background-color:brown;"></div>
            <div id="info"></div>

        </div>
    </div>

    <!--================================================== -->
@endsection

@section('head')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('script')
    <script>
        var items = {!! json_encode($items) !!}
        var infoes = []
        var markers = []
        var map = {}
        var infowindow = '';

        function initMap() {
            var bounds = new google.maps.LatLngBounds()
            var icon = '{{ asset('assets/images/icon-location.png') }}'

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: {lat: 13.736717, lng: 100.523186}
            });

            map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById('info'));

            $.each(items, function (i, item) {
                infoes[item.id] = new google.maps.InfoWindow({
                    content:  item.full_address
                });

                markers[item.id] = new google.maps.Marker({
                    map: map,
                    draggable: false,
                    position: {lat:  item.address.location.lat, lng: item.address.location.lng},
                    icon: icon
                });

                markers[item.id].addListener('click', function() {
                    if(infowindow){
                        infoes[infowindow].close()
                    }
                    var id = item.id
                    infowindow = id
                    infoes[item.id].open(map, markers[item.id])
                });

                bounds.extend(markers[item.id].getPosition())
            })

            map.fitBounds(bounds);
        }

        $(document).ready(function(){
            // Create select
            $.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
                $("#stores").select2({
                    matcher: oldMatcher(matchStart)
                })
            });

            // Pan to marker when user select branch from dropdown list
            $("#stores").on("select2:select", function (e) {
                if(infowindow){
                    infoes[infowindow].close()
                }
                var id = e.params.data.id
                infowindow = id
                map.panTo(markers[id].getPosition())
                infoes[id].open(map, markers[id])
                map.setZoom(17)
            });
        });

        function matchStart (term, text, option) {
            var str = option.title;
            if (str.toLowerCase().indexOf(term.toLowerCase()) > 0) {
                return true;
            }

            return false;
        }
    </script>
    <!-- google map script must locate here -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=geometry&callback=initMap"></script>
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
@endsection