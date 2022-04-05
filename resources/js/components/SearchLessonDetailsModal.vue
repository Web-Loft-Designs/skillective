<template>
  <li class="location-li" @click="open">
    <p
      v-if="selectedLesson.lesson_type != 'virtual'"
      v-html="selectedLesson.location"
    ></p>
    <magnific-popup-modal
      @close="clearFormAndClosePopup"
      class="modal-lesson-info"
      :show="false"
      :config="{
        closeOnBgClick: true,
        showCloseBtn: true,
        enableEscapeKey: false,
      }"
      ref="modalLocation"
    >
      <div v-if="lessonDetails != null">
        <div class="d-flex flex-wrap" v-if="selectedLesson != null">
          <div
            class="col-md-6 col-12 p-0 map-wrap"
            v-if="selectedLesson.lesson_type != 'virtual'"
          >
            <google-map-multiple
              :current-user-can-book="currentUserCanBook"
              :dataid="'name2'"
              :center="{
                lat: selectedLesson.lat,
                lng: selectedLesson.lng,
              }"
              :marker="markersLocation"
            ></google-map-multiple>
          </div>
          <div class="col-md-6 col-12 content-modal">
            <div class="location-wrap">
              <div class="avatar-location">
                <img :src="selectedLesson.instructor.profile.image" alt="" />
              </div>
              <div class="location-name">
                <a
                  :href="
                    'https://www.instagram.com/' +
                    selectedLesson.instructor.profile.instagram_handle
                  "
                  >@{{ selectedLesson.instructor.profile.instagram_handle }}</a
                >
                <span>{{ selectedLesson.instructor.full_name }} </span>
              </div>
            </div>
            <p class="location" v-if="selectedLesson.lesson_type == 'virtual'">
              Virtual Lesson
            </p>
            <p
              class="location"
              v-if="selectedLesson.lesson_type != 'virtual'"
              v-html="selectedLesson.location"
            ></p>
            <a
              :href="'/profile/' + selectedLesson.instructor.id"
              class="green-link"
              >View schedule</a
            >

            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>
          </div>
        </div>
      </div>
    </magnific-popup-modal>
  </li>
</template>

<script>
import siteAPI from "../mixins/siteAPI.js";
import skillectiveHelper from "../mixins/skillectiveHelper.js";
import MagnificPopupModal from "./external/MagnificPopupModal";

export default {
  components: {
    MagnificPopupModal,
  },
  mixins: [siteAPI, skillectiveHelper],
  props: [
    "lessonDetails",
    "modalWindow",
    "showNotifyBtn",
    "studentList",
    "currentUserCanBook",
  ],
  data() {
    return {
      showNotifyClientsBtn: false,
      selectedLesson: null,
      countAvatarsToShow: 7,
      countLessonStudents: 0,
      markersLocation: [
        {
          position: {
            latitude: this.lessonDetails.lat,
            longitude: this.lessonDetails.lng,
          },
          colorMarker: "green",
          content: this.lessonDetails,
          id: this.lessonDetails.id,
        },
      ],
    };
  },
  methods: {
    open() {
      this.$refs.modalLocation.open();
      axios
        .get(
          "/api/lessons/upcoming-nearby/" +
            this.selectedLesson.id +
            "?distance=2000"
        )
        .then((response) => {
          if (response.data.data.length > 0) {
            response.data.data.forEach((item) => {
              this.markersLocation.push({
                position: {
                  latitude: item.lat,
                  longitude: item.lng,
                },
                colorMarker: "red",
                content: item,
                id: item.id,
              });
            });
          } else {
            this.markersLocation = [
              {
                position: {
                  latitude: this.lessonDetails.lat,
                  longitude: this.lessonDetails.lng,
                },
                colorMarker: "green",
                content: this.lessonDetails,
                id: this.lessonDetails.id,
              },
            ];
          }
        })
        .catch((error) => this.apiHandleError(error));
    },
    closeModal: function () {
      this.selectedLesson = null;
      this.countLessonStudents = 0;
      this.clearSubmittedForm();
      this.modalWindow.close();
    },
    notifyClients: function () {
      this.$emit("notification");
      this.closeModal();
    },
    clearFormAndClosePopup() {},
    updateLessonDetails() {
      this.showLoader = false;
      this.apiGet("/api/lesson/" + this.lessonDetails.id);
      // this.selectedLesson = this.lessonDetails;

      this.showLoader = true;
    },
    componentHandleGetResponse(responseData) {
      this.selectedLesson = responseData.data.data;
      this.selectedLesson.content = responseData.data.data;
      this.selectedLesson.title = this.selectedLesson.genre.title;
      this.selectedLesson.fullDate =
        moment(this.selectedLesson.start).format("MMM") +
        " " +
        moment(this.selectedLesson.start).format("DD") +
        ", " +
        moment(this.selectedLesson.start).format("hh:mma") +
        " - " +
        moment(this.selectedLesson.end).format("hh:mma");
      this.countLessonStudents = this.selectedLesson.students.length;

      this.modalWindow.open();
    },
    cancelLesson(lesson) {
      this.apiDelete("/api/lesson/" + lesson.id);
    },
    componentHandleDeleteResponse(responseData) {
      this.$emit("lessons-cancelled", this.selectedLesson.id);
      this.selectedLesson.is_cancelled = true;
      this.showNotifyClientsBtn = false;
      setTimeout(() => {
        this.closeModal();
      }, 1000);
    },
  },
  created: function () {
    // this.updateLessonDetails()
    this.selectedLesson = this.lessonDetails;
    this.showNotifyClientsBtn = this.showNotifyBtn;
  },
  watch: {},
};
</script>