<template>
  <div class="calendar-input-what-tab">
    
    <anim-loader v-if="isLoading" />

    <div class="calendar-input-what-tab__table" v-else-if="genres.length">
      <div class="calendar-input-what-tab__header">
        <h3 class="calendar-input-what-tab__heading">Genres</h3>
      </div>
      <button class="calendar-input-what-tab__genre" v-for="(genre, index) in genres" :key="index" @click.prevent="chooseGenre(genre.title, genre.id)">
        <div class="calendar-input-what-tab__genre-name">
          <span>{{ genre.title }}</span>
        </div>
      </button>
      <!-- <a class="calendar-input-what-tab__more" href="/">View More...</a> -->
    </div>

    <div class="calendar-input-what-tab__empty" v-else>No genres found</div>

  </div>
</template>

<script>
import genreService from "../../../services/genreService";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import autocompleteHelper from "../../../helpers/autocompleteHelper";

export default {
  name: "CalendarInputWhatTab",
  components: {
    AnimLoader,
  },
  props: {
    value: Object,
    preloadedGenres: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      text: "",
      genres: this.preloadedGenres,
      isLoading: false,
    }
  },
  beforeMount() {
    this.setWhatValue(this.value);
  },
  methods: {
    request() {
      genreService.autocompleteGenres(this.text).then((genres) => {
        if (genres.length > 8) {
          this.genres = genres.slice(0, 8);
        } else {
          this.genres = genres;
        }
        this.isLoading = false;
        if (genres.length) {
          this.$emit("autocomplete", autocompleteHelper.getAutocomplete([ genres[0].title ], this.text));
        } else {
          this.$emit("autocomplete", "");
        }
      });
    },
    highlightAutocomplete(str) {
      return autocompleteHelper.highlightAutocomplete(str, this.text);
    },
    chooseGenre(genreName, genreId) {
      this.text = genreName;
      this.$emit("choose-genre", { genreName, genreId });
    },
    setWhatValue(v) {
      let needRequest = false;

      if (v.text) {
        if (v.text != this.text) {
          this.text = v.text;
          needRequest = true;
          this.isLoading = true;
        }
      } else {
        this.genres = this.preloadedGenres;
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
    isLoading(newValue) {
      this.$emit("loading-changed", this.isLoading);
    },
  },
}
</script>

<style lang="scss" scoped>
@import "../CalendarInputWhoTab/CalendarInputWhoTab.scss";
</style>