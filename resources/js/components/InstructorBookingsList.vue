<template>
  <div id="profile-bookings-container">
    <div class="table-top d-flex align-items-center">
      <h2 class="page-title">Bookings</h2>
    </div>
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <span
          v-if="selectedBookings.length > 0"
          @click="cancelManyBookings"
          class="btn btn-danger mr-2"
          >Cancel {{ selectedBookings.length }} bookings</span
        >
      </div>
      <div class="row" v-if="pagination.total_pages > 1">
        <div
          class="col-12 d-flex justify-content-end align-items-center custom-pag"
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
          ></paginate>
        </div>
      </div>
    </div>

    <div v-if="errorText" class="has-error" v-html="errorText"></div>
    <div
      v-if="componentSuccessText"
      class="has-success"
      v-html="componentSuccessText"
    ></div>

    <instructor-bookings-dashboard-list
      :bookings="bookings"
      :showPastLesson="true"
    >
    </instructor-bookings-dashboard-list>

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
        ></paginate>
      </div>
    </div>
  </div>
</template>

<script>
import siteAPI from "../mixins/siteAPI.js";
import skillectiveHelper from "../mixins/skillectiveHelper.js";
import manageVideoLesson from "../mixins/manageVideoLesson.js";
import Paginate from "vuejs-paginate";
import $ from "jquery";

export default {
  mixins: [siteAPI, skillectiveHelper, manageVideoLesson],
  props: {
    bookings: null,
    bookingsMeta: {},
  },
  components: {
    Paginate,
  },
  data() {
    return {
      componentSuccessText: "",
      selectedBookings: [],
      searchString: "",
      showOnly: "pending",
      allSelected: false,
      indeterminate: false,
      pagination: {
        total: 0,
        total_pages: 0,
        current_page: 0,
        per_page: 0,
      },
      listLoaded: true,
    };
  },
  methods: {
    selectAll: function () {
      this.selectedBookings = [];

      if (this.allSelected) {
        this.indeterminate = false;
        for (let user in this.listItems) {
          this.selectedBookings.push(this.listItems[user].id.toString());
        }
      }
    },
    select: function () {
      if (this.listItems.length == this.selectedBookings.length) {
        this.allSelected = 1;
        this.indeterminate = false;
      } else if (this.selectedBookings.length === 0) {
        this.allSelected = 0;
        this.indeterminate = false;
      } else {
        this.allSelected = 0;
        this.indeterminate = true;
      }
    },
    getBookings() {
      this.listLoaded = false;
      let queryParams = {};

      queryParams.type = this.showOnly;

      if (this.pagination.current_page != undefined)
        queryParams.page = this.pagination.current_page;
      else queryParams.page = 1;

      if (this.searchString != "") queryParams.s = this.searchString;

      this.updateUrlQueryParams(queryParams);

      let getUrl =
        this.showOnly == "lesson_requests"
          ? "/api/lesson-requests"
          : "/api/instructor/bookings";

      this.apiGet(getUrl, {
        params: queryParams,
      });
    },
    cancelBooking(booking) {
      this.componentSuccessText = "";
      this.apiDelete("/api/instructor/booking/" + booking.id);
    },
    cancelManyBookings() {
      this.componentSuccessText = "";
      this.apiPost("/api/instructor/bookings/cancel", {
        bookings: this.selectedBookings,
      });
    },
    approveBooking(booking) {
      this.componentSuccessText = "";
      this.apiPut("/api/instructor/booking/approve/" + booking.id);
    },
    componentHandleDeleteResponse(responseData) {
      $(".update-value").text(parseInt($(".update-value").text()) - 1);
      if (this.pagination.count == 1 && this.pagination.current_page > 1) {
        // last booking on page cancelled > go to prev page
        this.pagination.current_page -= 1;
      }
      this.componentSuccessText = this.successText;
      this.selectedBookings = [];
      this.getBookings();
    },
    componentHandlePostResponse(responseData) {
      if (
        this.pagination.count == parseInt(responseData.data) &&
        this.pagination.current_page > 1
      ) {
        // last booking on page cancelled > go to prev page
        this.pagination.current_page -= 1;
      }
      this.componentSuccessText = this.successText;
      this.selectedBookings = [];
      this.getBookings();
    },
    componentHandlePutResponse(responseData) {
      if (
        this.pagination.count == parseInt(responseData.data) &&
        this.pagination.current_page > 1
      ) {
        // last booking on page cancelled > go to prev page
        this.pagination.current_page -= 1;
      }
      $(".update-value").text(parseInt($(".update-value").text()) - 1);
      this.componentSuccessText = this.successText;
      this.selectedBookings = [];
      this.getBookings();
    },
    paginatorClickCallback(pageNum) {
      this.pagination.current_page = pageNum;
      this.getBookings();
    },
    onChangePerPage() {
      this.pagination.current_page = 1;

      Cookies.set("instructorBookingsPerPage", this.pagination.per_page);

      this.getBookings();
    },
    searchBookings() {
      this.pagination.current_page = 1;
      this.getBookings();
    },
    componentHandleGetResponse(responseData) {

      if (
        responseData.data.meta != undefined &&
        responseData.data.meta.pagination != undefined
      ) {
        this.pagination.count = responseData.data.meta.pagination.count;
        this.pagination.total = responseData.data.meta.pagination.total;
        this.pagination.total_pages =
          responseData.data.meta.pagination.total_pages;
        this.pagination.current_page =
          responseData.data.meta.pagination.current_page;
        this.pagination.per_page = responseData.data.meta.pagination.per_page;
      }
      this.allSelected = false;
      this.indeterminate = false;
      this.listLoaded = true;
    },
    toggleShowOnly(type) {
      this.showOnly = type;
      this.pagination.current_page = 1;
      this.getBookings();
    },
    isPastLesson(lessonStart) {
      return moment(lessonStart).isBefore();
    },
    viewLessonRequest(lessonRequest) {
      this.$root.$emit("lessonRequestUpdateInit", lessonRequest);
    },
  },
  created: function () {

    console.log(this.bookingsMeta)

    if (
      this.bookingsMeta != undefined &&
      this.bookingsMeta.pagination != undefined
    ) {
      this.pagination.count = this.bookingsMeta.pagination.count;
      this.pagination.total = this.bookingsMeta.pagination.total;
      this.pagination.total_pages = this.bookingsMeta.pagination.total_pages;
      this.pagination.current_page = this.bookingsMeta.pagination.current_page;
      this.pagination.per_page = this.bookingsMeta.pagination.per_page;
    }
    if (this.getUrlParameter("s"))
      this.searchString = this.getUrlParameter("s");
    if (this.getUrlParameter("type"))
      this.showOnly = this.getUrlParameter("type");

    this.debouncedGetBookings = _.debounce(this.searchBookings, 500);
  },
  watch: {
    searchString: function (newSearchString, oldSearchString) {
      this.debouncedGetBookings();
    },
  },
  computed: {
    firstListItemNumber: function () {
      return (
        this.pagination.current_page * this.pagination.per_page -
        this.pagination.per_page +
        1
      );
    },
    lastListItemNumber: function () {
      if (this.pagination.count == this.pagination.per_page) {
        return this.firstListItemNumber + this.pagination.per_page - 1;
      } else {
        return this.firstListItemNumber + this.pagination.count - 1;
      }
    },
  }
};
</script>