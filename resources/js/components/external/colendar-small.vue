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
          right: 'timeGridWeek,dayGridMonth',
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
        @eventClick='dateClick'
        @select='selected'
      ></FullCalendar>
      <a
        v-if='isAdmin'
        :href="'/backend/lessons?instructor=' + instructorId"
        class='link-style'
      >View all instructor lessons</a
      >

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

var FileSaver = require('file-saver')

export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
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
  methods: {
    clearFormAndClosePopup() {
    },
    removeIt(id) {
      var temp = null
      this.events.forEach(function (item, key) {
        if (item.id == id) {
          temp = key
        }
      })
      this.events.splice(temp, 1)
    },
    openSend() {
      ``
    },
    scrollDown: function () {
      let calendarApi = this.$refs.fullCalendarSmall.getApi()

      let currentTimeHour = calendarApi.scrollTime.split(':')[0]

      if (currentTimeHour == '14') {
        return
      } else {
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

      if (currentTimeHour == '00') {
        return
      } else {
        let newTime = Number(currentTimeHour)

        newTime -= 1

        if (newTime < 10) {
          newTime = '0' + newTime
        }
        calendarApi.scrollTime = `${ newTime }:00:00`
        calendarApi.scrollToTime(`${ newTime }:00:00`)
      }
    },
    viewRender: function (info) {
      console.log(3)
      if (info.view.type === 'timeGridWeek') {
        console.log(4)
        this.triggerView = 'week'

        let calendarApi = this.$refs.fullCalendarSmall.getApi()

        const cont = document.querySelector('.fc-view-container')

        const buttonUp = document.createElement('button')
        buttonUp.classList.add('fc-button--arrow')
        buttonUp.classList.add('fc-button--up')

        buttonUp.innerHTML = 'Expand'
        buttonUp.addEventListener('click', this.scrollUp)

        cont.prepend(buttonUp)

        const buttonDown = document.createElement('button')
        buttonDown.classList.add('fc-button--arrow')
        buttonDown.classList.add('fc-button--down')

        buttonDown.innerHTML = 'Expand'
        buttonDown.addEventListener('click', this.scrollDown)
        cont.appendChild(buttonDown)

        calendarApi.scrollTime = '08:00:00'

        calendarApi.scrollToTime('08:00:00')
      } else if (info.view.type === 'dayGridMonth') {
        this.triggerView = 'month'
        const buttons = document.querySelectorAll('.fc-button--arrow')

        buttons.forEach((item) => {
          item.remove()
        }, [])
      }
      if (this.triggerOld !== null) {
        console.log(info.view.currentStart, 'zxc')
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
          .get(
            '/api/instructor/' +
            this.instructorId +
            '/lessons?' +
            this.triggerView +
            '=' +
            moment(info.view.currentStart).format('YYYY-MM-DD')
          )
          .then((response) => {
            this.events = response.data.data
            this.events = this.events.map(function (item) {
              item.title = item.genre.title

              let userTzOffset = new Date().getTimezoneOffset() * 60 * 1000

              let lessonTimeZoneObj = countriesAndTimezones.getTimezone(item.timezone_id_name)

              var jan = new Date(0, 1)
              var jul = new Date(6, 1)
              var stdTimezoneOffset = Math.max(
                jan.getTimezoneOffset(),
                jul.getTimezoneOffset()
              )

              var today = new Date()

              var isDstObserved = false

              if (today.getTimezoneOffset() < stdTimezoneOffset) {
                isDstObserved = true
              }

              var _tzOffset = 1

              if (isDstObserved) {
                _tzOffset = lessonTimeZoneObj.dstOffset * 60 * 1000
              } else {
                _tzOffset = lessonTimeZoneObj.utcOffset * 60 * 1000
              }

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

              // console.log(item.date, 'item.date')
              // console.log(item.end, 'item.end')

              return item
            })
            this.events = this.events.filter(function (item) {
              return moment().diff(item.end) <= 0
            })
            console.log(this.events, 'this.events')

            this.loader.hide()
            this.loader = null
          })
          .catch((error) => {
            this.events = []
            this.loader.hide()
            this.loader = null
          })
      }
    },
    eventRender: function (info) {
      console.log(2)
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      if (moment(info.event.start, 'x') <= moment(new Date(), 'x')) {
        info.el.className = info.el.className + ' last-event'
      }
      var count =
        parseInt(info.event.extendedProps.spots_count) -
        parseInt(info.event.extendedProps.count_booked)
      if (count === 1) {
        info.el.className = info.el.className + ' red-event'
      } else if (count === 2) {
        info.el.className = info.el.className + ' yellow-event'
      } else if (count > 2) {
        info.el.className = info.el.className + ' green-event'
      } else {
        info.el.className = info.el.className + ' grey-event'
      }
      info.el.innerHTML =
        info.el.innerHTML +
        '<span class="spot-left">Spots left: ' +
        count +
        '</span>'
      console.log(!this.lessonIdParsed)
      if (!this.lessonIdParsed) {
        const params = urlHelper.parseQueryParams()
        // axios
        //   .get(
        //     "/api/instructor/" +
        //     this.instructorId +
        //     "/lessons?" +
        //     this.triggerView +
        //     "=" +
        //     moment(params.date).format("YYYY-MM-DD")
        //   ).then(res => {
        //     this.events = res.data.data
        // })
        // calendarApi.gotoDate(params.date)
        if (params.lessonId) {
          if (params.lessonId === info.event.id) {
            this.lessonIdParsed = true
            this.dateClick(info)
          }
        }
      }
    },
    selectOverlap: function (event) {
      console.log(event)
      return true
    },
    // selected: function (info) {
    //   console.log(info);
    //   // this.selectRangeTrigger = info;
    // },
    selected: function (info) {
      let calendarApi = this.$refs.fullCalendar.getApi()
      calendarApi.changeView('timeGridWeek')
      calendarApi.gotoDate(info.start)
    },
    dateClick: function (info) {
      this.selectedEvent = {
        id: info.event.id,
        lat: info.event.extendedProps.lat,
        lng: info.event.extendedProps.lng,
        title: info.event.title,
        fullDate:
          moment(info.event.start).format('MMM') +
          ' ' +
          moment(info.event.start).format('DD') +
          ', ' +
          moment(info.event.start).format('hh:mma') +
          ' - ' +
          moment(info.event.end).format('hh:mma'),
        location: info.event.extendedProps.location,
        price: info.event.extendedProps.spot_price,
        students: info.event.extendedProps.students,
        content: info.event.extendedProps
      }

    },
    closeModal: function () {
      this.$refs.modal.close()
    },
    downloadExportedFile: function () {
      if (this.loader == null) {
        this.loader = this.$loading.show({
          zIndex: 9999999
        })
      }
      let calendarApi = this.$refs.fullCalendarSmall.getApi()
      // this.scrollTime1 = moment(this.events[0].start).count('hh:mm:ss');
      let currentTime = moment(calendarApi.view.currentStart).format(
        'YYYY-MM-DD'
      )
      calendarApi.scrollTime = moment(this.events[0].start).format('HH:mm:ss')
      calendarApi.scrollToTime(moment(this.events[0].start).format('HH:mm:ss'))
      return axios
        .get('/api/instructor/lessons/export?month=' + currentTime, {
          responseType: 'blob'
        })
        .then((response) => {
          FileSaver.saveAs(response.data, 'lessons-list.xlsx')
          this.loader.hide()
          this.loader = null
        })
        .catch((error) => {
          this.loader.hide()
          this.loader = null
        })
      //				this.$http.get('/api/lessons/export', {responseType: 'arraybuffer'})
      //					.then(response => {
      //						    this.downloadFile(response, 'lessons-list', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '.xlsx')
      //                        }, response => {
      //                            // Manage errors
      //                        }
      //			        );
    },
  },
  mounted() {
    let calendarApi = this.$refs.fullCalendarSmall.getApi()
    const params = urlHelper.parseQueryParams()
    if (params.date) {
      calendarApi.gotoDate(params.date)
      axios
        .get(
          "/api/instructor/" +
          this.instructorId +
          "/lessons?" +
          this.triggerView +
          "=" +
          moment(params.date).format("YYYY-MM-DD")
        ).then(res => {
          this.events = res.data.data
      })
    }
  }
}
</script>
