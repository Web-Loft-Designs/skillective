<template>
  <div class='bookings-table'>
    <div class='bookings-table-header'>
      <h2>
        Bookings
        <span class='pl-4 filter-table'>
          <a
            href='#'
            @click.prevent="toggleShowOnly('current')"
            :class="{'active':(showOnly=='current')}"
          >Current</a>
          <a
            href='#'
            @click.prevent="toggleShowOnly('past')"
            :class="{'active':(showOnly=='past')}"
          >Past</a>
          <a
            href='#'
            @click.prevent="toggleShowOnly('cancelled')"
            :class="{'active':(showOnly=='cancelled')}"
          >Cancelled</a>
        </span>
      </h2>
    </div>

    <div v-if='errorText' class='has-error'>{{ errorText }}</div>
    <div v-if='successText' class='has-success'>{{ successText }}</div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="w-55">#</th>
                    <th class="w-55"></th>
                    <th class="w-100px">Instagram</th>
                    <th class="w-140">Name</th>
                    <th class="w-140">Location</th>
                    <th class="w-100px">Date</th>
                    <th class="time-width">Time</th>
                    <th>Price</th>
                    <th class="start-in-fix">Start in:</th>
                </tr>
                </thead>
                <tbody>
                <template v-if='listItems'>
                    <tr v-for="(booking, index) in listItems">
                        <td>{{ booking.id }}</td>

                        <td v-if="listLoaded==true">
                            <img :src="booking.lesson.instructor?.profile.image" />
                        </td>
                        <td v-else>
                            <img :src="booking.instructor?.profile.image" />
                        </td>

                        <td v-if="listLoaded==true" class="width-fix">
                            <div class="width-fix-content">
                                <a
                                  v-if="booking.lesson.instructor && booking.lesson.instructor.profile && booking.lesson.instructor.profile.instagram_handle!=null"
                                  :href="'https://www.instagram.com/' + booking.lesson.instructor.profile.instagram_handle"
                                  target="_blank"
                                >@{{ booking.lesson.instructor.profile.instagram_handle }}</a>
                            </div>
                        </td>
                        <td v-else class="width-fix">
                            <div class="width-fix-content">
                                <a
                                  v-if="booking.instructor && booking.instructor.profile && booking.instructor.profile.instagram_handle!=null"
                                  :href="'https://www.instagram.com/' + booking.instructor.profile.instagram_handle"
                                  target="_blank"
                                >@{{ booking.instructor.profile.instagram_handle }}</a>
                            </div>
                        </td>

                        <td v-if="listLoaded==true" class="width-fix">
                            <div class="width-fix-content">
                                <a
                                  :href="'/profile/'+booking.lesson.instructor?.id"
                                  class="link-to-profile"
                                >{{ booking.lesson.instructor?.full_name }}</a>
                            </div>
                        </td>
                        <td v-else class="width-fix">
                            <div class="width-fix-content">
                                <a
                                  :href="'/profile/'+booking.instructor?.id"
                                  class="link-to-profile"
                                >{{ booking.instructor?.full_name }}</a>
                            </div>
                        </td>

                        <td class="width-fix" v-if="listLoaded==true">
                            <div
                              class="width-fix-content"
                              v-if="booking.lesson.lesson_type=='in_person' || booking.lesson.lesson_type=='in_person_client'"
                              v-html="booking.lesson.location"
                            ></div>
                            <div
                              class="width-fix-content"
                              v-if="booking.lesson.lesson_type=='virtual'"
                            >Virtual Lesson</div>
                        </td>
                        <td class="width-fix" v-else>
                            <div
                              class="width-fix-content"
                              v-if="booking.lesson_type=='in_person'"
                              v-html="booking.location"
                            ></div>
                            <div class="width-fix-content" v-if="booking.lesson_type=='virtual'">Virtual Lesson</div>
                        </td>

                        <td
                          v-if="listLoaded==true"
                        >{{ booking.lesson.start | moment("MM/D/YYYY") }}</td>
                        <td v-else>{{ booking.start | moment("MM/D/YYYY") }}</td>

                        <td
                          v-if="listLoaded==true"
                        >{{ booking.lesson.start | moment("h:mm a") }} - {{ booking.lesson.end | moment("h:mm a") }} {{ booking.lesson.timezone_id }}</td>
                        <td
                          v-else
                        >{{ booking.start | moment("h:mm a") }} - {{ booking.end | moment("h:mm a") }} {{ booking.timezone_id }}</td>

                        <td v-if="listLoaded==true">${{ booking.spot_price }}</td>
                        <td v-else>${{ booking.lesson_price }}</td>

                        <td class="no-wrap w-200" v-if="listLoaded==true">
                            <lesson-participant-room-controls
                              v-if="showOnly=='current' && listLoaded==true && !booking.lesson.room_completed"
                              :booking="booking"
                            ></lesson-participant-room-controls>

                            <span
                              class="btn btn-notify"
                              @click="notifyInstructor(booking.lesson.instructor)"
                              v-if="booking.lesson.instructor.profile.notification_methods.length>0"
                            >contact</span>
                            <span
                              class="btn btn-danger"
                              @click="requestCancelBooking(booking)"
                              v-if="(booking.status!='cancelled' && !isPastLesson(booking.lesson.start))"
                            >Request Cancel</span>
                        </td>

                        <td v-else-if="booking.lesson_id != null">
                            <a
                              @click.prevent="addToCart(booking.lesson_id)"
                              class="btn-approve"
                            >Add to cart</a>
                        </td>
                        <td v-else>
                            <a href="#" @click.prevent="viewLessonRequest(booking)" class="btn-approve">View</a>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
            <div v-if="listItems.length === 0">
                <p class="text-center">No {{ showOnly.replace('_',' ') }} bookings</p>
            </div>
        </div>
        <a
            :href="'/student/bookings?type=' + showOnly"
            class="btn btn-block btn-secondary"
            v-if="this.pagination.total>5"
        >View all</a>
    </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import { mapActions, mapMutations } from 'vuex'

export default {
  mixins: [siteAPI, skillectiveHelper],
  props: {
    bookings: null,
    bookingsMeta: {}
  },
  data() {
    return {
      showOnly: 'current',
      pagination: {
        total: 0
      },
      listLoaded: true
    }
  },
  methods: {
    ...mapActions(['addItemToCartAtStart']),
    addToCart: async function (lessonId) {
      await this.addItemToCartAtStart({
        lessonId,
        specialRequest: this.specialRequestText || ''
      })
      this.$root.$emit('showMiniCart')
    },
    getBookings() {
      this.listLoaded = false
      let queryParams = {}

      queryParams.type = this.showOnly
      queryParams.limit = 5

      let getUrl = "/api/student/bookings"

      this.apiGet(getUrl, {
        params: queryParams
      })
    },
    notifyInstructor(client) {
      this.$root.$emit('initNotificationsForm', [client])
    },
    requestCancelBooking(booking) {
      this.apiDelete('/api/student/booking/' + booking.id)
    },
    componentHandleDeleteResponse(responseData) {
      this.getBookings()
    },
    componentHandlePostResponse(responseData) {
      this.getBookings()
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data
      if (
        responseData.data.meta != undefined &&
        responseData.data.meta.pagination != undefined
      ) {
        this.pagination.total = responseData.data.meta.pagination.total
      }
      this.listLoaded = true
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
    this.listItems = this.bookings
    if (
      this.bookingsMeta != undefined &&
      this.bookingsMeta.pagination != undefined
    ) {
      this.pagination.total = this.bookingsMeta.pagination.total
    }
  },
  mounted() {
    this.$root.$on('lessonRequestUpdated', id => {
      for (var i = 0; i < this.listItems.length; i++) {
        if (this.listItems[i].id == id) {
          this.listItems.splice(i, 1)
        }
      }
    })
  }
}
</script>


