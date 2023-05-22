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
          placeholder: 'Search my library',
          length: 'wide',
        },
      ],
      selectedGenre: 'All',
      lessons: [],
      genres: [],
      params: {}
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
    async filterChanged(filterValue) {
      this.isLoading = true
      if (filterValue.type === 'search') {
        await this.searchData(filterValue.value.target.value)
      }
      if (filterValue.type === 'select') {
        await this.filterByGenre(filterValue.value)
      }
      this.isLoading = false
    },
    async searchData(searchVal) {
      this.params.search = searchVal
      this.lessons = await lessonService.myLibraryLessons(this.params)
    },
    async filterByGenre(filterVal) {
      const selectedGenre = this.genres.find(el => filterVal.toLowerCase() === el.title.toLowerCase())
      this.params.genre = filterVal === 'All' ? '' : selectedGenre.id
      this.lessons = await lessonService.myLibraryLessons(this.params)
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
