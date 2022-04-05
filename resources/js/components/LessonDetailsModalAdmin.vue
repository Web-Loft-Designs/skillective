<template>
            <div>
                <div class="d-flex flex-wrap" v-if="selectedLesson!=null">
                    <div class="col-md-6 col-12 p-0 map-wrap" v-if="selectedLesson.lat && selectedLesson.lng">
                        <google-map-single
                                :current-user-can-book="currentUserCanBook"
                                :dataid="'name'"
                                :center="{
                                    lat: selectedLesson.lat,
                                    lng: selectedLesson.lng
                                }" :marker="[{
                                position: {
                                    latitude: selectedLesson.lat,
                                    longitude: selectedLesson.lng,

                                },
                                 content: selectedLesson.content,
                                 id: selectedLesson.id
                            }]"></google-map-single>
                    </div>
                    <div :class="{'col-12 content-modal': true, 'col-md-6' : (selectedLesson.lat && selectedLesson.lng), 'col-md-12' : (!selectedLesson.lat)}">
                        <h2 class="d-flex align-items-center">
                            {{selectedLesson.title}}
                            <span class="private-title ">
                                <span v-if="selectedLesson.spots_count == 1"><img src="../../images/man-user.svg" alt=""></span>
                                <span v-if="selectedLesson.spots_count > 1"><img src="../../images/multiple-users-silhouette.svg" alt=""></span>
                            </span>
                            <span class="lesson-type">Lesson Type: {{ getLessonTypeName(selectedLesson.lesson_type) }}</span>
                        </h2>

                        <p class="price">{{ selectedLesson.fullDate }}</p>
                        <div class="avatar-stack" v-if="selectedLesson.students.length > 0 && studentList === true">
                            <span v-for="(student, index) in selectedLesson.students.slice(0,countAvatarsToShow)">
                                <img :src="student.profile.image" :alt="student.full_name" :title="student.full_name">
                            </span>
                            <span v-if="countLessonStudents>countAvatarsToShow"><img src="/images/ava_1.png" alt=""><span>{{ countAvatarsToShow-countAvatarsToShow }}+</span></span>
                        </div>

                        <p class="location"  v-if="selectedLesson.lesson_type!='virtual'" v-html="selectedLesson.location"></p>
                        <p><strong>${{ selectedLesson.spot_price}}</strong> per lesson</p>
                        <a v-if="selectedLesson.is_cancelled!=1  && studentList === true" @click.prevent="cancelLesson(selectedLesson)" class="cancel-lesson" href="#">Cancel lesson</a>
                        <div v-if="errorText" class="has-error">{{ errorText }}</div>
                        <div v-if="successText" class="has-success">{{ successText }}</div>
                    </div>
                </div>
            </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';
	import MagnificPopupModal from './external/MagnificPopupModal';

	export default {
		components: {
			MagnificPopupModal
		},
		mixins : [siteAPI, skillectiveHelper],
		props: ['lessonDetails', 'modalWindow', 'showNotifyBtn','studentList','studentCancel', 'currentUserCanBook'],
		data() {
			return {
				showNotifyClientsBtn : false,
				selectedLesson: null,
				countAvatarsToShow : 7,
                countLessonStudents : 0
			}
		},
		methods: {
			closeModal: function () {
				this.selectedLesson = null;
				this.countLessonStudents = 0;
				this.clearSubmittedForm();
				this.modalWindow.close();
			},
			notifyClients: function(){
                this.$emit('notification',this.selectedLesson.students);
                this.closeModal();
            },
            updateLessonDetails(){
				this.showLoader = false;
                this.apiGet('/api/lesson/' + this.lessonDetails.id);
                // this.selectedLesson = this.lessonDetails;

				this.showLoader = true;
            },
            componentHandleGetResponse(responseData) {
                this.selectedLesson = responseData.data.data;
                this.selectedLesson.content = responseData.data.data;
                this.selectedLesson.title = this.selectedLesson.genre.title;
                this.selectedLesson.fullDate = moment(this.selectedLesson.start).format('MMM') + ' ' + moment(this.selectedLesson.start).format('DD')+', '+moment(this.selectedLesson.start).format('hh:mma')+' - '+moment(this.selectedLesson.end).format('hh:mma');
                this.countLessonStudents = this.selectedLesson.students.length;
                this.modalWindow.open();
            },
			cancelLesson(lesson){
				this.apiDelete('/api/lesson/' + lesson.id);
			},
            cancelRequestLesson(lesson){
				this.apiDelete('/api/student/booking/' + lesson.id);
			},
			componentHandleDeleteResponse(responseData){
				this.$emit('lessons-cancelled', this.selectedLesson.id);
				this.selectedLesson.is_cancelled = true;
				this.showNotifyClientsBtn       = false;
				setTimeout(() => {
                    this.closeModal();
                },1000)

			},
		},
        created : function(){
            this.updateLessonDetails()
			// this.selectedLesson = this.lessonDetails;
			this.showNotifyClientsBtn = this.showNotifyBtn;
		},
		watch: {
            lessonDetails: function () {
               this.updateLessonDetails()
			},
		},
	}
</script>