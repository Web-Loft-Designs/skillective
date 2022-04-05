<template>
    <div>
        <div class="clients-table-header">
            <div class="clients-table-header">
                <div class="table-top d-flex align-items-center">
                    <h2>Lessons</h2>
                    <div class="ml-3 d-flex align-items-center">
                        <button @click.prevent="toggleShowOnly('current')" :class="{'active':(showOnly=='current')}">Current</button>
                        <button  @click.prevent="toggleShowOnly('past')" :class="{'active':(showOnly=='past')}">Past</button>
                        <button  @click.prevent="toggleShowOnly('cancelled')" :class="{'active':(showOnly=='cancelled')}">Cancelled</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsives">
            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

            <table class="table">
                <thead>
                <tr>
                    <th  class="w-180 hidden-in-mobile hidden-in-tabled">Genre</th>
                    <th class="w-140">Instructor name</th>
                    <th class="w-100px">instagram handle</th>
                    <th class="hidden-in-mobile">Slots(left)</th>
                    <th class="w-140 hidden-in-mobile hidden-in-tabled">Location</th>
                    <th class="hidden-in-mobile">Date</th>
                    <th class="time-width hidden-in-mobile">Time</th>
                    <th class="hidden-in-mobile hidden-in-tabled">Price</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(lesson,index) in listItems">
                    <td class="hidden-in-mobile hidden-in-tabled"><span class="bars">{{ lesson.genre.title }}</span></td>
                    <td class="width-fix"><div class="width-fix-content"><a :href="'/profile/'+lesson.instructor.id" class="link-to-profile">{{lesson.instructor.full_name}}</a></div></td>
                    <td class="width-fix"><div class="width-fix-content"><a v-if="lesson.instructor.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + lesson.instructor.profile.instagram_handle" target="_blank">{{ '@' + lesson.instructor.profile.instagram_handle }}</a></div></td>
                    <td class="hidden-in-mobile">{{ lesson.spots_count }}({{ lesson.spots_count-lesson.count_booked }})</td>
                    <td class="width-fix hidden-in-mobile hidden-in-tabled">
                        <div class="width-fix-content" v-if="lesson.lesson_type=='in_person'" v-html="lesson.location"></div>
                        <div class="width-fix-content" v-if="lesson.lesson_type=='virtual'">Virtual Lesson</div>
                    </td>
                    <td class="hidden-in-mobile">{{ lesson.start | moment("M/D/YYYY") }}</td>
                    <td class="hidden-in-mobile">{{ lesson.start | moment("h:mm a") }} - {{ lesson.end | moment("h:mm a") }} {{ lesson.timezone_id }}</td>
                    <td class="hidden-in-mobile hidden-in-tabled">${{ lesson.spot_price }}</td>
                    <td class="action">
                        <div class="dropdown">
                            <button class=" btn btn-left" @click.prevent="lessonDetailsModal(lesson)">View</button>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a v-if="lesson.is_cancelled!=1" class="dropdown-item red" href="#" @click.prevent="cancelLesson(lesson)">Cancel lesson</a>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <magnific-popup-modal  @close="clearFormAndClosePopup" class="modal-lesson-info" :show="false" :config="{closeOnBgClick:true,showCloseBtn:true,enableEscapeKey:false}" ref="modal">
            <lesson-details-modal-admin
                    :current-user-can-book="currentUserCanBook"
                    :student-cancel="false"
                    :student-list="true"
                    @lessons-cancelled="getLessons()"
                    v-if="lessonInModal!=null"
                    :lesson-details="lessonInModal"
                    :show-notify-btn="false"
                    :modal-window="this.$refs.modal"></lesson-details-modal-admin>
        </magnific-popup-modal>
    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';
    import MagnificPopupModal from '../external/MagnificPopupModal';

	export default {
		mixins : [siteAPI, skillectiveHelper],
         props: {
			 instructor: null,
			 currentUserCanBook : false
         },
        components : {
            // Paginate,
            MagnificPopupModal
        },
		data() {
			return {
                listItems: [],
                showOnly: 'current',
                lessonInModal: null,
			}
		},
		methods: {
            cancelLesson(lesson){
                this.apiDelete('/api/lesson/' + lesson.id);
            },
            getLessons() {
				let queryParams = {};
				queryParams.limit = 5;
				if (this.showOnly!='')
					queryParams.type = this.showOnly;
				if (this.instructor!=null)
					queryParams.instructor = this.instructor;

				this.apiGet('/api/admin/lessons', {
					params: queryParams
				});
			},
            clearFormAndClosePopup() {},
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data.data;
                this.listItems = this.listItems.filter(function (item,index) {
                    return index < 5
                })
			},
            lessonDetailsModal(lesson){
                this.lessonInModal = lesson;
            },
            componentHandleDeleteResponse(responseData){
                this.getLessons();
            },
            getClientGenresList(clientGenres){
                var _titles = [];
                if(clientGenres.length > 3) {
                    for (var i = 0 ; i<3; i++)
                        _titles.push(clientGenres[i].title);
                    if (_titles.length>0)
                        return '<span class="bars">'+_titles.join('</span><span class="bars">')+'</span>';
                    return '';
                } else {
                    for (var i = 0 ; i<clientGenres.length; i++)
                        _titles.push(clientGenres[i].title);
                    if (_titles.length>0)
                        return '<span class="bars">'+_titles.join('</span><span class="bars">')+'</span>';
                    return '';
                }
            },
            toggleShowOnly(type){
                this.showOnly = type;
                this.getLessons();
            }
		},
        created : function(){
            this.getLessons();
        },
		mounted() {
			this.$root.$on('instructorLessonsAdded', () => {
				this.getLessons();
			});
		}
	}
</script>