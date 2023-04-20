<template>
  <div id='lesson-form-container'>
    <magnific-popup-modal
      ref='modal'
      :config='{
        closeOnBgClick: false,
        showCloseBtn: true,
        enableEscapeKey: false,
      }'
      :show='false'
      class='ie-fix'
      @modalClosed='clearFormAndClosePopup'
    >
      <div class='modal-add-lesson'>
        <form
          method='post'
          @keypress.enter.prevent
          @submit.prevent='onSubmit'
        >
          <div class='row'>
            <img
              v-if='previewFileName'
              ref='previewImage'
              alt='Lesson preview'
              class='video-lesson__image'
              src='/images/upload-image.svg'
            />
            <h2
              v-if='!fields.id'
              class='login-box-msg col-12'
            >
              Add Event
            </h2>
            <h2
              v-else
              class='login-box-msg col-12'
            >
              Edit Lesson
            </h2>

            <div
              v-if='fields.id'
              class='form-group col-12'
            >
              <label>Share link</label>
              <copy-input
                :value='shareLink'
                readonly
              />
            </div>

            <div
              :class="{ 'has-error': errors.genre }"
              class='col-12 form-group has-feedback'
            >
              <label>Skill</label>
              <select
                v-model='fields.genre'
                class='form-control'
                name='genre'
              >
                <option value></option>
                <option
                  v-for='(genre, key) in formGenres'
                  :key='key'
                  :value='genre.id'
                >
                  {{ genre.title }}
                </option>
              </select>
              <span
                v-if='errors.genre'
                class='help-block'
              >
                <strong>{{ errors.genre[0] }}</strong>
              </span>
            </div>
            <div class='form-group col-4 has-feedback'>
              <label>Event Hero Image</label>
              <div
                :class="{
                'add-lesson-popup__upload': true,
                'add-lesson-popup__upload--uploaded': previewFileName,
              }"
              >
                <input
                  id='upload-preview-input'
                  ref='uploadPreviewInput'
                  accept='image/png, image/jpeg'
                  name='upload-preview-input'
                  type='file'
                  @change='previewFileChanged'
                />
                <label for='upload-preview-input'>Upload a file</label>
                <div class='add-lesson-popup__upload-image'>
                  <img
                    ref='uploadPreviewImage'
                    alt='Upload Lesson preview file'
                    src='/images/upload-image.svg'
                  />
                </div>
                <span class='add-lesson-popup__upload-title'>
                  Upload Image
                </span>
                <span class='add-lesson-popup__upload-formats'>PNG, JPG up to 10MB</span>
                <div
                  v-if='previewFileName'
                  class='add-lesson-popup__upload-actions'
                >
                  <button
                    title='Reupload file'
                    @click.prevent='reuploadPreviewFile()'
                  >
                    <img
                      alt='Reupload file'
                      src='/images/upload-cloud-outline.svg'
                    />
                    Reupload file
                  </button>
                  <button
                    title='Remove file'
                    @click.prevent='clearPreviewFile()'
                  >
                    <img
                      alt='Remove file'
                      src='/images/upload-trash.svg'
                    />
                    Remove file
                  </button>
                </div>
              </div>
              <upload-progress-bar v-model='uploadPreviewProgress'/>
              <field-errors v-model='errors.preview'/>
            </div>
            <div
              :class="{ 'has-error': errors.lesson_type }"
              class='form-group col-4 has-feedback'
            >
              <label>Event Type</label>
              <select
                v-model='fields.lesson_type'
                class='form-control'
              >
                <option
                  v-for='(lessonTypeTitle, lessonTypeName) in lessonTypes'
                  :key='lessonTypeName'
                  :value='lessonTypeName'
                >
                  {{ lessonTypeTitle }}
                </option>
              </select>
              <span
                v-if='errors.lesson_type'
                class='help-block'
              >
                <strong>{{ errors.lesson_type[0] }}</strong>
              </span>
            </div>
            <div
              v-if="fields.lesson_type === 'in_person'"
              :class="{ 'has-error': errors.location }"
              class='form-group col-12 has-feedback'
            >
              <label>Location</label>
              <input
                ref='lessonLocation'
                v-model='fields.location'
                class='form-control'
                name='location'
                type='text'
                value
              />
              <span
                v-if='errors.location'
                class='help-block'
              >
                <strong>{{ errors.location[0] }}</strong>
              </span>
            </div>
            <div
              v-if="fields.lesson_type === 'virtual'"
              :class="{ 'has-error': errors.timezone_id }"
              class='col-12 form-group has-feedback'
            >
              <label>Time Zone</label>
              <select
                v-model='fields.timezone_id'
                class='form-control'
              >
                <option
                  disabled
                  value
                >
                  Select...
                </option>
                <option
                  v-for='(value, key) in timeZoneOptions'
                  :key='key'
                  :value='key'
                >
                  {{ value }}
                </option>
              </select>
              <span
                v-if='errors.timezone_id'
                class='help-block'
              >
                <strong>{{ errors.timezone_id[0] }}</strong>
              </span>
            </div>

            <div
              :class="{ 'has-error': errors.date }"
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
            >
              <label>Date</label>
              <!--<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" v-model="fields.date" name="date" :placeholder="'mm/dd/yyyy'" :format="'MM/dd/yyyy'"></datepicker>-->

              <dropdown-datepicker
                v-if='isDateInputInit'
                ref='datepicker'
                v-model='fields.date'
                :minYear='2021'
                display-format='mdy'
                maxDate='2030-01-01'
                submit-format='yyyy-mm-dd'
              ></dropdown-datepicker>

              <span
                v-if='errors.date'
                class='help-block'
              >
                <strong>{{ errors.date[0] }}</strong>
              </span>
            </div>

            <div
              :class="{ 'has-error': errors.time_from }"
              class='time-from col-lg-6 col-sm-6 col-12 form-group has-feedback'
            >
              <label>Time from</label>
              <vue-timepicker
                ref='timeFrom'
                v-model='fields.time_from'
                :minute-interval='15'
                apm-label='AM/PM'
                close-on-complete
                format='h:mm a'
                hour-label='Hour'
                minute-label='Minute'
                placeholder='Start Time'
                @change='timeFormChange'
                @open="clearTimepicker('timeFrom')"
              ></vue-timepicker>
              <span
                v-if='errors.time_from'
                class='help-block'
              >
                <strong>{{ errors.time_from[0] }}</strong>
              </span>
            </div>

            <div
              :class="{ 'has-error': errors.time_to }"
              class='time-to col-lg-6 col-sm-6 col-12 form-group has-feedback'
            >
              <label>Time to</label>
              <vue-timepicker
                ref='timeTo'
                v-model='fields.time_to'
                :minute-interval='15'
                apm-label='AM/PM'
                close-on-complete
                format='h:mm a'
                hour-label='Hour'
                minute-label='Minute'
                placeholder='End Time'
                @open="clearTimepicker('timeTo')"
              ></vue-timepicker>

              <span
                v-if='errors.time_to'
                class='help-block'
              >
                <strong>{{ errors.time_to[0] }}</strong>
              </span>
            </div>

            <div
              v-if='!fields.id'
              class='col-lg-6 col-sm-6 col-12 form-group has-feedback'
            >
              <label for='enableIntervals'>
                Enable time intervals
                <input v-model='isTimeIntervals' type='checkbox' name='enableIntervals' id='enableIntervals'>
                <span class='checkmark'></span>
              </label>
            </div>

            <div
              v-if='!fields.id'
              class='col-lg-6 col-sm-6 col-12 form-group has-feedback field'
            >
              <label for='enableRecurring'>
                  Enable recurring
                <input v-model='isRecurring' type='checkbox' name='enableRecurring' id='enableRecurring'>
                <span class='checkmark'></span>
              </label>
            </div>

            <div
              v-if='isRecurring && !fields.id'
              class='col-lg-6 col-sm-6 col-12 form-group has-feedback'
            >
              <label> Recurrence frequencies: </label>
              <select
                v-model='fields.recurrence_frequencies'
                class='form-control'
              >
                <option value='0'>Disable recurring</option>
                <option value='day'>Daily</option>
                <option value='week'>Weekly</option>
                <option value='week2'>Every 2 Weeks</option>
                <option value='month'>Monthly</option>
              </select>
              <span
                v-if='errors.recurrence_frequencies'
                class='help-block'
              >
                <strong>{{ errors.recurrence_frequencies[0] }}</strong>
              </span>
            </div>
            <div
              v-if='isRecurring && !fields.id'
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
            >
              <label> Recurrence until </label>
              <dropdown-datepicker
                v-if='isDateInputInit'
                ref='recurrenceUntil'
                v-model='fields.recurrence_until'
                :minYear='2022'
                display-format='mdy'
                maxDate='2030-01-01'
                submit-format='yyyy-mm-dd'
              ></dropdown-datepicker>
              <span
                v-if='errors.recurrence_until'
                class='help-block'
              >
                <strong>{{ errors.recurrence_until[0] }}</strong>
              </span>
            </div>

            <div class='time-intervals-dropdowns'>
              <div
                v-if='isTimeIntervals && !fields.id'
                :class="{ 'has-error': errors.time_interval }"
                class='
                  time-from
                  col-lg-6 col-sm-6 col-12
                  form-group
                  has-feedback
                '
              >
                <label>Time Intervals</label>
                <select
                  v-model='fields.time_interval'
                  class='form-control'
                >
                  <option value='0'>No intervals</option>
                  <option value='30'>30 min</option>
                  <option value='60'>1 hour</option>
                  <option value='90'>1.5 hour</option>
                  <option value='120'>2 hours</option>
                  <option value='180'>3 hours</option>
                  <option value='240'>4 hours</option>
                </select>
                <span
                  v-if='errors.time_interval'
                  class='help-block'
                >
                  <strong>{{ errors.time_interval[0] }}</strong>
                </span>
              </div>

              <div
                v-if='isTimeIntervals && !fields.id'
                :class="{ 'has-error': errors.interval_break }"
                class='
                  time-from
                  col-lg-6 col-sm-6 col-12
                  form-group
                  has-feedback
                '
              >
                <label>Interval Breaks</label>
                <select
                  v-model='fields.interval_break'
                  class='form-control'
                >
                  <option value='0'>No breaks</option>
                  <option value='15'>15 min</option>
                  <option value='30'>30 min</option>
                  <option value='45'>45 min</option>
                  <option value='60'>1 hour</option>
                </select>
                <span
                  v-if='errors.interval_break'
                  class='help-block'
                >
                  <strong>{{ errors.interval_break[0] }}</strong>
                </span>
              </div>

              <div
                v-if='isTimeIntervals && !fields.id'
                :class="{ 'has-error': numError }"
                class='time-to col-lg-6 col-sm-6 col-12 form-group has-feedback'
              >
                <label> Num </label>
                <input
                  :value='num'
                  class='form-control'
                  disabled
                  type='number'
                />
                <span
                  v-if='numError'
                  class='help-block'
                >
                  <strong>{{ numError }}</strong>
                </span>
              </div>
            </div>
            <div
              :class="{
                disabled: fields.count_booked === 1,
                'has-error': errors.spot_price,
              }"
              class='form-group col-lg-9 col-sm-9 col-12 has-feedback'
            >
              <label>Price</label>
              <div class='d-flex'>
                <span class='dollar-wrapper'>
                  <!--<masked-input class="form-control" v-model="fields.spot_price" mask="111.11" />-->
                  <input
                    v-model='fields.spot_price'
                    :disabled='fields.count_booked === 1'
                    class='form-control'
                    min='0'
                    step='0.01'
                    type='number'
                  />
                </span>
                <span class='per-lesson'>per person</span>
              </div>

              <span
                v-if='errors.spot_price'
                class='maw-200 help-block'
              >
                <strong>{{ errors.spot_price[0] }}</strong>
              </span>
            </div>

            <div
              :class="{ 'has-error': errors.spots_count }"
              class='form-group col-lg-3 col-sm-3 col-12 has-feedback'
            >
              <span class='private-lesson'>
                <span v-if='fields.spots_count === 1'>
                  <img
                    alt
                    src='/images/man-user.svg'
                  />
                </span>
                <span v-if='fields.spots_count > 1'>
                  <img
                    alt
                    src='/images/multiple-users-silhouette.svg'
                  />
                </span>
              </span>
              <label>Attendance Limit</label>
              <input
                v-model.number='fields.spots_count'
                class='form-control'
                max='50'
                min='1'
                type='number'
                @input='replaceInput'
              />
              <span
                v-if='errors.spots_count'
                class='help-block'
              >
                <strong>{{ errors.spots_count[0] }}</strong>
              </span>
            </div>

            <div
              :class="{ 'has-error': errors.description }"
              class='form-group col-12 has-feedback'
            >
              <label>Event Description:</label>
              <text-editor
                v-model='fields.description'
                name='description'
                placeholder='Describe what you are teaching, offering, or sharing...'
              />
              <span
                v-if='errors.description'
                class='help-block'
              >
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

            <div class='col-12'>
              <button
                v-if='fields.id'
                class='btn btn-primary btn-block'
                type='submit'
                @keypress.enter.prevent
              >
                Save lesson
              </button>
              <button
                v-else
                class='btn btn-primary btn-block'
                type='submit'
                @keypress.enter.prevent
              >
                Add lesson
              </button>

              <button
                v-if='fields.id'
                :class="{
                  'cancel-lesson': students.length === 0,
                }"
                class='btn btn-primary btn-block'
                type='submit'
                @click='cancelLesson(fields.id)'
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
import MaskedInput from 'vue-masked-input'
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import MagnificPopupModal from './external/MagnificPopupModal'
import DropdownDatepicker from 'vue-dropdown-datepicker'
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue'
import CopyInput from './discounts/CopyInput/CopyInput'
import shareHelper from '../helpers/shareHelper'
import TextEditor from './profile/TextEditor/TextEditor'
import { mapState } from 'vuex'
import UploadProgressBar from './instructor/UploadProgressBar/UploadProgressBar'
import FieldErrors from './instructor/FieldErrors/FieldErrors'
import instructorService from '../services/instructorService'

require('jquery.maskedinput/src/jquery.maskedinput')

export default {
  components: {
    FieldErrors,
    UploadProgressBar,
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
      isRecurring: false,
      uploadPreviewProgress: 0,
      previewFileName: null,
      shareLink: '',
      fields: {
        recurrence_until: null,
        recurrence_frequencies: 0,
        preview: '',
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
        count_booked: 0
      },
      timeOptions: [],
      timeZoneOptions: [],
      formGenres: [],
      lessonTypes: [],
      isDateInputInit: false,
      isTimeIntervals: false,
      students: []
    }
  },
  computed: {
    ...mapState(['datesFromCalendar']),
    num() {
      if (
        this.fields.date &&
        this.fields.date_to &&
        this.fields.time_to &&
        this.fields.time_from &&
        this.fields.time_interval
      ) {
        let start = this.fields.date + ' ' + this.fields.time_from,
          end = this.fields.date_to + ' ' + this.fields.time_to

        let minutesStart = moment(start, ['YYYY-MM-DD H:mm: a'])
            .unix(),
          minutesEnd = moment(end, ['YYYY-MM-DD H:mm: a'])
            .unix()

        let diffInMinutes = (minutesEnd - minutesStart) / 60

        let lessonsCount = diffInMinutes / this.fields.time_interval
        return Math.floor(lessonsCount)
      } else {
        return 0
      }
    },
    numError() {
      if (
        this.fields.date &&
        this.fields.date_to &&
        this.fields.time_to &&
        this.fields.time_from &&
        this.fields.time_interval
      ) {
        let start = this.fields.date + ' ' + this.fields.time_from,
          end = this.fields.date_to + ' ' + this.fields.time_to

        let minutesStart = moment(start, ['YYYY-MM-DD H:mm: a'])
            .unix(),
          minutesEnd = moment(end, ['YYYY-MM-DD H:mm: a'])
            .unix()

        let diffInMinutes = (minutesEnd - minutesStart) / 60

        let lessonsCount =
          diffInMinutes /
          (Number(this.fields.time_interval) +
            Number(this.fields.interval_break))

        if (Math.floor(lessonsCount) < 1) {
          return 'The specified time interval is not enough for 1 lesson'
        } else {
          return false
        }
      } else {
        return false
      }
    }
  },
  watch: {
    isRecurring() {
      if (!this.isRecurring) {
        this.fields.recurrence_until = null
        this.fields.recurrence_frequencies = 0
      }
    },
    isTimeIntervals() {
      if (!this.isTimeIntervals) {
        this.fields.time_interval = 0
        this.fields.interval_break = 0
      }
    },
    datesFromCalendar: {
      handler() {
        if (this.datesFromCalendar.type === 'timeGridWeek') {
          this.fields.time_from = moment(this.datesFromCalendar.start)
            .format('hh:mm a')
          this.fields.time_to = moment(this.datesFromCalendar.end)
            .format('hh:mm a')
        }
        this.fields.date = moment(this.datesFromCalendar.start)
          .format('YYYY-MM-DD')
        setTimeout(() => {
          if (this.$refs.datepicker) {
            this.$refs.datepicker.day = Number(
              moment(this.datesFromCalendar.start)
                .format('DD')
            )
            this.$refs.datepicker.month = Number(
              moment(this.datesFromCalendar.start)
                .format('MM')
            )
            this.$refs.datepicker.year = Number(
              moment(this.datesFromCalendar.start)
                .format('YYYY')
            )
          }
        }, 0)
        this.openPopup()
      },
      deep: true
    },
    fields: {
      handler: function (value) {
        if (value.date && value.date !== value.oldValue) {
          value.date_to = value.date
        }

        if (value.lesson_type === 'in_person_client') {
          setTimeout(() => {
            this.fields.timezone_id =
              Intl.DateTimeFormat()
                .resolvedOptions().timeZone
          }, 1)
        }
      },
      deep: true
    }
  },
  created() {
    this.timeOptions = this.getTimeOptions()
    this.timeZoneOptions = {
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
      'America/Cuiaba': 'America/Cuiaba UTC-04:00',
      'America/Santiago': 'America/Santiago UTC-04:00',
      'America/St_Johns': 'America/St_Johns UTC-03:30',
      'America/Sao_Paulo': 'America/Sao_Paulo UTC-03:00',
      'America/Godthab': 'America/Godthab UTC-03:00',
      'America/Cayenne': 'America/Cayenne UTC-03:00',
      'America/Argentina/Buenos_Aires': 'America/Argentina/Buenos_Aires UTC-03:00',
      'America/Montevideo': 'America/Montevideo UTC-03:00',
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
      'Australia/Adelaide': 'Australia/Adelaide UTC+09:30',
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
      'Pacific/Apia': 'Pacific/Apia UTC+13:00'
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

        this.fields.time_from = moment(selectRange.startStr)
          .format('h:mm a')

        setTimeout(() => {
          setTimeout(() => {
            this.fields.time_to = moment(selectRange.endStr)
              .format('h:mm a')
          }, 10)

          if (this.$refs.datepicker) {
            this.$refs.datepicker.day = Number(
              moment(selectRange.startStr)
                .format('DD')
            )
            this.$refs.datepicker.month = Number(
              moment(selectRange.startStr)
                .format('MM')
            )
            this.$refs.datepicker.year = Number(
              moment(selectRange.startStr)
                .format('YYYY')
            )
          }

          if (this.$refs.datepickerTo) {
            this.$refs.datepickerTo.day = Number(
              moment(selectRange.startStr)
                .format('DD')
            )
            this.$refs.datepickerTo.month = Number(
              moment(selectRange.startStr)
                .format('MM')
            )
            this.$refs.datepickerTo.year = Number(
              moment(selectRange.startStr)
                .format('YYYY')
            )
          }
        }, 1)

        this.openPopup()
      }
    })

    this.$root.$on('lessonUpdateInit', (lesson) => {
      this.fields = {
        id: lesson.id,
        genre: lesson.genre_id,
        date: moment(lesson.start)
          .format('YYYY-MM-DD'),
        date_to: moment(lesson.end)
          .format('YYYY-MM-DD'),
        time_from: moment(lesson.start, ['YYYY-MM-DD HH:mm:ss'])
          .format(
            'h:mm a'
          ),
        spots_count: lesson.spots_count,
        spot_price: lesson.spot_price,
        location: lesson.location,
        lesson_type: lesson.lesson_type,
        timezone_id: lesson.timezone_id,
        description: lesson.description,
        count_booked: lesson.count_booked,
        preview: lesson.preview
      }
      setTimeout(() => {
        this.$refs.previewImage.src = lesson.preview
        this.$refs.uploadPreviewImage.src = lesson.preview
      }, 0)
      this.previewFileName = lesson.preview

      this.isDateInputInit = true

      setTimeout(() => {
        if (this.$refs.datepicker) {
          this.$refs.datepicker.day = Number(
            moment(this.fields.date)
              .format('DD')
          )
          this.$refs.datepicker.month = Number(
            moment(this.fields.date)
              .format('MM')
          )
          this.$refs.datepicker.year = Number(
            moment(this.fields.date)
              .format('YYYY')
          )
        }

        if (this.$refs.datepickerTo) {
          this.$refs.datepickerTo.day = Number(
            moment(this.fields.date_to)
              .format('DD')
          )
          this.$refs.datepickerTo.month = Number(
            moment(this.fields.date_to)
              .format('MM')
          )
          this.$refs.datepickerTo.year = Number(
            moment(this.fields.date_to)
              .format('YYYY')
          )
        }
      }, 1)

      setTimeout(() => {
        this.fields.time_to = moment(lesson.end, ['YYYY-MM-DD HH:mm:ss'])
          .format('h:mm a')
        this.openPopup()
      }, 1)

      this.shareLink = this.getShareLink()
    })
  },
  methods: {
    clearPreviewFile() {
      this.previewFileName = null
      this.$refs.uploadPreviewImage.src = '/images/upload-image.svg'
      document.getElementById('upload-preview-input').value = ''
    },
    reuploadPreviewFile() {
      document.querySelector('#upload-preview-input + label')
        .click()
    },
    async previewFileChanged(event) {
      const fileName = event.target.files.length > 0 && event.target.files[0].name
      const data = await instructorService.setLessonImage(event.target.files[0], (percent) => {
        this.uploadPreviewProgress = percent
      })
      this.$refs.uploadPreviewImage.src = data
      this.fields.preview = data
      setTimeout(() => this.$refs.previewImage.src = data, 0)
      if (fileName) {
        this.previewFileName = fileName
      }
      this.uploadPreviewProgress = 101
    },
    getShareLink() {
      console.log(this.fields, 'zxc')
      return shareHelper.buildShareLink(this.instructorId, this.fields.id, this.fields.date)
    },
    initDateFrom() {
      const today = new Date()

      this.$refs.datepicker.year = today.getFullYear()
      this.$refs.datepicker.month = today.getMonth() + 1
      this.$refs.datepicker.day = today.getDate()

      if (moment(this.fields.date))
        this.fields.date = moment(today)
          .format('YYYY-MM-DD')
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
      if (moment(this.fields.time_from) && this.datesFromCalendar.type !== 'timeGridWeek') {
        this.fields.time_to = moment(this.fields.time_from, ['h:mm a'])
          .add('30', 'minutes')
          .format('h:mm a')
      }
    },
    onSubmit() {
      if (moment(this.fields.date))
        this.fields.date = moment(this.fields.date)
          .format('YYYY-MM-DD')

      if (moment(this.fields.date_to))
        this.fields.date_to = moment(this.fields.date_to)
          .format('YYYY-MM-DD')

      if (moment(this.fields.time_from)) {
        this.fields.time_from = moment(this.fields.time_from, [
          'h:mm A'
        ])
          .format('HH:mm:ss')
      }

      if (moment(this.fields.time_to)) {
        this.fields.time_to = moment(this.fields.time_to, ['h:mm A'])
          .format(
            'HH:mm:ss'
          )
      }
      this.fields.id
        ? this.apiPut('/api/instructor/lesson/' + this.fields.id, this.fields)
        : this.apiPost('/api/instructor/lesson', this.fields)
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
      setTimeout(() => {
        if (!this.fields.date) {
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
      this.fields.lesson_type = 'in_person'
      this.isRecurring = false
      this.isTimeIntervals = false
      this.clearPreviewFile()
    },
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this
      var autocomplete = this.initializeLocationField(this.$refs[_ref], [
        'address'
      ])
      google.maps.event.addListener(autocomplete, 'place_changed', function () {
        thisComponent.fields.location = thisComponent.$refs[_ref].value
      })
    }
  }
}
</script>

<style lang='scss'>
@import './instructor/AddLessonPopup/AddLessonPopup.scss';
@import './student/VideoLessonsList/VideoLessonsList.scss';
.pac-container {
  z-index: 10000 !important;
}
label {
  color: #444;
  font-family: 'Hind Vadodara';
  font-size: 16px;
  font-weight: 400;
  position: relative;
  padding-left: 35px;
  cursor: pointer;
  margin-right: 10px;
  margin-top: 10px;
  input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
  }
  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #fff;
    border: 1px solid #cccccc;
  }

  &:hover input ~ .checkmark {
    background-color: #fff;
    border: 1px solid #eee;
  }

  input:checked ~ .checkmark {
    background-color: #8ada00;
    border: 1px solid #8ada00;
  }


  input:checked ~ .checkmark:after {
    display: block;
    -webkit-transform: rotate(45deg) scale(1);
    -ms-transform: rotate(45deg) scale(1);
    transform: rotate(45deg) scale(1);
  }

  .checkmark:after {
    content: "";
    position: absolute;
    left: 8px;
    top: 3px;
    width: 9px;
    height: 16px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg) scale(0);
    -ms-transform: rotate(45deg) scale(0);
    transform: rotate(45deg) scale(0);
    transition: all 0.25s ease;
  }
}
</style>
