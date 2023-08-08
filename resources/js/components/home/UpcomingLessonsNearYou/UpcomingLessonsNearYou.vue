<template>
  <div v-if='isLoading || lessons.length > 0' class='upcoming-lessons-near-you'>
    <h2 class='upcoming-lessons-near-you__heading'>
      Upcoming Lessons Near You
    </h2>

    <anim-loader v-if='isLoading'/>

    <slick v-else ref='upcomingLessonsNearYouSlick' :options='slickOptions'>
      <div
        v-for='(lesson, index) in lessons'
        :key='index'
        class='nearby-lesson__outer'
      >
        <div class='nearby-lesson'>
          <div class='nearby-lesson__body'>
            <div class='nearby-lesson__image-container'>
              <a :href="'/profile/' + lesson.instructor_id">
                <img
                  :src='lesson.instructor.profile.image'
                  alt='lesson-image'
                  class='nearby-lesson__image'
                />
                <div class='nearby-lesson__upfront'>
                  View more lessons
                </div>
              </a>
            </div>
            <h5 class='nearby-lesson__insta'>
              <a :href="'https://www.instagram.com/' + lesson.instructor.profile.instagram_handle ">
                @{{lesson.instructor.profile.instagram_handle }}
              </a>
            </h5>
            <a :href="'/profile/' + lesson.instructor_id" class='nearby-lesson__name'>
              {{ lesson.instructor.full_name }}
            </a>
          </div>
          <div class='nearby-lesson__footer'>
            <div class='nearby-lesson__genre'>
              {{ lesson.genre.title }}
            </div>
            <span class='nearby-lesson__price'>${{ lesson.spot_price }}</span>
            <span class='nearby-lesson__subprice'>lesson</span>
            <div class='nearby-lesson__date'>{{ formatDate(lesson.start, lesson.end) }}</div>
            <div class='nearby-lesson__location'>{{ lesson.city }}, {{ lesson.state }}</div>
            <loader-button
              :disabled='!canBook'
              :isLoading='cartIsLoading'
              class='nearby-lesson__book-lesson'
              text='Book lesson'
              @click='addToCart(lesson)'
            />
          </div>
        </div>
      </div>
    </slick>

    <div class='upcoming-lessons-near-you__footer'>
      <a class='upcoming-lessons-near-you__view-all' href='/lessons'>View All Lessons</a>
    </div>

    <info-popup ref='infoPopup'/>

  </div>
</template>

<script>
import Slick from 'vue-slick'
import InfoPopup from '../../instructor/InfoPopup/InfoPopup.vue'
import LoaderButton from '../../cart/LoaderButton/LoaderButton.vue'
import AnimLoader from '../../cart/AnimLoader/AnimLoader.vue'
import lessonService from '../../../services/lessonService'
import dateHelper from '../../../helpers/dateHelper'
import {mapActions} from 'vuex'
import countriesAndTimezones from "countries-and-timezones";
import moment from "moment-timezone";

export default {
  name: 'UpcomingLessonsNearYou',
  components: {
    Slick,
    LoaderButton,
    AnimLoader,
    InfoPopup
  },
  props: {
    canBook: {
      type: Boolean,
      default: false
    }
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
              slidesToScroll: 3
            }
          },
          {
            breakpoint: 1100,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 650,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      },
      lessons: [],
      isLoading: true,
      cartIsLoading: false
    }
  },
  async mounted() {
    await lessonService.upcomingNearbyLessons()
        .then((response) => {
          this.lessons = response
          this.lessons = this.lessons.map(function (item) {
            item.title = item.genre.title
            let userTzOffset = new Date().getTimezoneOffset() * 60 * 1000
            let lessonTimeZoneObj = countriesAndTimezones.getTimezone(item.timezone_id_name)
            let jan = new Date(0, 1)
            let jul = new Date(6, 1)
            let stdTimezoneOffset = Math.max(
                jan.getTimezoneOffset(),
                jul.getTimezoneOffset()
            )
            let today = new Date()
            let isDstObserved = false
            if (today.getTimezoneOffset() < stdTimezoneOffset) isDstObserved = true
            let _tzOffset = 1
            isDstObserved
                ? _tzOffset = lessonTimeZoneObj.dstOffset * 60 * 1000
                : _tzOffset = lessonTimeZoneObj.utcOffset * 60 * 1000
            let dummyStart =
                new Date(item.start.replace(/\s/, 'T')).getTime() -
                userTzOffset -
                _tzOffset
            item.start = moment(dummyStart).format('YYYY-MM-DD HH:mm:ss')
            let dummyEnd =
                new Date(item.end.replace(/\s/, 'T')).getTime() -
                userTzOffset -
                _tzOffset
            item.end = moment(dummyEnd).format('YYYY-MM-DD HH:mm:ss')
            item.date = moment(item.start).format('YYYY-MM-DD')
            return item
          })
          this.lessons = this.lessons.filter(function (item) {
            return moment().diff(item.end) <= 0
          })
        })
    this.isLoading = false
  },
  watch: {
    lessons() {
      if (this.$refs.upcomingLessonsNearYouSlick) {
        this.$refs.upcomingLessonsNearYouSlick.destroy()
        this.$nextTick(() => {
          this.$refs.upcomingLessonsNearYouSlick.create()
        })
      }
    }
  },
  methods: {
    formatDate(startStr, endStr) {
      return dateHelper.formatDate(startStr, endStr)
    },
    formatMonth(month) {
      return dateHelper.threeLetMonthNameByNumber(month)
    },
    formatTime(hours, minutes) {
      return dateHelper.formatTime(hours, minutes)
    },
    ...mapActions(['addItemToCartAtStart']),
    async addToCart(lesson) {
      this.cartIsLoading = true
      const result = await this.addItemToCartAtStart({
        lessonId: lesson.id,
        specialRequest: ''
      })
      if (result.success) {
        this.$root.$emit('showMiniCart')
      } else {
        this.$refs.infoPopup.showInfo(result.message)
      }
      this.cartIsLoading = false
    }
  }
}
</script>

<style lang='scss' scoped>
@import './UpcomingLessonsNearYou.scss';
</style>
