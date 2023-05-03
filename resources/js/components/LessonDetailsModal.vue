<template>
  <div class='d-flex flex-wrap' v-if='selectedLesson != null'>
      <img
        ref='previewImage'
        class="video-lesson__image"
        :src='selectedLesson.preview'
        v-if='selectedLesson.preview'
        alt="Lesson preview"
      />
      <div :class="{ 'col-12 content-modal': true, 'col-md-12': true }">
        <h2 class='d-flex align-items-center'>
          {{ selectedLesson.title }}
        </h2>
        <p class='instructor-full_name'>{{ selectedLesson.genre.title }}</p>
        <p class='instructor-full_name'>{{ selectedLesson.instructor.full_name }}</p>
        <p class='instructor-instagram'>@{{ selectedLesson.instructor.profile.instagram_handle }}</p>
        <span class='lesson-type'>
          Lesson Type: {{ getLessonTypeName(selectedLesson.lesson_type) }}
        </span>
        <p class='price'>{{ selectedLesson.fullDate }}</p>
        <div
          class='avatar-stack'
          v-if='selectedLesson.students.length > 0 && studentList === true'
        >
          <span 
          v-for='(student, index) in selectedLesson.students.slice(0,countAvatarsToShow)'
          :key="index"
          >
            <img
              :src='student.profile.image'
              :alt='student.full_name'
              :title='student.full_name'
            />
          </span>
          <span v-if='countLessonStudents > countAvatarsToShow'>
            <img src='/images/ava_1.png' alt/>
            <span>{{ countAvatarsToShow - countAvatarsToShow }}+</span>
          </span>
        </div>
        <p
          class='location'
          v-if="selectedLesson.lesson_type !== 'virtual'"
          v-html='selectedLesson.location'
        ></p>
        <p v-if='selectedLesson.description'>
          Note: <br/>
          <content-viewer :content='selectedLesson.description' />
        </p>
        <p>
          <strong>${{ selectedLesson.spot_price }}</strong> per lesson
        </p>
        <a
          v-if='selectedLesson.students.length > 0 && studentList === true'
          @click.prevent='notifyClients()'
          href='#'
          class='btn-green'
        >
          Notify clients
        </a>
        <a
          v-if='
            selectedLesson.count_booked < 2 &&
            selectedLesson.is_cancelled !== 1 &&
            studentList === true
          '
          @click.prevent='editLesson(selectedLesson)'
          class='btn-green'
          href='#'
        >
          Edit lesson
        </a>
        <a
          v-if='selectedLesson.is_cancelled !== 1 && studentList === true'
          @click.prevent='cancelLesson(selectedLesson)'
          :class="{
            'cancel-lesson':
              selectedLesson.students.length === 0 || studentList === false,
          }"
          href='#'
        >
          Cancel lesson
        </a>
        <a
          v-if='studentCancel && !canCancel'
          @click.prevent='cancelRequestLesson(selectedLesson)'
          :class="{
            'cancel-lesson':
              selectedLesson.students.length === 0 || studentList === false,
          }"
          href='#'
        >
          Request Cancel
        </a>
        <p class='booking-request-title'>What I want to learn:</p>
        <textarea
          class='booking-request-area'
          placeholder='notes on what you hope to gain or other instructions'
          v-model='specialRequestText'
        ></textarea>
        <loader-button
          v-if="currentUserCanBook && studentList === 'booking-button' && !canCancel"
          :isLoading='isLoading'
          text='Add to cart'
          @click='addToCart(selectedLesson)'
        />
        <a
          v-if='canCancel'
          @click.prevent='cancelRequestLesson(selectedLesson)'
          :class="{
            'cancel-lesson':
              selectedLesson.students.length === 0 || studentList === false,
          }"
          href='#'
        >
          Request Cancel
        </a>
        <div v-if='errorText' class='has-error'>{{ errorText }}</div>
        <div v-if='successText' class='has-success'>{{ successText }}</div>
      </div>
    </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import MagnificPopupModal from './external/MagnificPopupModal'
import {getTimezone} from 'countries-and-timezones'
import {mapActions, mapGetters} from 'vuex'
import LoaderButton from './cart/LoaderButton/LoaderButton.vue'
import ContentViewer from './profile/ContentViewer/ContentViewer'

export default {
  components: {
    MagnificPopupModal,
    LoaderButton,
    ContentViewer
  },
  mixins: [siteAPI, skillectiveHelper],
  props: [
    'lessonDetails',
    'modalWindow',
    'showNotifyBtn',
    'studentList',
    'studentCancel',
    'currentUserCanBook',
    'loggedInAsStudent',
  ],
  data() {
    return {
      showNotifyClientsBtn: false,
      selectedLesson: null,
      countAvatarsToShow: 7,
      countLessonStudents: 0,
      specialRequestText: '',
      authStudent: {}
    }
  },
  computed: {
    isLoading() {
      return this.isCartLoading()
    },
    canCancel() {
      return this.authStudent.authStudentBooked
    },
  },
  methods: {
    ...mapActions(['addItemToCartAtStart']),
    ...mapGetters(['isCartLoading']),
    addToCart: async function (lesson) {
      const result = await this.addItemToCartAtStart({
        lessonId: lesson.id,
        specialRequest: this.specialRequestText || '',
      });

      if (result.success || (result.data && result.data.success)) {
        this.closeModal();
        this.$root.$emit("showMiniCart");
      } else {
        this.errorText = result.message;
      }
    },
    closeModal: function() {
      this.selectedLesson = null
      this.countLessonStudents = 0
      this.clearSubmittedForm()
      this.modalWindow.close()
    },
    notifyClients: function() {
      this.$emit('notification', this.selectedLesson.students)
      this.closeModal()
    },
    updateLessonDetails() {
      this.apiGet('/api/lesson/' + this.lessonDetails.id)
    },
    editLesson(lesson) {
      this.closeModal()
      this.$root.$emit('lessonUpdateInit', lesson)
    },
    componentHandleGetResponse(responseData) {
      this.selectedLesson = responseData.data.data
      this.authStudent = responseData.data.data.authStudent
      this.selectedLesson.content = responseData.data.data
      this.selectedLesson.fullDate =
        moment(this.selectedLesson.start).format('MMM') +
        ' ' +
        moment(this.selectedLesson.start).format('DD') +
        ', ' +
        moment(this.selectedLesson.start).format('hh:mma') +
        ' - ' +
        moment(this.selectedLesson.end).format('hh:mma') +
        ' ' +
        this.selectedLesson.timezone_abbrev

      this.countLessonStudents = this.selectedLesson.students.length
      this.modalWindow.open()
    },
    cancelLesson(lesson) {
      this.apiDelete('/api/lesson/' + lesson.id)
    },
    cancelRequestLesson(lesson) {
      this.apiDelete('/api/student/booking/' + this.authStudent.booking_id)
    },
    componentHandleDeleteResponse() {
      this.$emit('lessons-cancelled', this.selectedLesson.id)
      this.selectedLesson.is_cancelled = true
      this.showNotifyClientsBtn = false
      setTimeout(() => {
        this.closeModal()
      }, 1000)
    },
  },
  created() {
    this.updateLessonDetails()
    this.showNotifyClientsBtn = this.showNotifyBtn
  },
  watch: {
    lessonDetails: function() {
      this.updateLessonDetails()
    },
  },
}
</script>
<style lang="scss" scoped>
  .instructor-instagram {
    font-family: Hind Vadodara;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 24px;
    color: #AAAAAA;
    margin-bottom: 5px;
  }

  .instructor-full_name {
    margin-bottom: 5px;
    font-size: 16px;
    font-weight: 600;
  }
</style>