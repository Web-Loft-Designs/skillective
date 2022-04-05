<template>
    <div>
        <div class="clients-table-header">
            <div class="table-top d-flex align-items-center">
                <h2>Clients</h2>
                <div class="ml-3 d-flex align-items-center">
                    <button @click.prevent="toggleShowOnly('active')" :class="{'active':(showOnly=='active')}">Active</button>
                    <button @click.prevent="toggleShowOnly('approved')" :class="{'active':(showOnly=='approved')}">Incomplete</button>
                    <button @click.prevent="toggleShowOnly('suspended')" :class="{'active':(showOnly=='suspended')}">Suspended</button>
                </div>
            </div>

           <!--<modal-invate :text-title="'Invite Users'" :text-button="'Invite User'"></modal-invate>-->
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
                    <th class="w-140 hidden-in-mobile">Email</th>
                    <th class="phone-width hidden-in-mobile">Phone</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(user, index) in listItems">
                    <td class="hidden-in-mobile"><img :src="user.profile.image" alt=""/></td>
                    <td class="width-fix"><div class="width-fix-content"><a v-if="user.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + user.profile.instagram_handle" target="_blank">{{ '@' + user.profile.instagram_handle }}</a></div></td>
                    <td class="width-fix"><div class="width-fix-content"><a :href="'/profile/'+user.id" class="link-to-profile">{{ user.full_name }}</a></div></td>
                    <td class="width-fix hidden-in-mobile"><div class="width-fix-content">{{ user.email }}</div></td>
                    <td class="hidden-in-mobile">{{ user.profile.mobile_phone }}</td>
                    <td class="action">
                        <div class="dropdown">
                            <button class="btn btn-left" @click.prevent="showUserPublicProfile(user.id)">View</button>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" :href="'/profile/edit/'+user.id">Edit profile</a>
                                <a class="dropdown-item" href="#" @click.prevent="notifyInstructor(user.id)">Contact</a>
                                <!--<a class="dropdown-item" href="#" @click.prevent="">Invited instructors</a>-->
                                <a v-if="user.status!='blocked'" class="dropdown-item red" href="#" @click.prevent="blockUser(user)">Suspend</a>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <a :href="'backend/students?status=' + showOnly" class="btn btn-block btn-secondary" v-if="this.pagination.total>5">View all</a>
    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
			students: null,
			studentsMeta: null
		},
		data() {
			return {
				showOnly: 'active',
				pagination : {
					total : 0,
					total_pages : 0,
					current_page : 0,
					per_page : 0
				}
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
			getUsers() {
				let queryParams = {};

				queryParams.status = this.showOnly;
				queryParams.limit = 5;

				this.apiGet('/api/admin/students', {
					params: queryParams
				});
			},
            notifyInstructor(client){
                var temp = [];
                temp.push({'id': client});
                this.$root.$emit('initNotificationsForm', temp);
            },
			blockUser(user){
				this.apiDelete('/api/admin/users/' + user.id);
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
			showUserPublicProfile(userId){
				window.location = '/profile/' + userId;
			},
			notifyUser(user){
                var temp = [];
                temp.push({'id': user});
				this.$root.$emit('initNotificationsForm', temp);
			},
		},
        created : function(){
			this.listItems = this.students;
			if (this.studentsMeta!=undefined && this.studentsMeta.pagination!=undefined){
				this.pagination.count           = this.studentsMeta.pagination.count;
				this.pagination.total           = this.studentsMeta.pagination.total;
				this.pagination.total_pages     = this.studentsMeta.pagination.total_pages;
				this.pagination.current_page    = this.studentsMeta.pagination.current_page;
				this.pagination.per_page        = this.studentsMeta.pagination.per_page;
			}
        }
	}
</script>