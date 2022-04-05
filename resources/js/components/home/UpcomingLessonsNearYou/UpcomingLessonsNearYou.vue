<template>
  <div v-if="lessons.length > 0" class="upcoming-lessons-near-you">
    <h2 class="upcoming-lessons-near-you__heading">
      Upcoming Lessons Near You
    </h2>
    <slick :options="slickOptions" ref="upcomingLessonsNearYouSlick">
      <div
        class="nearby-lesson__outer"
        v-for="(lesson, index) in lessons"
        :key="index"
      >
        <a href="/" class="nearby-lesson">
          <div class="nearby-lesson__body">
            <div class="nearby-lesson__image-container">
              <img
                class="nearby-lesson__image"
                :src="lesson.instructor.profile.image"
              />
              <div class="nearby-lesson__upfront">View more lessons</div>
            </div>
            <h5 class="nearby-lesson__insta">
              @{{ lesson.instructor.profile.instagram_handle }}
            </h5>
            <span class="nearby-lesson__name">{{
              lesson.instructor.full_name
            }}</span>
          </div>
          <div class="nearby-lesson__footer">
            <div class="nearby-lesson__genre">{{ lesson.genre.title }}</div>
            <span class="nearby-lesson__price">${{ lesson.spot_price }}</span>
            <span class="nearby-lesson__subprice">lesson</span>
            <div class="nearby-lesson__date">
              {{ formatDate(lesson.start, lesson.end) }}
            </div>
            <div class="nearby-lesson__location">
              {{ lesson.city }}, {{ lesson.state }}
            </div>
            <button class="nearby-lesson__book-lesson">Book lesson</button>
          </div>
        </a>
      </div>
    </slick>
    <a href="/" class="upcoming-lessons-near-you__view-all">View All Lessons</a>
  </div>
</template>

<script>
import Slick from "vue-slick";
import lessonService from "../../../services/lessonService";
import dateHelper from "../../../helpers/dateHelper";

export default {
  name: "UpcomingLessonsNearYou",
  components: {
    Slick,
  },
  data() {
    return {
      slickOptions: {
        infinite: false,
        dots: false,
        slidesToShow: 4,
        speed: 500,
        slidesToScroll: 4,
        responsive: [
          {
            breakpoint: 1300,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
            },
          },
          {
            breakpoint: 1100,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2,
            },
          },
          {
            breakpoint: 650,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            },
          },
        ],
      },
      lessons: [],
    };
  },
  async mounted() {
    this.lessons = await lessonService.upcomingNearbyLessons();
  },
  watch: {
    lessons() {
      if (this.$refs.upcomingLessonsNearYouSlick) {
        this.$refs.upcomingLessonsNearYouSlick.destroy();
        this.$nextTick(() => {
          this.$refs.upcomingLessonsNearYouSlick.create();
        });
      }
    },
  },
  methods: {
    formatDate(startStr, endStr) {
      return dateHelper.formatDate(startStr, endStr);
    },
    formatMonth(month) {
      return dateHelper.threeLetMonthNameByNumber(month);
    },
    formatTime(hours, minutes) {
      return dateHelper.formatTime(hours, minutes);
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./UpcomingLessonsNearYou.scss";
</style>