<template>
    <div id="admin-lessons-container">

        <div class="table-top mb-5 d-flex align-items-center">
            <h2 class=" page-title">Lessons</h2>
            <span class="filter-table d-flex">
                <button @click.prevent="toggleShowOnly('current')" :class="{'active':(showOnly=='current')}">Current</button>
                <button @click.prevent="toggleShowOnly('past')" :class="{'active':(showOnly=='past')}">Past</button>
                <button @click.prevent="toggleShowOnly('cancelled')" :class="{'active':(showOnly=='cancelled')}">Cancelled</button>
            </span>
        </div>

        <div class="d-flex responsive-mobile justify-content-between align-items-center">
            <div class="d-flex  responsive-mobile align-items-center">
                <div v-if="selectedLessons.length>0" class="d-flex align-items-center">
                    <span @click="cancelManyLessons" class="btn btn-danger mr-2">Cancel {{ selectedLessons.length }} lessons</span>
                </div>
                <input type="text" v-model="searchString" placeholder="Search lessons"/>
            </div>
        </div>

        <div v-if="errorText" class="has-error">{{ errorText }}</div>
        <div v-if="successText" class="has-success">{{ successText }}</div>

        <div class="table-responsives">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col" v-if="showOnly!='cancelled'">
                        <span class="checkbox-wrapper">
                            <label for="checkAll">
                                <input @change="selectAll" :indeterminate.prop="indeterminate" v-model="allSelected" id="checkAll" type="checkbox"/>
                                <span class="checkmark" :class="{'indeterminate': indeterminate == true }"></span>
                            </label>
                        </span>
                    </th>
                    <th class="w-180 hidden-in-mobile hidden-in-tabled" scope="col">Genre</th>
                    <th class="w-140" scope="col">Instructor Name</th>
                    <th class="w-100px" scope="col">Instagram handle</th>
                    <th scope="col" class="hidden-in-mobile">Slots(left)</th>
                    <th class="w-140 hidden-in-mobile hidden-in-tabled" scope="col">Location</th>
                    <th scope="col" class="hidden-in-mobile">Date</th>
                    <th scope="col" class="time-width hidden-in-mobile">Time</th>
                    <th scope="col" class="hidden-in-mobile hidden-in-tabled">Price</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(lesson, index) in listItems">
                    <td v-if="showOnly!='cancelled'"><span class="checkbox-wrapper"><label><input  @change="select" type="checkbox" v-model="selectedLessons" :value="lesson.id"/><span class="checkmark"></span></label></span></td>
                    <td class="hidden-in-mobile hidden-in-tabled"><span class="bars">{{ lesson.genre.title }}</span></td>
                    <td class="width-fix"><div class="width-fix-content"><a :href="'/profile/'+lesson.instructor.id" class="link-to-profile">{{ lesson.instructor.full_name}}</a></div></td>
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

        <div class="row justify-content-center" v-if="pagination.total_pages>1">
            <div class="col-6 select-show">
                <label>Show</label>
                <select v-model="pagination.per_page" @change="onChangePerPage">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center custom-pag">
                <span>{{ firstListItemNumber }}-{{ lastListItemNumber }} of {{ pagination.total }}</span>
                <paginate
                        :page-count="pagination.total_pages"
                        :force-page="pagination.current_page"
                        :prev-text="'Prev'"
                        :next-text="'Next'"
                        :click-handler="paginatorClickCallback"
                        :container-class="'clients-pagination'">
                </paginate>
            </div>
        </div>

        <magnific-popup-modal @close="clearFormAndClosePopup" class="modal-lesson-info" :show="false" :config="{closeOnBgClick:true,showCloseBtn:true,enableEscapeKey:false}" ref="modal">
            <lesson-details-modal-admin
                    :current-user-can-book="currentUserCanBook"
                    v-if="lessonInModal!=null"
                    @lessons-cancelled="getLessons()"
                    :lesson-details="lessonInModal"
                    :student-cancel="false"
                    :student-list="true"
                    :show-notify-btn="false"
                    :modal-window="this.$refs.modal"></lesson-details-modal-admin>
        </magnific-popup-modal>

    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';
	import Paginate from 'vuejs-paginate';
	import MagnificPopupModal from '../external/MagnificPopupModal';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
			lessons: null,
			lessonsMeta: {},
			currentUserCanBook : false
		},
		components : {
			Paginate,
			MagnificPopupModal
		},
		data() {
			return {
				allSelected: false,
                indeterminate: false,
				showOnly: 'current',
				lessonInModal: null,
				selectedLessons: [],
				searchString : '',
				instructor: null,
				pagination : {
					total : 0,
					total_pages : 0,
					current_page : 0,
					per_page : 0
				}
			}
		},
		methods: {
			selectAll: function() {
				this.selectedLessons = [];

				if (this.allSelected) {
					this.indeterminate = false;
					for (let user in this.listItems) {
						this.selectedLessons.push(this.listItems[user].id.toString());
					}
				}
			},
            clearFormAndClosePopup() {},
			select: function() {
				if(this.listItems.length == this.selectedLessons.length ) {
					this.allSelected = 1;
					this.indeterminate = false;
				} else if(this.selectedLessons.length === 0) {
					this.allSelected = 0;
					this.indeterminate = false;
				} else {
					this.allSelected = 0;
					this.indeterminate = true;
				}
			},
			getLessons() {
				let queryParams = {};
				if (this.pagination.current_page != undefined)
					queryParams.page = this.pagination.current_page;
				else
					queryParams.page = 1;

				if (this.searchString!='')
				    queryParams.s = this.searchString;
				if (this.showOnly!='')
				    queryParams.type = this.showOnly;
				if (this.instructor!=null)
					queryParams.instructor = this.instructor;

				this.updateUrlQueryParams(queryParams);

				this.apiGet('/api/admin/lessons', {
					params: queryParams
				});
			},
			cancelLesson(lesson){
				this.apiDelete('/api/lesson/' + lesson.id);
			},
			cancelManyLessons(){
				this.apiPost('/api/admin/lessons/cancel', {lessons : this.selectedLessons});
			},
			lessonDetailsModal(lesson){
                this.lessonInModal = lesson;
            },
			componentHandleDeleteResponse(responseData){
				if (this.pagination.count == 1 && this.pagination.current_page>1){ // last lesson on page cancelled > go to prev page
					this.pagination.current_page -= 1;
				}
				this.getLessons();
			},
			componentHandlePostResponse(responseData){
				if (this.pagination.count == parseInt(responseData.data) && this.pagination.current_page>1){ // last lesson on page cancelled > go to prev page
					this.pagination.current_page -= 1;
				}
				this.selectedLessons = [];
				this.getLessons();
			},
			paginatorClickCallback(pageNum) {
				this.pagination.current_page = pageNum;
				this.getLessons();
			},
			onChangePerPage() {
				this.pagination.current_page = 1;
				Cookies.set('adminLessonsPerPage', this.pagination.per_page);
				this.getLessons();
			},
			searchLessons(){
				this.pagination.current_page = 1;
				this.getLessons();
			},
			componentHandleGetResponse(responseData) {
				this.selectedLessons = [];
				this.listItems = responseData.data.data;
				if (responseData.data.meta!=undefined && responseData.data.meta.pagination!=undefined){
					this.pagination.count           = responseData.data.meta.pagination.count;
					this.pagination.total           = responseData.data.meta.pagination.total;
					this.pagination.total_pages     = responseData.data.meta.pagination.total_pages;
					this.pagination.current_page    = responseData.data.meta.pagination.current_page;
					this.pagination.per_page        = responseData.data.meta.pagination.per_page;
				}
                this.allSelected= false;
                this.indeterminate= false;
			},
			toggleShowOnly(type){
				this.showOnly = type;
				this.allSelected = 0;
				this.selectedItems = [];
				this.pagination.current_page = 1;
				this.getLessons();
			}
		},
		created : function(){
			this.listItems = this.lessons;
			if (this.lessonsMeta!=undefined && this.lessonsMeta.pagination!=undefined){
				this.pagination.count           = this.lessonsMeta.pagination.count;
				this.pagination.total           = this.lessonsMeta.pagination.total;
				this.pagination.total_pages     = this.lessonsMeta.pagination.total_pages;
				this.pagination.current_page    = this.lessonsMeta.pagination.current_page;
				this.pagination.per_page        = this.lessonsMeta.pagination.per_page;
			}
			if (this.getUrlParameter('s'))
				this.searchString = this.getUrlParameter('s');
			if (this.getUrlParameter('instructor'))
				this.instructor = this.getUrlParameter('instructor');
			if (this.getUrlParameter('type'))
				this.showOnly = this.getUrlParameter('type');

			this.debouncedGetLessons = _.debounce(this.searchLessons, 500)
		},
		watch: {
			searchString: function (newSearchString, oldSearchString) {
				this.debouncedGetLessons()
			},
		},
		computed : {
			firstListItemNumber : function() {
				return this.pagination.current_page*this.pagination.per_page - this.pagination.per_page + 1;
			},
			lastListItemNumber : function() {
				if ( this.pagination.count == this.pagination.per_page ){
					return this.firstListItemNumber + this.pagination.per_page - 1;
				}else{
					return this.firstListItemNumber + this.pagination.count - 1;
				}
			}
		},
		mounted() {
			this.$root.$on('lessonsCancelled', () => {
				if (this.pagination.count == 1 && this.pagination.current_page>1){ // last lesson on page cancelled > go to prev page
					this.pagination.current_page -= 1;
				}
				this.getLessons();
			});
		}
	}
</script>