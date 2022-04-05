<template>
  <div class="video-lessons-list">
    <div class="video-lessons-list__list" v-if="lessons.length">
      <div
        v-for="(lesson, lessonIndex) in lessons"
        :key="lessonIndex"
        class="video-lesson"
      >
        <img
          class="video-lesson__image"
          :src="lesson.preview"
          :alt="lesson.preview"
        />
        <div v-if="showInstructorInfo" class="video-lesson__instructor-info">
          <img
            :src="lesson.instructor.profile.image"
            :alt="lesson.instructor.full_name"
          />
          <h6>
            <a
              :href="
                'https://www.instagram.com/' +
                lesson.instructor.instagram_handle
              "
            >
              @{{ lesson.instructor.instagram_handle }}
            </a>
          </h6>
          <span>
            <a :href="'/profile/' + lesson.instructor_id">
              {{ lesson.instructor.full_name }}
            </a></span
          >
        </div>
        <div
          :class="{
            'video-lesson__content': true,
            'video-lesson__content--wide-row-titles': purchased,
          }"
        >
          <div class="video-lesson__title-container">
            <span class="video-lesson__title">{{ lesson.title }}</span>
            <options-menu
              v-if="optionsMenuItems.length > 0"
              :options="optionsMenuItems"
              @menu-item-clicked="optionsMenuItemClicked($event, lesson)"
            />
          </div>
          <div class="video-lesson__genres">
            <span class="video-lesson__genre">{{ lesson.genre.title }}</span>
          </div>
          <div v-if="purchased" class="video-lesson__row">
            <span class="video-lesson__row-title">Purchase date:</span>
            <span class="video-lesson__row-value">{{
              lesson.purchaseDate
            }}</span>
          </div>
          <div class="video-lesson__row">
            <span class="video-lesson__row-title">Created:</span>
            <span class="video-lesson__row-value">{{ lesson.start }}</span>
          </div>
          <div v-if="lesson.duration" class="video-lesson__row">
            <span class="video-lesson__row-title">Duration:</span>
            <span class="video-lesson__row-value">{{ lesson.duration }}</span>
          </div>

          <div v-if="isInstructorView" class="video-lesson__row">
            <span class="video-lesson__row-title">Total Revenue:</span>
            <span class="video-lesson__row-value">
              ${{ lesson.totalRevenue }}
            </span>
          </div>

          <div v-if="isInstructorView" class="video-lesson__row">
            <span class="video-lesson__row-title">Total Purchares:</span>
            <span class="video-lesson__row-value">{{
              lesson.totalPurchares
            }}</span>
          </div>

          <div v-if="!purchased" class="video-lesson__row">
            <span class="video-lesson__row-title video-lesson__row-title--price"
              >Price:</span
            >
            <span class="video-lesson__row-value video-lesson__row-value--price"
              >${{ lesson.price }}</span
            >
          </div>
          <div class="video-lesson__button">
            <a
              v-if="cardButton == 'watch-student'"
              class="video-lesson__more-info"
              :href="'/student/library/video/' + lesson.id"
              >Watch the Video</a
            >
            <a
              v-if="cardButton == 'watch-instructor'"
              class="video-lesson__more-info"
              :href="'/instructor/my-shop/video/' + lesson.id"
              >Watch the Video</a
            >
            <button
              v-if="cardButton == 'more-info'"
              class="video-lesson__more-info"
              @click.prevent="showMoreInfoPopup(lesson)"
            >
              More info
            </button>
            <button
              v-if="
                cardButton == 'watch-instructor' ||
                cardButton == 'watch-student'
              "
              class="video-lesson__more-info-link"
              @click.prevent="showMoreInfoPopup(lesson)"
            >
              Show all info
            </button>
          </div>
        </div>
      </div>
    </div>

    <span v-else class="video-lessons-list__empty"
      >No lessons for your request</span
    >

    <collapse-transition>
      <div
        v-if="!collapsed && collapsedLessons.length > 0"
        class="video-lessons-list__collapsed"
      >
        <div class="video-lessons-list__list">
          <div
            v-for="(lesson, lessonIndex) in collapsedLessons"
            :key="lessonIndex"
            class="video-lesson"
          >
            <img
              class="video-lesson__image"
              :src="lesson.image"
              :alt="lesson.image"
            />
            <div
              v-if="showInstructorInfo"
              class="video-lesson__instructor-info"
            >
              <img
                :src="lesson.instructorImage"
                :alt="lesson.instructorImage"
              />
              <h6>
                <a
                  :href="
                    'https://www.instagram.com/' +
                    lesson.instructorHandle
                  "
                >
                  @{{ lesson.instagram_handle }}
                </a>
              </h6>
              <span>
                <a :href="'/profile/' + lesson.instructor_id">
                  {{ lesson.instructor }}
                </a></span
              >
            </div>
            <div class="video-lesson__content">
              <div class="video-lesson__title-container">
                <span class="video-lesson__title">{{ lesson.title }}</span>
                <options-menu
                  v-if="optionsMenuItems.length > 0"
                  :options="optionsMenuItems"
                  @menu-item-clicked="optionsMenuItemClicked($event, lesson)"
                />
              </div>
              <div class="video-lesson__genres">
                <span class="video-lesson__genre">{{
                  lesson.genre.title
                }}</span>
              </div>
              <div class="video-lesson__row">
                <span class="video-lesson__row-title">Created:</span>
                <span class="video-lesson__row-value">{{ lesson.start }}</span>
              </div>
              <div v-if="lesson.duration" class="video-lesson__row">
                <span class="video-lesson__row-title">Duration:</span>
                <span class="video-lesson__row-value">{{
                  lesson.duration
                }}</span>
              </div>
              <div class="video-lesson__row">
                <span
                  class="video-lesson__row-title video-lesson__row-title--price"
                  >Price:</span
                >
                <span
                  class="video-lesson__row-value video-lesson__row-value--price"
                  >${{ lesson.price }}</span
                >
              </div>
              <a
                v-if="
                  cardButton == 'watch-instructor' ||
                  cardButton == 'watch-student'
                "
                class="video-lesson__more-info"
                :href="'/student/library/video/' + lesson.id"
                >Watch the Video</a
              >
              <button
                v-if="cardButton == 'more-info'"
                class="video-lesson__more-info"
                @click.prevent="showMoreInfoPopup(lesson)"
              >
                More info
              </button>
              <button
                v-if="
                  cardButton == 'watch-instructor' ||
                  cardButton == 'watch-student'
                "
                class="video-lesson__more-info-link"
                @click.prevent="showMoreInfoPopup(lesson)"
              >
                Show all info
              </button>
            </div>
          </div>
        </div>
      </div>
    </collapse-transition>
    <button
      v-if="collapsed && collapsedLessons.length > 0"
      class="video-lessons-list__show-more"
      @click.prevent="collapsed = false"
    >
      Show more
    </button>
    <button
      v-if="!collapsed && collapsedLessons.length > 0"
      class="video-lessons-list__show-more"
      @click.prevent="collapsed = true"
    >
      Show less
    </button>
    <video-lesson-info-popup
      :logged-in-as-student="loggedInAsStudent"
      ref="videoLessonInfoPopup"
      :popup-button="popupButton"
    />
  </div>
</template>

<script>
import CollapseTransition from "@ivanv/vue-collapse-transition/src/CollapseTransition.vue";
import VideoLessonInfoPopup from "../VideoLessonInfoPopup/VideoLessonInfoPopup.vue";
import OptionsMenu from "../OptionsMenu/OptionsMenu.vue";

export default {
  name: "VideoLessonsList",
  components: {
    CollapseTransition,
    VideoLessonInfoPopup,
    OptionsMenu,
  },
  methods: {
    showMoreInfoPopup(lesson) {
      this.$refs.videoLessonInfoPopup.showPopup(lesson);
    },
    optionsMenuItemClicked(menuItemIndex, lesson) {
      this.$emit("options-menu-item-clicked", {
        menuItemIndex,
        lesson,
      });
    },
  },
  props: {
    lessons: {
      type: Array,
      default: () => {
        return [];
      },
    },
    collapsedLessons: {
      type: Array,
      default: () => {
        return [];
      },
    },
    showInstructorInfo: {
      type: Boolean,
      default: false,
    },
    purchased: {
      type: Boolean,
      default: false,
    },
    isInstructorView: {
      type: Boolean,
      deafult: false,
    },
    optionsMenuItems: {
      type: Array,
      default: () => {
        return [];
      },
    },
    cardButton: {
      type: String,
      default: null,
      // possible options: [ null, "watch-student", "watch-instructor", "more-info" ]
    },
    popupButton: {
      type: String,
      default: null,
      // possible options: [ null, "add-to-cart", "watch-student", "watch-instructor" ]
    },
    loggedInAsStudent: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      collapsed: true,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "./VideoLessonsList.scss";
</style>