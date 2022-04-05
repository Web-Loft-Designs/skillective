<template>
  <div class="google-map" :id="id"></div>
</template>


<script>
export default {
  props: ["dataid", "marker", "center", "hoverid", "currentUserCanBook"],
  data: function () {
    return {
      map: null,
      id: this.dataid,
      markers: null,
      infowindow: null,
      markersMap: null,
    };
  },
  watch: {
    hoverid: function () {
      if (this.hoverid === null) {
        this.infowindow.close();
      } else {
        var marker = this.markersMap.filter((item) => {
          return item.id === this.hoverid;
        });
        if (marker.length) {
          marker = marker[0];
          var countSlot =
            marker.content.spots_count - marker.content.count_booked;
          var description = "";

          if (marker.content.description) {
            description =
              "<p><strong>Note:</strong> " +
              marker.content.description +
              "</p>";
          }
          var contentHtml =
            '<div class="info-content">' +
            '<div class="info-content-header">' +
            '<div class="info-avatar-wrap"> <img src="' +
            marker.content.instructor.profile.image +
            '" /> </div>' +
            '<span><a v-if="marker.content.instructor.profile.instagram_handle!=null" href="https://www.instagram.com/' +
            marker.content.instructor.profile.instagram_handle +
            '" target="_blank">@' +
            marker.content.instructor.profile.instagram_handle +
            "</a>" +
            "<p>" +
            marker.content.instructor.full_name +
            "</p></span>" +
            "</div>" +
            '<div class="info-content-body">' +
            '<div class="left">' +
            '<h3 class="info-title">' +
            marker.content.genre.title +
            "</h3>" +
            '<p class="time">' +
            moment(marker.content.start).format("MMM DD") +
            ", " +
            moment(marker.content.start).format("h:mm A") +
            " - " +
            moment(marker.content.end).format("h:mm A") +
            "</p>" +
            '<p class="location">' +
            marker.content.location +
            "</p>" +
            "<p><strong>spots left:</strong> " +
            countSlot +
            "</p>" +
            description +
            "</div>" +
            "<div class=price>" +
            "<p>" +
            "<strong>$" +
            marker.content.spot_price +
            "</strong>" +
            "lesson" +
            "</p>" +
            "</div>" +
            "</div>" +
            "</div>";
          this.infowindow.setContent(contentHtml);
          this.infowindow.open(this.map, marker);
        }
      }
    },
    marker: function () {
      this.markers = this.marker;
      var element = document.getElementById(this.id);
      var options = {
        zoom: 12,
        center: this.center,
      };
      this.map = new google.maps.Map(element, options);
      var markersBounds = new google.maps.LatLngBounds();
      this.infowindow = null;
      var infowindow = (this.infowindow = new google.maps.InfoWindow());
      this.markersMap = [];
      this.markers.forEach((marker) => {
        var markerColor =
          marker.content.spots_count - marker.content.count_booked;
        var position = new google.maps.LatLng(
          marker.position.latitude,
          marker.position.longitude
        );
        marker.map = this.map;
        marker.position = position;
        marker.id = marker.content.id;

        if (markerColor > 2) {
          marker.icon =
            window.location.protocol +
            "//" +
            window.location.host +
            "/images/marker1.png";
        } else if (markerColor === 2) {
          marker.icon =
            window.location.protocol +
            "//" +
            window.location.host +
            "/images/marker-yellow.png";
        } else if (markerColor === 1) {
          marker.icon =
            window.location.protocol +
            "//" +
            window.location.host +
            "/images/marker-red.png";
        } else {
          marker.icon =
            window.location.protocol +
            "//" +
            window.location.host +
            "/images/marker-grey.png";
        }

        var currentMarker = new google.maps.Marker(marker);
        this.markersMap.push(currentMarker);
        var countSlot =
          marker.content.spots_count - marker.content.count_booked;

        var description = "";

        if (marker.content.description) {
          description =
            "<p><strong>Note:</strong> " + marker.content.description + "</p>";
        }

        currentMarker.addListener("click", function () {
          var contentHtml =
            '<div class="info-content">' +
            '<div class="info-content-header">' +
            '<div class="info-avatar-wrap"> <img src="' +
            marker.content.instructor.profile.image +
            '" /> </div>' +
            '<span><a v-if="marker.content.instructor.profile.instagram_handle!=null" href="https://www.instagram.com/' +
            marker.content.instructor.profile.instagram_handle +
            '" target="_blank">@' +
            marker.content.instructor.profile.instagram_handle +
            "</a>" +
            "<p>" +
            marker.content.instructor.full_name +
            "</p></span>" +
            "</div>" +
            '<div class="info-content-body">' +
            '<div class="left">' +
            '<h3 class="info-title">' +
            marker.content.genre.title +
            "</h3>" +
            '<p class="time">' +
            moment(marker.content.start).format("MMM DD") +
            ", " +
            moment(marker.content.start).format("h:mm A") +
            " - " +
            moment(marker.content.end).format("h:mm A") +
            "</p>" +
            '<p class="location">' +
            marker.content.location +
            "</p>" +
            "<p><strong>spots left:</strong> " +
            countSlot +
            "</p>" +
            description +
            "</div>" +
            "<div class=price>" +
            "<p>" +
            "<strong>$" +
            marker.content.spot_price +
            "</strong>" +
            "lesson" +
            "</p>" +
            "</div>" +
            "</div>" +
            "</div>";
          infowindow.setContent(contentHtml);
          infowindow.open(marker.map, currentMarker);
        });
      });

      this.markers.forEach((marker) => {
        markersBounds.extend(marker.position);
      });

      setTimeout(() => {
        this.map.setCenter(markersBounds.getCenter());
        this.map.fitBounds(markersBounds, 0);
      }, 100);
      if (this.markersMap.length === 1) {
        setTimeout(() => {
          this.map.setZoom(12);
        }, 100);
      }
    },
  },
  computed: {
    mapMarkers: function () {
      return this.markers;
    },
  },
  mounted: function () {
    // this.markers = this.marker
    // var element = document.getElementById(this.id);
    // var options = {
    //     zoom: 12,
    //     center: this.center
    // }
    // this.map = new google.maps.Map(element, options);
    // var markersBounds = new google.maps.LatLngBounds();
    // this.infowindow = null;
    // var infowindow = this.infowindow = new google.maps.InfoWindow();
    // this.markersMap = [];
    // this.markers.forEach((marker) => {
    //     var position = new google.maps.LatLng(marker.position.latitude, marker.position.longitude)
    //     marker.map = this.map
    //     marker.position = position;
    //     marker.id = marker.content.id;
    //     if(marker.colorMarker === 'green') {
    //         marker.icon = window.location.protocol+'//'+window.location.host+'/images/marker1.png';
    //     } else {
    //         marker.icon = window.location.protocol+'//'+window.location.host+'/images/marker-red.png';
    //     }
    //
    //     markersBounds.extend(marker.position);
    //
    //     var currentMarker = new google.maps.Marker(marker);
    //     this.markersMap.push(currentMarker);
    //     currentMarker.addListener('click', function() {
    //         var contentHtml = '<div class="info-content">' +
    //             '<div class="info-content-header">' +
    //             '<img src="'+marker.content.instructor.profile.image+'" />'+
    //             '<span><a v-if="marker.content.instructor.profile.instagram_handle!=null" href="https://www.instagram.com/' + marker.content.instructor.profile.instagram_handle+'" target="_blank">@'+marker.content.instructor.profile.instagram_handle+'</a>'+
    //             '<p>'+marker.content.instructor.full_name+'</p></span>'+
    //             '</div>' +
    //             '<div class="info-content-body">' +
    //             '<div class="left">'+
    //             '<h3 class="info-title">'+ marker.content.genre.title  +'</h3>'+
    //             '<p class="time">'+moment(marker.content.start).format("MMM DD")+', '+ moment(marker.content.start).format("h:mm A")+' - '+ moment(marker.content.end).format("h:mm A")+'</p>'+
    //             '<p class="location">'+marker.content.location+'</p>'+
    //             '</div>'+
    //             '<div class=price>' +
    //             '<p>' +
    //             '<strong>$'+marker.content.spot_price+'</strong>' +
    //             'lesson' +
    //             '</p>' +
    //             '</div>'
    //         '</div>'+
    //         '</div>'
    //         infowindow.setContent(contentHtml);
    //         infowindow.open(marker.map, currentMarker);
    //     });
    // })
    // // this.map.setCenter(markersBounds.getCenter(), this.map.fitBounds(markersBounds));
    // if(this.markersMap.length === 1) {
    //     setTimeout(() => {
    //         this.map.setZoom(12)
    //     },100)
    // }
  },
  methods: {},
};
</script>
<style scoped>
.google-map {
  width: 100%;
  height: 280px;
  margin: 0 auto;
  background: gray;
}
</style>