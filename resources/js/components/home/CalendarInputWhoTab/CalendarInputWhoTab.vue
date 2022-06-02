<template>
  <div class="calendar-input-who-tab">

    <anim-loader v-if="isLoading" />

    <div class="calendar-input-who-tab__table" v-else-if="instructors.length">
      <div class="calendar-input-who-tab__header">
        <h3 class="calendar-input-who-tab__heading">Name</h3>
        <h3 class="calendar-input-who-tab__heading">Handle</h3>
      </div>
      <button 
        class="calendar-input-who-tab__instructor" 
        v-for="(instructor, index) in instructors" 
        @click.prevent="chooseInstructor(
          instructor.full_name,
          instructor.profile.instagram_handle,
          instructor.id
        )" 
        :key="index"
      >
        <div class="calendar-input-who-tab__instructor-name">
          <img :src="instructor.profile.image" />
          <span v-html="highlightAutocomplete(instructor.full_name)"></span>
        </div>
        <div class="calendar-input-who-tab__handle">
          <span v-html="'@' + highlightAutocomplete(instructor.profile.instagram_handle)"></span>
        </div>
      </button>
      <a class="calendar-input-who-tab__more" href="/instructors">View More...</a>
    </div>

    <div class="calendar-input-who-tab__empty" v-else>No instructors found</div>

  </div>
</template>

<script>
import instructorService from "../../../services/instructorService";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import autocompleteHelper from "../../../helpers/autocompleteHelper";
import { mapState } from 'vuex';

export default {
  name: "CalendarInputWhoTab",
  components: {
    AnimLoader,
  },
  props: {
    value: Object,
    getAll: Boolean,
  },
  data() {
    return {
      text: "",
      instructors: [],
      isLoading: true,
      timeout: null,
    }
  },
  beforeMount() {
    this.setWhoValue(this.value);
  },
  methods: {
    request() {
      instructorService.autocompleteInstructors(this.text).then((instructors) => {
        if (instructors.length > 8) {
          this.instructors = instructors.slice(0, 8);
        } else {
          this.instructors = instructors;
        }
        this.isLoading = false;
        if (instructors.length) {
          this.$emit("autocomplete", autocompleteHelper.getAutocomplete(
            [ instructors[0].first_name, instructors[0].last_name, instructors[0].profile.instagram_handle ], 
            this.text
          ));
        } else {
          this.$emit("autocomplete", "");
        }
      });
    },
    highlightAutocomplete(str) {
      return autocompleteHelper.highlightAutocomplete(str, this.text);
    },
    chooseInstructor(fullName, instagramHandle, instructorId) {
      this.text = fullName;
      this.$emit("choose-instructor", { fullName, instagramHandle, instructorId });
    },
    setWhoValue(v) {
      let needRequest = false;

      if (v.text && v.text != this.text) {
        this.text = v.text;
        needRequest = true;
        this.isLoading = true;
      }

      if (v.text && needRequest) {
        if (this.timeout) {
          clearTimeout(this.timeout);
        }
        this.timeout = setTimeout(this.request, 500);
      }
    },
  },
  computed: {
    ...mapState(['allInstructors'])
  },
  watch: {
    isLoading(newValue) {
      this.$emit("loading-changed", this.isLoading);
    },
    getAll() {
      this.isLoading = true
      this.instructors = this.allInstructors
      this.isLoading = false
      this.$emit("loading-changed", this.isLoading)
    }
  },
}
</script>

<style lang="scss" scoped>
@import "./CalendarInputWhoTab.scss";
</style>