<template>
  <div id="profile-form-container">
    <form method="post" @submit.prevent="onSubmit">
      <p class="login-box-msg">{{ title }}</p>
      <p class="text" v-html="description"></p>

      <div class="row geo-wrap" v-for="(geoLocation, index) in listItems">
        <div class="col-md-6 col-12">
          <div
            class="form-group has-feedback"
            :class="{ 'has-error': errors[index + '.location'] }"
          >
            <input
              type="text"
              class="form-control"
              value=""
              v-model="geoLocation.location"
              placeholder="Location"
              :ref="'geoLocation' + index"
              :id="'geoLocation' + index"
            />
            <span class="help-block" v-if="errors[index + '.location']">
              <strong>{{ errors[index + ".location"][0] }}</strong>
            </span>
          </div>
        </div>

        <div class="col-md-6 col-12 d-flex flex-wrap align-items-center">
          <div
            class="form-group w-50 has-feedback"
            :class="{ 'has-error': errors[index + '.limit'] }"
          >
            <select class="form-control" v-model="geoLocation.limit">
              <option value="">Select Limit</option>
              <option
                v-for="(limitName, limitValue) in availableLimits"
                :value="limitValue"
              >
                {{ limitName }}
              </option>
            </select>
            <span class="help-block" v-if="errors[index + '.limit']">
              <strong>{{ errors[index + ".limit"][0] }}</strong>
            </span>
          </div>

          <div
            class="form-group w-50 has-feedback"
            :class="{
              'has-error':
                errors[index + '.date_from'] || errors[index + '.date_to'],
            }"
          >
            <!--<datepicker v-model="geoLocation.date_from" name="date_from" :format="'yyyy-MM-dd'"></datepicker>-->
            <vue-hotel-datepicker
              :startDate="geoLocation.date_from"
              :endDate="geoLocation.date_to"
              @reset="reset"
              @update="changeDate($event, geoLocation)"
              :confirmText="'Apply'"
              :resetText="'Clear'"
              :mobile="'mobile'"
              :placeholder="'Choose date range'"
            ></vue-hotel-datepicker>
            <!--<input type="hidden" name="date_from" v-model="geoLocation.date_from">-->
            <!--<input type="hidden" name="date_to" v-model="geoLocation.date_to">-->
            <span
              class="help-block"
              v-if="errors[index + '.date_from'] || errors[index + '.date_to']"
            >
              <span v-if="errors[index + '.date_from']">
                <strong
                  v-if="
                    errors[index + '.date_from'][0] === 'Invalid date format'
                  "
                  >Date required</strong
                >
              </span>
              <span v-else>
                <strong v-if="errors[index + '.date_to']">Date required</strong>
              </span>
            </span>
          </div>

          <!--<div class="form-group has-feedback" :class="{ 'has-error' : (errors[index + '.date_to']) }">-->
          <!--<datepicker v-model="geoLocation.date_to" name="date_to" :format="'yyyy-MM-dd'"></datepicker>-->
          <!--<span class="help-block" v-if="errors[index + '.date_to']">-->
          <!--<strong>{{ errors[index + '.date_to'][0] }}</strong>-->
          <!--</span>-->
          <!--</div>-->

          <span class="btn-del" @click="deleteLocation(geoLocation)">
            <img src="../../images/remove-icon.png" alt="" />
          </span>
        </div>
      </div>

      <div class="form-group">
        <span @click="addLocation()" class="link-style"
          ><img src="../../images/add-location-icon.png" alt="" />Add
          Location</span
        >
      </div>

      <div v-if="errorText" class="has-error form-group">{{ errorText }}</div>
      <div v-if="successText" class="has-success form-group">
        {{ successText }}
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</template>

<script>
import siteAPI from "../mixins/siteAPI.js";
import skillectiveHelper from "../mixins/skillectiveHelper.js";
import ClickOutside from "vue-click-outside";
import $ from "jquery";

export default {
  mixins: [siteAPI, skillectiveHelper],
  props: {
    availableLimits: null,
    userGeoLocations: null,
    userId: null,
    title: null,
    description: null,
  },
  data() {
    return {};
  },
  directives: {
    ClickOutside,
  },
  methods: {
    closeIt: function (e) {
      // this.$refs.datepickerid.forEach(item => {
      //     if(item.active) {
      //         item.close();
      //     }
      // })
    },
    changeDate: function (e, geoLocation) {
      geoLocation.date_from = e.start;
      geoLocation.date_to = e.end;
      // this.fields.date_from = e.start;
      // this.fields.date_to = e.end;
    },
    reset: function () {
      // this.fields.date_from = '';
      // this.fields.date_to = '';
    },
    onSubmit() {
      for (var i = 0; i < this.listItems.length; i++) {
        if (moment(this.listItems[i].date_from))
          this.listItems[i].date_from = moment(
            this.listItems[i].date_from
          ).format("YYYY-MM-DD");
        if (moment(this.listItems[i].date_to))
          this.listItems[i].date_to = moment(this.listItems[i].date_to).format(
            "YYYY-MM-DD"
          );
        // if (this.listItems[i].date_from instanceof Date)
        // 	this.listItems[i].date_from = this.listItems[i].date_from.toISOString().split('T')[0];
        // if (this.listItems[i].date_to instanceof Date)
        // 	this.listItems[i].date_to = this.listItems[i].date_to.toISOString().split('T')[0];
      }

      var submitUrl = "/api/user/geo-locations";
      if (this.userId != null) submitUrl += "/" + this.userId;
      this.apiPut(submitUrl, this.listItems);
    },
    addLocation() {
      this.listItems.push({
        id: null,
        date_from: "",
        date_to: "",
        location: null,
        limit: null,
      });

      setTimeout(() => {
        var counter = 0;
        for (var _ref in this.$refs) {
          if (this.$refs.hasOwnProperty(_ref)) {
            if (counter == this.listItems.length - 1) {
              this.initNewPlacesAutocomplete(_ref);
            }
            counter++;
          }
        }
      }, 50);
    },
    deleteLocation(geoLocation) {
      this.itemToDelete = geoLocation;
      if (this.itemToDelete.id > 0) {
        this.apiDelete("/api/user/geo-locations/" + geoLocation.id);
      } else {
        this.componentHandleDeleteResponse(null);
      }
    },
    componentHandlePutResponse(responseData) {
      this.listItems = responseData.data;
    },
    componentHandleDeleteResponse(responseData) {
      if (this.itemToDelete != null) {
        this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
        this.itemToDelete = null;
      }
    },
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this;
      var autocomplete = null;
      autocomplete = this.initializeLocationField(this.$refs[_ref][0], [
        "address",
      ]);
      google.maps.event.addListener(
        autocomplete,
        "place_changed",
        function (e) {
          thisComponent.listItems[
            parseInt(_ref.replace("geoLocation", ""))
          ].location = thisComponent.$refs[_ref][0].value;
        }
      );
    },
  },
  created: function () {
    this.listItems = this.userGeoLocations;
  },
  mounted() {
    for (var _ref in this.$refs) {
      if (this.$refs.hasOwnProperty(_ref)) {
        this.initNewPlacesAutocomplete(_ref);
      }
    }
  },
};
</script>