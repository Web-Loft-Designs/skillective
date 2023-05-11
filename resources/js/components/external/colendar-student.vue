<template>
  <div class="calendar-component calendar-component-grey">
    <div class="calendar-component-top">
      <h2>
        Schedule
        <button type="button" @click="openShareModal">
          <img src="/images/sare-icon-green.png" alt="" /><span>Share</span>
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
          right: 'custom1,timeGridWeek,dayGridMonth'
        }"
        :custom-buttons="{
          custom1: {
          text: 'Day',
            click: goToCurrentDay
          }
        }"
        :defaultView="'dayGridMonth'"
        :navLinks="true"
        :firstDay="0"
        :nowIndicator="true"
        :plugins="calendarPlugins"
        :events="events"
        :showNonCurrentDates="false"
        :fixedWeekCount="false"
        :eventRender="eventRender"
        :eventTimeFormat="eventTimeFormat"
        :datesRender="viewRender"
        slotDuration="00:15:00"
        slotLabelInterval="01:00"
        :selectable="true"
        :selectConstraint="{
          startTime: '00:00',
          endTime: '24:00',
        }"
        :selectAllow="selectAllow"
        @select="selected"
        @eventClick="dateClick"
        ref="fullCalendar"
        timeZone="UTC"
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
        <lesson-details-modal
          :current-user-can-book="currentUserCanBook"
          v-if="selectedEvent != null"
          :lesson-details="selectedEvent"
          :show-notify-btn="false"
          :student-list="false"
          :student-cancel="true"
          :modal-window="this.$refs.modal"
          @lessons-cancelled="removeIt"
        ></lesson-details-modal>
      </magnific-popup-modal>
      <magnific-popup-modal
        @close="clearFormAndClosePopup"
        class="modal-share"
        :show="false"
        :config="{
          closeOnBgClick: true,
          showCloseBtn: true,
          enableEscapeKey: false,
        }"
        ref="modal2"
      >
        <share-modal :modal-window="this.$refs.modal2"></share-modal>
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
import countriesAndTimezones from "countries-and-timezones";
import moment from 'moment-timezone'
var FileSaver = require("file-saver");

export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
    MagnificPopupModal,
  },
  mixins: [siteAPI],
  props: ["currentUserCanBook"],
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
      selectedEvent: null,
    };
  },
  computed: {
    heightCalendar() {
      return this.$refs?.fullCalendar.$el.offsetHeight
    }
  },
  methods: {
    selectAllow: function (selectInfo) {
      return moment().diff(selectInfo.start) <= 0;
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
    openShareModal() {
      this.$refs.modal2.open();
    },
    scrollDown: function () {
      let calendarApi = this.$refs.fullCalendar.getApi();

      let currentTimeHour = calendarApi.scrollTime.split(":")[0];

      if (currentTimeHour == "14") {
        return;
      } else {
        let newTime = Number(currentTimeHour);

        newTime += 1;

        if (newTime < 10) {
          newTime = "0" + newTime;
        }

        calendarApi.scrollTime = `${newTime}:00:00`;
        calendarApi.scrollToTime(`${newTime}:00:00`);
      }
    },
    scrollUp: function () {
      let calendarApi = this.$refs.fullCalendar.getApi();
      let currentTimeHour = calendarApi.scrollTime.split(":")[0];

      if (currentTimeHour == "00") {
        return;
      } else {
        let newTime = Number(currentTimeHour);

        newTime -= 1;

        if (newTime < 10) {
          newTime = "0" + newTime;
        }
        calendarApi.scrollTime = `${newTime}:00:00`;
        calendarApi.scrollToTime(`${newTime}:00:00`);
      }
    },
    removeUpDownButtons() {
      console.log('remove')
      const buttons = document.querySelectorAll(".fc-button--arrow");
      buttons.forEach((item) => {
        item.remove();
      }, []);
    },
    injectUpDownButtons() {
      setTimeout(() => {
        const cont = document.querySelector(".fc-body");
        const buttonUp = document.createElement("button");
        buttonUp.classList.add("fc-button--arrow");
        buttonUp.classList.add("fc-button--up");

        buttonUp.innerHTML = "Expand";
        buttonUp.addEventListener("click", this.scrollUp);
        cont.prepend(buttonUp);

        const buttonDown = document.createElement("button");
        buttonDown.classList.add("fc-button--arrow");
        buttonDown.classList.add("fc-button--down");

        buttonDown.innerHTML = "Expand";
        buttonDown.addEventListener("click", this.scrollDown);
        cont.appendChild(buttonDown);
      }, 0);
    },
    goToCurrentDay() {
      let calendarApi = this.$refs.fullCalendar.getApi()
      calendarApi.changeView('timeGridDay',new Date())
      this.injectUpDownButtons()
      calendarApi.scrollTime = "08:00:00";
      calendarApi.scrollToTime("08:00:00");
    },
    viewRender: function (info) {
      if (info.view.type === "timeGridWeek") {
        this.triggerView = "week";

        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.scrollTime = "08:00:00";

        calendarApi.scrollToTime("08:00:00");
      } else if (info.view.type === "timeGridDay") {
        this.injectUpDownButtons();
        this.triggerView = "day";
      } else {
        this.removeUpDownButtons()
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

      if (this.trigger && info.view.type !== 'timeGridDay') {
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
            this.events = [];
            response.data.data.data.forEach((item) => {
              item.lesson.bookingId = item.id;
              this.events.push(item.lesson);
            });

            this.events = this.events.map(function (item) {
              item.title = item.genre.title;
         let userTzOffset = new Date().getTimezoneOffset() * 60 * 1000;

              let lessonTimeZoneObj = countriesAndTimezones.getTimezone(item.timezone_id_name);

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

            if (this.triggerView == "week" || this.triggerView == "day") {
              this.injectUpDownButtons();
            }

            let calendarApi = this.$refs.fullCalendar.getApi();
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
      if (this.heightCalendar > 500) {
        info.el.className = info.el.className + ' big-event'
      } else {
        info.el.className = info.el.className + ' small-event'
      }
      info.el.className = info.el.className + ' test-circle';
      var count =
        parseInt(info.event.extendedProps.spots_count) -
        parseInt(info.event.extendedProps.count_booked);
      info.el.innerHTML =
        info.el.innerHTML +
        '<span class="spot-left">Spots left: ' +
        count +
        "</span>";
    },
    IsDateHasEvent: function (date) {
      var allEvents = this.events;

      if (allEvents.length) {
        var event = allEvents.filter(function (v) {
          return v.date === date;
        });
        return event.length > 0;
      } else {
        return false;
      }
    },
    selected: function (info) {
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.changeView("timeGridWeek");
      calendarApi.gotoDate(info.start);
    },
    dateClick: function (info) {
      let calendarApi = this.$refs.fullCalendar.getApi()
      if(info.view.type === 'timeGridWeek' || info.view.type === 'timeGridDay') {
        this.selectedEvent = {
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
        }
      } else if (info.view.type === 'dayGridMonth') {
        calendarApi.changeView('timeGridDay',moment(info.event.start).format('YYYY-MM-DD'))
        calendarApi.scrollTime = moment(this.events[0].start).format(
          "HH:mm:ss"
        );
        calendarApi.scrollToTime(
          moment(this.events[0].start).format("HH:mm:ss")
        );
      }
    },
    closeModal: function () {
      this.$refs.modal.close();
    },
    updateCalendar: function (lesson) {
      lesson.title = lesson.genre.title;
      lesson.date = moment(lesson.start).format("YYYY-MM-DD");
      this.events.push(lesson);
    },
  },
};
</script>

<style lang='scss'>
.test-circle {
  position: absolute !important;
  bottom: -15px !important;
  left: 50% !important;
  right: 50% !important;
  transform: translate(-50%, -50%) !important;
  background-color: transparent !important;
  border: 1px solid #8ada00 !important;
  width: 70% !important;
}
.custom-button {
  color: #0a0a0a;
  background-color: #8ada00 !important;

  &:hover {
    background-color: #7ac000 !important;
  }
}
</style>
