<template>
  <div class='my-library'>
    <div class='my-library__container'>
      <filter-header
        :button='button'
        :filters='filters'
        heading='My Library'
        @filter-changed='filterChanged($event)'
      />

      <div class='my-library__content'>
        <anim-loader v-if='isLoading'/>
        <video-lessons-list
          v-else
          :can-book='canBook'
          :lessons='lessons'
          card-button='watch-student'
          popup-button='watch-student'
          purchased
          show-instructor-info
        />
      </div>

      <div class='my-library__footer'>
        <pagination
          :current-page='currentPage'
          :page-count='pageCount'
        />
      </div>
    </div>
  </div>
</template>

<script>
import FilterHeader from '../../instructor/FilterHeader/FilterHeader.vue'
import VideoLessonsList from '../VideoLessonsList/VideoLessonsList.vue'
import Pagination from '../Pagination/Pagination.vue'
import AnimLoader from '../../cart/AnimLoader/AnimLoader.vue'
import lessonService from '../../../services/lessonService'
import {mapActions} from 'vuex'

export default {
  name: 'MyLibrary',
  components: {
    FilterHeader,
    VideoLessonsList,
    Pagination,
    AnimLoader,
  },
  props: {
    canBook: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isLoading: false,
      currentPage: 1,
      pageCount: 1,
      button: {
        type: 'link',
        text: 'Shop for Lessons and Tutorials',
        href: '/globalshop',
      },
      filters: [
        {
          type: 'select',
          title: 'Skill',
          options: [],
          placeholder: 'Select skill',
        },
        {
          type: 'search',
          title: '',
          placeholder: 'Enter the question',
          length: 'wide',
        },
      ],
      selectedGenre: 'All',
      lessons: [],
      genres: [],
    }
  },
  async created() {
    this.isLoading = true
    this.lessons = await lessonService.myLibraryLessons()
    await this.setGenres()
    this.isLoading = false
  },
  methods: {
    ...mapActions(['getStudentGenres']),
    async filterChanged(e) {
      this.isLoading = true
      const selectedGenre = this.genres.find(el => e.value.toLowerCase() === el.title.toLowerCase())
      e.value === 'All'
        ? this.lessons = await lessonService.myLibraryLessons()
        : this.lessons = await lessonService.myLibraryLessons({genre: selectedGenre.id})
      this.isLoading = false
    },
    async setGenres() {
      this.genres = await this.getStudentGenres()
      this.filters[0].options = ['All', ...this.genres.map(el => el.title)]
    },
  },
}
</script>

<style lang='scss' scoped>
@import './MyLibrary.scss';
</style>
