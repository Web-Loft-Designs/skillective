<template>
    <div class="google-map" :id="id"></div>
</template>


<script>
    export default {
        props: ['dataid','marker','center','hoverid'],
        data: function () {
            return {
                map: null,
                id: this.dataid,
                markers: null,
                infowindow: null,
                markersMap: null
            }
        },
        watch: {
            hoverid: function () {
                this.$emit('infoWindowClose')
                if(this.hoverid === null) {
                    this.infowindow.close();
                } else {
                    var marker = this.markersMap.filter((item) => {
                        return item.id === this.hoverid
                    })
                    if(marker.length) {
                        marker = marker[0];
                        var bookBtn = '<div style="flex: 0 0 100%; max-width: 100%; width: 100%;"><a href="/profile/'+marker.content.id+'" class="btn btn-block">View Profile</a></div>';
                        var _priceText = '';
                        if (marker.content.min_rate!=null && marker.content.min_rate>0)
                            _priceText = '<p>' +
                                '<strong>$'+(marker.content.min_rate!=marker.content.max_rate ? marker.content.min_rate +' - $'+ marker.content.max_rate : marker.content.min_rate)+'</strong>' +
                                'lesson' +
                                '</p>';
                        var contentHtml = '<div class="info-content">' +
                            '<div class="info-content-header">' +
                            '<div class="info-avatar-wrap"> <img src="'+marker.content.profile.image+'" /> </div>'+
                            '<span><a v-if="marker.content.profile.instagram_handle!=null" href="https://www.instagram.com/' + marker.content.profile.instagram_handle+'" target="_blank">@'+marker.content.profile.instagram_handle+'</a>'+
                            '<p>'+marker.content.full_name+'</p></span>'+
                            '</div>' +
                            '<div class="info-content-body">' +
                            '<div class="left">'+
                            '<p><strong>'+marker.content.full_name+'</strong></p>'+
                            //'<h3 class="info-title">'+ marker.content.genre.title  +'</h3>'+
                            '<p class="location">'+marker.content.profile.city + ', ' + marker.content.profile.state+'</p>'+
                            '</div>'+
                            '<div class=price>' + _priceText + '</div>'+
                            bookBtn+
                        '</div>'+
                        '</div>'
                        this.infowindow.setContent(contentHtml);
                        this.infowindow.open(this.map,marker);
                    }
                }
            },
            marker: function () {
                this.markers = this.marker
                var element = document.getElementById(this.id);
                var options = {
                    zoom: 12,
                    center: this.center
                }
                this.map = new google.maps.Map(element, options)
                var markersBounds = new google.maps.LatLngBounds();
                var infowindow = this.infowindow = new google.maps.InfoWindow();
                this.markersMap = [];
                this.markers.forEach((marker) => {
                        var position = new google.maps.LatLng(marker.position.latitude, marker.position.longitude)
                        marker.map = this.map
                        marker.position = position;
                        marker.id = marker.content.id;
                        marker.icon = window.location.protocol+'//'+window.location.host+'/images/marker1.png';

                        var currentMarker = new google.maps.Marker(marker);
                        this.markersMap.push(currentMarker);
                        currentMarker.addListener('click', () => {
							var bookBtn = '<div style="flex: 0 0 100%; max-width: 100%; width: 100%;"><a href="/profile/'+marker.content.id+'" class="btn btn-block">View Profile</a></div>';
                            var _priceText = '';
                            if (marker.content.min_rate!=null && marker.content.min_rate>0)
                                _priceText = '<p>' +
                                    '<strong>$'+(marker.content.min_rate!=marker.content.max_rate ? marker.content.min_rate +' - $'+ marker.content.max_rate : marker.content.min_rate)+'</strong>' +
                                    'lesson' +
                                    '</p>';
                            var contentHtml = '<div class="info-content">' +
                                '<div class="info-content-header">' +
                                '<div class="info-avatar-wrap"> <img src="'+marker.content.profile.image+'" /> </div>'+
                                '<span><a v-if="marker.content.profile.instagram_handle!=null" href="https://www.instagram.com/' + marker.content.profile.instagram_handle+'" target="_blank">@'+marker.content.profile.instagram_handle+'</a>'+
                                '<p>'+marker.content.full_name+'</p></span>'+
                                '</div>' +
                                '<div class="info-content-body">' +
                                '<div class="left">'+
                                '<p><strong>'+marker.content.full_name+'</strong></p>'+
                                //'<h3 class="info-title">'+ marker.content.genre.title  +'</h3>'+
                                '<p class="location">'+marker.content.profile.city + ', ' + marker.content.profile.state+'</p>'+
                                '</div>'+
                                '<div class=price>' + _priceText + '</div>'+
                                bookBtn +
                            '</div>'+
                            '</div>'
                            infowindow.setContent(contentHtml);
                            infowindow.open(marker.map, currentMarker);
                            this.$emit('infoWindowOpen',marker.content.id)
                        });
                })
                this.markers.forEach((marker) => {
                    markersBounds.extend(marker.position);
                })
                setTimeout(() => {
                    this.map.setCenter(markersBounds.getCenter());
                    this.map.fitBounds(markersBounds)
                },100)

                if(this.markersMap.length === 1) {
                    setTimeout(() => {
                        this.map.setZoom(12);
                    },1000)
                }
            }
        },
        computed: {
            mapMarkers: function () {
                return this.markers
            }
        },
        mounted: function () {
            this.markers = this.marker
            var element = document.getElementById(this.id);
            var options = {
                zoom: 12,
                center: this.center
            }
            this.map = new google.maps.Map(element, options);
            var markersBounds = new google.maps.LatLngBounds();
            this.infowindow = null;
            var infowindow = this.infowindow = new google.maps.InfoWindow();
            this.markersMap = [];
            this.markers.forEach((marker) => {
                    var position = new google.maps.LatLng(marker.position.latitude, marker.position.longitude)
                    marker.map = this.map
                    marker.position = position;
                    marker.id = marker.content.id;

                    marker.icon = window.location.protocol+'//'+window.location.host+'/images/marker1.png';

                    var currentMarker = new google.maps.Marker(marker);
                    this.markersMap.push(currentMarker);
                    currentMarker.addListener('click', function() {
                        var countSlot = marker.content.spots_count - marker.content.count_booked;
						var bookBtn = '<div style="flex: 0 0 100%; max-width: 100%; width: 100%;"><a href="/profile/'+marker.content.id+'" class="btn btn-block">View Profile</a></div>';
						var _priceText = '';
                        if (marker.content.min_rate!=null && marker.content.min_rate>0)
                            _priceText = '<p>' +
                                '<strong>$'+(marker.content.min_rate!=marker.content.max_rate ? marker.content.min_rate +' - $'+ marker.content.max_rate : marker.content.min_rate)+'</strong>' +
                                'lesson' +
                                '</p>';
                        var contentHtml = '<div class="info-content">' +
                            '<div class="info-content-header">' +
                            '<div class="info-avatar-wrap"> <img src="'+marker.content.profile.image+'" /> </div>'+
                            '<span><a v-if="marker.content.profile.instagram_handle!=null" href="https://www.instagram.com/' + marker.content.profile.instagram_handle+'" target="_blank">@'+marker.content.profile.instagram_handle+'</a>'+
                            '<p>'+marker.content.full_name+'</p></span>'+
                            '</div>' +
                            '<div class="info-content-body">' +
                            '<div class="left">'+
                            '<p><strong>'+marker.content.full_name+'</strong></p>'+
//                            '<h3 class="info-title">'+ marker.content.genre.title  +'</h3>'+
                            '<p class="location">'+marker.content.profile.city + ', ' + marker.content.profile.state+'</p>'+
                            '</div>'+
                            '<div class=price>' + _priceText + '</div>'+
                            bookBtn+
                        '</div>'+
                        '</div>'
                        infowindow.setContent(contentHtml);
                        infowindow.open(marker.map, currentMarker);
                        this.$emit('infoWindowOpen',marker.content.id)
                    });

            })
            this.markers.forEach((marker) => {
                markersBounds.extend(marker.position);
            })
            setTimeout(() => {
                this.map.setCenter(markersBounds.getCenter());
                this.map.fitBounds(markersBounds);
            },100)
            if(this.markersMap.length === 1) {
                setTimeout(() => {
                    this.map.setCenter(markersBounds.getCenter());
                    this.map.setZoom(12)
                },400)
            }

        },
        methods: {}
    }

</script>
<style scoped>
    .google-map {
        width: 100%;
        height: 280px;
        margin: 0 auto;
        background: gray;
    }
</style>