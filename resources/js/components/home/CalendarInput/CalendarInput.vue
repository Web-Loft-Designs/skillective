<template>
  <div :class="{
      'calendar-input-holder': true,
      'calendar-input-holder--visible': !showFixedSearchByDefault && !alwaysOnTop,
    }">
    <div
      :class="{
        'calendar-input': true,
        'calendar-input--showfixed': showFixedCalendarInput || alwaysOnTop,
        'calendar-input--alwaysontop': alwaysOnTop,
      }"
    >
      <new-header ref="newHeader" :logo-src="logoSrc">
        <template v-slot:center>
          <transition name="slidein">
            <div
              class="calendar-input__fixed-nav"
              v-if="windowWidth > 1400 && (!showFixedSearch || showFixedCalendarInput)"
            >
              <button
                @click.prevent="onLearnInPerson"
                :class="{ active: !whereValue.virtual }"
              >
                Learn In-Person
              </button>
              <button
                @click.prevent="onLearnVirtually"
                :class="{ active: (whereValue.virtual && !preRecorded) }"
              >
                Learn Virtually
              </button>
              <button
                @click.prevent="onPreRecorded"
                :class="{ active: preRecorded && whereValue.virtual }"
              >
                Pre-Recorded Lessons
              </button>
            </div>
          </transition>
          <transition name="slidein">
            <button
              :class="{
                'calendar-input__fixed-search': true,
                'calendar-input__fixed-search--only-icon': showFixedSearchByDefault,
              }"
              v-if="windowWidth > 1400 && (showFixedSearch && !showFixedCalendarInput)"
              @click.prevent="showFixedCalendarInput = true"
            >
              <span>Search</span>
              <img src="/images/calendarinput-search-small.svg" alt="Search" />
            </button>
          </transition>
        </template>
        <template v-slot:right>
          <slot name="header-right"></slot>
        </template>
      </new-header>

      <transition name="show">
        <div
          class="calendar-input__backdrop"
          v-if="showFixedCalendarInput || selectedTab"
          v-scroll-lock="showFixedCalendarInput || selectedTab"
          @click.prevent="
            showFixedCalendarInput = false;
            toggleTab();
          "
        ></div>
      </transition>

      <div class="calendar-input__fixed-nav" v-if="windowWidth <= 1400 && !showFixedSearchByDefault">
        <button
          @click.prevent="onLearnInPerson"
          :class="{ active: !whereValue.virtual }"
        >
          Learn In-Person
        </button>
        <button
          @click.prevent="onLearnVirtually"
          :class="{ active: (whereValue.virtual && !preRecorded) }"
        >
          Learn Virtually
        </button>
        <button
          @click.prevent="onPreRecorded"
          :class="{ active: preRecorded && whereValue.virtual }"
        >
          Pre-Recorded Lessons
        </button>
      </div>

      <div class="calendar-input__selector" v-if="!showFixedSearchByDefault || showFixedCalendarInput">
        <button
          @keydown.space.prevent="appendSpaceFix('who', whoValue.text)"
          @keydown.enter.prevent="goSearch()"
          @click.prevent="toggleTab('who', $event)"
          :class="{
            'calendar-input-option': true,
            'calendar-input-option--active': selectedTab == 'who',
          }"
        >
          <span class="calendar-input-option__title">Who</span>
          <input
            type="text"
            class="calendar-input-option__input"
            placeholder="Do you want to learn from?"
            v-model="whoValue.text"
            @click.stop="toggleTab('who', $event)"
          />
          <span
            class="calendar-input-option__autocomplete"
            v-if="
              whoValue.text &&
              whoValue.autocompleteText &&
              !someAutocompleIsLoading &&
              selectedTab == 'who'
            "
            >{{ whoValue.autocompleteText }}</span
          >
        </button>
        <button
          @keydown.space.prevent="appendSpaceFix('what', whatValue.text)"
          @keydown.enter.prevent="goSearch()"
          @click.prevent="toggleTab('what', $event)"
          :class="{
            'calendar-input-option': true,
            'calendar-input-option--active': selectedTab == 'what',
          }"
        >
          <span class="calendar-input-option__title">What</span>
          <input
            type="text"
            class="calendar-input-option__input"
            placeholder="Do you want to learn?"
            v-model="whatValue.text"
            @click.stop="selectedTab = 'what'"
          />
          <span
            class="calendar-input-option__autocomplete"
            v-if="
              whatValue.text &&
              whatValue.autocompleteText &&
              !someAutocompleIsLoading &&
              selectedTab == 'what'
            "
            >{{ whatValue.autocompleteText }}</span
          >
        </button>
        <button
          v-if="!preRecorded"
          @click.prevent="toggleTab('when', $event)"
          :class="{
            'calendar-input-option': true,
            'calendar-input-option--active': selectedTab == 'when',
            'calendar-input-option--noclick': preRecorded,
          }"
          :disabled="preRecorded"
        >
          <span class="calendar-input-option__title">When</span>
          <span
            :class="{
              'calendar-input-option__input': true,
              'calendar-input-option__input--when': true,
              'calendar-input-option__input--small': whenValue.flexibleMode,
            }"
            >{{ whenText }}</span
          >
          <button
            v-if="
              !whenValue.flexibleMode && whenValue.from && selectedTab == 'when'
            "
            @click.stop.prevent="clearWhenValue()"
            class="calendar-input-option__clear"
          >
            Clear
          </button>
        </button>
        <button
          v-if="!preRecorded"
          @keydown.space.prevent="appendSpaceFix('where', whereValue.text)"
          @keydown.enter.prevent="goSearch()"
          @click.prevent="toggleTab('where', $event)"
          :class="{
            'calendar-input-option': true,
            'calendar-input-option--active': selectedTab == 'where',
            'calendar-input-option--disabled': whereValue.virtual,
          }"
        >
          <span class="calendar-input-option__title">Where</span>
          <input
            type="text"
            class="calendar-input-option__input"
            placeholder="Do you want to learn?"
            v-model="whereValue.text"
            @click.stop="selectedTab = 'where'"
            :disabled="whereValue.virtual"
          />
          <span
            class="calendar-input-option__autocomplete"
            v-if="
              whereValue.text &&
              whereValue.autocompleteText &&
              !someAutocompleIsLoading &&
              selectedTab == 'where'
            "
            >{{ whereValue.autocompleteText }}</span
          >
          <button
            @click.stop.prevent="goSearch()"
            class="calendar-input__search"
          >
            <img src="/images/calendarinput-search.svg" alt="Search" />
            Search
          </button>
        </button>
        <button
          v-if="preRecorded"
          @keydown.space.prevent="appendSpaceFix('topic', topicValue.text)"
          @keydown.enter.prevent="goSearch()"
          @click.prevent="toggleTab('topic', $event)"
          :class="{
            'calendar-input-option': true,
            'calendar-input-option--active': selectedTab == 'topic',
          }"
        >
          <span class="calendar-input-option__title">Topic</span>
          <input
            type="text"
            class="calendar-input-option__input"
            placeholder="Of the lesson."
            v-model="topicValue.text"
            @click.stop="selectedTab = 'topic'"
          />
          <button
            @click.stop.prevent="goSearch()"
            class="calendar-input__search"
          >
            <img src="/images/calendarinput-search.svg" alt="Search" />
            Search
          </button>
        </button>
      </div>

      <div class="calendar-input__tabs">
        <transition name="slidein">
          <div
            class="calendar-input__tab calendar-input__tab--who"
            v-if="(selectedTab == 'who' && whoValue.text) || (allInstructors.length > 0 && selectedTab == 'who')"
          >
            <calendar-input-who-tab
              @autocomplete="setWhoAutocompleteText($event)"
              @choose-instructor="chooseInstructor($event)"
              ref="calendarInputWhoTab"
              :value="whoValue"
              @loading-changed="setAutocompleteLoading($event)"
              :getAll="getAll"
            />
          </div>
        </transition>
        <transition name="slidein">
          <div
            class="calendar-input__tab calendar-input__tab--what"
            v-if="(selectedTab == 'what' && whatValue.text) || (selectedTab == 'what' && allGenres.length)"
          >
            <calendar-input-what-tab
              :preloaded-genres="allGenres"
              @autocomplete="setWhatAutocompleteText($event)"
              @choose-genre="chooseGenre($event)"
              ref="calendarInputWhatTab"
              :value="whatValue"
              @loading-changed="setAutocompleteLoading($event)"
            />
          </div>
        </transition>
        <transition name="slidein">
          <div
            class="calendar-input__tab calendar-input__tab--when"
            v-if="selectedTab == 'when'"
          >
            <calendar-input-when-tab
              @value-changed="whenTabValueChanged($event)"
              ref="calendarInputWhenTab"
              :value="whenValue"
            />
          </div>
        </transition>
        <transition name="slidein">
          <div
            class="calendar-input__tab calendar-input__tab--where"
            v-if="selectedTab == 'where'"
          >
            <calendar-input-where-tab
              @virtual-changed="setVirtual($event)"
              @autocomplete="setWhereAutocompleteText($event)"
              @choose-location="chooseLocation($event)"
              ref="calendarInputWhereTab"
              :value="whereValue"
              @loading-changed="setAutocompleteLoading($event)"
              @go-search="goSearch()"
            />
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import CalendarInputWhoTab from "../CalendarInputWhoTab/CalendarInputWhoTab.vue";
import CalendarInputWhatTab from "../CalendarInputWhatTab/CalendarInputWhatTab.vue";
import CalendarInputWhenTab from "../CalendarInputWhenTab/CalendarInputWhenTab.vue";
import CalendarInputWhereTab from "../CalendarInputWhereTab/CalendarInputWhereTab.vue";
import NewHeader from "../../header/NewHeader/NewHeader.vue";
import dateHelper from "../../../helpers/dateHelper";
import urlHelper from "../../../helpers/urlHelper";
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: "CalendarInput",
  components: {
    CalendarInputWhoTab,
    CalendarInputWhatTab,
    CalendarInputWhenTab,
    CalendarInputWhereTab,
    NewHeader,
  },
  props: {
    showFixedSearchByDefault: {
      type: Boolean,
      default: false,
    },
    alwaysOnTop: {
      type: Boolean,
      default: false,
    },
    genres: {
      type: Array,
      default: () => {
        return [];
      },
    },
    defaultLessonType: {
      type: String,
      default: "",
    },
    logoSrc: {
      type: String,
      default: null,
    },
    ajaxEvent: {
      type: String,
      default: null,
    },
  },
  async created() {
    if (!this.genres.length) {
      await this.getAllGenres()
    } else {
      this.SET_ALL_GENRES(this.genres)
    }
    if (this.defaultLessonType) {
      urlHelper.updateQueryParams({
        lesson_type: this.defaultLessonType,
      }, false);
    }
    this.parseQueryParams();
  },
  mounted() {
    if (window.location.search === '?learnVirtually=true') {
      this.whereValue.virtual = true
    }
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
    window.addEventListener("scroll", () => {
      this.checkScroll();
    });
    this.checkScroll();
  },
  data() {
    return {
      selectedTab: null,
      someAutocompleIsLoading: false,
      whoValue: {
        text: null,
        handle: null,
        autocompleteText: null,
      },
      whatValue: {
        id: null,
        text: null,
        autocompleteText: null,
      },
      whenValue: {
        from: null,
        to: null,
        flexibleMode: false,
        choosedFlexibleDays: null,
        choosedFlexibleMonths: null,
      },
      whereValue: {
        virtual: false,
        text: null,
        autocompleteText: null,
      },
      topicValue: {
        text: null,
      },
      whenText: "Do you want to learn?",
      showFixedSearch: this.showFixedSearchByDefault,
      showFixedCalendarInput: false,
      windowWidth: window.innerWidth,
      preRecorded: false,
      getAll: false
    };
  },
  methods: {
    ...mapActions(['getAllInstructors', 'getAllGenres']),
    ...mapMutations(['SET_ALL_GENRES']),
    onPreRecorded() {
      const params = this.buildQueryParams();
      this.preRecorded = true
      this.whereValue.virtual = true
      this.selectedTab = null
      urlHelper.updateQueryParams(params, true, true, this.preRecorded ? '/globalshop' : '/lessons')
    },
    onLearnVirtually() {
      if (this.preRecorded) {
        urlHelper.updateQueryParams({learnVirtually: true}, true, true, '/')
      }
      this.whereValue.virtual = true
      this.preRecorded = false
    },
    onLearnInPerson() {
      if (this.preRecorded) {
        urlHelper.updateQueryParams({}, true, true, '/')
      }
      this.whereValue.virtual = false
    },
    parseQueryParams() {
      const params = urlHelper.parseQueryParams();

      if (params.instructor_name) {
        this.whoValue.text = params.instructor_name;
      }
      if (params.instructor_handle) {
        this.whoValue.handle = params.instructor_handle;
      }
      if (params.genre) {
        if (this.allGenres.length) {
          this.allGenres.map((genre) => {
            if (genre.id == params.genre) {
              this.whatValue.text = genre.title;
              this.whatValue.id = genre.id;
            }
          });
        }
      }
      if (params.date_from) {
        this.whenValue.from = dateHelper.filterToDate(params.date_from);
      }
      if (params.date_to) {
        this.whenValue.to = dateHelper.filterToDate(params.date_to);
      }
      if (params.flexible_days) {
        this.whenValue.flexibleMode = true;
        this.whenValue.choosedFlexibleDays = params.flexible_days;
      }
      if (params.flexible_months) {
        this.whenValue.choosedFlexibleMonths = dateHelper.filterToFlexibleMonths(params.flexible_months);
      }
      if (params.location) {
        this.whereValue.text = params.location;
      }
      if (params.lesson_type == "pre_recorded") {
        this.preRecorded = true;
        this.whereValue.virtual = true;
        if (params.topic) {
          this.topicValue.text = params.topic;
        }
      } else {
        this.whereValue.virtual = (params.lesson_type == "virtual");
      }
    },
    buildQueryParams() {
      const params = {};
      if (this.whoValue.text) {
        params.instructor_name = this.whoValue.text;
      }
      if (this.whoValue.handle) {
        params.instagram_handle = this.whoValue.handle;
      }
      if (this.whatValue.id) {
        params.genre = this.whatValue.id;
      }
      if (this.preRecorded) {
        params.lesson_type = "pre_recorded";
        if (this.topicValue.text) {
          params.topic = this.topicValue.text;
        }
      } else {
        if (this.whenValue.flexibleMode) {
          let flexMonths = "";
          this.whenValue.choosedFlexibleMonths.map((month, index) => {
            flexMonths += month.number + 1;
            if (index < this.whenValue.choosedFlexibleMonths.length - 1) {
              flexMonths += ",";
            }
          });
          params.flexible_months = flexMonths;
          params.flexible_days = this.whenValue.choosedFlexibleDays;
        } else if (this.whenValue.from && this.whenValue.to) {
          params.date_from = dateHelper.dateToFilter(this.whenValue.from);
          params.date_to = dateHelper.dateToFilter(this.whenValue.to);
        }
        if (this.whereValue.virtual) {
          params.lesson_type = "virtual";
        } else if (this.whereValue.text) {
          params.location = this.whereValue.text;
          params.lesson_type = "in_person";
        } else {
          params.lesson_type = "in_person";
        }
      }
      return params;
    },
    goSearch() {
      this.selectedTab = "";

      const params = this.buildQueryParams();
      const paramsLen = Object.keys(params).length;
      if (paramsLen == 1 && params.lesson_type && params.lesson_type != "pre_recorded") {
        window.location.href = "/instructors";
      } else if (
        paramsLen == 2 && 
        params.lesson_type && 
        params.lesson_type != "pre_recorded" &&
        params.instructor_name
      ) {
        urlHelper.updateQueryParams({
          instructor_name: params.instructor_name,
        }, true, true, "/instructors");
      } else if (
        paramsLen == 3 && 
        params.lesson_type && 
        params.lesson_type != "pre_recorded" && 
        params.instructor_name && 
        params.instagram_handle
      ) {
        window.location.href = "/profile/" + this.whoValue.id;
      } else {
        if (
          (this.ajaxEvent == "globalShopLoadLessons" && this.preRecorded) ||
          (this.ajaxEvent == "lessonsLoadLessons" && !this.preRecorded)
        ) {
          urlHelper.updateQueryParams(params, true);
          this.$root.$emit(this.ajaxEvent, params);
        } else {
          urlHelper.updateQueryParams(params, true, true, this.preRecorded ? "/globalshop" : "/lessons");
        }
      }
    },
    setAutocompleteLoading(bool) {
      this.someAutocompleIsLoading = bool;
    },
    setVirtual(virtual) {
      this.whereValue.virtual = virtual;
    },
    chooseLocation(text) {
      this.whereValue.text = text;
      this.whereValue.autocompleteText = "";
      this.selectedTab = "";
    },
    chooseInstructor({ fullName, instagramHandle, instructorId }) {
      this.whoValue.id = instructorId;
      this.whoValue.text = fullName;
      this.whoValue.handle = instagramHandle;
      this.whoValue.autocompleteText = "";
      this.selectedTab = "";
    },
    chooseGenre({ genreName, genreId }) {
      this.whatValue.id = genreId;
      this.whatValue.text = genreName;
      this.whatValue.autocompleteText = "";
      this.selectedTab = "";
    },
    setWhoAutocompleteText(text) {
      this.whoValue.autocompleteText = this.whoValue.text + text;
    },
    setWhatAutocompleteText(text) {
      this.whatValue.autocompleteText = this.whatValue.text + text;
    },
    setWhereAutocompleteText(text) {
      this.whereValue.autocompleteText = this.whereValue.text + text;
    },
    whenTabValueChanged(newValue) {
      this.whenValue = newValue;
    },
    clearWhenValue() {
      this.$refs.calendarInputWhenTab.clearValue();
    },
    async toggleTab(tab = null, event = null) {
      this.getAll = false
      if (this.selectedTab == tab) {
        this.selectedTab = null;
      } else {
        this.selectedTab = tab;
      }
      if (this.selectedTab === 'who' && event.target.value === '') {
        await this.getAllInstructors()
        this.getAll = true
      }
      if (event) {
        const input = event.target.getElementsByTagName("input")[0];
        if (input) {
          input.focus();
        }
      }
    },
    checkScroll() {
      this.$refs.newHeader.setNoShadow(
        (!this.showFixedSearchByDefault || this.showFixedCalendarInput) && (!(window.pageYOffset > 0) || this.showFixedCalendarInput)
      );
      this.showFixedSearch = this.showFixedSearchByDefault || (!this.alwaysOnTop && window.pageYOffset > 80);
    },
    appendSpaceFix(tabName, str) {
      if (tabName == "who") {
        if (this.whoValue.text) this.whoValue.text += " ";
      } else if (tabName == "what") {
        if (this.whatValue.text) this.whatValue.text += " ";
      } else if (tabName == "where") {
        if (this.whereValue.text) this.whereValue.text += " ";
      } else if (tabName == "topic") {
        if (this.topicValue.text) this.topicValue.text += " ";
      }
    },
  },
  watch: {
    showFixedCalendarInput() {
      this.checkScroll();
    },
    whenValue: {
      handler(newValue) {
        if (newValue.flexibleMode) {
          this.whenText = dateHelper.formatFlexibleDate(
            newValue.choosedFlexibleDays,
            newValue.choosedFlexibleMonths
          );
        } else {
          if (!newValue.from) {
            this.whenText = "Do you want to learn?";
          } else {
            this.whenText = dateHelper.formatDateWithoutTime(
              newValue.from,
              newValue.to
            );
          }
        }
      },
      deep: true,
    },
    whereValue: {
      handler(newValue) {
        if (newValue.virtual) {
          this.whereValue.text = "";
        } else {
          this.preRecorded = false;
        }

        if (this.$refs.calendarInputWhereTab) {
          this.$refs.calendarInputWhereTab.setWhereValue(newValue);
        }
      },
      deep: true,
    },
    whoValue: {
      handler(newValue) {
        if (this.$refs.calendarInputWhoTab) {
          this.$refs.calendarInputWhoTab.setWhoValue(newValue);
        }
      },
      deep: true,
    },
    whatValue: {
      handler(newValue) {
        if (newValue.text == "") {
          newValue.id = null;
        };
        if (this.$refs.calendarInputWhatTab) {
          this.$refs.calendarInputWhatTab.setWhatValue(newValue);
        }
      },
      deep: true,
    },
  },
  computed: {
    ...mapState(['allInstructors', 'allGenres'])
  }
};
</script>

<style lang="scss" scoped>
@import "./CalendarInput.scss";
</style>