<template>
  <div class="my-library-player">
    <div class="my-library-player__container">
      <a
        v-if="isInstructor"
        class="my-library-player__back"
        href="/instructor/my-shop"
        >Back to My Shop</a
      >
      <a v-else class="my-library-player__back" href="/student/library"
        >Back to My Library</a
      >

      <h1 class="my-library-player__heading" v-if="!isLoading">
        {{ lesson.title }}
      </h1>

      <anim-loader v-if="isLoading" />

      <div v-else class="my-library-player__row">
        <div class="my-library-player__col">
          <div class="my-library-player__video-holder" v-if="lesson.video">
            <video-player
              ref="videoPlayer"
              class="my-library-player__video-player"
              :options="playerOptions"
            />
          </div>
          <h2
            class="my-library-player__heading"
            v-if="lesson.video && lesson.documents.length"
          >
            Documents:
          </h2>
          <ul v-if="lesson.documents.length" class="my-library-player__documents">
            <li
              v-for="(document, index) in lesson.documents"
              :key="index"
              class="my-library-player__document"
            >
              <img
                class="my-library-player__doc-image"
                :src="lesson.documentsPath + document.name"
                alt="Document preview"
                v-if="!fileExtFromUrl(document.name)"
              />
              <span v-else class="my-library-player__doc-image">{{ fileExtFromUrl(document.name) }}</span>
              <div class="my-library-player__doc-info">
                <h6 class="my-library-player__doc-title">{{ document.name }}</h6>
                <a target="_blank" :href="lesson.documentsPath + document.name" class="my-library-player__doc-view">View</a>
                <!-- <span class="my-library-player__doc-subtitle">
                  {{ document.subtitle }}
                </span> -->
              </div>
            </li>
          </ul>
        </div>
        <div class="my-library-player__col">
          <div class="my-library-player__content">
            <div class="my-library-player__instructor">
              <img
                :src="lesson.instructor.profile.image"
                :alt="lesson.instructor.profile.image"
              />
              <h6>@{{ lesson.instructor.instagram_handle }}</h6>
              <span>{{ lesson.instructor.full_name }}</span>
            </div>
            <div class="my-library-player__genres">
              <span class="my-library-player__genre">{{
                lesson.genre.title
              }}</span>
            </div>
            <div
              v-if="lesson.purchased_at"
              class="my-library-player__content-row"
            >
              <span class="my-library-player__content-row-title"
                >Purchase date:</span
              >
              <span class="my-library-player__content-row-value">{{
                lesson.purchased_at
              }}</span>
            </div>
            <div
              v-if="lesson.created_at"
              class="my-library-player__content-row"
            >
              <span class="my-library-player__content-row-title">Created:</span>
              <span class="my-library-player__content-row-value">{{
                lesson.created_at
              }}</span>
            </div>
            <div v-if="lesson.duration" class="my-library-player__content-row">
              <span class="my-library-player__content-row-title"
                >Duration:</span
              >
              <span class="my-library-player__content-row-value">{{
                lesson.duration
              }}</span>
            </div>
            <p class="my-library-player__note">
              Note:<br />{{ lesson.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import videojs7 from "vue-videojs7";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import lessonService from "../../../services/lessonService";
import urlHelper from "../../../helpers/urlHelper";

export default {
  name: "MyLibraryPlayer",
  components: {
    VideoPlayer: videojs7.videoPlayer,
    AnimLoader,
  },
  props: {
    isInstructor: {
      type: Boolean,
      default: false,
    },
  },
  async mounted() {
    const currentUrl = window.location.pathname;
    const lessonId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1);
    this.lesson = this.isInstructor
      ? await lessonService.myShopLessonById(lessonId)
      : await lessonService.myLibraryLessonById(lessonId);
    this.isLoading = false;
    setTimeout(() => {
      this.playVideo(this.lesson.video);
    }, 0);
  },
  computed: {
    player() {
      return this.$refs.videoPlayer ? this.$refs.videoPlayer.player : null;
    },
  },
  methods: {
    playVideo(source) {
      const video = {
        withCredentials: false,
        type: "video/mp4",
        src: source,
      };
      this.player.reset();
      this.player.src(video);
    },
    fileExtFromUrl(url) {
      if (url) {
        const ext = urlHelper.fileExtFromUrl(url);
        if (ext == "PNG" || ext == "JPG" || ext == "JPEG") {
          return null;
        }
        return ext;
      }
      return null;
    },
  },
  data() {
    return {
      isLoading: true,
      lesson: {
        documentsPath: "",
        video: null,
        genre: {
          title: null,
        },
        instructor: {
          instagram_handle: null,
          profile: {
            image: null,
          },
          full_name: null,
        },
        duration: null,
        title: null,
        description: null,
        purchased_at: null,
        created_at: null,
        documents: [],
      },
      playerOptions: {
        autoplay: false,
        controls: true,
        controlBar: {
          timeDivider: false,
          durationDisplay: false,
        },
      },
    };
  },
};
</script>

<style lang="scss" scoped>
@import "./MyLibraryPlayer.scss";
</style>