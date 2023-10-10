<template>
  <div class='bookings-table'>
    <div class='bookings-table-header d-bookings-table-header'>
      <h2>
        <span class='filter-table'>
          <a
            :class="{ active: showOnly == 'all' }"
            href='#'
            @click.prevent="toggleShowOnly('all')"
          >
            All
          </a>
          <!--          <a-->
          <!--            href="#"-->
          <!--            @click.prevent="toggleShowOnly('pending')"-->
          <!--            :class="{ active: showOnly == 'pending' }"-->
          <!--            >Pending ({{ pendingCount }})</a-->
          <!--          >-->
          <!--          <a-->
          <!--            href="#"-->
          <!--            @click.prevent="toggleShowOnly('approved')"-->
          <!--            :class="{ active: showOnly == 'approved' }"-->
          <!--            >Approved-->
          <!--          </a>-->
          <a
            v-if='showPastLesson'
            :class="{ active: showOnly == 'past' }"
            href='#'
            @click.prevent="toggleShowOnly('past')"
          >
            Past
          </a>
          <a
            :class="{ active: showOnly == 'lesson_requests' }"
            href='#'
            @click.prevent="toggleShowOnly('lesson_requests')"
          >
            Client Booking Requests ({{ pendingRequestCount }})
          </a>
        </span>
        <div class='sort-select'>
          <select class='form-control' @change='sortBy'>
            <option value='date'>Start date</option>
            <option value='price_desc'>Price (high to low)</option>
            <option value='price_asc'>Price (low to high)</option>
            <option value='participants'>Participants</option>
          </select>
        </div>
      </h2>
    </div>
    <div v-if='errorText' class='has-error' v-html='errorText'></div>
    <div v-if='successText' class='has-success' v-html='successText'></div>
    <div class='table-responsive'>
      <table class='table instructor-table'>
        <thead>
        <tr>
          <th class='b-date'>Date</th>
          <th class='b-bookings'>Bookings</th>
        </tr>
        </thead>
        <tbody v-if='listLoaded'>
        <tr v-for='(item, index) in listItems' v-bind:key='index'>
          <td class='b-list-date'>
              <span>
                <img alt='' src='/images/b-date-icon.png'/> {{ item.date }}
              </span>
          </td>
          <td v-if="showOnly != 'lesson_requests'" class='b-content'>
            <dashboard-booking-item
              v-for='(lesson, index) in item.lessons'
              v-bind:key='index'
              :approveBooking='approveBooking'
              :cancelBooking='cancelBooking'
              :getBookings='getBookings'
              :showOnly='showOnly'
              v-bind:lesson='lesson'
              v-bind:listLoaded='listLoaded'
            >
            </dashboard-booking-item>
          </td>
          <td v-else class='b-content'>
            <dashboard-booking-item-request
              v-for='(lesson, index) in item.lessons'
              v-bind:key='index'
              :getBookings='getBookings'
              :viewLessonRequest='viewLessonRequest'
              v-bind:lesson='lesson'
              v-bind:listLoaded='listLoaded'
            >
            </dashboard-booking-item-request>
          </td>
        </tr>
        </tbody>
      </table>
      <div v-if='listLoaded' class='mobile-bokings-list'>
        <div
          v-for='(item, index) in listItems'
          v-bind:key='index'
          class='mobile-bokings-list--item'
        >
          <div class='b-list-date'>
            <span>
              <img alt='' src='/images/b-date-icon.png'/> {{ item.date }}
            </span>
          </div>
          <div v-if="showOnly != 'lesson_requests'" class='b-content'>
            <dashboard-booking-item
              v-for='(lesson, index) in item.lessons'
              v-bind:key='index'
              :approveBooking='approveBooking'
              :cancelBooking='cancelBooking'
              :getBookings='getBookings'
              :showOnly='showOnly'
              v-bind:lesson='lesson'
              v-bind:listLoaded='listLoaded'
            >
            </dashboard-booking-item>
          </div>
          <div v-else class='b-content'>
            <dashboard-booking-item-request
              v-for='(lesson, index) in item.lessons'
              v-bind:key='index'
              :getBookings='getBookings'
              :viewLessonRequest='viewLessonRequest'
              v-bind:lesson='lesson'
              v-bind:listLoaded='listLoaded'
            >
            </dashboard-booking-item-request>
          </div>
        </div>
      </div>
      <div v-if='listItems.length === 0'>
        <p class='text-center'>No bookings</p>
      </div>
    </div>
    <a
      v-if='this.pagination.total > 5'
      :href="'/instructor/bookings?type=' + showOnly"
      class='btn btn-block btn-secondary'
    >View all</a
    >
  </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import manageVideoLesson from '../mixins/manageVideoLesson.js'
import $ from 'jquery'
import countriesAndTimezones from 'countries-and-timezones'
import moment from 'moment-timezone'

export default {
  mixins: [siteAPI, skillectiveHelper, manageVideoLesson],
  props: {
    bookings: null,
    bookingsMeta: {},
    showPastLesson: false
  },
  data() {
    return {
      showOnly: 'all',
      sort: 'date',
      pagination: {
        total: 0
      },
      listLoaded: false,
      listData: [
        {
          lessons: []
        }
      ],
      pendingCount: 0,
      pendingRequestCount: 0
    }
  },
  watch: {
    showOnly: function () {
      this.getCountMeta()
    }
  },
  methods: {
    getBookings() {
      this.listItems = []
      this.listLoaded = false
      let queryParams = {}
      queryParams.type = this.showOnly
      queryParams.limit = 1000
      queryParams.sort = this.sort
      let getUrl =
        this.showOnly == 'lesson_requests'
          ? '/api/lesson-requests'
          : '/api/instructor/lessons/dashboard'
      this.apiGet(getUrl, {
        params: queryParams
      })
    },
    getCountMeta() {
      this.apiGet('/api/instructor/lessons/meta')
    },
    transformBookings(bookings) {
      const userTzOffset = new Date().getTimezoneOffset() * 60 * 1000
      const stdTimezoneOffset = Math.max(
        (new Date(0, 1).getTimezoneOffset()), (new Date(6, 1).getTimezoneOffset())
      )
      const isDstObserved = (new Date().getTimezoneOffset()) < stdTimezoneOffset
      bookings = bookings.map(item => {
        let lessonTimeZoneObj = countriesAndTimezones.getTimezone(item.timezone_id_name)
        let _tzOffset = isDstObserved ? lessonTimeZoneObj.dstOffset * 60 * 1000 : lessonTimeZoneObj.utcOffset * 60 * 1000
        let dummyStart = new Date(item.start.replace(/\s/, 'T')).getTime() - userTzOffset - _tzOffset
        item.start_prepared = moment(dummyStart).format('YYYY-MM-DD HH:mm:ss')
        let dummyEnd = new Date(item.end.replace(/\s/, 'T')).getTime() - userTzOffset - _tzOffset
        item.end_prepared = moment(dummyEnd).format('YYYY-MM-DD HH:mm:ss')
        return item
      })

      const groups = bookings.reduce((groups, lesson) => {
        const date = moment(lesson.start_prepared).format('MM/DD/YY')
        if (!groups[date]) {
          groups[date] = []
        }
        groups[date].push(lesson)
        return groups
      }, {})

      const groupArrays = Object.keys(groups).map((date) => {
        return {
          date,
          lessons: groups[date]
        }
      })

      return groupArrays
    },
    sortBy(e) {
      this.sort = e.target.value
      this.getBookings()
    },
    cancelBooking(booking) {
      this.apiDelete('/api/instructor/booking/' + booking.id)
    },
    approveBooking(booking) {
      this.apiPut('/api/instructor/booking/approve/' + booking.id)
    },
    componentHandleDeleteResponse(responseData) {
      $('.update-value').text(parseInt($('.update-value').text()) - 1)
      this.getBookings()
    },
    componentHandlePostResponse(responseData) {
      this.getBookings()
    },
    componentHandlePutResponse(responseData) {
      $('.update-value').text(parseInt($('.update-value').text()) - 1)
      this.getBookings()
    },
    componentHandleGetResponse(responseData, action) {
      if (action == '/api/instructor/lessons/meta') {
        this.pendingCount = responseData.data.pending
        this.pendingRequestCount = responseData.data.requests
      } else {
        const data = this.transformBookings(responseData.data.data)
        this.listData = []
        setTimeout(() => {
          this.listItems = data
        }, 1)
        if (
          responseData.data.meta != undefined &&
          responseData.data.meta.pagination != undefined
        ) {
          this.pagination.total = responseData.data.meta.pagination.total
        }
        this.listLoaded = true
      }
    },
    isPastLesson(lessonStart) {
      return moment(lessonStart).isBefore()
    },
    toggleShowOnly(type) {
      this.showOnly = type
      this.getBookings()
    },
    viewLessonRequest(lessonRequest) {
      this.$root.$emit('lessonRequestUpdateInit', lessonRequest)
    }
  },
  created: function () {
    this.listItems = this.transformBookings(this.bookings.data)
    if (
      this.bookingsMeta != undefined &&
      this.bookingsMeta.pagination != undefined
    ) {
      this.pagination.total = this.bookingsMeta.pagination.total
    }
  },
  mounted() {
    this.getBookings()
    this.getCountMeta()
    this.$root.$on('lessonRequestUpdated', (id) => {
      for (var i = 0; i < this.listItems.length; i++) {
        if (this.listItems[i].id == id) {
          this.listItems.splice(i, 1)
        }
      }
    })
  }
}
</script>
