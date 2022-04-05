<template>
  <div class="dashboard-booking-item">
    <div class="dashboard-bookings-item--header">
      <span class="genre"> {{ genre.title }}</span>
      <div class="dashboard-bookings-item--header-right">
        <span class="price"> ${{ bookings_summ }} </span>
        <span
          @click="toggleOppened"
          :class="{ 'dropdown-icon': true, 'dropdown-icon--active': isOpened }"
        >
          <img src="/images/b-drop-icon.png" alt="" />
        </span>
      </div>
    </div>
    <div class="dashboard-bookings-item--content">
      <div class="dashboard-bookings-item--content-top">
        <div class="left-side">
          <ul>
            <li class="b-radial">
              <div class="b-students-count">
                <div
                  v-if="count_booked == spots_count"
                  class="progress-success"
                >
                  <img src="/images/b-check-icon.png" alt="" />
                </div>
                <radial-progress-bar
                  :diameter="24"
                  :completed-steps="count_booked"
                  :total-steps="spots_count"
                  startColor="#8ADA00"
                  stopColor="#8ADA00"
                  innerStrokeColor="#F4F4F6"
                  :strokeWidth="4"
                  :innerStrokeWidth="4"
                  v-else
                >
                </radial-progress-bar>
                <span>
                  {{ count_booked }} of {{ spots_count }} Clients Bookings
                </span>
              </div>

              <span v-if="new_bookings_count > 0" class="new-bookings-alert">
                {{ new_bookings_count }} New Bookings
              </span>

              <span
                v-if="cancelationBookings.length > 0"
                class="cancel-bookings-alert"
              >
                {{ cancelationBookings.length }} Cancellation Requests
              </span>
            </li>
            <li v-if="lesson_type === 'virtual'">
              <img src="/images/b-virtual-icon.png" alt="" /> Virtual Lesson
            </li>
            <li
              v-if="
                lesson_type === 'in_person' ||
                lesson_type === 'in_person_client'
              "
            >
              <img src="/images/b-person-icon.png" alt="" />
              <span v-html="formatedLocation" />
            </li>
            <li>
              <img src="/images/b-time-icon.png" alt="" /> {{ lessonStart }} -
              {{ lessonEnd }}, ${{ spot_price }}/client
            </li>
          </ul>
        </div>
        <div class="right-side">
          <span class="price price--mobile"> ${{ bookings_summ }} </span>
          <div class="cd--control-outer">
            <dashboard-booking-item-countdown
              v-if="
                listLoaded == true &&
                !lesson.room_completed &&
                lesson_type == 'virtual' &&
                !isPastLesson(end) &&
                bookings.length > 0
              "
              :lesson="lesson"
            >
            </dashboard-booking-item-countdown>
          </div>
          <div v-if="showOnly != 'past'" class="more-wrap">
            <button @click="toggleDropdown" class="more">
              <img src="/images/b-dots-icon.png" alt="" />
            </button>
            <div v-if="isDropdownOpened" class="more-dropdown">
              <ul>
                <li v-if="count_booked < 2" @click="editLesson(lesson)">
                  Edit
                </li>
                <li @click="deleteLesson">Delete</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="cd--control-outer cd--control-outer--mobile">
          <dashboard-booking-item-countdown
            v-if="
              listLoaded == true &&
              !lesson.room_completed &&
              lesson_type == 'virtual' &&
              !isPastLesson(end) &&
              bookings.length > 0
            "
            :lesson="lesson"
          >
          </dashboard-booking-item-countdown>
        </div>
      </div>
      <div v-if="isOpened" class="dashboard-bookings-item--content-bottom">
        <div v-if="showOnly !== 'past'" class="tabs-inner">
          <ul>
            <li
              @click="selectTab('new')"
              :class="{ 'is-active': active === 'new' }"
            >
              New bookings ({{ new_bookings_count }})
            </li>
            <li
              @click="selectTab('cancellation')"
              :class="{ 'is-active': active === 'cancellation' }"
            >
              Cancellation ({{ cancelationBookings.length }})
            </li>
          </ul>

          <div class="tab-inner" v-if="active === 'new'">
            <div class="pending-bookings">
              <table>
                <thead>
                  <tr>
                    <th>Photo</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(booking, index) in newBookings" :key="index">
                    <td class="width-fix-avatar">
                      <img :src="booking.student.profile.image" />
                    </td>
                    <td class="width-fix-fullname">
                      {{ booking.student.full_name }}
                    </td>
                    <td class="width-fix">
                      <div class="width-fix-content">
                        {{ booking.student.email }}
                      </div>
                    </td>
                    <td class="width-fix">
                      <div class="width-fix-content">
                        {{ booking.student.profile.mobile_phone }}
                      </div>
                    </td>
                    <td v-if="listLoaded == true">
                      <a
                        href="#"
                        @click.prevent="approveBooking(booking)"
                        class="btn-approve"
                        v-if="
                          booking.status == 'pending' && !isPastLesson(start)
                        "
                        >Approve</a
                      >
                      <a
                        href="#"
                        @click.prevent="cancelBooking(booking)"
                        class="btn-cancel"
                        v-if="
                          booking.status != 'cancelled' && !isPastLesson(start)
                        "
                        >Cancel</a
                      >
                    </td>
                  </tr>
                </tbody>
              </table>

              <div class="pending-bookings--mobile">
                <div
                  class="mobile--user-b-item"
                  v-for="(booking, index) in newBookings"
                  :key="index"
                >
                  <div class="user-left">
                    <img :src="booking.student.profile.image" />
                  </div>
                  <div class="user-right">
                    <ul>
                      <li>
                        <span class="k"> Student </span>
                        <span class="v"> {{ booking.student.full_name }} </span>
                      </li>
                      <li>
                        <span class="k"> Email </span>
                        <span class="v"> {{ booking.student.email }} </span>
                      </li>
                      <li>
                        <span class="k"> Phone </span>
                        <span class="v">
                          {{ booking.student.profile.mobile_phone }}
                        </span>
                      </li>
                    </ul>
                    <div class="user-b-bottom">
                      <a
                        href="#"
                        @click.prevent="approveBooking(booking)"
                        class="btn-approve"
                        v-if="
                          booking.status == 'pending' && !isPastLesson(start)
                        "
                        >Approve</a
                      >
                      <a
                        href="#"
                        @click.prevent="cancelBooking(booking)"
                        class="btn-cancel"
                        v-if="
                          booking.status != 'cancelled' && !isPastLesson(start)
                        "
                        >Cancel</a
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-inner" v-if="active === 'cancellation'">
            <div class="pending-bookings">
              <table>
                <thead>
                  <tr>
                    <th>Photo</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(booking, index) in cancelationBookings"
                    :key="index"
                  >
                    <td><img :src="booking.student.profile.image" /></td>
                    <td>{{ booking.student.full_name }}</td>
                    <td class="width-fix">
                      <div class="width-fix-content">
                        {{ booking.student.email }}
                      </div>
                    </td>
                    <td class="width-fix">
                      <div class="width-fix-content">
                        {{ booking.student.profile.mobile_phone }}
                      </div>
                    </td>
                    <td v-if="listLoaded == true">
                      <a
                        href="#"
                        @click.prevent="cancelBooking(booking)"
                        class="btn-cancel"
                        v-if="
                          booking.status != 'cancelled' && !isPastLesson(start)
                        "
                        >Cancel</a
                      >
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="pending-bookings--mobile">
                <div
                  class="mobile--user-b-item"
                  v-for="(booking, index) in cancelationBookings"
                  :key="index"
                >
                  <div class="user-left">
                    <img :src="booking.student.profile.image" />
                  </div>
                  <div class="user-right">
                    <ul>
                      <li>
                        <span class="k"> Student </span>
                        <span class="v"> {{ booking.student.full_name }} </span>
                      </li>
                      <li>
                        <span class="k"> Email </span>
                        <span class="v"> {{ booking.student.email }} </span>
                      </li>
                      <li>
                        <span class="k"> Phone </span>
                        <span class="v">
                          {{ booking.student.profile.mobile_phone }}
                        </span>
                      </li>
                    </ul>
                    <div class="user-b-bottom">
                      <a
                        href="#"
                        @click.prevent="cancelBooking(booking)"
                        class="btn-cancel"
                        v-if="
                          booking.status != 'cancelled' && !isPastLesson(start)
                        "
                        >Cancel</a
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="aprooved-bookings">
          <h3>
            Approved Bookings ({{ approvedBookings.length }})
            <button
              v-if="showOnly !== 'past' && bookings_students.length > 0"
              @click="notifyAll"
            >
              Notify clients
            </button>
          </h3>
          <table>
            <thead>
              <tr>
                <th class="b-th-image">Photo</th>
                <th class="b-th-name">Student</th>
                <th class="b-th-email">Email</th>
                <th class="b-th-phone">Phone</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(booking, index) in approvedBookings" :key="index">
                <td class="width-fix">
                  <div class="width-fix-content">
                    <img :src="booking.student.profile.image" />
                  </div>
                </td>
                <td class="width-fix-fullname">
                  {{ booking.student.full_name }}
                </td>
                <td class="width-fix">
                  <div class="width-fix-content">
                    {{ booking.student.email }}
                  </div>
                </td>
                <td class="width-fix">
                  <div class="width-fix-content">
                    {{ booking.student.profile.mobile_phone }}
                  </div>
                </td>
                <td>
                  <a
                    class="btn-notify"
                    @click="notifyClient(booking.student.profile)"
                    v-if="!isPastLesson(lesson.start)"
                    >Notify</a
                  >
                  <span
                    class="btn btn-cancel"
                    @click="cancelBooking(booking)"
                    v-if="
                      booking.status != 'cancelled' &&
                      !isPastLesson(lesson.start)
                    "
                    >Cancel</span
                  >
                </td>
              </tr>
            </tbody>
          </table>

          <div class="pending-bookings--mobile">
            <div
              class="mobile--user-b-item"
              v-for="(booking, index) in approvedBookings"
              :key="index"
            >
              <div class="user-left">
                <img :src="booking.student.profile.image" />
              </div>
              <div class="user-right">
                <ul>
                  <li>
                    <span class="k"> Student </span>
                    <span class="v"> {{ booking.student.full_name }} </span>
                  </li>
                  <li>
                    <span class="k"> Email </span>
                    <span class="v"> {{ booking.student.email }} </span>
                  </li>
                  <li>
                    <span class="k"> Phone </span>
                    <span class="v">
                      {{ booking.student.profile.mobile_phone }}
                    </span>
                  </li>
                </ul>
                <div class="user-b-bottom">
                  <a
                    class="btn-notify"
                    @click="notifyClient(booking.student.profile)"
                    v-if="!isPastLesson(lesson.start)"
                    >Notify</a
                  >
                  <span
                    class="btn btn-cancel"
                    @click="cancelBooking(booking)"
                    v-if="
                      booking.status != 'cancelled' &&
                      !isPastLesson(lesson.start)
                    "
                    >Cancel</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import RadialProgressBar from "vue-radial-progress";
import siteAPI from "../../mixins/siteAPI.js";
import skillectiveHelper from "../../mixins/skillectiveHelper.js";
import manageVideoLesson from "../../mixins/manageVideoLesson.js";

export default {
  mixins: [siteAPI, skillectiveHelper, manageVideoLesson],
  props: {
    lesson: {},
    listLoaded: true,
    approveBooking: null,
    cancelBooking: null,
    getBookings: null,
    showOnly: String,
  },
  components: {
    RadialProgressBar,
  },
  data() {
    return {
      genre: {
        title: "",
      },
      spots_count: 0,
      spot_price: "",
      bookings: [],
      lesson_type: "",
      fill: { color: "#8ADA00" },
      start: "",
      isOpened: false,
      active: "new",
      isDropdownOpened: false,
    };
  },
  methods: {
    isPastLesson(lessonStart) {
      let nowDate = new Date(),
        start = new Date(lessonStart);

      let diff =
        start.getTime() -
        new Date(
          nowDate.toLocaleString("en-us", {
            timeZone: this.lesson.timezone_id_name,
          })
        ).getTime();
      if (diff < 0) return true;

      return false;
    },
    toggleOppened() {
      this.isOpened = !this.isOpened;
    },
    selectTab(selectedTab) {
      this.active = selectedTab;
    },
    notifyClient(client) {
      this.$root.$emit("initNotificationsForm", [client]);
    },
    toggleDropdown() {
      this.isDropdownOpened = !this.isDropdownOpened;
    },
    initEdit() {
      this.isDropdownOpened = false;
    },
    editLesson(lesson) {
      this.$root.$emit("lessonUpdateInit", lesson);
    },
    deleteLesson() {
      this.apiDelete("/api/lesson/" + this.lesson.id);
    },
    componentHandleDeleteResponse(responseData) {
      this.getBookings();
      this.isDropdownOpened = false;
    },
    notifyAll() {
      this.$root.$emit("initNotificationsForm", this.bookings_students);
    },
  },
  computed: {
    formatedLocation: function (){
      let result = ''

      if(this.lesson_type === 'in_person_client'){
        if(this.location){
          result =  this.location.replace('<br/>', '') + "- <b> Client specified </b>"
        }
        else{
           result = 'Location has not been determined yet'
        }
      }
      else if(this.lesson_type === 'in_person'){
         result =  this.location.replace('<br/>', '')
      }

      return result;
    },
    count_booked: function () {
      if (this.bookings && this.bookings.length > 0) {
        let filteredBookings = this.bookings.filter((item) => {
          if (this.isPastLesson(this.start)) {
            return (
              item.status == "complete" ||
              item.status == "payment_in_escrow" ||
              item.status == "payment_released"
            );
          } else {
            return item.status != "cancelled";
          }
        });
        return filteredBookings.length;
      } else {
        return 0;
      }
    },
    bookings_students: function () {
      if (this.bookings && this.bookings.length > 0) {
        const students = [];

        this.bookings.forEach((item) => {
          console.log(item);
          if (
            (item.status == "payment_in_escrow" &&
              !item.has_cancellation_request) ||
            (item.status == "payment_released" &&
              !item.has_cancellation_request) ||
            (item.status == "complete" && !item.has_cancellation_request)
          ) {
            students.push(item.student);
          }
        });

        return students;
      } else {
        return [];
      }
    },
    bookings_summ: function () {
      if (this.bookings && this.bookings.length > 0) {
        let summ = 0;

        this.bookings.forEach((item) => {
          if (item.status != "cancelled" && item.status != "pending") {
            summ += Number(this.lesson.spot_price);
          }
        });

        return summ;
      } else {
        return 0;
      }
    },
    new_bookings_count: function () {
      if (this.bookings && this.bookings.length > 0) {
        let pendingBookings = this.bookings.filter(
          (item) => item.status == "pending" && !this.isPastLesson(this.start)
        );

        return pendingBookings.length;
      } else {
        return 0;
      }
    },
    newBookings: function () {
      if (this.bookings && this.bookings.length > 0) {
        let pendingBookings = this.bookings.filter(
          (item) => item.status == "pending" && !this.isPastLesson(this.start)
        );

        return pendingBookings;
      } else {
        return [];
      }
    },
    approvedBookings: function () {
      if (this.bookings && this.bookings.length > 0) {
        let pendingBookings = this.bookings.filter(
          (item) =>
            (item.status == "payment_in_escrow" &&
              !item.has_cancellation_request) ||
            (item.status == "payment_released" &&
              !item.has_cancellation_request) ||
            (item.status == "complete" && !item.has_cancellation_request)
        );

        return pendingBookings;
      } else {
        return [];
      }
    },
    cancelationBookings: function () {
      if (this.bookings && this.bookings.length > 0) {
        let pendingBookings = this.bookings.filter(
          (item) => item.has_cancellation_request
        );

        return pendingBookings;
      } else {
        return [];
      }
    },
    lessonStart: function () {
      return moment(this.lesson.start).format("MMMM DD h:mm A");
    },
    lessonEnd: function () {
      return moment(this.lesson.end).format("MMMM DD h:mm A");
    },
  },
  watch: {
    lesson: function () {
      this.genre = this.lesson.genre;
      this.spot_price = this.lesson.spot_price;
      this.spots_count = this.lesson.spots_count;
      this.location = this.lesson.location;
      this.lesson_type = this.lesson.lesson_type;
      this.bookings = this.lesson.bookings;

      this.start = this.lesson.start;
      this.end = this.lesson.end;
    },
  },
  mounted () {
    this.genre = this.lesson.genre;
    this.spot_price = this.lesson.spot_price;
    this.spots_count = this.lesson.spots_count;
    this.location = this.lesson.location;
    this.lesson_type = this.lesson.lesson_type;
    this.bookings = this.lesson.bookings;

    this.start = this.lesson.start;
    this.end = this.lesson.end;
  },
};
</script>