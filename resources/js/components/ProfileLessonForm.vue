<template>
  <div id='lesson-form-container'>
    <magnific-popup-modal
      class='ie-fix'
      @modalClosed='clearFormAndClosePopup'
      :show='false'
      :config='{
        closeOnBgClick: true,
        showCloseBtn: true,
        enableEscapeKey: false,
      }'
      ref='modal'
    >
      <div class='modal-add-lesson'>
        <form method='post' @keypress.enter.prevent @submit.prevent='onSubmit'>
          <div class='row'>
            <h2 v-if='!fields.id' class='login-box-msg col-12'>
              Add Lesson Time
            </h2>
            <h2 v-else class='login-box-msg col-12'>Edit Lesson</h2>

            <div class="form-group col-12" v-if="fields.id">
              <label>Share link</label>
              <copy-input :value="shareLink" readonly />
            </div>

            <div
              class='col-12 form-group has-feedback'
              :class="{ 'has-error': errors.genre }"
            >
              <label>Genre</label>
              <select class='form-control' name='genre' v-model='fields.genre'>
                <option value></option>
                <option
                  v-for='(genre, key) in formGenres'
                  :key='key'
                  :value='genre.id'
                >
                  {{ genre.title }}
                </option>
              </select>
              <span class='help-block' v-if='errors.genre'>
                <strong>{{ errors.genre[0] }}</strong>
              </span>
            </div>
            <div
              class='form-group col-4 has-feedback'
              :class="{ 'has-error': errors.lesson_type }"
            >
              <label>Type of Lesson</label>
              <select class='form-control' v-model='fields.lesson_type'>
                <option
                  v-for='(lessonTypeTitle, lessonTypeName) in lessonTypes'
                  :value='lessonTypeName'
                  :key='lessonTypeName'
                >
                  {{ lessonTypeTitle }}
                </option>
              </select>
              <span class='help-block' v-if='errors.lesson_type'>
                <strong>{{ errors.lesson_type[0] }}</strong>
              </span>
            </div>
            <div
              class='form-group col-12 has-feedback'
              :class="{ 'has-error': errors.location }"
              v-if="fields.lesson_type === 'in_person'"
            >
              <label>Location</label>
              <input
                type='text'
                class='form-control'
                name='location'
                value
                v-model='fields.location'
                ref='lessonLocation'
              />
              <span class='help-block' v-if='errors.location'>
                <strong>{{ errors.location[0] }}</strong>
              </span>
            </div>
            <div
              class='col-12 form-group has-feedback'
              :class="{ 'has-error': errors.timezone_id }"
              v-if="fields.lesson_type === 'virtual'"
            >
              <label>Time Zone</label>
              <select class='form-control' v-model='fields.timezone_id'>
                <option value disabled>Select...</option>
                <option
                  v-for='(value, key) in timeZomeOptions'
                  :value='key'
                  :key='key'
                >
                  {{ value }}
                </option>
              </select>
              <span class='help-block' v-if='errors.timezone_id'>
                <strong>{{ errors.timezone_id[0] }}</strong>
              </span>
            </div>

            <div
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.date }"
            >

              <label>Date</label>
              <!--<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" v-model="fields.date" name="date" :placeholder="'mm/dd/yyyy'" :format="'MM/dd/yyyy'"></datepicker>-->

              <dropdown-datepicker
                v-if='isDateInputInit'
                display-format='mdy'
                v-model='fields.date'
                submit-format='yyyy-mm-dd'
                key='sombun3'
                ref='datepicker'
                :minYear='2021'
                maxDate='2030-01-01'
              ></dropdown-datepicker>

              <span class='help-block' v-if='errors.date'>
                <strong>{{ errors.date[0] }}</strong>

              </span>
            </div>

            <!--            <div class='col-12 form-group has-feedback'>-->
            <!--              <toggle-button-->
            <!--                :value='isOvernight'-->
            <!--                :color="{ checked: '#a94442', unchecked: '#01bd00' }"-->
            <!--                :sync='true'-->
            <!--                :labels="{-->
            <!--                  checked: 'Disable overnight',-->
            <!--                  unchecked: 'Enable overnight',-->
            <!--                }"-->
            <!--                @change='toggleOvernight'-->
            <!--                :font-size='14'-->
            <!--                :height='38'-->
            <!--                :width='186'-->
            <!--              />-->
            <!--            </div>-->

            <div
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.date }"
              v-if='isOvernight'
            >
              <label>Date to</label>
              <dropdown-datepicker
                v-if='isDateInputInit'
                display-format='mdy'
                v-model='fields.date_to'
                submit-format='yyyy-mm-dd'
                key='sombun5'
                ref='datepickerTo'
                :minYear='2021'
                maxDate='2030-01-01'
              ></dropdown-datepicker>

              <span class='help-block' v-if='errors.date_to'>
                <strong>{{ errors.date_to[0] }}</strong>
              </span>
            </div>
            <div
              class='time-from col-lg-6 col-sm-6 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.time_from }"
            >
              <label>Time from</label>
              <vue-timepicker
                :minute-interval='15'
                v-model='fields.time_from'
                placeholder='Start Time'
                format='h:mm a'
                close-on-complete
                ref='timeFrom'
                hour-label='Hour'
                minute-label='Minute'
                apm-label='AM/PM'
                @change='timeFormChange'
                @open="clearTimepicker('timeFrom')"
              ></vue-timepicker>
              <span class='help-block' v-if='errors.time_from'>
                <strong>{{ errors.time_from[0] }}</strong>
              </span>
            </div>

            <div
              class='time-to col-lg-6 col-sm-6 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.time_to }"
            >
              <label>Time to</label>
              <vue-timepicker
                :minute-interval='15'
                v-model='fields.time_to'
                placeholder='End Time'
                format='h:mm a'
                close-on-complete
                ref='timeTo'
                hour-label='Hour'
                minute-label='Minute'
                apm-label='AM/PM'
                @open="clearTimepicker('timeTo')"
              ></vue-timepicker>

              <span class='help-block' v-if='errors.time_to'>
                <strong>{{ errors.time_to[0] }}</strong>
              </span>
            </div>

            <div
              v-if='!fields.id'
              class='col-lg-6 col-sm-6 col-12 form-group has-feedback'
            >
              <toggle-button
                :value='isTimeIntervals'
                :color="{ checked: '#a94442', unchecked: '#01bd00' }"
                :sync='true'
                :labels="{
                  checked: 'Disable time intervals',
                  unchecked: 'Enable time intervals',
                }"
                @change='toggleTimeInervals'
                :font-size='14'
                :height='38'
                :width='186'
              />
            </div>
            <div
              v-if='!fields.id'
              class='col-lg-6 col-sm-6 col-12 form-group has-feedback'
            >
              <toggle-button
                :value='isReccuring'
                :color="{ checked: '#a94442', unchecked: '#01bd00' }"
                :sync='true'
                :labels="{
                  checked: 'Disable recurring',
                  unchecked: 'Enable recurring',
                }"
                @change='toggleReccuring'
                :font-size='14'
                :height='38'
                :width='166'
              />
            </div>

            <div class="time-intervals-dropdowns">

              <div
                class='time-from col-lg-6 col-sm-6 col-12 form-group has-feedback'
                :class="{ 'has-error': errors.time_interval }"
                v-if='isTimeIntervals && !fields.id'
              >
                <label>Time Intervals</label>
                <select class='form-control' v-model='fields.time_interval'>
                  <option value='0'>No intervals</option>
                  <option value='30'>30 min</option>
                  <option value='60'>1 hour</option>
                  <option value='90'>1.5 hour</option>
                  <option value='120'>2 hours</option>
                  <option value='180'>3 hours</option>
                  <option value='240'>4 hours</option>
                </select>
                <span class='help-block' v-if='errors.time_interval'>
                  <strong>{{ errors.time_interval[0] }}</strong>
                </span>
              </div>

              <div
                class='time-from col-lg-6 col-sm-6 col-12 form-group has-feedback'
                :class="{ 'has-error': errors.interval_break }"
                v-if='isTimeIntervals && !fields.id'
              >
                <label>Interval Breaks</label>
                <select class='form-control' v-model='fields.interval_break'>
                  <option value='0'>No breaks</option>
                  <option value='15'>15 min</option>
                  <option value='30'>30 min</option>
                  <option value='45'>45 min</option>
                  <option value='60'>1 hour</option>
                </select>
                <span class="help-block" v-if='errors.interval_break'>
                  <strong>{{ errors.interval_break[0] }}</strong>
                </span>
              </div>

            <div
              class='time-to col-lg-6 col-sm-6 col-12 form-group has-feedback'
              :class="{ 'has-error': numError }"
              v-if='isTimeIntervals && !fields.id'
            >
              <label> Num </label>
              <input type='number' disabled class='form-control' :value='num'/>
              <span class='help-block' v-if='numError'>
                <strong>{{ numError }}</strong>
              </span>
            </div>

            </div>

            <div
              class='col-lg-6 col-sm-6 col-12 form-group has-feedback'
              v-if='isReccuring && !fields.id'
            >
              <label> Recurrence frequencies: </label>
              <select
                class='form-control'
                v-model='fields.recurrence_frequencies'
              >
                <option value='0'>Disable recurring</option>
                <option value='day'>Daily</option>
                <option value='week'>Weekly</option>
                <option value='week2'>Every 2 Weeks</option>
                <option value='month'>Monthly</option>
              </select>
              <span class='help-block' v-if='errors.recurrence_frequencies'>
                <strong>{{ errors.recurrence_frequencies[0] }}</strong>
              </span>
            </div>

            <div
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
              v-if='isReccuring && !fields.id'
            >
              <label> Recurrence until </label>
              <dropdown-datepicker
                v-if='isDateInputInit'
                display-format='mdy'
                v-model='fields.recurrence_until'
                submit-format='yyyy-mm-dd'
                ref='recurrenceUntil'
                :minYear='2021'
                maxDate='2030-01-01'
              ></dropdown-datepicker>
              <span class='help-block' v-if='errors.recurrence_until'>
                <strong>{{ errors.recurrence_until[0] }}</strong>
              </span>
            </div>

            <div
              class='form-group col-lg-9 col-sm-9 col-12 has-feedback'
              :class="{
                disabled: fields.count_booked === 1,
                'has-error': errors.spot_price,
              }"
            >
              <label>Price</label>
              <div class='d-flex'>
                <span class='dollar-wrapper'>
                  <!--<masked-input class="form-control" v-model="fields.spot_price" mask="111.11" />-->
                  <input
                    type='number'
                    min='0'
                    step='0.01'
                    class='form-control'
                    v-model='fields.spot_price'
                    :disabled='fields.count_booked === 1'
                  />
                </span>
                <span class='per-lesson'>Per lesson</span>
              </div>

              <span class='maw-200 help-block' v-if='errors.spot_price'>
                <strong>{{ errors.spot_price[0] }}</strong>
              </span>
            </div>

            <div
              class='form-group col-lg-3 col-sm-3 col-12 has-feedback'
              :class="{ 'has-error': errors.spots_count }"
            >
              <span class='private-lesson'>
                <span v-if='fields.spots_count === 1'>
                  <img src='../../images/man-user.svg' alt/>
                </span>
                <span v-if='fields.spots_count > 1'>
                  <img src='../../images/multiple-users-silhouette.svg' alt/>
                </span>
              </span>
              <label>Max students</label>
              <input
                @input='replaceInput'
                class='form-control'
                min='1'
                max='50'
                v-model.number='fields.spots_count'
                type='number'
              />
              <span class='help-block' v-if='errors.spots_count'>
                <strong>{{ errors.spots_count[0] }}</strong>
              </span>
            </div>

            <div
              class='form-group col-12 has-feedback'
              :class="{ 'has-error': errors.description }"
            >
              <label>Description</label>
              <text-editor
                name="description"
                v-model="fields.description"
              />
              <span class="help-block" v-if="errors.description">
                <strong>{{ errors.description[0] }}</strong>
              </span>
            </div>

            <div class='col-12'>
              <span>
                Instructor is responsible for collecting any and all applicable
                taxes for the location in which the lesson takes place.
              </span>
            </div>
            <div class='col-12'>
              <div v-if='errorText' class='has-error'>{{ errorText }}</div>
              <div v-if='successText' class='has-success'>
                {{ successText }}
              </div>
            </div>

            <div class='col-12'>
              <button
                @keypress.enter.prevent
                type='submit'
                class='btn btn-primary btn-block'
                v-if='fields.id'
              >
                Save lesson
              </button>
              <button
                @keypress.enter.prevent
                type='submit'
                class='btn btn-primary btn-block'
                v-else
              >
                Add lesson
              </button>

              <button
                @click='cancelLesson(fields.id)'
                type='submit'
                v-if='fields.id'
                class='btn btn-primary btn-block'
                :class="{
                  'cancel-lesson': students.length === 0,
                }"
              >
                Cancel lesson
              </button>
            </div>
          </div>
        </form>
      </div>
    </magnific-popup-modal>
  </div>
</template>

<script>
import MaskedInput from 'vue-masked-input';
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import MagnificPopupModal from './external/MagnificPopupModal'
import DropdownDatepicker from 'vue-dropdown-datepicker'
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue'
import CopyInput from './discounts/CopyInput/CopyInput';
import shareHelper from '../helpers/shareHelper';
import TextEditor from './profile/TextEditor/TextEditor';

require('jquery.maskedinput/src/jquery.maskedinput')

export default {
  components: {
    MaskedInput,
    MagnificPopupModal,
    DropdownDatepicker,
    VueTimepicker,
    CopyInput,
    TextEditor
  },
  mixins: [siteAPI, skillectiveHelper],
  props: ['lesson', 'userGenres', 'siteGenres', 'selectRange', 'instructorId'],
  data() {
    return {
      shareLink: "",
      fields: {
        genre: null,
        date: '',
        date_to: null,
        time_from: '',
        time_to: '',
        spots_count: null,
        spot_price: null,
        location: '',
        description: null,
        price_per: 'lesson',
        lesson_type: 'in_person',
        time_interval: 0,
        interval_break: 0,
        timezone_id: '',
        recurrence_until: null,
        recurrence_frequencies: 0,
        count_booked: 0,
      },
      timeOptions: [],
      timeZomeOptions: [],
      formGenres: [],
      lessonTypes: [],
      isDateInputInit: false,
      isTimeIntervals: false,
      isOvernight: true,
      isReccuring: false,
      students: [],
    }
  },
  computed: {
    num: function() {
      if (
        this.fields.date &&
        this.fields.date_to &&
        this.fields.time_to &&
        this.fields.time_from &&
        this.fields.time_interval
      ) {
        let start = this.fields.date + ' ' + this.fields.time_from,
          end = this.fields.date_to + ' ' + this.fields.time_to

        let minutesStart = moment(start, ['YYYY-MM-DD H:mm: a']).unix(),
          minutesEnd = moment(end, ['YYYY-MM-DD H:mm: a']).unix()

        let diffInMinutes = (minutesEnd - minutesStart) / 60

        let lessonsCount = diffInMinutes / this.fields.time_interval;
        return Math.floor(lessonsCount);
      } else {
        return 0
      }
    },

    numError: function() {
      if (
        this.fields.date &&
        this.fields.date_to &&
        this.fields.time_to &&
        this.fields.time_from &&
        this.fields.time_interval
      ) {
        let start = this.fields.date + ' ' + this.fields.time_from,
          end = this.fields.date_to + ' ' + this.fields.time_to

        let minutesStart = moment(start, ['YYYY-MM-DD H:mm: a']).unix(),
          minutesEnd = moment(end, ['YYYY-MM-DD H:mm: a']).unix()

        let diffInMinutes = (minutesEnd - minutesStart) / 60

        let lessonsCount = diffInMinutes / (Number(this.fields.time_interval) + Number(this.fields.interval_break));

        if (Math.floor(lessonsCount) < 1) {
          return 'The specified time interval is not enough for 1 lesson'
        } else {
          return false
        }
      } else {
        return false
      }
    },
  },

  watch: {
    fields: {
      handler(value) {
        if (value.date && (value.date !== value.oldValue) && !this.isOvernight) {
          value.date_to = value.date
        }

        if (value.lesson_type === 'in_person_client') {
          setTimeout(() => {
            this.fields.timezone_id = Intl.DateTimeFormat().resolvedOptions().timeZone
          }, 1)
        }

        if (value.lesson_type === "in_person") {
            var thisComponent = this;
            var autocomplete = this.initializeLocationField(this.$refs["lessonLocation"],["address"]);

            console.log('google.maps.event.addListener', google.maps.event);
            google.maps.event.addListener(
              autocomplete,
                "place_changed",
                function (e) {
                  thisComponent.fields.location = thisComponent.$refs["lessonLocation"].value;
              }
            );
        }

      },
      deep: true,
    },
  },
  methods: {
    getShareLink() {
      return shareHelper.buildShareLink(this.instructorId, this.fields.id);
    },
    initDateFrom() {
      const today = new Date()

      this.$refs.datepicker.year = today.getFullYear()
      this.$refs.datepicker.month = today.getMonth() + 1
      this.$refs.datepicker.day = today.getDate()

      if (moment(this.fields.date))
        this.fields.date = moment(today).format('YYYY-MM-DD')

      if (this.isOvernight) {
        const tomorrow = new Date(today)
        tomorrow.setDate(tomorrow.getDate() + 1)

        this.$refs.datepickerTo.year = tomorrow.getFullYear()
        this.$refs.datepickerTo.month = tomorrow.getMonth() + 1
        this.$refs.datepickerTo.day = tomorrow.getDate()

        if (moment(this.fields.date_to))
          this.fields.date_to = moment(tomorrow).format('YYYY-MM-DD')
      } else {
        if (moment(this.fields.date_to))
          this.fields.date_to = moment(today).format('YYYY-MM-DD')
      }
    },
    toggleTimeInervals() {
      this.isTimeIntervals = !this.isTimeIntervals
    },
    toggleReccuring() {
      this.isReccuring = !this.isReccuring
    },
    cancelLesson(lesson) {
      this.apiDelete('/api/lesson/' + lesson)
    },
    replaceInput() {
      this.fields.spots_count > 50 && (this.fields.spots_count = 50)
    },
    clearTimepicker(input) {
      if (!input) {
        return false
      }

      this.$refs[input].hour = ''
      this.$refs[input].minute = ''
      this.$refs[input].apm = ''
    },
    timeFormChange() {
      if (moment(this.fields.time_from)) {
        this.fields.time_to = moment(this.fields.time_from, ['h:mm a']).add('30', 'minutes').format('h:mm a')
      }
    },
    onSubmit() {
      if (moment(this.fields.date))
        this.fields.date = moment(this.fields.date).format('YYYY-MM-DD')

      if (moment(this.fields.date_to))
        this.fields.date_to = moment(this.fields.date_to).format('YYYY-MM-DD')

      if (moment(this.fields.time_from)) {
        this.fields.time_from = moment(this.fields.time_from, [
          'h:mm A',
        ]).format('HH:mm:ss')
      }

      if (moment(this.fields.time_to)) {
        this.fields.time_to = moment(this.fields.time_to, ['h:mm A']).format(
          'HH:mm:ss',
        )
      }

      if (this.fields.id > 0)
        this.apiPut('/api/instructor/lesson/' + this.fields.id, this.fields)
      else this.apiPost('/api/instructor/lesson', this.fields)
    },
    componentHandlePostResponse(responseData) {
      this.clearFormAndClosePopup()
      this.$root.$emit('lessonCreated', responseData.data)
      this.$emit('lesson', responseData.data)

      this.$root.$emit('createdLessons')

      this.$refs.modal.close()
    },
    componentHandlePutResponse(responseData) {
      this.clearFormAndClosePopup()
      this.$root.$emit('lessonUpdated', responseData.data)

      this.$root.$emit('createdLessons')

      this.$refs.modal.close()
    },
    openPopup() {
      this.isDateInputInit = true
      this.isOvernight = true

      setTimeout(() => {
        if (this.fields.date) {
          this.isOvernight = this.fields.date !== this.fields.date_to
        } else {
          this.isOvernight = false
          this.initDateFrom()
        }
      }, 1)

      this.formGenres = _.cloneDeep(this.userGenres)
      if (this.fields.genre !== null) {
        let _inFormGenres = false
        for (let i = 0; i < this.formGenres.length; i++)
          if (this.fields.genre === this.formGenres[i].id) {
            _inFormGenres = true
            break
          }

        if (!_inFormGenres)
          for (let i = 0; i < this.siteGenres.length; i++)
            if (this.fields.genre === this.siteGenres[i].id) {
              this.formGenres.push(this.siteGenres[i])
              break
            }
      }

      this.$refs.modal.open()
    },
    clearFormAndClosePopup() {
      this.clearSubmittedForm()
      this.date = null

      this.id = null

      this.genre = ''

      this.successText = null

      this.isDateInputInit = false

      this.$refs.timeTo.hour = ''
      this.$refs.timeTo.minute = ''
      this.$refs.timeTo.apm = ''

      this.$refs.timeFrom.hour = ''
      this.$refs.timeFrom.minute = ''
      this.$refs.timeFrom.apm = ''
      this.$refs.lessonLocation = null

      this.isOvernight = true
    },
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this
      var autocomplete = this.initializeLocationField(this.$refs[_ref], [
        'address',
      ])
      google.maps.event.addListener(
        autocomplete,
        'place_changed',
        function() {
          thisComponent.fields.location = thisComponent.$refs[_ref].value
        },
      )
    },
  },
  created: function() {
    this.timeOptions = this.getTimeOptions()
    this.timeZomeOptions = {
      'America/New_York': 'America/New_York UTC-05:00',
      'America/Indiana/Indianapolis': 'America/Indiana/Indianapolis UTC-05:00',
      'America/Chicago': 'America/Chicago UTC-05:00',
      'America/Denver': 'America/Denver UTC-07:00',
      'America/Phoenix': 'America/Phoenix UTC-07:00',
      'America/Los_Angeles': 'America/Los_Angeles UTC-08:00',
      'America/Anchorage': 'America/Anchorage UTC-09:00',
      'Pacific/Honolulu': 'Pacific/Honolulu UTC-10:00',
      'America/Chihuahua': 'America/Chihuahua UTC-07:00',
      'America/Guatemala': 'America/Guatemala UTC-06:00',
      'America/Regina': 'America/Regina UTC-06:00',
      'America/Mexico_City': 'America/Mexico_City UTC-06:00',
      'America/Bogota': 'America/Bogota UTC-05:00',
      'America/Caracas': 'America/Caracas UTC-04:30',
      'America/Halifax': 'America/Halifax UTC-04:00',
      'America/Asuncion': 'America/Asuncion UTC-04:00',
      'America/La_Paz': 'America/La_Paz UTC-04:00',
      'America/Cuiaba': '	America/Cuiaba UTC-04:00',
      'America/Santiago': '	America/Santiago UTC-04:00',
      'America/St_Johns': 'America/St_Johns UTC-03:30',
      'America/Sao_Paulo': '	America/Sao_Paulo UTC-03:00',
      'America/Santiago': '	America/Godthab UTC-03:00',
      'America/Cayenne': '	America/Cayenne UTC-03:00',
      'America/Argentina/Buenos_Aires':
        'America/Argentina/Buenos_Aires UTC-03:00',
      'America/Montevideo': '	America/Montevideo UTC-03:00',
      'Atlantic/Cape_Verde': 'Atlantic/Cape_Verde UTC-01:00',
      'Atlantic/Azores': 'Atlantic/Azores UTC-01:00',
      'Africa/Casablanca': 'Africa/Casablanca UTC+00:00',
      'Atlantic/Reykjavik': 'Atlantic/Reykjavik UTC+00:00',
      'Europe/London': 'Europe/London UTC+00:00',
      'Europe/Berlin': 'Europe/Berlin UTC+01:00',
      'Europe/Paris': 'Europe/Paris UTC+01:00',
      'Africa/Lagos': 'Africa/Lagos UTC+01:00',
      'Europe/Budapest': 'Europe/Budapest UTC+01:00',
      'Europe/Warsaw': 'Europe/Warsaw UTC+01:00',
      'Africa/Windhoek': 'Africa/Windhoek UTC+01:00',
      'Europe/Istanbul': 'Europe/Istanbul UTC+02:00',
      'Europe/Kiev': 'Europe/Kiev UTC+02:00',
      'Africa/Cairo': 'Africa/Cairo UTC+02:00',
      'Asia/Damascus': 'Asia/Damascus UTC+02:00',
      'Asia/Amman': 'Europe/Amman UTC+02:00',
      'Africa/Johannesburg': 'Africa/Johannesburg UTC+02:00',
      'Asia/Jerusalem': 'Asia/Jerusalem UTC+02:00',
      'Asia/Beirut': 'Asia/Beirut UTC+02:00',
      'Asia/Baghdad': 'Asia/Baghdad UTC+03:00',
      'Europe/Minsk': 'Europe/Minsk UTC+03:00',
      'Asia/Riyadh': 'Asia/Riyadh UTC+03:00',
      'Africa/Nairobi': 'Africa/Nairobi UTC+03:00',
      'Asia/Tehran': 'Asia/Tehran UTC+03:30',
      'Europe/Moscow': 'Europe/ Moscow UTC+04:00',
      'Asia/Tbilisi': 'Asia/Tbilisi UTC+04:00',
      'Asia/Yerevan': 'Asia/Yerevan UTC+04:00',
      'Asia/Dubai': 'Asia/Dubai UTC+04:00',
      'Asia/Baku': 'Asia/Baku UTC+04:00',
      'Indian/Mauritius': 'Indian/Mauritius UTC+04:00',
      'Asia/Kabul': 'Asia/Kabul UTC+04:30',
      'Asia/Tashkent': 'Asia/Tashkent UTC+05:00',
      'Asia/Karachi': '	Asia/Karachi UTC+05:00',
      'Asia/Colombo': 'Asia/Colombo UTC+05:30',
      'Asia/Kolkata': 'Asia/Kolkata UTC+05:30',
      'Asia/Kathmandu': 'Asia/Kathmandu UTC+05:45',
      'Asia/Almaty': 'Asia/Almaty UTC+06:00',
      'Asia/Dhaka': 'Asia/Dhaka UTC+06:00',
      'Asia/Yekaterinburg': 'Asia/Yekaterinburg UTC+06:00',
      'Asia/Yangon': 'Asia/Yangon UTC+06:30',
      'Asia/Bangkok': 'Asia/Bangkok UTC+07:00',
      'Asia/Novosibirsk': 'Asia/Novosibirsk UTC+07:00',
      'Asia/Krasnoyarsk': 'Asia/Krasnoyarsk UTC+08:00',
      'Asia/Ulaanbaatar': 'Asia/Ulaanbaatar UTC+08:00',
      'Asia/Shanghai': 'Asia/Shanghai UTC+08:00',
      'Australia/Perth': 'Australia/Perth UTC+08:00',
      'Asia/Singapore': 'Asia/Singapore UTC+08:00',
      'Asia/Taipei': 'Asia/Taipei UTC+08:00',
      'Asia/Irkutsk': 'Asia/Irkutsk UTC+09:00',
      'Asia/Seoul': 'Asia/Seoul UTC+09:00',
      'Asia/Tokyo': 'Asia/Tokyo UTC+09:00',
      'Australia/Darwin': '	Australia/Darwin UTC+09:30',
      'Australia/Adelaide': '	Australia/Adelaide UTC+09:30',
      'Australia/Hobart': '	Australia/Hobart UTC+10:00',
      'Asia/Yakutsk': 'Asia/Yakutsk UTC+10:00',
      'Australia/Brisbane': '	Australia/Brisbane UTC+10:00',
      'Pacific/Port_Moresby': 'Pacific/Port_Moresby UTC+10:00',
      'Australia/Sydney': 'Australia/Sydney UTC+10:00',
      'Asia/Vladivostok': 'Asia/Vladivostok UTC+11:00',
      'Pacific/Guadalcanal': 'Pacific/Guadalcanal UTC+11:00',
      'Pacific/Fiji': 'Pacific/Fiji UTC+12:00',
      'Asia/Magadan': 'Asia/Magadan UTC+12:00',
      'Pacific/Auckland': 'Pacific/Auckland UTC+12:00',
      'Pacific/Tongatapu': 'Pacific/Tongatapu UTC+13:00',
      'Pacific/Apia': 'Pacific/Apia UTC+13:00',
    }
    this.formGenres = this.userGenres
    this.lessonTypes =
      window.lessonTypes !== undefined ? window.lessonTypes : []

    this.$root.$on('openAddLessonModal', () => {
      this.openPopup()
    })
  },
  mounted() {
    this.initNewPlacesAutocomplete('lessonLocation')

    this.$root.$on('openAddLessonWithRange', (selectRange) => {
      if (selectRange !== null) {
        this.fields.date = selectRange.startStr
        this.fields.date_to = selectRange.startStr
        this.isDateInputInit = true

        this.fields.time_from = moment(selectRange.startStr).format('h:mm a')

        setTimeout(() => {
          setTimeout(() => {
            this.fields.time_to = moment(selectRange.endStr).format('h:mm a')
          }, 10)

          if (this.$refs.datepicker) {
            this.$refs.datepicker.day = Number(
              moment(selectRange.startStr).format('DD'),
            )
            this.$refs.datepicker.month = Number(
              moment(selectRange.startStr).format('MM'),
            )
            this.$refs.datepicker.year = Number(
              moment(selectRange.startStr).format('YYYY'),
            )
          }

          if (this.$refs.datepickerTo) {
            this.$refs.datepickerTo.day = Number(
              moment(selectRange.startStr).format('DD'),
            )
            this.$refs.datepickerTo.month = Number(
              moment(selectRange.startStr).format('MM'),
            )
            this.$refs.datepickerTo.year = Number(
              moment(selectRange.startStr).format('YYYY'),
            )
          }
        }, 1)

        this.openPopup()
      }
    })

    this.$root.$on('lessonUpdateInit', (lesson) => {
      const parser = new DOMParser()
      const currentLocation = parser.parseFromString(lesson.location, 'text/html').body.textContent

      this.fields = {
        id: lesson.id,
        genre: lesson.genre_id,
        date: moment(lesson.start).format('YYYY-MM-DD'),
        date_to: moment(lesson.end).format('YYYY-MM-DD'),
        time_from: moment(lesson.start, ['YYYY-MM-DD HH:mm:ss']).format(
          'h:mm a',
        ),
        spots_count: lesson.spots_count,
        spot_price: lesson.spot_price,
        location: currentLocation,
        lesson_type: lesson.lesson_type,
        timezone_id: lesson.timezone_id,
        description: lesson.description,
        count_booked: lesson.count_booked,
      }

      this.isDateInputInit = true

      setTimeout(() => {
        if (this.$refs.datepicker) {
          this.$refs.datepicker.day = Number(
            moment(this.fields.date).format('DD'),
          )
          this.$refs.datepicker.month = Number(
            moment(this.fields.date).format('MM'),
          )
          this.$refs.datepicker.year = Number(
            moment(this.fields.date).format('YYYY'),
          )
        }

        if (this.$refs.datepickerTo) {
          this.$refs.datepickerTo.day = Number(
            moment(this.fields.date_to).format('DD'),
          )
          this.$refs.datepickerTo.month = Number(
            moment(this.fields.date_to).format('MM'),
          )
          this.$refs.datepickerTo.year = Number(
            moment(this.fields.date_to).format('YYYY'),
          )
        }
      }, 1)

      setTimeout(() => {
        (this.fields.time_to = moment(lesson.end, [
          'YYYY-MM-DD HH:mm:ss',
        ]).format('h:mm a')),
          this.openPopup();
      }, 1);

      this.shareLink = this.getShareLink();
    });
  },
}
</script>
