<template>
  <div class='calendar-component'>
    <div class='calendar-component-top'>
      <h2>Schedule</h2>
      <div v-if='errorText' class='has-error'>{{ errorText }}</div>
      <div v-if='successText' class='has-success'>{{ successText }}</div>
    </div>
    <div class='calendar-component-body'>
      <FullCalendar
        ref='fullCalendarSmall'
        :datesRender='viewRender'
        :defaultView="'dayGridMonth'"
        :eventRender='eventRender'
        :eventTimeFormat='eventTimeFormat'
        :events='events'
        :firstDay='0'
        :fixedWeekCount='false'
        :footer="{
          right: 'today,timeGridWeek,dayGridMonth',
        }"
        :header="{
          left: 'prev',
          center: 'title',
          right: 'next',
        }"
        :navLinks='true'
        :nowIndicator='true'
        :plugins='calendarPlugins'
        :showNonCurrentDates='false'
        slotDuration='00:15:00'
        slotLabelInterval='01:00'
        :selectConstraint="{
          startTime: '00:00',
          endTime: '24:00',
        }"
        @eventClick='dateClick'
        @select='selected'
        timeZone="UTC"
      ></FullCalendar>
      <a
        v-if='isAdmin'
        :href="'/backend/lessons?instructor=' + instructorId"
        class='link-style'
      >
        View all instructor lessons
      </a>
      <magnific-popup-modal
        ref='modal'
        :config='{
          closeOnBgClick: true,
          showCloseBtn: true,
          enableEscapeKey: false,
        }'
        :show='false'
        class='modal-lesson-info'
        @close='clearFormAndClosePopup'
      >
        <lesson-details-modal
          v-if='selectedEvent != null'
          :current-user-can-book='currentUserCanBook'
          :lesson-details='selectedEvent'
          :logged-in-as-student='loggedInAsStudent'
          :modal-window='this.$refs.modal'
          :show-notify-btn='true'
          :student-cancel='false'
          :student-list='studentList'
          @notification='openSend'
          @lessons-cancelled='removeIt'
        />
      </magnific-popup-modal>
      <magnific-popup-modal
        ref='modal2'
        :config='{
          closeOnBgClick: true,
          showCloseBtn: true,
          enableEscapeKey: false,
        }'
        :show='false'
        class='panding-popup'
        @close='clearFormAndClosePopup'
      >
        <panding-popup :modal-window='this.$refs.modal2'></panding-popup>
      </magnific-popup-modal>
    </div>
  </div>
</template>

<script>
import urlHelper from '../../helpers/urlHelper'
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import MagnificPopupModal from './MagnificPopupModal'
import siteAPI from '../../mixins/siteAPI.js'
import countriesAndTimezones from 'countries-and-timezones'
import moment from 'moment-timezone'

export default {
  components: {
    FullCalendar,
    MagnificPopupModal
  },
  mixins: [siteAPI],
  props: [
    'lessons',
    'userGenres',
    'siteGenres',
    'isAdmin',
    'instructorId',
    'studentList',
    'currentUserCanBook',
    'loggedInAsStudent'
  ],
  data() {
    return {
      calendarPlugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      eventTimeFormat: {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true,
        meridiem: 'short'
      },
      selectRangeTrigger: null,
      events: [],
      trigger: true,
      triggerOld: null,
      triggerView: 'month',
      scrollTime1: '0:00:00',
      selectedEvent: null,
      lessonIdParsed: false
    }
  },
  mounted() {
    let calendarApi = this.$refs.fullCalendarSmall.getApi()
    const params = urlHelper.parseQueryParams()
    if (params.date) {
      calendarApi.gotoDate(params.date)
      axios
        .get(
          '/api/instructor/' +
          this.instructorId +
          '/lessons?' +
          this.triggerView +
          '=' +
          moment(params.date).format('YYYY-MM-DD')
        ).then(res => {
        this.events = res.data.data
      })
    }
  },
  computed: {
    heightCalendar() {
      return this.$refs?.fullCalendarSmall.$el.offsetHeight
    }
  },
  methods: {
    clearFormAndClosePopup() {
    },
    removeIt(id) {
      let temp = null
      this.events.forEach(function (item, key) {
        if (item.id === id) temp = key
      })
      this.events.splice(temp, 1)
    },
    openSend() {
      ``
    },
    scrollDown: function () {
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      let currentTimeHour = calendarApi.scrollTime.split(':')[0]
      if (currentTimeHour != '14') {
        let newTime = Number(currentTimeHour)
        newTime += 1
        if (newTime < 10) {
          newTime = '0' + newTime
        }
        calendarApi.scrollTime = `${ newTime }:00:00`
        calendarApi.scrollToTime(`${ newTime }:00:00`)
      }
    },
    scrollUp: function () {
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      let currentTimeHour = calendarApi.scrollTime.split(':')[0]
      if (currentTimeHour != '00') {
        let newTime = Number(currentTimeHour)
        newTime -= 1
        if (newTime < 10) {
          newTime = '0' + newTime
        }
        calendarApi.scrollTime = `${ newTime }:00:00`
        calendarApi.scrollToTime(`${ newTime }:00:00`)
      }
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
    viewRender: function (info) {
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      if (info.view.type === 'timeGridWeek' || info.view.type === 'timeGridDay') {
        this.triggerView = 'week'
        calendarApi.scrollTime = "08:00:00"
        this.injectUpDownButtons()
      } else if (info.view.type === 'dayGridMonth') {
        this.triggerView = 'month'
        const buttons = document.querySelectorAll('.fc-button--arrow')
        buttons.forEach((item) => {
          item.remove()
        }, [])
      } else if(info.view.type === "timeGridDay") {
        this.injectUpDownButtons()
        this.triggerView = "day"
      }
      if (this.triggerOld !== null) {
        if (
          this.triggerOld !==
          moment(info.view.currentStart).format('YYYY-MM-DD')
        ) {
          this.trigger = true
          this.triggerOld = moment(info.view.currentStart).format('YYYY-MM-DD')
        } else {
          this.trigger = false
        }
      } else {
        this.triggerOld = moment(info.view.currentStart).format('YYYY-MM-DD')
      }
      if (this.trigger) {
        this.trigger = false
        if (this.loader == null) {
          this.loader = this.$loading.show({
            zIndex: 9999999
          })
        }
        axios
          .get(`/api/instructor/${ this.instructorId }/lessons?${ this.triggerView }=${ moment(info.view.currentStart).format('YYYY-MM-DD') }`)
          .then((response) => {
            this.events = response.data.data
            this.events = this.events.map(function (item) {
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
            this.events = this.events.filter(function (item) {
              return moment().diff(item.end) <= 0
            })

            if (this.triggerView == "week" || this.triggerView == "day") {
              this.injectUpDownButtons();
            }

            calendarApi.scrollToTime(
              moment(this.events[0].start).format("HH:mm:ss")
            )
            this.loader.hide()
            this.loader = null
          })
          .catch(() => {
            this.events = []
            this.loader.hide()
            this.loader = null
          })
      }
    },
    eventRender: function (info) {
      if (moment(info.event.start, 'x') <= moment(new Date(), 'x')) {
        info.el.className = info.el.className + ' last-event'
      }
      if (this.heightCalendar > 500) {
        info.el.className = info.el.className + ' big-event'
      } else {
        info.el.className = info.el.className + ' small-event'
      }
      info.el.className = info.el.className + ' test-circle'
      // let count =
      //   parseInt(info.event.extendedProps.spots_count) -
      //   parseInt(info.event.extendedProps.count_booked)
      // if (count === 1) {
      //   info.el.className = info.el.className + ' red-event'
      // } else if (count === 2) {
      //   info.el.className = info.el.className + ' yellow-event'
      // } else if (count > 2) {
      //   info.el.className = info.el.className + ' green-event'
      // } else {
      //   info.el.className = info.el.className + ' grey-event'
      // }
      // info.el.innerHTML =
      //   info.el.innerHTML +
      //   '<span class="spot-left">Spots left: ' +
      //   count +
      //   '</span>'
      if (!this.lessonIdParsed) {
        const params = urlHelper.parseQueryParams()
        if (params.lessonId) {
          if (params.lessonId === info.event.id) {
            this.lessonIdParsed = true
            this.dateClick(info)
          }
        }
      }
    },
    selectOverlap: function () {
      return true
    },
    selected: function (info) {
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      calendarApi.changeView('timeGridWeek')
      calendarApi.gotoDate(info.start)
    },
    dateClick: function (info) {
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      if(info.view.type === 'timeGridWeek' || info.view.type === 'timeGridDay') {
        this.selectedEvent = {
          id: info.event.id,
          lat: info.event.extendedProps.lat,
          lng: info.event.extendedProps.lng,
          title: info.event.title,
          fullDate: `${ moment(info.event.start).format('MMM') } ${ moment(info.event.start).format('DD') }, ${ moment(info.event.start).format('hh:mma') } - ${ moment(info.event.end).format('hh:mma') }`,
          location: info.event.extendedProps.location,
          price: info.event.extendedProps.spot_price,
          students: info.event.extendedProps.students,
          content: info.event.extendedProps
        }
      } else if(info.view.type === 'dayGridMonth') {
        calendarApi.changeView('timeGridDay',moment(info.event.start).format('YYYY-MM-DD'))
      }
    },
    closeModal: function () {
      this.$refs.modal.close()
    }
  }
}
</script>
