<template>
  <div class="calendar-input-where-tab">

    <div class="calendar-input-where-tab__header">
      <h3 v-if="virtual" class="calendar-input-where-tab__heading">Virtual Lessons</h3>
      <h3 v-else class="calendar-input-where-tab__heading">Person Lessons</h3>
      <button v-if="virtual" class="calendar-input-where-tab__switch-virtual" @click.prevent="virtual = false">Switch to In-Person Lessons</button>
      <button v-else class="calendar-input-where-tab__switch-virtual" @click.prevent="virtual = true">Switch to Virtual Lessons</button>
    </div>

    <button
      v-if="!isLoading && !results.length && !virtual && !text"
      class="calendar-input-where-tab__explore-nearby"
      @click.prevent="exploreNearby()"
    >
      <img src="/images/calendarinput-explore-nearby.svg" alt="Explore Nearby Lessons" />
      <span>Explore Nearby Lessons</span>
      <anim-loader v-if="isGeoLoading" />
    </button>

    <anim-loader v-if="isLoading && !virtual" class="calendar-input-where-tab__loading" />
    
    <div class="calendar-input-where-tab__table" v-if="!isLoading && results.length && !virtual">
      <button class="calendar-input-where-tab__result" v-for="(result, index) in results" :key="index" @click.prevent="chooseLocation(result.city)">
        <img class="calendar-input-where-tab__pin" src="/images/map-pin-green.svg" />
        <span class="calendar-input-where-tab__name">{{ result.city }}</span>
        <span class="calendar-input-where-tab__count">{{ formatCount(result.city_count) }} Lessons</span>
        <span class="calendar-input-where-tab__count">
          <a 
            @click.stop
            :href="'/instructors?location=' + text" 
          >{{ formatCount(result.instructors_count) }} Instructors</a>
        </span>
      </button>
      <a class="calendar-input-where-tab__more" href="/">View More...</a>
    </div>

    <div class="calendar-input-where-tab__empty" v-if="!isLoading && !results.length && !virtual && text">No places found</div>

    <div class="calendar-input-where-tab__virtual-blank" v-if="virtual">
      <div class="calendar-input-where-tab__virtual-blank-content">
        <img src="/images/virtual-monitor.svg" />
        <span>Place does not matter in a virtual lesson.</span>
        <button @click.prevent="virtual = false">Go to in-person lessons</button>
      </div>
    </div>

  </div>
</template>

<script>
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import locationService from "../../../services/locationService";
import autocompleteHelper from "../../../helpers/autocompleteHelper";
import geoService from "../../../services/geoService";

export default {
  name: "CalendarInputWhereTab",
  components: {
    AnimLoader,
  },
  props: {
    value: Object,
  },
  data() {
    return {
      isGeoLoading: false,
      virtual: false,
      results: [],
      isLoading: false,
      text: "",
    }
  },
  beforeMount() {
    this.setWhereValue(this.value);
  },
  methods: {
    async exploreNearby() {
      this.isGeoLoading = true;
      const loc = await geoService.getLocation();

      if (loc && !this.virtual && !this.results.length && !this.text) {
        this.text = loc;
        this.chooseLocation(loc);
        this.goSearch();
      }
      this.isGeoLoading = false;
    },
    request() {
      locationService.autocompleteLocation(this.text).then((results) => {
        if (results.length > 8) {
          this.results = results.slice(0, 8);
        } else {
          this.results = results;
        }
        this.isLoading = false;
        if (results.length) {
          this.$emit("autocomplete", autocompleteHelper.getAutocomplete([ results[0].city ], this.text));
        } else {
          this.$emit("autocomplete", "");
        }
      });
    },
    formatCount(count) {
      return count;
    },
    goSearch() {
      this.$emit("go-search");
    },
    chooseLocation(locationName) {
      this.text = locationName;
      this.$emit("choose-location", locationName);
    },
    setWhereValue(v) {
      let needRequest = false;

      this.virtual = v.virtual;
      if (v.text) {
        if (v.text != this.text) {
          this.text = v.text;
          needRequest = true;
          this.isLoading = true;
        }
      } else {
        this.results = [];
        this.text = "";
        this.isLoading = false;
        clearTimeout(this.timeout);
      }

      if (v.text && needRequest) {
        if (this.timeout) {
          clearTimeout(this.timeout);
        }
        this.timeout = setTimeout(this.request, 500);
      }
    },
  },
  watch: {
    virtual(newValue) {
      this.$emit('virtual-changed', this.virtual);
    },
    isLoading(newValue) {
      this.$emit("loading-changed", this.isLoading);
    },
  },
}
</script>

<style lang="scss" scoped>
@import "./CalendarInputWhereTab.scss";
</style>