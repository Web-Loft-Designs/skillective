<template>
  <div class="dashboard-booking-item">
    <div class="dashboard-bookings-item--header">
      <span class="genre"> {{ genre.title }} </span>
    </div>
    <div class="dashboard-bookings-item--content">
      <div class="dashboard-bookings-item--content-top">
        <div class="left-side">
          <ul>
            <li class="b-r--student-line">
              <img :src="lesson.student.profile.image" />
              <span> {{ lesson.student.full_name }} </span>
            </li>
            <li v-if="lesson_type === 'virtual'">
              <img src="/images/b-virtual-icon.png" alt="" /> Virtual Lesson
            </li>
            <li v-if="lesson_type === 'in_person'">
              <img src="/images/b-person-icon.png" alt="" />
              <span v-html="location.replace('<br/>', '')" />
            </li>
            <li>
              <img src="/images/b-time-icon.png" alt="" /> {{ lessonStart }} -
              {{ lessonEnd }}, ${{ lesson.lesson_price }}/client
            </li>
          </ul>
        </div>
        <div class="right-side">
          <div class="more-wrap">
            <button @click="toggleDropdown" class="more">
              <img src="/images/b-dots-icon.png" alt="" />
            </button>
            <div v-if="isDropdownOpened" class="more-dropdown">
              <ul>
                <li @click="viewLessonRequest(lesson)">View</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div v-if="isOpened" class="dashboard-bookings-item--content-bottom">
        <div class="tabs-inner">
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
              Cancellation (0)
            </li>
          </ul>


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
    viewLessonRequest: null,
    getBookings: null,
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
      return moment(lessonStart).isBefore();
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
    deleteLesson() {
      this.apiDelete("/api/lesson/" + this.lesson.id);
    },
    componentHandleDeleteResponse(responseData) {
      this.getBookings();
      this.isDropdownOpened = false;
    },
  },
  computed: {
    count_booked: function () {
      if (this.bookings && this.bookings.length > 0) {
        let filteredBookings = this.bookings.filter((item) => {
          return item.status != "cancelled";
        });
        return filteredBookings.length;
      } else {
        return 0;
      }
    },
    bookings_summ: function () {
      if (this.bookings && this.bookings.length > 0) {
        let summ = 0;

        let pendingBookings = this.bookings.forEach((item) => {
          if (item.status != "cancelled") {
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
          (item) => item.status == "pending"
        );

        return pendingBookings.length;
      } else {
        return 0;
      }
    },
    newBookings: function () {
      if (this.bookings && this.bookings.length > 0) {
        let pendingBookings = this.bookings.filter(
          (item) => item.status == "pending"
        );

        return pendingBookings;
      } else {
        return [];
      }
    },
    approvedBookings: function () {
      if (this.bookings && this.bookings.length > 0) {
        let pendingBookings = this.bookings.filter(
          (item) => item.status == "payment_in_escrow"
        );

        return pendingBookings;
      } else {
        return [];
      }
    },
    cancelationBookings: function () {
      return [];
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
  mounted: function () {

    console.log(this.lesson)
    
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