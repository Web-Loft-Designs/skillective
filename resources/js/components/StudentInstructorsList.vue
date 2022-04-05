<template>
    <div id="profile-instructors-container">
        <div class="table-top d-flex align-items-center">
            <h2 class=" page-title">Instructors</h2>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div v-if="selectedInstructors.length>0">
                    <span @click="deleteManyInstructors" class="btn btn-danger mr-2">Delete {{ selectedInstructors.length }} instructors</span>
                    <!--<span @click="notifyManyInstructors" class="btn btn-primary mr-2">Notify {{ selectedInstructors.length }} instructors</span>-->
                </div>
                <input type="text" v-model="searchString" placeholder="Search instructor"/>
            </div>

        </div>

            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">
                            <span class="checkbox-wrapper">
                                <label for="checkAll">
                                    <input @change="selectAll" id="checkAll" :indeterminate.prop="indeterminate" v-model="allSelected" type="checkbox"/>
                                    <span class="checkmark" :class="{'indeterminate':indeterminate === true}"></span>
                                </label>
                            </span>
                        </th>
                        <th class="w-46" scope="col w-l">-</th>
                        <th class="w-46" scope="col w-l">-</th>
                        <th class="w-46" scope="col w-l">#</th>
                        <th class="w-55" scope="col w-a"></th>
                        <th class="w-100px" scope="col">Instagram</th>
                        <th class="w-140" scope="col">Name</th>
                        <!--<th scope="col">Email</th>-->
                        <!--<th scope="col">Phone</th>-->
                        <th scope="col">Genre</th>
                        <!--<th scope="col">Notify via</th>-->
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(instructor, index) in listItems">
                        <td><span class="checkbox-wrapper"><label><input @change="select" type="checkbox" v-model="selectedInstructors" :value="instructor.id"/><span class="checkmark"></span></label></span></td>
                        <td class="w-l">
                            <student-instructor-geo-notifications-toggle v-bind:instructor-id="instructor.id" v-bind:geo-notifications-allowed="instructor.geoNotificationsAllowed?true:false"></student-instructor-geo-notifications-toggle>
                            <student-instructor-virtual-lesson-notifications-toggle v-bind:instructor-id="instructor.id" v-bind:virtual-lesson-notifications-allowed="instructor.virtualNotificationsAllowed?true:false"></student-instructor-virtual-lesson-notifications-toggle>

                        </td>
                        <td class="w-l">
                            <favorite-instructor v-bind:instructor-id="instructor.id" v-bind:is-favorite="instructor.isFavorite?true:false"></favorite-instructor>
                        </td>
                        <td class="w-l">{{ (pagination.total - firstListItemNumber - index +1) }}</td>
                        <td class="w-a"><img :src="instructor.profile.image"/></td>
                        <td class="width-fix"><div class="width-fix-content"><a v-if="instructor.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + instructor.profile.instagram_handle" target="_blank">{{ instructor.profile.instagram_handle }}</a></div></td>
                        <td class="width-fix"><div class="width-fix-content"><a :href="'/profile/'+instructor.id" class="link-to-profile">{{ instructor.full_name }}</a></div></td>
                        <!--<td>{{ instructor.email }}</td>-->
                        <!--<td>{{ instructor.profile.mobile_phone }}</td>-->
                        <td class="cusotm-w" v-html="getClientGenresList(instructor.genres)"></td>
                        <!--<td>-->
                            <!--<span v-if="instructor.profile.notification_methods.indexOf('email')!==-1" >email</span>-->
                            <!--<span v-if="instructor.profile.notification_methods.indexOf('sms')!==-1" >sms</span>-->
                            <!--<span v-if="instructor.profile.notification_methods.indexOf('whatsapp')!==-1" >whatsapp</span>-->
                        <!--</td>-->
                        <td>
                            <!--<span class="btn btn-default" @click="notifyInstructor" v-if="instructor.profile.notification_methods.length>0">Notify</span>-->
                            <span class="btn btn-danger" @click="deleteInstructor(instructor)">Delete</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row" v-if="pagination.total_pages>1">
            <div class="col-6 select-show">
                <label>Per Page</label>
                <select v-model="pagination.per_page" @change="onChangePerPage">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center custom-pag">
                <span>{{ firstListItemNumber }} - {{ lastListItemNumber }} of {{ pagination.total }}</span>
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

        <confirmation-popup ref="confirmationPopup" />

    </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js';
import skillectiveHelper from '../mixins/skillectiveHelper.js';
import Paginate from 'vuejs-paginate';
import ConfirmationPopup from "./instructor/ConfirmationPopup/ConfirmationPopup.vue";

export default {
    mixins : [siteAPI, skillectiveHelper],
    props: {
        instructors: null,
        instructorsMeta: {}
    },
    components : {
        Paginate,
        ConfirmationPopup,
    },
    data() {
        return {
            selectedInstructors: [],
            searchString : '',
            allSelected: false,
            indeterminate: false,
            pagination : {
                total : 0,
                total_pages : 0,
                current_page : 0,
                per_page : 0
            }
        }
    },
    methods: {
        confirmDelete(text, action) {
            this.$refs.confirmationPopup.showConfirm(text, () => {
                action();
            });
        },
        selectAll: function() {
            this.selectedInstructors = [];

            if (this.allSelected) {
                this.indeterminate = false;
                for (let user in this.listItems) {
                    this.selectedInstructors.push(this.listItems[user].id.toString());
                }
            }
        },
        select: function() {
            if(this.listItems.length == this.selectedInstructors.length ) {
                this.allSelected = 1;
                this.indeterminate = false;
            } else if(this.selectedInstructors.length === 0) {
                this.allSelected = 0;
                this.indeterminate = false;
            } else {
                this.allSelected = 0;
                this.indeterminate = true;
            }
        },
        getInstructors() {
            let queryParams = {};
            if (this.pagination.current_page != undefined)
                queryParams.page = this.pagination.current_page;
            else
                queryParams.page = 1;

            queryParams.s = this.searchString;

            this.updateUrlQueryParams(queryParams);

            this.apiGet('/api/student/instructors', {
                params: queryParams
            });
        },
        deleteInstructor(instructor){
            this.confirmDelete("Are you sure you want to delete this instructor?", () => {
                this.apiDelete('/api/student/instructor/' + instructor.id);}
            );
        },
        deleteManyInstructors(){
            this.confirmDelete("Are you sure you want to delete selected instructors?", () => {
                this.apiPost('/api/student/instructors/remove', {instructors : this.selectedInstructors});
            });
        },
        componentHandleDeleteResponse(responseData){
            if (this.pagination.count == 1 && this.pagination.current_page>1){ // last instructor on page deleted > go to prev page
                this.pagination.current_page -= 1;
            }
            this.getInstructors();
        },
        componentHandlePostResponse(responseData){
            if (this.pagination.count == parseInt(responseData.data) && this.pagination.current_page>1){ // last instructor on page deleted > go to prev page
                this.pagination.current_page -= 1;
            }
            this.selectedInstructors = [];
            this.getInstructors();
        },
        paginatorClickCallback(pageNum) {
            this.pagination.current_page = pageNum;
            this.getInstructors();
        },
        onChangePerPage() {
            this.pagination.current_page = 1;
            Cookies.set('studentInstructorsPerPage', this.pagination.per_page);
            this.getInstructors();
        },
        searchInstructors(){
            this.pagination.current_page = 1;
            this.getInstructors();
        },
        componentHandleGetResponse(responseData) {
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

        }
    },
    created : function(){
        this.listItems = this.instructors;
        if (this.instructorsMeta!=undefined && this.instructorsMeta.pagination!=undefined){
            this.pagination.count           = this.instructorsMeta.pagination.count;
            this.pagination.total           = this.instructorsMeta.pagination.total;
            this.pagination.total_pages     = this.instructorsMeta.pagination.total_pages;
            this.pagination.current_page    = this.instructorsMeta.pagination.current_page;
            this.pagination.per_page        = this.instructorsMeta.pagination.per_page;
        }
        this.$set(this.listItems, 'notify', [
            {
                property: false
            }
        ])

        if (this.getUrlParameter('s'))
            this.searchString = this.getUrlParameter('s');

        this.debouncedGetInstructors = _.debounce(this.searchInstructors, 500)
    },
    watch: {
        searchString: function (newSearchString, oldSearchString) {
            this.debouncedGetInstructors()
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
        this.$root.$on('studentInstructorsAdded', () => {
            this.searchInstructors();
        });
    }
}
</script>