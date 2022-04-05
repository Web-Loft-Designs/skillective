<template>
  <div class="calendar-component">
    <div class="calendar-component-top">
      <h2>
        Schedule
        <button type="button" @click="downloadExportedFile()">
          <img src="/images/upload-btn.svg" alt="" /><span>Export</span>
        </button>
      </h2>

      <div v-if="errorText" class="has-error">{{ errorText }}</div>
      <div v-if="successText" class="has-success">{{ successText }}</div>
    </div>
    <div class="calendar-component-body">
      <FullCalendar
        :header="{
          left: 'prev',
          center: 'title',
          right: 'next',
        }"
        :footer="{
          right: 'timeGridWeek,dayGridMonth',
        }"
        :defaultView="'dayGridMonth'"
        :navLinks="true"
        :firstDay="0"
        :nowIndicator="true"
        :plugins="calendarPlugins"
        :events="events"
        :showNonCurrentDates="false"
        :eventRender="eventRender"
        :eventTimeFormat="eventTimeFormat"
        :selectable="true"
        :selectOverlap="selectOverlap"
        slotDuration="00:15:00"
        slotLabelInterval="01:00"
        :selectConstraint="{
          startTime: '00:00',
          endTime: '24:00',
        }"
        :selectAllow="selectAllow"
        :datesRender="viewRender"
        @select="selected"
        @eventClick="dateClick"
        ref="fullCalendar"
      ></FullCalendar>

      <magnific-popup-modal
        @close="clearFormAndClosePopup"
        class="modal-lesson-info"
        :show="false"
        :config="{
          closeOnBgClick: true,
          showCloseBtn: true,
          enableEscapeKey: false,
        }"
        ref="modal"
      >
        <booking-details-modal
          v-if="selectedBooking != null"
          :lesson-details="selectedBooking"
          :show-notify-btn="true"
          :modal-window="this.$refs.modal"
          @notification="openSend"
          @bookings-cancelled="removeIt"
        ></booking-details-modal>
      </magnific-popup-modal>
      <magnific-popup-modal
        @close="clearFormAndClosePopup"
        class="panding-popup"
        :show="false"
        :config="{
          closeOnBgClick: true,
          showCloseBtn: true,
          enableEscapeKey: false,
        }"
        ref="modal2"
      >
        <panding-popup :modal-window="this.$refs.modal2"></panding-popup>
      </magnific-popup-modal>
    </div>
  </div>
</template>


<script>
import $ from "jquery";
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import MagnificPopupModal from "./MagnificPopupModal";
import siteAPI from "../../mixins/siteAPI.js";
import { getTimezone } from "countries-and-timezones";
var FileSaver = require("file-saver");

export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
    MagnificPopupModal,
  },
  mixins: [siteAPI],
  props: ["bookings", "userGenres", "siteGenres"],
  data() {
    return {
      calendarPlugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      eventTimeFormat: {
        hour: "numeric",
        minute: "numeric",
        hour12: true,
        meridiem: "short",
      },
      selectRangeTrigger: null,
      events: [],
      trigger: true,
      triggerOld: null,
      triggerView: "month",
      scrollTime1: "0:00:00",
      selectedBooking: null,
    };
  },
  mounted: function () {
    // this.getBookings();
    // this.$root.$on('bookingsCancelled', () => {
    // this.getBookings();
    // });
  },
  methods: {
    getBookings() {
      let queryParams = {};
      //				queryParams.month = calentar month;

      this.apiGet("/api/student/bookings", {
        params: queryParams,
      });
    },
    clearFormAndClosePopup() {},
    removeIt(id) {
      var temp = null;
      this.events.forEach(function (item, key) {
        if (item.id == id) {
          temp = key;
        }
      });
      this.events.splice(temp, 1);
    },
    openSend() {
      setTimeout(() => {
        this.$refs.modal2.open();
      }, 100);
    },
    componentHandleGetResponse(response) {
      this.events = response.data;

      console.log('BOOOKED')
      this.events = this.events.map(function (item) {
        item.genre = item.genre;
        let userTzOffset = new Date().getTimezoneOffset() * 60 * 1000;

        let tzOffset = 0;

        if (item.lesson_type === "virtual") {
          tzOffset = getTimezone(item.timezone_id_name).utcOffset * 60 * 1000;
        }

        let dummyStart =
          new Date(item.start.replace(/\s/, "T")).getTime() -
          tzOffset -
          userTzOffset;

        item.start = moment(dummyStart).format("YYYY-MM-DD HH:mm:ss");

        let dummyEnd =
          new Date(item.end.replace(/\s/, "T")).getTime() -
          tzOffset -
          userTzOffset;

        item.end = moment(dummyEnd).format("YYYY-MM-DD HH:mm:ss");

        item.date = moment(item.start).format("YYYY-MM-DD");

        return item;
      });
      this.events = this.events.filter(function (item) {
        return moment().diff(item.end) <= 0;
      });
    },
    viewRender: function (info) {
      if (info.view.type === "timeGridWeek") {
        this.triggerView = "week";
      } else if (info.view.type === "timeGridDay") {
        this.triggerView = "day";
      } else {
        this.triggerView = "month";
      }
      if (this.triggerOld !== null) {
        if (
          this.triggerOld !==
          moment(info.view.currentStart).format("YYYY-MM-DD")
        ) {
          this.trigger = true;
          this.triggerOld = moment(info.view.currentStart).format("YYYY-MM-DD");
        } else {
          this.trigger = false;
        }
      } else {
        this.triggerOld = moment(info.view.currentStart).format("YYYY-MM-DD");
      }

      if (this.trigger) {
        this.trigger = false;
        if (this.loader == null) {
          this.loader = this.$loading.show({
            zIndex: 9999999,
          });
        }
        axios
          .get(
            "/api/student/bookings?" +
              this.triggerView +
              "=" +
              moment(info.view.currentStart).format("YYYY-MM-DD")
          )
          .then((response) => {
            this.events = response.data.data;
            this.events = this.events.map(function (item) {
              item.title = item.genre.title;
         let userTzOffset = new Date().getTimezoneOffset() * 60 * 1000;

              let lessonTimeZoneObj = getTimezone(item.timezone_id_name);

              var jan = new Date(0, 1);
              var jul = new Date(6, 1);
              var stdTimezoneOffset = Math.max(
                jan.getTimezoneOffset(),
                jul.getTimezoneOffset()
              );

              var today = new Date();

              var isDstObserved = false;

              if (today.getTimezoneOffset() < stdTimezoneOffset) {
                isDstObserved = true;
              }

              var _tzOffset = 1;

              if (isDstObserved) {
                _tzOffset = lessonTimeZoneObj.dstOffset * 60 * 1000;
              } else {
                _tzOffset = lessonTimeZoneObj.utcOffset * 60 * 1000;
              }

              let dummyStart =
                new Date(item.start.replace(/\s/, "T")).getTime() -
                userTzOffset -
                _tzOffset;

              item.start = moment(dummyStart).format("YYYY-MM-DD HH:mm:ss");

              let dummyEnd =
                new Date(item.end.replace(/\s/, "T")).getTime() -
                userTzOffset -
                _tzOffset;

              item.end = moment(dummyEnd).format("YYYY-MM-DD HH:mm:ss");

              item.date = moment(item.start).format("YYYY-MM-DD");

              return item;
            });
            this.events = this.events.filter(function (item) {
              return moment().diff(item.end) <= 0;
            });

            let calendarApi = this.$refs.fullCalendar.getApi();
            // this.scrollTime1 = moment(this.events[0].start).count('hh:mm:ss');
            calendarApi.scrollTime = moment(this.events[0].start).format(
              "HH:mm:ss"
            );
            calendarApi.scrollToTime(
              moment(this.events[0].start).format("HH:mm:ss")
            );
            this.loader.hide();
            this.loader = null;
          })
          .catch((error) => {
            this.events = [];
            this.loader.hide();
            this.loader = null;
          });
      }
    },
    eventRender: function (info) {
      if (moment(info.event.start, "x") <= moment(new Date(), "x")) {
        info.el.className = info.el.className + " last-event";
      }
      var count =
        parseInt(info.event.extendedProps.spots_count) -
        parseInt(info.event.extendedProps.count_booked);
      if (count === 1) {
        info.el.className = info.el.className + " red-event";
      } else if (count === 2) {
        info.el.className = info.el.className + " yellow-event";
      } else if (count > 2) {
        info.el.className = info.el.className + " green-event";
      } else {
        info.el.className = info.el.className + " grey-event";
      }
      info.el.innerHTML =
        info.el.innerHTML +
        '<span class="spot-left">Spots left: ' +
        count +
        "</span>";
      let calendarApi = this.$refs.fullCalendar.getApi();
      // this.scrollTime1 = moment(this.events[0].start).count('hh:mm:ss');
      setTimeout(() => {
        calendarApi.scrollTime = moment(this.events[0].start).format(
          "HH:mm:ss"
        );
        calendarApi.scrollToTime(
          moment(this.events[0].start).format("HH:mm:ss")
        );
      }, 100);
    },
    IsDateHasEvent: function (date) {
      var allEvents = this.events;

      if (allEvents.length) {
        var event = allEvents.filter(function (v) {
          return v.date === date;
        });
        return event.length > 0;
      }
    },
    selectOverlap: function (event) {
      if (event) {
        return false;
      }
    },
    // selected: function (info) {
    //   this.selectRangeTrigger = info;
    // },
    selected: function (info) {
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.changeView("timeGridWeek");
      calendarApi.gotoDate(info.start);
    },
    dateClick: function (info) {
      this.selectedBooking = {
        id: info.event.id,
        lat: info.event.extendedProps.lat,
        lng: info.event.extendedProps.lng,
        title: info.event.title,
        fullDate:
          moment(info.event.start).format("MMM") +
          " " +
          moment(info.event.start).format("DD") +
          ", " +
          moment(info.event.start).format("hh:mma") +
          " - " +
          moment(info.event.end).format("hh:mma"),
        location: info.event.extendedProps.location,
        price: info.event.extendedProps.spot_price,
        students: info.event.extendedProps.students,
        content: info.event.extendedProps,
      };
      // this.$refs.modal.open();
    },
    closeModal: function () {
      this.$refs.modal.close();
    },
    updateCalendar: function (booking) {
      booking.title = booking.genre.title;
      booking.date = moment(booking.start).format("YYYY-MM-DD");
      this.events.push(booking);
    },
    selectAllow: function (selectInfo) {
      return moment().diff(selectInfo.start) <= 0;
    },
    downloadExportedFile: function () {
      if (this.loader == null) {
        this.loader = this.$loading.show({
          zIndex: 9999999,
        });
      }
      let calendarApi = this.$refs.fullCalendar.getApi();
      // this.scrollTime1 = moment(this.events[0].start).count('hh:mm:ss');
      let currentTime = moment(calendarApi.view.currentStart).format(
        "YYYY-MM-DD"
      );
      // calendarApi.scrollToTime(moment(this.events[0].start).format('YYYY-MM-DD'))
      return axios
        .get("/api/student/bookings/export?month=" + currentTime, {
          responseType: "blob",
        })
        .then((response) => {
          FileSaver.saveAs(response.data, "bookings-list.xlsx");
          this.loader.hide();
          this.loader = null;
        })
        .catch((error) => {
          this.loader.hide();
          this.loader = null;
        });
      //				this.$http.get('/api/bookings/export', {responseType: 'arraybuffer'})
      //					.then(response => {
      //						    this.downloadFile(response, 'bookings-list', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '.xlsx')
      //                        }, response => {
      //                            // Manage errors
      //                        }
      //			        );
    },
  },
};
</script>