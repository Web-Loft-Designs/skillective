<template>
  <div id="simple-search-form-fields" class="d-flex flex-wrap">
    <div
      class="form-group has-feedback genre-input genre-input--lesson"
      :class="{ 'has-error': errors.genres }"
    >
      <label>Genre</label>
      <select
        class="form-control"
        v-bind:class="{ 'select-empty': fields.genre === '' }"
        name="genre"
        v-model="fields.genre"
      >
        <option selected value="">Select Genre</option>
        <option v-for="genre in siteGenres" :value="genre.id">
          {{ genre.title }}
        </option>
      </select>
      <span class="help-block" v-if="errors.genre">
        <strong>{{ errors.genre[0] }}</strong>
      </span>
    </div>

    <div
      class="form-group has-feedback type-of-lesson"
      :class="{ 'has-error': errors.lesson_type }"
    >
      <label>Type of Lesson</label>
      <select
        class="form-control"
        v-model="fields.lesson_type"
        name="lesson_type"
      >
        <option value=""></option>
        <option
          v-for="(lessonTypeTitle, lessonTypeName) in lessonTypes"
          :value="lessonTypeName"
        >
          {{ lessonTypeTitle }}
        </option>
      </select>
      <span class="help-block" v-if="errors.lesson_type">
        <strong>{{ errors.lesson_type[0] }}</strong>
      </span>
    </div>

    <div
      class="form-group has-feedback instagram-handle-input"
      :class="{ 'has-error': errors.instagram_handle }"
    >
      <label>Instructor Instagram Handle</label>
      <input
        type="text"
        class="form-control"
        name="instagram_handle"
        value=""
        v-model="fields.instagram_handle"
        placeholder="@instagram_name"
      />
      <span class="help-block" v-if="errors.instagram_handle">
        <strong>{{ errors.instagram_handle[0] }}</strong>
      </span>
    </div>

    <div
      class="form-group has-feedback instructor-name-input"
      :class="{ 'has-error': errors.instructor_name }"
    >
      <label>Instructor name</label>
      <input
        type="text"
        class="form-control"
        name="instructor_name"
        value=""
        v-model="fields.instructor_name"
        placeholder=""
      />
      <span class="help-block" v-if="errors.instructor_name">
        <strong>{{ errors.instructor_name[0] }}</strong>
      </span>
    </div>

    <div
      class="form-group has-feedback location-input"
      :class="{ 'has-error': errors.timezone_id }"
      v-if="fields.lesson_type == 'virtual'"
    >
      <label>Time Zone</label>
      <select class="form-control" v-model="fields.timezone_id">
        <option value="" disabled>Select...</option>
        <option
          v-for="timeZomeOption in timeZomeOptions"
          :value="timeZomeOption.name"
        >
          {{ timeZomeOption.name }}
        </option>
      </select>
      <span class="help-block" v-if="errors.timezone_id">
        <strong>{{ errors.timezone_id[0] }}</strong>
      </span>
    </div>
    <div
      class="form-group has-feedback location-input"
      :class="{ 'has-error': errors.location }"
      v-if="fields.lesson_type != 'virtual'"
    >
      <label>Location</label>
      <span class="location-input-wrapper">
        <input
          type="text"
          class="form-control"
          ref="lessonLocationFilter"
          id="lesson-location-filter"
          name="location"
          value=""
          v-model="fields.location"
          placeholder="Where are you going to practice?"
        />
        <span class="location-button" @click="geoLocationButton"></span>
      </span>
      <span class="help-block" v-if="errors.location">
        <strong>{{ errors.location[0] }}</strong>
      </span>
    </div>

    <div
      class="form-group has-feedback date-input"
      :class="{ 'has-error': errors.date_from }"
    >
      <label>Date range</label>
      <!--<input type="text" class="form-control" name="time_from" v-model="fields.date_from" placeholder="Choose date range">-->
      <vue-hotel-datepicker
        :startDate="fields.date_from"
        :endDate="fields.date_to"
        v-click-outside="closeIt"
        @reset="reset"
        @update="changeDate"
        :closeDatepickerOnClickOutside="true"
        :confirmText="'Apply'"
        :resetText="'Clear'"
        :mobile="'mobile'"
        :placeholder="'Choose date range'"
      ></vue-hotel-datepicker>
      <input type="hidden" name="date_from" v-model="fields.date_from" />
      <input type="hidden" name="date_to" v-model="fields.date_to" />
      <span class="help-block" v-if="errors.date_from">
        <strong>{{ errors.date_from[0] }}</strong>
      </span>
      <span class="help-block" v-if="errors.date_to">
        <strong>{{ errors.date_to[0] }}</strong>
      </span>
    </div>
    <!--<div class="form-group has-feedback date-to-input" :class="{ 'has-error' : errors.date_to }">-->
    <!--<datepicker v-model="fields.date_to" name="date_to" :format="'yyyy-MM-dd'"></datepicker>-->

    <!--<span class="help-block" v-if="errors.date_to">-->
    <!--<strong>{{ errors.date_to[0] }}</strong>-->
    <!--</span>-->
    <!--</div>-->
    <div
      class="form-group has-feedback time-input"
      :class="{ 'has-error': errors.time_from }"
    >
      <label>Time range</label>
      <time-picker
        :time-from="''"
        :time-to="''"
        @changeTimeModel="
          (start) => {
            fields.time_from = start[0];
            fields.time_to = start[1];
          }
        "
      ></time-picker>
      <!--<select>-->
      <!--<option value="">Select</option>-->
      <!--<option v-for="timeoption in timeOptions.slice(0, -1)" :value='timeoption.value'>{{ timeoption.title }}</option>-->
      <!--</select>-->
      <span class="help-block" v-if="errors.time_from">
        <strong>{{ errors.time_from[0] }}</strong>
      </span>
      <span class="help-block" v-if="errors.time_to">
        <strong>{{ errors.time_to[0] }}</strong>
      </span>
    </div>
    <!--<div class="form-group has-feedback time-to-input" :class="{ 'has-error' : errors.time_to }">-->
    <!--<label>Time to</label>-->
    <!--<select class="form-control" name="time_to" v-model="fields.time_to">-->
    <!--<option value="">Select</option>-->
    <!--<option v-for="timeoption in timeOptions.slice(1)" :value='timeoption.value'>{{ timeoption.title }}</option>-->
    <!--</select>-->
    <!--<span class="help-block" v-if="errors.time_to">-->
    <!--<strong>{{ errors.time_to[0] }}</strong>-->
    <!--</span>-->
    <!--</div>-->
    <div v-if="errorText" class="has-error">{{ errorText }}</div>
  </div>
</template>

<script>
import skillectiveHelper from "../mixins/skillectiveHelper.js";
import ClickOutside from "vue-click-outside";
const ct = require("countries-and-timezones");

export default {
  mixins: [skillectiveHelper],
  props: ["siteGenres", "defaultLocation"],
  directives: {
    ClickOutside,
  },
  data() {
    return {
      checkin: new Date(),
      checkout: new Date(),
      timeOptions: [],
      timeZomeOptions: [],
      fields: {
        instructor_name: "",
        instagram_handle: "",
        location: "",
        date_from: "",
        date_to: "",
        time_from: "",
        time_to: "",
        genre: "",
        lesson_type: "",
        timezone_id: "",
      },
      errors: [],
      errorText: null,
      lessonTypes: [],
    };
  },
  methods: {
    close: function () {},
    geoLocationButton() {
      if (navigator.geolocation) {
        var latlng = (latlng = { lat: parseFloat(0), lng: parseFloat(0) });

        navigator.geolocation.getCurrentPosition(
          (position) => {
            latlng = {
              lat: parseFloat(position.coords.latitude),
              lng: parseFloat(position.coords.longitude),
            };
            let geocoder = new google.maps.Geocoder();

            geocoder.geocode({ location: latlng }, (results, status) => {
              if (status == "OK") {
                var complited_address = "";
                results[0].address_components.map((item) => {
                  if (item.types[0] == "locality") {
                    complited_address = item.short_name;
                  }

                  if (item.types[0] == "administrative_area_level_1") {
                    complited_address =
                      complited_address + ", " + item.short_name;
                  }

                  if (item.types[0] == "country") {
                    complited_address =
                      complited_address + ", " + item.short_name;
                  }
                });
                this.fields.location = complited_address;
              } else {
                alert(
                  "Geocode was not successful for the following reason: " +
                    status
                );
              }
            });
          },
          function (error) {
            console.log(error);
            alert("Please try again");
          }
        );
      } else {
        alert("geolocation not enabled");
      }
    },
    closeIt: function (e) {
      var el = document.getElementsByClassName("close");
      if (el.length > 0) {
        el[0].click();
      }
    },
    changeDate: function (e) {
      this.fields.date_from = e.start;
      this.fields.date_to = e.end;
    },
    reset: function () {
      this.fields.date_from = "";
      this.fields.date_to = "";
    },
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this;
      var autocomplete = this.initializeLocationField(this.$refs[_ref], [
        "address",
      ]);
      google.maps.event.addListener(
        autocomplete,
        "place_changed",
        function (e) {
          thisComponent.fields.location = thisComponent.$refs[_ref].value;
        }
      );
    },
  },
  created: function () {
    this.timeOptions = this.getTimeOptions();
    this.fields.location = this.defaultLocation;
    this.lessonTypes =
      window.lessonTypes != undefined ? window.lessonTypes : [];
    this.timeZomeOptions = ct.getAllTimezones();
    // window.usTimezones!=undefined ? window.usTimezones : [];
    //ct.getTimezonesForCountry('US');  // getAllTimezones()
  },
  mounted: function () {
    this.initNewPlacesAutocomplete("lessonLocationFilter");
    // var autocomplete = this.initializeLocationField(this.$refs.lessonLocationFilter);
    //            var thisComponent = this;
    //			google.maps.event.addListener(autocomplete, 'place_changed', function (e) {
    //				var place = autocomplete.getPlace();
    //				if (place!=undefined && place.address_components!=undefined)
    //				    thisComponent.getPlaceAddressComponents(place);
    // if (place && place.geometry) {
    // 	$('#hiddenLat').val(place.geometry.location.lat());
    // 	$('#hiddenLng').val(place.geometry.location.lng());
    // } else {
    // 	$('#hiddenLat').val('');
    // 	$('#hiddenLng').val('');
    // }

    //			});
  },
};
</script>