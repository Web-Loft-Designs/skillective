<template>
  <div class='featured-instructors'>
    <h2 class='featured-instructors__heading'>Trending Pre-Recorded Lessons</h2>
        <video-lessons-list
          :is-slider='true'
          :can-book="canBook"
          :lessons="preloadedLessons"
          show-instructor-info
          card-button='more-info'
          popup-button='add-to-cart'
        />
  </div>
</template>

<script>
import Slick from 'vue-slick'
import VideoLessonsList from '../../student/VideoLessonsList/VideoLessonsList'

export default {
  name: 'GlobalShopHome',
  components: { VideoLessonsList, Slick },
  props: {
    preloadedLessons:{
      type: Array,
      default: () => {
        return []
      },
    },
    canBook: {
      type: Boolean,
      default: false,
    },
  }
}
</script>

<style lang='scss' scoped>
@import "./GlobalShopHome.scss";
</style>
