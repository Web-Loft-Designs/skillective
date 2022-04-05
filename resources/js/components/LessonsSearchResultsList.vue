<template>
  <div
    id="profile-lessons-container"
    class="container-result"
    v-bind:class="{'open-map-wrapper': mapActive && listItems.length!==0 }"
  >
    <!-- <calendar-input-when-popup
      @value-changed="whenValueChanged($event)"
      ref="calendarInputWhenPopup"
    /> -->
    <!-- <div
      id="list-search-form-fields"
      class="top-filter"
      v-bind:class="{'mobile-show': mobileShowFilter }"
    >
      <button @click="mobileShowFilter1()" type="button" class="mobile-show-filter"></button>
      <form @submit.prevent="onSubmit">
        <div class="row">
          <div class="form-group has-feedback genre-input" :class="{ 'has-error' : errors.genres }">
            <label>Genre</label>
            <select
              class="form-control"
              v-bind:class="{ 'select-empty': fields.genre === ''}"
              name="genre"
              v-model="fields.genre"
            >
              <option selected value></option>
              <option v-for="genre in siteGenres" :value="genre.id">{{ genre.title }}</option>
            </select>
            <span class="help-block" v-if="errors.genre">
              <strong>{{ errors.genre[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback location-input"
            :class="{ 'has-error' : errors.timezone_id }"
            v-if="fields.lesson_type=='virtual'"
          >
            <label>Time Zone</label>
            <select class="form-control" v-model="fields.timezone_id">
              <option value disabled>Select...</option>
              <option
                v-for="timeZomeOption in timeZomeOptions"
                :value="timeZomeOption.name"
              >{{ timeZomeOption.name }}</option>
            </select>
            <span class="help-block" v-if="errors.timezone_id">
              <strong>{{ errors.timezone_id[0] }}</strong>
            </span>
          </div>
          <div
            class="form-group has-feedback location-input"
            :class="{ 'has-error' : errors.location }"
            v-if="fields.lesson_type!='virtual'"
          >
            <label>Location</label>
            <span class="location-input-wrapper">
              <input
                type="text"
                class="form-control"
                name="location"
                v-model="fields.location"
                ref="lessonLocationFilter"
              />
              <span class="location-button" @click="geoLocationButton"></span>
            </span>
            <span class="help-block" v-if="errors.location">
              <strong>{{ errors.location[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback date-input"
            :class="{ 'has-error' : errors.date_from }"
          >
            <label>Date range</label>
            <input
              type="text"
              class="form-control"
              value="Do you want to learn?"
              ref="whenButton"
              @click.prevent="toggleWhenPopup()"
              readonly
            />
            <input type="hidden" name="date_to" v-model="fields.date_to" />
            <input type="hidden" name="date_from" v-model="fields.date_from" />
            <input type="hidden" name="flexible_months" v-model="fields.flexible_months" />
            <input type="hidden" name="flexible_days" v-model="fields.flexible_days" />
            <span class="help-block" v-if="errors.date_from">
              <strong>{{ errors.date_from[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback time-input"
            :class="{ 'has-error' : errors.time_from }"
          >
            <label>Time range</label>
            <time-picker
              :hidePlaceholder="true"
              :time-from="fields.time_from"
              :time-to="fields.time_to"
              @changeTimeModel="function(start) {
                    fields.time_from = start[0];
                    fields.time_to = start[1];
                }"
            ></time-picker>
            <span class="help-block" v-if="errors.time_from">
              <strong>{{ errors.time_from[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback instagram-handle-input"
            :class="{ 'has-error' : errors.instagram_handle }"
          >
            <label>Instagram Handle</label>
            <input
              type="text"
              class="form-control"
              name="instagram_handle"
              value
              v-model="fields.instagram_handle"
            />
            <span class="help-block" v-if="errors.instagram_handle">
              <strong>{{ errors.instagram_handle[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback instructor-name-input"
            :class="{ 'has-error' : errors.instructor_name }"
          >
            <label>Instructor name</label>
            <input
              type="text"
              class="form-control"
              name="instructor_name"
              value
              v-model="fields.instructor_name"
            />
            <span class="help-block" v-if="errors.instructor_name">
              <strong>{{ errors.instructor_name[0] }}</strong>
            </span>
          </div>

          <div class="form-group has-feedback" :class="{ 'has-error' : errors.lesson_type }">
            <label>Type of Lesson</label>
            <select class="form-control" v-model="fields.lesson_type" name="lesson_type">
              <option value></option>
              <option
                v-for="(lessonTypeTitle, lessonTypeName) in lessonTypes"
                :value="lessonTypeName"
              >{{ lessonTypeTitle }}</option>
            </select>
            <span class="help-block" v-if="errors.lesson_type">
              <strong>{{ errors.lesson_type[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback time-input"
            :class="{ 'has-error' : errors.price_from }"
          >
            <label>Price</label>
            <time-price
              :hidePlaceholder="true"
              :priceFromProp="fields.price_from"
              :priceToProp="fields.price_to"
              @changeTimeModel="test"
            ></time-price>
            <span class="help-block" v-if="errors.price_from">
              <strong>{{ errors.price_from[0] }}</strong>
            </span>
          </div>

          <div class="form-group">
            <input type="submit" value="Search Lessons" />
          </div>
          <div v-if="errorText" class="has-error">{{ errorText }}</div>
        </div>
      </form>
    </div> -->

    <transition name="slidein">
      <div 
        class="result-wrapper-map" 
        v-show="mapActive && listItems.length!==0"
        @click.stop=""
      >
        <google-map-single
          :dataid="'search'"
          :center="{
                                    lat: 10,
                                    lng:10
                                }"
          :hoverid="hoverID"
          :marker="markers"
          :current-user-can-book="currentUserCanBook"
          @infoWindowOpen="triggerOpen"
          @infoWindowClose="triggerClose"
        ></google-map-single>
      </div>
    </transition>
    <div>
      <!-- <div class="top-sort-wrapper" v-show="listItems.length!==0"> -->
        <!-- <div class="sort-wrapper">
          <form>
            <label>Sort by</label>
            <select v-model="sortOrder" @change="onChangeOrder">
              <option value="spot_price_asc">Lowest price</option>
              <option value="spot_price_desc">Highest price</option>
              <option value="start_asc">Lesson (soonest)</option>
              <option value="start_desc">Lesson (Latest)</option>
            </select>
          </form>
        </div> -->
        <!-- <div class="map-switch">
          <p>Map</p>
          <label
            @click="mapActive = !mapActive"
            v-bind:class="{'open-map': mapActive }"
            class="switch-label"
          >
            <span v-if="!mapActive">Hide</span>
            <span v-else>Show</span>
          </label>
        </div> -->
      <!-- </div> -->

      <!-- <div class="result-wrapper-items"> -->
        <!-- <div class="item" v-for="(lesson, index) in listItems" :key="index">
          <div
            class="item-wrapper"
            :data-id="lesson.id"
            @mouseover="hoverTrigger(lesson.id)"
            v-click-outside="unHoverTrigger"
            @click.stop="hoverTrigger(lesson.id)"
          >
            <div class="item-body"> -->
              <!-- <a target="_blank" :href="'/profile/' + lesson.instructor.id"> -->
                <!--<span class="avatar" :style="'background-image: url('+lesson.instructor.profile.image+')'">-->
                <!-- <span class="avatar">
                  <a target="_blank" :href="'/profile/' + lesson.instructor.id" class="profile-link">
                    <img :src="lesson.instructor.profile.image" alt />
                    <span> -->
                      <!-- View
                      <br />instructor -->
                      <!-- View more lessons 
                    </span>
                  </a>
                </span> -->
              <!-- </a> -->
              <!-- <a
                class="insta"
                v-if="lesson.instructor.profile.instagram_handle!=null"
                :href="'https://www.instagram.com/' + lesson.instructor.profile.instagram_handle"
                target="_blank"
              >
                <strong>@{{ lesson.instructor.profile.instagram_handle }}</strong>
              </a>
              <span>
                <a
                  :href="'/profile/'+lesson.instructor.id"
                  class="link-to-profile"
                >{{ lesson.instructor.full_name }}</a>
              </span>
            </div>
            <div class="item-footer">
              <div class="left">
                <div class="item--header-title">
                  <div class='item--header-title-wrap'> -->
                    <!-- <img v-if="lesson.lesson_type=='in_person'" src="/images/in-person-green.svg" /> -->
                    <!-- <img
                      v-if="lesson.lesson_type=='virtual'"
                      src="/images/private-lesson-green.svg"
                    /> -->
                    <!-- <span> {{lesson.genre.title}} </span>
                  </div>
                  <div class="item--header-price-wrap">
                       <span class='item--header-price' >${{ lesson.spot_price }}</span> <span> lesson</span>    
                  </div>
                </div>
                <p class='item--content'>
                  <strong>{{ lesson.start | moment("MMM DD") }}, {{ lesson.start | moment("h:mm A") }} – {{ lesson.end | moment("h:mm A") }} ({{ lesson.timezone_id }})</strong>
                </p>
                <p v-if="lesson.lesson_type=='in_person'" v-html="lesson.location"></p>
              </div>
              <div class="full">
                <a
                  @click.prevent="addToCart(lesson.id)"
                  v-if="currentUserCanBook"
                  class="btn btn-block"
                >Add to cart</a>
              </div>
            </div>
          </div>
        </div>
        <div v-if="listItems.length==0" class="col-12 p-5 mt-5">
          <h3 class="text-center">Nothing found</h3>
        </div>
      </div> -->

      <!-- <div class="lessons-pagination-outer">
        <paginate
          v-if="pagination.total_pages>1"
          v-model="pagination.current_page"
          :page-count="pagination.total_pages"
          :force-page="pagination.current_page"
          :prev-text="'Prev'"
          :next-text="'Next'"
          :click-handler="paginatorClickCallback"
          :container-class="'lessons-pagination'"
        ></paginate>
      </div> -->
    </div>
  </div>
</template>

<script>
// import { mapActions, mapMutations } from 'vuex';
import siteAPI from "../mixins/siteAPI.js";
import skillectiveHelper from "../mixins/skillectiveHelper.js";
// import Paginate from "vuejs-paginate";
import ClickOutside from "vue-click-outside";
import $ from "jquery";
const ct = require("countries-and-timezones");
// import VueAutonumeric from '../../../node_modules/vue-autonumeric/src/components/VueAutonumeric.vue';
// import CalendarInputWhenPopup from "../components/home/CalendarInputWhenPopup/CalendarInputWhenPopup.vue";
// import dateHelper from "../helpers/dateHelper";

export default {
  mixins: [siteAPI, skillectiveHelper],
  props: {
    loggedInAsStudent: false,
    lessons: null,
    lessonsMeta: {},
    siteGenres: null,
    currentUserCanBook: false,
  },
  directives: {
    ClickOutside
  },
  // components: {
  //   // VueAutonumeric,
  //   Paginate,
  //   // CalendarInputWhenPopup,
  // },
  data() {
    return {
      // showWhenPopup: false,
      listMeta: [],
      timeOptions: [],
      timeZomeOptions: [],
      // sortOrder: "start_asc",
      mapActive: true,
      // mobileShowFilter: false,
      markers: [],
      hoderFlag: false,
      hoverID: null,
      fields: {
        instructor_name: "",
        instagram_handle: "",
        location: "",
        date_from: undefined,
        date_to: undefined,
        flexible_months: null,
        flexible_days: null,
        time_from: "",
        time_to: "",
        price_from: "",
        price_to: "",
        genre: "",
        lesson_type: "",
        timezone_id: ""
      },
      pagination: {
        count: 0,
        total: 0,
        total_pages: 0,
        current_page: 0
      },
      errors: [],
      errorText: null,
      lessonTypes: []
    };
  },
  methods: {
    // whenValueChanged(newValue) {
    //   if (newValue.whenText) this.$refs.whenButton.value = newValue.whenText;
    //   if (newValue.flexibleMode) {
    //     this.fields.date_from = "";
    //     this.fields.date_to = "";
    //     this.fields.flexible_days = newValue.choosedFlexibleDays;
    //     this.fields.flexible_months = newValue.choosedFlexibleMonths;
    //   } else {
    //     this.fields.date_from = newValue.from;
    //     this.fields.date_to = newValue.to;
    //     this.fields.flexible_days = "";
    //     this.fields.flexible_months = "";
    //   }
    // },
    // toggleWhenPopup() {
    //   this.showWhenPopup = !this.showWhenPopup;
    //   this.$refs.calendarInputWhenPopup.setShow(this.showWhenPopup);
    // },
    // ...mapActions(['addItemToCartAtStart']),
    // addToCart: async function(lessonId) {  
    //   if (this.loggedInAsStudent) {
    //     await this.addItemToCartAtStart({
    //       lessonId,
    //       specialRequest: "",
    //     });
    //     this.$root.$emit("showMiniCart");
    //   } else {
    //     window.location.href = "/login";
    //   }
    // },
    triggerClose() {
      // $(".item-wrapper").removeClass("active");
      // this.$root.$emit("lessonsListHoverTrigger", null);
    },
    triggerOpen(id) {
      // this.hoverID = id;
      $([document.documentElement, document.body]).animate(
        {
          scrollTop: $("[data-id=" + id + "]").offset().top - 200
        },
        250
      );
      // $(".item-wrapper").removeClass("active");
      // $("[data-id=" + id + "]").addClass("active");
      this.$root.$emit("lessonsListHoverTrigger", id);
    },
    // geoLocationButton() {
    //   if (navigator.geolocation) {
    //     var latlng = (latlng = { lat: parseFloat(0), lng: parseFloat(0) });

    //     navigator.geolocation.getCurrentPosition(
    //       position => {
    //         latlng = {
    //           lat: parseFloat(position.coords.latitude),
    //           lng: parseFloat(position.coords.longitude)
    //         };
    //         let geocoder = new google.maps.Geocoder();

    //         geocoder.geocode({ location: latlng }, (results, status) => {
    //           if (status == "OK") {
    //             var complited_address = "";
    //             results[0].address_components.map(item => {
    //               if (item.types[0] == "locality") {
    //                 complited_address = item.short_name;
    //               }

    //               if (item.types[0] == "administrative_area_level_1") {
    //                 complited_address =
    //                   complited_address + ", " + item.short_name;
    //               }

    //               if (item.types[0] == "country") {
    //                 complited_address =
    //                   complited_address + ", " + item.short_name;
    //               }
    //             });
    //             this.fields.location = complited_address;
    //           } else {
    //             alert(
    //               "Geocode was not successful for the following reason: " +
    //                 status
    //             );
    //           }
    //         });
    //       },
    //       function(error) {
    //         console.log(error);
    //         alert("try again");
    //       }
    //     );
    //   } else {
    //     alert("geolocation not enabled");
    //   }
    // },
    // mobileShowFilter1() {
    //   this.mobileShowFilter = !this.mobileShowFilter;
    //   if (this.mobileShowFilter) {
    //     $("body").addClass("filter-active");
    //   } else {
    //     $("body").removeClass("filter-active");
    //   }
    //   $("#app-navbar-collapse").collapse("hide");
    //   // $('#app-navbar-collapse').removeClass('show');
    //   // $('#app-navbar-collapse').removeClass('in');
    //   // $('#app-navbar-collapse').attr('aria-expanded',false);
    //   // $('.navbar-toggle').addClass('collapsed').attr('aria-expanded',false);
    // },
    onSubmit() {
      // this.mobileShowFilter = false;
      // if (moment(this.fields.date_from))
      //     this.fields.date_from = moment(this.fields.date_from).format('YYYY-MM-DD');
      //
      // if (moment(this.fields.date_to))
      //     this.fields.date_to = moment(this.fields.date_from).format('YYYY-MM-DD');

      let queryParams = _.cloneDeep(this.fields);
      // let separatorIndex = this.sortOrder.lastIndexOf("_");
      // if (separatorIndex) {
      //   let _sortedBy = this.sortOrder.substring(
      //     this.sortOrder.lastIndexOf("_") + 1
      //   );
      //   if (_sortedBy == "asc" || _sortedBy == "desc") {
      //     queryParams.sortedBy = _sortedBy;
      //     queryParams.orderBy = this.sortOrder.substring(0, separatorIndex);
      //   }
      // }
      if (this.pagination.current_page != undefined) {
        queryParams.page = this.pagination.current_page;
      }
      this.updateUrlQueryParams(queryParams);

      this.apiGet("/api/lessons/search" + window.location.search, this.fields);
    },
    // hoverTrigger(id) {
    //   $(".item-wrapper").removeClass("active");
    //   this.hoverID = id;
    // },
    // unHoverTrigger() {
    //   $(".item-wrapper").removeClass("active");
    //   this.hoverID = null;
    // },
    // onChangeOrder() {
    //   this.onSubmit();
    // },
    paginatorClickCallback(pageNum) {
      this.onSubmit();
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data;
      if (
        responseData.data.meta != undefined &&
        responseData.data.meta.pagination != undefined
      ) {
        this.pagination.count = responseData.data.meta.pagination.count;
        this.pagination.total = responseData.data.meta.pagination.total;
        this.pagination.total_pages =
          responseData.data.meta.pagination.total_pages;
        this.pagination.current_page =
          responseData.data.meta.pagination.current_page;
      }
      this.markers = [];
      this.listItems.forEach(item => {
        this.markers.push({
          position: {
            latitude: item.lat,
            longitude: item.lng
          },
          content: item
        });
      });
    },
    changeDate: function(e) {
      this.fields.date_from = e.start;
      this.fields.date_to = e.end;
    },
    reset: function() {
      this.fields.date_from = "";
      this.fields.date_to = "";
    },
    closeIt: function(e) {
      var el = document.getElementsByClassName("close");
      if (el.length > 0) {
        el[0].click();
      }
    },
    test: function(start) {
      this.fields.price_from = start[0];
      this.fields.price_to = start[1];
    },
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this;
      var autocomplete = this.initializeLocationField(this.$refs[_ref], [
        "address"
      ]);
      google.maps.event.addListener(autocomplete, "place_changed", function(e) {
        thisComponent.fields.location = thisComponent.$refs[_ref].value;
      });
    }
  },
  created: function() {
    this.$root.$on("lessonsToggleMap", (show) => {
      this.mapActive = show;
    });
    this.$root.$on("lessonsHoverTrigger", (id = null) => {
      this.hoverID = id;
    });

    this.listItems = this.lessons;
    this.lessonTypes =
      window.lessonTypes != undefined ? window.lessonTypes : [];
    this.timeZomeOptions = ct.getAllTimezones();
      // window.usTimezones != undefined ? window.usTimezones : []; 
      //ct.getTimezonesForCountry('US'); // getAllTimezones()
    if (
      this.lessonsMeta != undefined &&
      this.lessonsMeta.pagination != undefined
    ) {
      this.pagination.count = this.lessonsMeta.pagination.count;
      this.pagination.total = this.lessonsMeta.pagination.total;
      this.pagination.total_pages = this.lessonsMeta.pagination.total_pages;
      this.pagination.current_page = this.lessonsMeta.pagination.current_page;
    }

    this.markers = [];

    this.timeOptions = this.getTimeOptions();
    for (var prop in this.fields) {
      if (this.fields.hasOwnProperty(prop))
        this.fields[prop] = this.getUrlParameter(prop);
    }
    // if (this.getUrlParameter("orderBy") && this.getUrlParameter("sortedBy")) {
    //   this.sortOrder = (
    //     this.getUrlParameter("orderBy") +
    //     "_" +
    //     this.getUrlParameter("sortedBy")
    //   ).toLowerCase();
    // }
  },
  mounted() {
    // let flexMonths = [];
    // if (this.fields.flexible_months) {
    //   let arr = this.fields.flexible_months.split(",");
    //   arr.map((item, index) => {
    //     arr[index] = { active: true, number: (item - 1) };
    //   });
    //   flexMonths = arr;
    // }

    // if ((this.fields.date_from != null && this.fields.date_from.length > 0) || (flexMonths.length > 0)) {
    //   const newValue = {
    //     from: this.fields.date_from ? dateHelper.filterToDate(this.fields.date_from) : null,
    //     to: this.fields.date_to ? dateHelper.filterToDate(this.fields.date_to) : null,
    //     flexibleMode: this.fields.date_to == null || this.fields.date_to.length == 0,
    //     choosedFlexibleMonths: flexMonths,
    //     choosedFlexibleDays: this.fields.flexible_days,
    //   };
    //   this.$refs.calendarInputWhenPopup.whenTabValueChanged(newValue);
    // }

    this.initNewPlacesAutocomplete("lessonLocationFilter");

    this.lessons.forEach(item => {
      if (item.lat)
        this.markers.push({
          position: {
            latitude: item.lat,
            longitude: item.lng
          },
          content: item
        });
    });
  }
};
</script>