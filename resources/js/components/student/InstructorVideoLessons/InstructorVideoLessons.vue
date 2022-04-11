<template>
  <div class='instructor-video-lessons'>
    <h2 class='instructor-video-lessons__heading'><a :href="'instructor/my-shop'">{{instructorName}}'s Pre-Recorded Lessons</a></h2>
    <anim-loader v-if='isLoading' />
    <video-lessons-list
      v-else
      :lessons='lessons'
      :collapsed-lessons='collapsedLessons'
      card-button='more-info'
      popup-button='add-to-cart'
      :logged-in-as-student='loggedInAsStudent'
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
    AnimLoader,
  },
  props: {
    instructorId: null,
    loggedInAsInstructor: {
      type: Boolean,
      default: false,
    },
    loggedInAsStudent: {
      type: Boolean,
      default: false,
    },
  },
  created() {
    const arr = window.location.href.split('/')
    this.myProfile = arr[arr.length - 1] === 'profile'
  },
  async mounted() {
    const lessons = await lessonService.instructorLessonsById(
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