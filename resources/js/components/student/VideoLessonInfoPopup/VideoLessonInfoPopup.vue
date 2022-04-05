<template>
  <div class="video-lesson-info-popup">
    <transition name="show">
      <div
        v-if="opened"
        class="video-lesson-info-popup__backdrop"
        @click.prevent="opened = false"
      ></div>
    </transition>

    <transition name="scalein">
      <div v-if="opened" class="video-lesson-info-popup__popup-container">
        <div
          class="video-lesson-info-popup__popup"
          v-scroll-lock="opened"
          @click.prevent.stop
        >
          <close-button
            class="video-lesson-info-popup__close"
            @click="opened = false"
          />
          <div class="video-lesson-info-popup__content">
            <h3 class="video-lesson-info-popup__heading">{{ lesson.title }}</h3>
            <div class="video-lesson-info-popup__images">
              <img
                class="video-lesson-info-popup__image"
                :src="lesson.preview"
                :alt="lesson.image"
              />
            </div>
            <span class="video-lesson-info-popup__type">
              <span>Lesson Type: </span>
              <span v-if="lesson.video && lesson.documents && lesson.documents.length">Video Lesson and Lesson Documentation</span>
              <span v-else-if="lesson.video">Video Lesson</span>
              <span v-else>Lesson documentation</span>
            </span>
            <div class="video-lesson-info-popup__genres">
              <span class="video-lesson-info-popup__genre">{{
                lesson.genre.title
              }}</span>
            </div>
            <div class="video-lesson-info-popup__row">
              <span class="video-lesson-info-popup__row-title">Created:</span>
              <span class="video-lesson-info-popup__row-value">{{
                lesson.start
              }}</span>
            </div>
            <div v-if="lesson.duration" class="video-lesson-info-popup__row">
              <span class="video-lesson-info-popup__row-title">Duration:</span>
              <span class="video-lesson-info-popup__row-value">{{
                lesson.duration
              }}</span>
            </div>
            <div class="video-lesson-info-popup__row">
              <span class="video-lesson-info-popup__row-title"
                >Instructor:</span
              >
              <span class="video-lesson-info-popup__row-value">
                <a :href="'/profile/' + lesson.instructor.id" @click.stop>{{
                  lesson.instructor.full_name
                }}</a>
              </span>
            </div>
            <p class="video-lesson-info-popup__note">
              Note:<br />{{ lesson.description }}
            </p>
            <div class="video-lesson-info-popup__row">
              <span
                class="
                  video-lesson-info-popup__row-title
                  video-lesson-info-popup__row-title--price
                "
                >Price:</span
              >
              <span
                class="
                  video-lesson-info-popup__row-value
                  video-lesson-info-popup__row-value--price
                "
                >${{ lesson.price }}</span
              >
            </div>
            <a
              v-if="popupButton == 'watch-student'"
              class="video-lesson-info-popup__button"
              :href="'/student/library/video/' + lesson.id"
              @click.stop
              >View the Lesson</a
            >
            <a
              v-if="popupButton == 'watch-instructor'"
              class="video-lesson-info-popup__button"
              :href="'/instructor/my-shop/video/' + lesson.id"
              @click.stop
              >View the Lesson</a
            >
            <loader-button
              v-if="popupButton == 'add-to-cart'"
              :isLoading="isLoading"
              text="Add to cart"
              @click="addToCart(lesson)"
            />
            <span v-if="errorMessage" class="video-lesson-info-popup__error">{{ errorMessage }}</span>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import CloseButton from "../CloseButton/CloseButton.vue";
import LoaderButton from "../../cart/LoaderButton/LoaderButton.vue";
import { mapActions } from "vuex";

export default {
  name: "VideoLessonInfoPopup",
  components: {
    CloseButton,
    LoaderButton,
  },
  props: {
    popupButton: {
      type: String,
      default: false,
      // possible options: [ null, "add-to-cart", "watch-student", "watch-instructor" ]
    },
    loggedInAsStudent: {
        type: Boolean,
        default: false,
    },
  },
  data() {
    return {
      opened: false,
      isLoading: false,
      errorMessage: null,
      lesson: {
        title: "",
        preview: "",
        genre: "",
        start: "",
        duration: "",
        instructor: {
          id: "",
        },
        description: "",
        price: "",
        id: "",
      },
    };
  },
  methods: {
    ...mapActions(["addItemToCartAtStart"]),
    showPopup(lesson) {
      this.lesson = lesson;
      this.errorMessage = null;
      this.opened = true;
    },
    async addToCart(lesson) {

      if (this.loggedInAsStudent) {
        this.isLoading = true;
        const result = await this.addItemToCartAtStart({
          lessonId: lesson.id,
          isPreRecorded: true,
        });
        this.isLoading = false;
        if (result.isError) {
          this.errorMessage = result.message;
        } else {
          this.opened = false;
          this.$root.$emit("showMiniCart");
        }
      } else {
        window.location.href = "/login";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./VideoLessonInfoPopup.scss";
</style>