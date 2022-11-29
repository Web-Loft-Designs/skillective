<template>
  <div id="notification-form-container">
    <magnific-popup-modal
      @close="clearFormAndClosePopup"
      class="notification-form"
      :class="{ 'simple-mode': mode === 'simple' }"
      :show="false"
      :config="{
        closeOnBgClick: true,
        showCloseBtn: true,
        enableEscapeKey: false,
      }"
      ref="modalNotification"
    >
      <div v-if="usersToNotify.length == 0">No users to notify selected</div>
      <div v-if="usersToNotify.length > 0" class="w-100">
        <div v-show="mode !== 'simple'" class="calender-form">
          <div class="calendar-component">
            <div class="calendar-component-top">
              <h2>Choose lesson</h2>
            </div>
            <div class="calendar-component-body">
              <FullCalendar
                :header="{
                  left: 'prev',
                  center: 'title',
                  right: 'next',
                }"
                :footer="{
                  right: 'timeGridDay,timeGridWeek,dayGridMonth',
                }"
                :showNonCurrentDates="false"
                :fixedWeekCount="false"
                :firstDay="0"
                :defaultView="'dayGridMonth'"
                :navLinks="true"
                :nowIndicator="true"
                :plugins="calendarPlugins"
                :events="events"
                :dayRender="dayRender"
                :eventRender="eventRender"
                :eventTimeFormat="eventTimeFormat"
                :selectable="false"
                :datesRender="viewRender"
                @eventClick="dateClick"
                ref="fullCalendarModal"
              ></FullCalendar>
            </div>
          </div>
        </div>
        <div class="block-form">
          <h2 v-if="usersToNotify.length == 1">
            Notify {{ usersToNotify[0].full_name }}
          </h2>
          <h2 v-else>
            Notify {{ usersToNotify.length }}
            {{ usersToNotify.length == 1 ? "client" : "clients" }}
          </h2>

          <div class="row">
            <div
              class="form-group has-feedback"
              :class="{ 'has-error': errors.message }"
            >
              <label>Message</label>
              <textarea
                class="form-control"
                name="message"
                placeholder
                v-model="fields.message"
              ></textarea>
              <span class="help-block" v-if="errors.message">
                <strong>{{ errors.message[0] }}</strong>
              </span>
            </div>
            <label v-if="!isStudent" class="col-12">Notify via</label>
            <div  v-if="!isStudent" class="form-group d-flex flex-wrap checkbox-wrapper has-feedback" :class="{ 'has-error' : errors.notification_methods }">

            <div class="field mr-4 mb-2" v-for="(notificationMethodName, notificationMethodKey) in availableNotificationMethods">
            <label :for="'method-'+notificationMethodKey" v-if="notificationMethodVisible(notificationMethodKey)">
            <input v-model="fields.notification_methods" type="checkbox" :id="'method-'+notificationMethodKey" :value="notificationMethodKey">
            <span class="checkmark"></span>
             - {{ notificationMethodName.replace(' Messages', '') }}
            </label>
            </div>

            <span class="help-block" v-if="errors.notification_methods">
            <strong>{{ errors.notification_methods[0] }}</strong>
            </span>
            </div>

            <div v-if="errorText" class="has-error col-12">{{ errorText }}</div>
            <div v-if="successText" class="has-success col-12">
              {{ successText }}
            </div>
            <div class="col-12">
              <span @click="sendNotifications" class="btn btn-default btn-block"
                >Send
                {{
                  usersToNotify.length == 1 ? "Notification" : "Notifications"
                }}</span
              >
            </div>
            <!--<div class="col-xs-4">-->
            <!--<span class="btn btn-danger" @click="clearFormAndClosePopup">Close Form</span>-->
            <!--</div>-->
          </div>
        </div>
      </div>
    </magnific-popup-modal>
  </div>
</template>

<script>
import siteAPI from "../mixins/siteAPI.js";
import MagnificPopupModal from "./external/MagnificPopupModal";
import $ from "jquery";
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import shareHelper from "../helpers/shareHelper";

export default {
  props: [
    "availableNotificationMethods",
    "mode",
    "isStudent",
    "upcomingLesson",
    "upcomingLessons",
    "instructorId",
  ],
  mixins: [siteAPI],
  data() {
    return {
      recepientsAvailableNotificationMethods: [],
      usersToNotify: [],
      fields: this.getInitialFieldsData(),
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
      lessonInfo: null,
    };
  },
  components: {
    FullCalendar,
    MagnificPopupModal,
  },
  methods: {
    notificationMethodVisible(notificationMethodKey){
    	return this.recepientsAvailableNotificationMethods.indexOf(notificationMethodKey)!==-1
    },
    getInitialFieldsData() {
      return {
        notification_methods: [],
        message: "",
        lesson: null,
        lesson_id: null,
      };
    },
    sendNotifications() {
      let _usersToNotify = [];
      for (var i = 0; i < this.usersToNotify.length; i++)
        _usersToNotify.push(this.usersToNotify[i].id);

      let requestParams = {};

      requestParams.users = _usersToNotify;
      requestParams.lesson_id = this.fields.lesson_id;
      requestParams.message = this.fields.message;

      this.apiPost("/api/send-notifications", requestParams);
    },
    componentHandlePostResponse(responseData) {
      this.clearFormSubmitPopup();
    },
    openPopup() {
      setTimeout(() => {
        this.$refs.modalNotification.open();
      }, 100);
    },
    clearFormAndClosePopup() {
      this.usersToNotify = [];
      this.successText = null;
      this.errorText = null;
      this.errors.message = null;
      // this.errors.notification_methods = null;
      this.fields = this.getInitialFieldsData();
      this.$refs.modalNotification.close();
    },
    clearFormSubmitPopup() {
      this.errorText = null;
      this.errors.message = null;
      // this.errors.notification_methods = null;
      this.fields = this.getInitialFieldsData();
    },
    dayRender: function (dayRenderInfo) {
      if (this.IsDateHasEvent(dayRenderInfo.el.dataset.date)) {
        dayRenderInfo.el.classList.add("has-event");
        $('[data-date="' + dayRenderInfo.el.dataset.date + '"]').addClass(
          "has-event"
        );
      }
      $(".fc-past").addClass("disabled");
    },
    viewRender: function (info) {
      var currentEvent = this.events;
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
            "/api/instructor/lessons?" +
              this.triggerView +
              "=" +
              moment(info.view.currentStart).format("YYYY-MM-DD")
          )
          .then((response) => {
            this.events = response.data.data;
            this.events.forEach(function (item) {
              item.title = item.genre.title;
              item.date = moment(item.start).format("YYYY-MM-DD");
              $('[data-date="' + item.date + '"]').addClass("has-event");
            });
            this.events = this.events.filter(function (item) {
              return moment().diff(item.end) <= 0;
            });

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
      info.el.innerHTML =
        info.el.innerHTML +
        '<span class="spot-left">Spots left: ' +
        count +
        "</span>";
      let calendarApi = this.$refs.fullCalendarModal.getApi();
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
    dateClick: function (info) {
      let { location, genre } = info.event.extendedProps;

      const instructorId = info.event.extendedProps.instructor.id;
      this.fields.lesson = info.event;
      var instaName = info.event.extendedProps.instructor.full_name;
      var date = moment(info.event.start).format("MM/DD/YYYY");
      var title = " at " + info.event.title;
      location = location ? location : "Virtual lesson";
      genre = genre.title;
      var time =
        "From " +
        moment(info.event.start).format("hh:mma") +
        " To " +
        moment(info.event.end).format("hh:mma");
      var price = " ($" + info.event.extendedProps.spot_price + ")";
      this.fields.message =
        instaName +
        "\nHas just opened time on their calendar for" +
        "\n" +
        genre +
        "\nLocation: " +
        location.replace(/<[^>]*>?/gm, "") +
        "\nDate: " +
        date +
        "\nTime: " +
        time +
        "\nPrice: " +
        price + (instructorId ? (
            "\nMore info: " +
            shareHelper.buildShareLink(instructorId, info.event.id)
          ) : ""
        );
      this.fields.lesson_id = info.event.id;
    },
  },
  created() {
    this.recepientsAvailableNotificationMethods = this.availableNotificationMethods;
  },
  mounted() {
        if (this.upcomingLesson) {
          const currentLesson = this.upcomingLesson;

          if (currentLesson) {
            this.fields.message = `${currentLesson.instructor.full_name}
    Has just opened time on their calendar for:
    ${currentLesson.genre.title}
    ${
      currentLesson.location
        ? "Location: " + currentLesson.location
        : "Virtual Lesson"
    }
    Date: ${moment(currentLesson.start).format("MM/DD/YYYY")}
    Time: ${moment(currentLesson.end).format("hh:mma")} To ${moment(
              currentLesson.start
            ).format("hh:mma")}
    Price: ($${currentLesson.spot_price})`;
          }
        }

    if (this.mode !== "simple") {
      this.$root.$on("initNotificationsForm", (usersToNotify) => {
        this.usersToNotify = usersToNotify;

        if(usersToNotify.length==1){
        	var userMethods = [];
        	for (var prop in this.usersToNotify[0].profile.notification_methods){;
        		if (this.usersToNotify[0].profile.notification_methods.hasOwnProperty(prop))
        			userMethods.push(this.usersToNotify[0].profile.notification_methods[prop]);
        	}
        	this.recepientsAvailableNotificationMethods = userMethods;
        }

        axios.get('/api/instructor/lessons?month='+moment().format('YYYY-MM-DD'))
            .then(response => {
                this.events = response.data.data;
                this.events.forEach(function(item) {
                    item.title = item.genre.title;
                    item.date = moment(item.start).format('YYYY-MM-DD');
                    $('[data-date="'+item.date+'"]').addClass('has-event');
                });
                this.events = this.events.filter(function (item) {
                    return moment().diff(item.end) <= 0
                })

                this.loader.hide();
                        this.loader = null;
            }).catch(error => {
            this.events = [];
            this.loader.hide();
            //         this.loader = null;
        });

        this.openPopup();
      });
    } else {
      this.$root.$on("initNotificationsForm", (usersToNotify) => {
        usersToNotify.map((item) => {
          this.usersToNotify.push({ id: item });
        });

        if(usersToNotify.length==1){
           var userMethods = [];
           console.log(this.usersToNotify)
           for (var prop in this.usersToNotify[0].profile.notification_methods){
        		if (this.usersToNotify[0].profile.notification_methods.hasOwnProperty(prop))
        		    userMethods.push(this.usersToNotify[0].profile.notification_methods[prop]);
           }
        	this.recepientsAvailableNotificationMethods = userMethods;
        }
        this.fields.message = "";
        this.openPopup();
      });
    }
  },
};
</script>

<style scoped>
.notification-form .calender-form{
  max-width: 100% !important;
  margin-top: 50px;
}
</style>