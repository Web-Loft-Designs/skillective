<template>
  <div id="profile-bookings-container">
    <div class="table-top d-flex align-items-center">
      <h2 class="page-title">Bookings</h2>
      <div class="d-flex align-items-center">
        <button
          @click.prevent="toggleShowOnly('current')"
          :class="{ active: showOnly == 'current' }"
        >
          Current
        </button>
        <button
          @click.prevent="toggleShowOnly('past')"
          :class="{ active: showOnly == 'past' }"
        >
          Past
        </button>
        <button
          @click.prevent="toggleShowOnly('cancelled')"
          :class="{ active: showOnly == 'cancelled' }"
        >
          Cancelled
        </button>
<!--        <button-->
<!--          @click.prevent="toggleShowOnly('lesson_requests')"-->
<!--          :class="{ active: showOnly == 'lesson_requests' }"-->
<!--        >-->
<!--          Booking Requests-->
<!--        </button>-->
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <span
          v-if="selectedBookings.length > 0"
          @click="requestCancelManyBookings"
          class="btn btn-danger mr-2"
          >Cancel {{ selectedBookings.length }} bookings</span
        >
        <input
          type="text"
          v-model="searchString"
          placeholder="Search bookings"
        />
      </div>
      <div class="row" v-if="pagination.total_pages > 1">
        <div
          class="
            col-12
            d-flex
            justify-content-end
            align-items-center
            custom-pag
          "
        >
          <span
            >{{ firstListItemNumber }}-{{ lastListItemNumber }} of
            {{ pagination.total }}</span
          >
          <paginate
            :page-count="pagination.total_pages"
            :force-page="pagination.current_page"
            :prev-text="'Prev'"
            :next-text="'Next'"
            :click-handler="paginatorClickCallback"
            :container-class="'clients-pagination'"
          >
          </paginate>
        </div>
      </div>
    </div>
    <div v-if="errorText" class="has-error">{{ errorText }}</div>
    <div v-if="successText" class="has-success">{{ successText }}</div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th
              scope="col"
              v-if="showOnly != 'lesson_requests' && listLoaded == true"
            >
              <span class="checkbox-wrapper">
                <label for="checkAll">
                  <input
                    @change="selectAll"
                    id="checkAll"
                    :indeterminate.prop="indeterminate"
                    v-model="allSelected"
                    type="checkbox"
                  />
                  <span
                    class="checkmark"
                    :class="{ indeterminate: indeterminate === true }"
                  ></span>
                </label>
              </span>
            </th>
            <th class="w-55" scope="col"></th>
            <th class="w-140" scope="col">Name</th>
            <th scope="col">Skill</th>
            <th class="w-140" scope="col">Lesson Type</th>
            <th scope="col">Lesson Date</th>
            <th scope="col" class="time-width">Time</th>
            <th scope="col">Price</th>
            <th class="w-200" scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(booking, index) in listItems" :key="index">
            <td v-if="showOnly != 'lesson_requests' && listLoaded == true">
              <span class="checkbox-wrapper"
                ><label
                  ><input
                    @change="select"
                    type="checkbox"
                    v-model="selectedBookings"
                    :value="booking.id" /><span class="checkmark"></span></label
              ></span>
            </td>

            <td v-if="showOnly != 'lesson_requests' && listLoaded == true">
              <img :src="booking.lesson.instructor.profile.image" />
            </td>
            <td v-else><img :src="booking.instructor.profile.image" /></td>

            <td
              v-if="showOnly != 'lesson_requests' && listLoaded == true"
              class="width-fix"
            >
              <div class="width-fix-content">
                <a
                  :href="'/profile/' + booking.lesson.instructor.id"
                  class="link-to-profile full-name-link"
                  >{{ booking.lesson.instructor.full_name }}</a
                >
              </div>
            </td>
            <td v-else class="width-fix">
              <div class="width-fix-content">
                <a
                  :href="'/profile/' + booking.instructor.id"
                  class="link-to-profile full-name-link"
                  >{{ booking.instructor.full_name }}</a
                >
              </div>
            </td>

            <td v-if="showOnly != 'lesson_requests' && listLoaded == true">
              {{ booking.lesson.genre.title }}
            </td>
            <td v-else>{{ booking.genre.title }}</td>

            <td
              class="width-fix"
              v-if="showOnly != 'lesson_requests' && listLoaded == true"
            >
              <div
                class="width-fix-content"
                v-if="
                  booking.lesson.lesson_type == 'in_person' ||
                  booking.lesson.lesson_type == 'in_person_client'
                "
                v-html="booking.lesson.location"
              ></div>
              <div
                class="width-fix-content"
                v-if="booking.lesson.lesson_type == 'virtual'"
              >
                Virtual Lesson
              </div>
            </td>
            <td class="width-fix" v-else>
              <div
                class="width-fix-content"
                v-if="booking.lesson_type == 'in_person'"
                v-html="booking.location"
              ></div>
              <div
                class="width-fix-content"
                v-if="booking.lesson_type == 'virtual'"
              >
                Virtual Lesson
              </div>
            </td>

            <td v-if="showOnly != 'lesson_requests' && listLoaded == true">
              {{ booking.lesson.start | moment('MM/D/YYYY') }}
            </td>
            <td v-else>{{ booking.start | moment('MM/D/YYYY') }}</td>

            <td
              v-if="showOnly != 'lesson_requests' && listLoaded == true"
              class="w-no"
            >
              {{ booking.lesson.start | moment('h:mm a') }} -
              {{ booking.lesson.end | moment('h:mm a') }}
              {{ booking.lesson.timezone_id }}
            </td>
            <td v-else>
              {{ booking.start | moment('h:mm a') }} -
              {{ booking.end | moment('h:mm a') }} {{ booking.timezone_id }}
            </td>

            <td v-if="showOnly != 'lesson_requests' && listLoaded == true">
              ${{ booking.spot_price }}
            </td>
            <td v-else>${{ booking.lesson_price }}</td>

            <td v-if="showOnly != 'lesson_requests' && listLoaded == true">
              <span class="d-flex w-100 align-items-center">
                <lesson-participant-room-controls
                  v-if="
                    showOnly == 'current' &&
                    listLoaded == true &&
                    !booking.lesson.room_completed
                  "
                  :booking="booking"
                ></lesson-participant-room-controls>
                <span
                  class="btn btn-notify"
                  @click="notifyInstructor(booking.lesson.instructor)"
                  v-if="
                    booking.lesson.instructor.profile.notification_methods
                      .length > 0
                  "
                  >Contact</span
                >
                <span
                  class="btn btn-danger"
                  @click="requestCancelBooking(booking)"
                  v-if="
                    booking.status != 'cancelled' &&
                    !isPastLesson(booking.lesson.start)
                  "
                  >Request Cancel</span
                >
              </span>
            </td>
            <td v-else>
              <a
                href="#"
                @click.prevent="viewLessonRequest(booking)"
                class="btn-approve"
                >View</a
              >
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="listItems.length === 0">
        <p class="text-center">No {{ showOnly.replace('_', ' ') }} bookings</p>
      </div>
    </div>
    <div class="row justify-content-center" v-if="pagination.total_pages > 1">
      <div class="col-6 select-show">
        <label>Per Page</label>
        <select v-model="pagination.per_page" @change="onChangePerPage">
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div
        class="col-6 d-flex justify-content-end align-items-center custom-pag"
      >
        <span
          >{{ firstListItemNumber }}-{{ lastListItemNumber }} of
          {{ pagination.total }}</span
        >
        <paginate
          :page-count="pagination.total_pages"
          :force-page="pagination.current_page"
          :prev-text="'Prev'"
          :next-text="'Next'"
          :click-handler="paginatorClickCallback"
          :container-class="'clients-pagination'"
        >
        </paginate>
      </div>
    </div>
  </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import Paginate from 'vuejs-paginate'

export default {
  mixins: [siteAPI, skillectiveHelper],
  props: {
    bookings: null,
    bookingsMeta: {},
  },
  components: {
    Paginate,
  },
  data() {
    return {
      selectedBookings: [],
      searchString: '',
      showOnly: 'current',
      allSelected: false,
      indeterminate: false,
      pagination: {
        total: 0,
        total_pages: 0,
        current_page: 0,
        per_page: 0,
      },
      listLoaded: true,
    }
  },
  methods: {
    selectAll: function () {
      this.selectedBookings = []

      if (this.allSelected) {
        this.indeterminate = false
        for (let user in this.listItems) {
          this.selectedBookings.push(this.listItems[user].id.toString())
        }
      }
    },
    select: function () {
      if (this.listItems.length == this.selectedBookings.length) {
        this.allSelected = 1
        this.indeterminate = false
      } else if (this.selectedBookings.length === 0) {
        this.allSelected = 0
        this.indeterminate = false
      } else {
        this.allSelected = 0
        this.indeterminate = true
      }
    },
    getBookings() {
      this.listLoaded = false
      let queryParams = {}
      if (this.pagination.current_page != undefined)
        queryParams.page = this.pagination.current_page
      else queryParams.page = 1

      if (this.searchString != '') queryParams.s = this.searchString

      queryParams.type = this.showOnly

      this.updateUrlQueryParams(queryParams)

      let getUrl =
        this.showOnly == 'lesson_requests'
          ? '/api/lesson-requests'
          : '/api/student/bookings'

      this.apiGet(getUrl, {
        params: queryParams,
      })
    },
    requestCancelBooking(booking) {
      this.apiDelete('/api/student/booking/' + booking.id)
    },
    requestCancelManyBookings() {
      this.apiPost('/api/student/bookings/cancel', {
        bookings: this.selectedBookings,
      })
    },
    notifyInstructor(client) {
      this.$root.$emit('initNotificationsForm', [client])
    },
    componentHandleDeleteResponse(responseData) {
      if (this.pagination.count == 1 && this.pagination.current_page > 1) {
        // last booking on page cancelled > go to prev page
        this.pagination.current_page -= 1
      }
      this.getBookings()
    },
    componentHandlePostResponse(responseData) {
      if (
        this.pagination.count == parseInt(responseData.data) &&
        this.pagination.current_page > 1
      ) {
        // last booking on page cancelled > go to prev page
        this.pagination.current_page -= 1
      }
      this.selectedBookings = []
      this.getBookings()
    },
    paginatorClickCallback(pageNum) {
      this.pagination.current_page = pageNum
      this.getBookings()
    },
    onChangePerPage() {
      this.pagination.current_page = 1
      Cookies.set('studentBookingsPerPage', this.pagination.per_page)
      this.getBookings()
    },
    searchBookings() {
      this.pagination.current_page = 1
      this.getBookings()
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data
      if (
        responseData.data.meta != undefined &&
        responseData.data.meta.pagination != undefined
      ) {
        this.pagination.count = responseData.data.meta.pagination.count
        this.pagination.total = responseData.data.meta.pagination.total
        this.pagination.total_pages =
          responseData.data.meta.pagination.total_pages
        this.pagination.current_page =
          responseData.data.meta.pagination.current_page
        this.pagination.per_page = responseData.data.meta.pagination.per_page
      }
      this.allSelected = false
      this.indeterminate = false
      this.listLoaded = true
    },
    toggleShowOnly(type) {
      this.showOnly = type
      this.pagination.current_page = 1
      this.getBookings()
    },
    isPastLesson(lessonStart) {
      return moment(lessonStart).isBefore()
    },
    viewLessonRequest(lessonRequest) {
      this.$root.$emit('lessonRequestUpdateInit', lessonRequest)
    },
  },
  created: function () {
    this.listItems = this.bookings
    if (
      this.bookingsMeta != undefined &&
      this.bookingsMeta.pagination != undefined
    ) {
      this.pagination.count = this.bookingsMeta.pagination.count
      this.pagination.total = this.bookingsMeta.pagination.total
      this.pagination.total_pages = this.bookingsMeta.pagination.total_pages
      this.pagination.current_page = this.bookingsMeta.pagination.current_page
      this.pagination.per_page = this.bookingsMeta.pagination.per_page
    }
    if (this.getUrlParameter('s')) this.searchString = this.getUrlParameter('s')
    if (this.getUrlParameter('type'))
      this.showOnly = this.getUrlParameter('type')
    this.debouncedGetBookings = _.debounce(this.searchBookings, 500)
  },
  watch: {
    searchString: function (newSearchString, oldSearchString) {
      this.debouncedGetBookings()
    },
  },
  computed: {
    firstListItemNumber: function () {
      return (
        this.pagination.current_page * this.pagination.per_page -
        this.pagination.per_page +
        1
      )
    },
    lastListItemNumber: function () {
      if (this.pagination.count == this.pagination.per_page) {
        return this.firstListItemNumber + this.pagination.per_page - 1
      } else {
        return this.firstListItemNumber + this.pagination.count - 1
      }
    },
  },
  mounted() {
    this.$root.$on('lessonRequestUpdated', (id) => {
      for (var i = 0; i < this.listItems.length; i++) {
        if (this.listItems[i].id == id) {
          this.listItems.splice(i, 1)
        }
      }
    })
  },
}
</script>
<style lang='scss' scoped>

.full-name-link {
  font-size: 14px !important;
  font-weight: 600 !important;
}
</style>
