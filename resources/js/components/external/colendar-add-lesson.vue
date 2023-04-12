<template>
  <div class='calendar-component calendar-component-instructor'>
    <div class='calendar-component-top'>
      <h2>
        Schedule
        <button
          type='button'
          @click='openPopup'
        >
          <img
            alt
            src='/images/plus-icon.svg'
          />
          <span>Add Time</span>

          <div class='tolltip bottom'>
            click here to add additional time slots in increments that you wish
            to be booked. Most lessons are 30-mins or an hour!
          </div>
        </button>

        <button
          type='button'
          @click='downloadExportedFile()'
        >
          <img
            alt=''
            src='/images/upload-btn.svg'
          />
          <span>Export</span>
        </button>

        <button
          type='button'
          @click='openEmbedPopup'
        >
          <span> Embed Calendar </span>
        </button>
      </h2>

      <div
        v-if='errorText'
        class='has-error'
      >
        {{ errorText }}
      </div>
      <div
        v-if='successText'
        class='has-success'
      >
        {{ successText }}
      </div>
    </div>
    <div class='calendar-component-body'>
      <FullCalendar
        ref='fullCalendar'
        :datesRender='viewRender'
        :defaultView="'dayGridMonth'"
        :displayEventTime='false'
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
        :selectAllow='selectAllow'
        :selectConstraint="{
          startTime: '00:00',
          endTime: '24:00',
        }"
        :selectOverlap='selectOverlap'
        :selectable='true'
        :showNonCurrentDates='false'
        slotDuration='00:15:00'
        slotLabelInterval='01:00'
        @eventClick='dateClick'
        @eventMouseEnter='eventMouseEnter'
        @select='selected'
      ></FullCalendar>

      <send-notification-form
        :is-student='false'
        :mode="'simple'"
        v-bind:available-notification-methods='availableNotificationMethods'
      ></send-notification-form>

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

      <magnific-popup-modal
        ref='modalEmbed'
        :config='{
          closeOnBgClick: true,
          showCloseBtn: true,
          enableEscapeKey: false,
        }'
        :show='false'
        class='embed-popup'
        @close='clearFormAndClosePopup'
      >
        <div class='embed-popup-inner'>
          <span class='title'> How to embed calendar </span>

          <span class='code-preview'> Add this code in head tag </span>
          <code v-pre>
            &lt;link rel="stylesheet"
            href="https://skillective.com/external-callendar/css/app.css" &gt;
          </code>
          <button
            class='btn btn-primary btn-block'
            @click="
              copyTextToClipboard(
                '&lt;link rel=&quot;stylesheet&quot;  href=&quot;https://skillective.com/external-callendar/css/app.css&quot;  &gt;'
              )
            "
          >
            Copy code
          </button>

          <span class='code-preview'> Add this code in end of body tag </span>

          <code v-pre>
            &lt;script
            src="https://skillective.com/external-callendar/main.js"&gt;
            &lt;script&gt;
          </code>

          <button
            class='btn btn-primary btn-block'
            @click="
              copyTextToClipboard(
                '&lt;script src=&quot;https://skillective.com/external-callendar/main.js&quot;&gt; &lt;script&gt;'
              )
            "
          >
            Copy code
          </button>

          <span class='code-preview'>
            And insert this tag in place where you want embed calendar
          </span>
          <code>
            &lt;div class="skillective-instructor__{{ instructorId }}"
            id="skillective-external-callendar"&gt;&lt;/div&gt;
          </code>

          <button
            class='btn btn-primary btn-block'
            @click="
              copyTextToClipboard(
                `<div class='&quot;skillective-instructor__4&quot;' id='&quot;skillective-external-callendar&quot;'></div>`
              )
            "
          >
            Copy code
          </button>
        </div>
      </magnific-popup-modal>
    </div>
  </div>
</template>


<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import MagnificPopupModal from './MagnificPopupModal'
import siteAPI from '../../mixins/siteAPI.js'
import moment from 'moment-timezone'
import countriesAndTimezones from 'countries-and-timezones'
import { mapMutations } from 'vuex'
import urlHelper from '../../helpers/urlHelper'

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
    'availableNotificationMethods',
    'instructorId',
    'currentUserCanBook'
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
      selectedEvent: null,
      prevViewInfo: null
    }
  },
  created: function () {
    this.$root.$on('createdLessons', () => {
      this.getLessonsByTriggerView()
    })
  },
  computed: {
    heightCalendar() {
      return this.$refs?.fullCalendar.$el.offsetHeight
    }
  },
  methods: {
    ...mapMutations(['SET_SELECTED_DATES']),
    copyTextToClipboard(text) {
      var textArea = document.createElement('input')

      textArea.style.padding = 0
      textArea.style.position = 'fixed'
      textArea.style.zIndex = 999999999
      // Clean up any borders.
      textArea.style.border = 'none'
      textArea.style.outline = 'none'
      textArea.style.boxShadow = 'none'

      // Avoid flash of the white box if rendered for any reason.
      textArea.style.background = 'transparent'

      textArea.textContent = text
      textArea.value = text

      document.querySelector('.embed-popup-inner').appendChild(textArea)

      textArea.focus()
      textArea.select()

      try {
        var successful = document.execCommand('copy')

        var msg = successful ? 'successful' : 'unsuccessful'
        console.log('Copying text command was ' + msg)
      } catch (err) {
        console.log('Oops, unable to copy')
      }

      document.querySelector('.embed-popup-inner').removeChild(textArea)
    },
    openPopup() {
      this.$root.$emit('openAddLessonModal')
    },
    openEmbedPopup() {
      this.$refs.modalEmbed.open()
    },
    clearFormAndClosePopup() {
    },
    openSend(client) {
      this.$root.$emit('initNotificationsForm', client)
    },
    scrollDown: function () {
      let calendarApi = this.$refs.fullCalendar.getApi()

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
      let calendarApi = this.$refs.fullCalendar.getApi()

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
    removeUpDownButtons() {
      const buttons = document.querySelectorAll('.fc-button--arrow')
      buttons.forEach((item) => {
        item.remove()
      }, [])
    },
    injectUpDownButtons() {
      setTimeout(() => {
        const cont = document.querySelector('.fc-body')

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
      }, 0)
    },
    viewRender: function (info) {
      if (info.view.type === 'timeGridWeek') {
        this.triggerView = 'week'

        let calendarApi = this.$refs.fullCalendar.getApi()

        this.injectUpDownButtons()

        calendarApi.scrollTime = '08:00:00'

        calendarApi.scrollToTime('08:00:00')
      } else if (info.view.type === 'timeGridDay') {
        this.triggerView = 'day'
      } else {
        this.triggerView = 'month'

        this.removeUpDownButtons()
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
        this.getLessonsByTriggerView(info)
      }
    },
    getLessonsByTriggerView: function (info) {
      if (!info) {
        info = this.prevViewInfo
      }

      this.prevViewInfo = info

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
          this.events = this.events.map((item) => {
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

            return item
          })

          this.events = this.events.filter(function (item) {
            return moment().diff(item.end) <= 0
          })

          if (this.triggerView == 'week' || this.triggerView == 'day') {
            this.injectUpDownButtons()
          }
          let calendarApi = this.$refs.fullCalendar.getApi();
          calendarApi.scrollTime = moment(this.events[1].start).format(
            "HH:mm:ss"
          );
          calendarApi.scrollToTime(
            moment(this.events[1].start).format("HH:mm:ss")
          );
          this.loader.hide()
          this.loader = null
        })
        .catch((error) => {
          this.events = []
          this.loader.hide()
          this.loader = null
        })
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
      var count =
        parseInt(info.event.extendedProps.spots_count) -
        parseInt(info.event.extendedProps.count_booked)
      // if (count === 1) {
      //   1
      //   info.el.className = info.el.className + ' red-event'
      // } else if (count === 2) {
      //   info.el.className = info.el.className + ' yellow-event'
      // } else if (count > 2) {
      //   info.el.className = info.el.className + ' green-event'
      // } else {
      //   info.el.className = info.el.className + ' grey-event'
      // }

      const elementBoundingRect = info.el.getBoundingClientRect()

      // console.log(info.el);

      info.el.innerHTML =
        info.el.innerHTML +
        '<span class="spot-left">Spots left: ' +
        count +
        '</span>' +
        `<div class='calendar-tooltip'>
            <span class='genre' > ${ info.event.extendedProps.genre.title } </span>
            <span class='type' >  Lesson Type ${ info.event.extendedProps.lesson_type } </span>
            <span class='note-title'> Note </span>
            <span class='note-content'> ${ info.event.extendedProps.description } </span>
            <span class='price'> Price $${ info.event.extendedProps.spot_price } per lesson </span>
      </div>`
    },
    eventMouseEnter: function ({ el }) {
      if (!el) {
        return
      }
      const wrapper = document.querySelector('.fc-slats')

      if (!wrapper) {
        return
      }
      const elementBoundingRect = el.getBoundingClientRect()
      const wrapperBoudingRect = wrapper.getBoundingClientRect()
      const tooltip = el.querySelector('.calendar-tooltip')

      if (!tooltip) {
        return
      }

      if (elementBoundingRect.right > wrapperBoudingRect.right - 100) {
        tooltip.style.right = 0
      }

      if (elementBoundingRect.left < wrapperBoudingRect.left + 100) {
        tooltip.style.left = 0
      }

      if (elementBoundingRect.top < wrapperBoudingRect.top + 300) {
        tooltip.style.bottom = 'auto'
        tooltip.style.top = 'calc(100% + 12px)'
      }
    },
    selectOverlap: function (event) {
      return true
    },
    selected: function (info) {
      let calendarApi = this.$refs.fullCalendar.getApi()
      calendarApi.changeView('timeGridWeek')
      calendarApi.gotoDate(info.start)
      const dates = {
        start: info.start,
        end: info.end,
        type: info.view.type
      }
      this.SET_SELECTED_DATES(dates)
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
        axios.get('/api/lesson/' + info.event.id).then(({ data }) => {
          this.$root.$emit('lessonUpdateInit', data.data.data)
        })
      }else if (info.view.type === 'dayGridMonth') {
        calendarApi.changeView('timeGridDay',moment(info.event.start).format('YYYY-MM-DD'))
      }

    },
    closeModal: function () {
      this.$refs.modal.close()
    },
    updateCalendar: function (lesson) {
      lesson.title = lesson.genre.title
      lesson.date = moment(lesson.start).format('YYYY-MM-DD')
      this.events.push(lesson)
    },

    selectAllow: function (selectInfo) {
      return moment().diff(selectInfo.start) <= 0
    },
    downloadExportedFile: function () {
      if (this.loader == null) {
        this.loader = this.$loading.show({
          zIndex: 9999999
        })
      }
      let calendarApi = this.$refs.fullCalendar.getApi()
      let currentTime = moment(calendarApi.view.currentStart).format(
        'YYYY-MM-DD'
      )
      // calendarApi.scrollTime = moment(this.events[0].start).format("HH:mm:ss");
      // calendarApi.scrollToTime(moment(this.events[0].start).format("HH:mm:ss"));
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
    }
  }
}
</script>
<style lang='scss'>
.fc-day-grid-event {
  cursor: pointer;
}
.big-event {
  height: 82px !important;
}

.small-event {
  height: 45px !important;
}
</style>