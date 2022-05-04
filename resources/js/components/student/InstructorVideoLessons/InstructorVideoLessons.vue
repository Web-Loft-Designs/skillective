<template>
  <div class='instructor-video-lessons'>
    <h2 class='instructor-video-lessons__heading'>
      <a :href="'/instructor/my-shop'" v-if="userRole==='Instructor'">{{instructorName}}'s Pre-Recorded Lessons</a>
      <span v-else>{{instructorName}}'s Pre-Recorded Lessons</span>
    </h2>
    <anim-loader v-if='isLoading' />
    <video-lessons-list
      v-else
      :lessons='lessons'
      :collapsed-lessons='collapsedLessons'
      card-button='more-info'
      popup-button='add-to-cart'
      :can-book='canBook'
    />
  </div>
</template>

<script>
import CollapseTransition from '@ivanv/vue-collapse-transition/src/CollapseTransition.vue'
import VideoLessonsList from '../VideoLessonsList/VideoLessonsList.vue'
import AnimLoader from '../../cart/AnimLoader/AnimLoader.vue'
import lessonService from '../../../services/lessonService'

export default {
  name: 'InstructorVideoLessons',
  components: {
    CollapseTransition,
    VideoLessonsList,
    AnimLoader
  },
  props: {
    instructorId: {
      type: String,
      default: null
    },
    canBook: {
      type: Boolean,
      default: false
    },
    userRole: {
      type: String,
      default: '',
    },
  },
  created() {
    const arr = window.location.href.split('/')
    this.myProfile = arr[arr.length - 1] === 'profile'
  },
  async mounted() {
    let lessons = await lessonService.instructorLessonsById(
      this.instructorId,
    )
    this.instructorName = lessons[0].instructor.first_name
    if (lessons.length > 3) {
      this.lessons = lessons.slice(0, 3)
      this.collapsedLessons = lessons.slice(3, lessons.length)
    } else {
      this.lessons = lessons
    }
    this.isLoading = false
  },
  data() {
    return {
      isLoading: true,
      lessons: [],
      collapsedLessons: [],
      myProfile: false,
      instructorName: '',
    }
  },
}
</script>

<style lang='scss' scoped>
@import "./InstructorVideoLessons.scss";
</style>
