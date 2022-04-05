<template>
    <div>
        <div class="clients-table-header">
            <div class="table-top d-flex align-items-center">
                <h2>Instructors</h2>
                <div class="ml-3 d-flex align-items-center">
                    <button @click.prevent="toggleShowOnly('active')" :class="{'active':(showOnly=='active')}">Active</button>
                    <!--<button @click.prevent="toggleShowOnly('approved')" :class="{'active':(showOnly=='approved')}">Approved</button>-->
                    <button @click.prevent="toggleShowOnly('suspended')" :class="{'active':(showOnly=='suspended')}">Suspended</button>
                    <button @click.prevent="toggleShowOnly('on_review')" :class="{'active':(showOnly=='on_review')}">On Review</button>
                </div>
            </div>
            <modal-invate :text-title="'Invite Instructors'" :text-button="'Invite Instructors'" :invite-type="'instructors'"></modal-invate>
        </div>
        <div class="table-responsives">
            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

            <table class="table">
                <thead>
                <tr>
                    <th class="w-55 hidden-in-mobile"></th>
                    <th class="w-100px">Instagram</th>
                    <th class="w-140">Name</th>
                    <th class="w-140 hidden-in-mobile hidden-in-tabled">Email</th>
                    <th class="w-140 hidden-in-mobile">Phone</th>
                    <th class="hidden-in-mobile hidden-in-tabled">Genre</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(user, index) in listItems">
                    <td class="hidden-in-mobile"><img :src="user.profile.image" alt=""/></td>
                    <td class="width-fix"><div class="width-fix-content"><a v-if="user.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + user.profile.instagram_handle" target="_blank">{{ '@' + user.profile.instagram_handle }}</a></div></td>
                    <td class="width-fix"><div class="width-fix-content"><a :href="'/profile/'+user.id" class="link-to-profile">{{ user.full_name }}</a></div></td>
                    <td class="width-fix hidden-in-mobile hidden-in-tabled"><div class="width-fix-content">{{ user.email }}</div></td>
                    <td class="hidden-in-mobile">{{ user.profile.mobile_phone }}</td>
                    <td class="hidden-in-mobile hidden-in-tabled"  v-html="getClientGenresList(user.genres)"></td>
                    <td class="action">
                        <div class="dropdown">
                            <button class="btn btn-left" @click.prevent="showUserPublicProfile(user.id)">View</button>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" :href="'/profile/edit/'+user.id">Edit profile</a>
                                <a class="dropdown-item" href="#" @click.prevent="notifyInstructor(user.id)">Contact</a>
                                <a class="dropdown-item" :href="'/backend/lessons?instructor='+user.id">Lessons</a>
                                <a class="dropdown-item" href="#" @click.prevent="">Invited instructors</a>
                                <a v-if="user.status=='on_review'" class="dropdown-item" href="#" @click.prevent="approveUser(user)">Approve</a>
                                <a v-if="user.status=='on_review'" class="dropdown-item" href="#" @click.prevent="denyUser(user)">Deny</a>
                                <a v-if="user.status=='approved'" class="dropdown-item" href="#" @click.prevent="sendFinishRegistrationReminder(user)">Resend Reminder</a>
                                <a v-if="user.status!='blocked' && user.status!='on_review'" class="dropdown-item red" href="#" @click.prevent="blockUser(user)">Suspend</a>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <a :href="'backend/instructors?status=' + showOnly" class="btn btn-block btn-secondary" v-if="this.pagination.total>5">View all</a>
    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
			instructors: null,
			instructorsMeta: null
		},
		data() {
			return {
				showOnly: 'active',
				pagination : {
					total : 0,
					total_pages : 0,
					current_page : 0,
					per_page : 0
				},
				reloadUsers : true
			}
		},
		methods: {
			toggleShowOnly(status){
				this.showOnly = status;
				this.allSelected = 0;
				this.selectedItems = [];
				this.pagination.current_page = 1;
				this.getUsers();
			},
			blockUser(user){
				this.apiDelete('/api/admin/users/' + user.id);
			},
            notifyInstructor(client){
                var temp = [];
                temp.push({'id': client});
                this.$root.$emit('initNotificationsForm', temp);
            },
			notifyUser(user){
                var temp = [];
                temp.push({'id': user});
                this.$root.$emit('initNotificationsForm', temp);
			},
			searchUsers(){
				this.pagination.current_page = 1;
				this.getUsers();
			},
			getUsers() {
				let queryParams = {};

				queryParams.status = this.showOnly;
				queryParams.limit = 5;

				this.apiGet('/api/admin/instructors', {
					params: queryParams
				});
			},
			approveUser(user){
				this.apiPut('/api/admin/instructor/approve/' + user.id);
			},
			denyUser(user){
				this.apiPut('/api/admin/instructor/deny/' + user.id);
			},
			sendFinishRegistrationReminder(user){
				this.reloadUsers = false;
				this.apiPost('/api/admin/instructor/resend-registration-reminder/' + user.id);
			},
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data.data;
				this.selectedItems = [];
				if (responseData.data.meta!=undefined && responseData.data.meta.pagination!=undefined){
					this.pagination.count           = responseData.data.meta.pagination.count;
					this.pagination.total           = responseData.data.meta.pagination.total;
					this.pagination.total_pages     = responseData.data.meta.pagination.total_pages;
					this.pagination.current_page    = responseData.data.meta.pagination.current_page;
					this.pagination.per_page        = responseData.data.meta.pagination.per_page;
				}
			},
			componentHandleDeleteResponse(responseData){
				this.getUsers();
			},
			componentHandlePutResponse(responseData){
				this.getUsers();
			},
			componentHandlePostResponse(responseData){
				if (this.reloadUsers==true)
					this.getUsers();
				this.reloadUsers = true;
			},
			showUserPublicProfile(userId){
				window.location = '/profile/' + userId;
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
		},
		mounted() {
			this.$root.$on('instructorClientsAdded', () => {
				this.getUsers();
			});
		}
	}
</script>