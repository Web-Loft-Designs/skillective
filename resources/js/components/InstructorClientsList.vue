<template>
    <div id="profile-clients-container">
        <div class="table-top d-flex align-items-center">
            <h2 class=" page-title">Clients</h2>
        </div>

        <div class="d-flex responsive-mobile justify-content-between align-items-center">
            <div class="d-flex  responsive-mobile align-items-center">
                <div v-if="selectedClients.length>0" class="d-flex align-items-center">
                    <span @click="deleteManyClients" class="btn btn-danger mr-2">Delete {{ selectedClients.length }} clients</span>
                    <span @click="notifyManyClients" class="btn btn-notify mr-2">Notify {{ selectedClients.length }} clients</span>
                </div>
                <input type="text" v-model="searchString" placeholder="Search client"/>
            </div>
            <div>
                <instructor-clients-add-form :count-invitations-sent="countInvitationsSent" :max-invites-enabled="maxInvitesEnabled" :alternative-invite-input-placeholder="alternativeInviteInputPlaceholder"></instructor-clients-add-form>
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
                                    <input @change="selectAll" :indeterminate.prop="indeterminate" v-model="allSelected" id="checkAll" type="checkbox"/>
                                    <span class="checkmark" :class="{'indeterminate':indeterminate === true}"></span>
                                </label>
                            </span>
                        </th>
                        <th  class="w-33" scope="col">#</th>
                        <th  class="w-55" scope="col"></th>
                        <th class="w-100px" scope="col">Instagram</th>
                        <th  class="w-140" scope="col">Name</th>
                        <th  class="w-140" scope="col">Email</th>
                        <th  class="w-140" scope="col">Phone</th>
                        <th scope="col">Genre</th>
                        <!--<th scope="col">Notify via</th>-->
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(client, index) in listItems">
                        <td><span class="checkbox-wrapper"><label><input @change="select" type="checkbox" v-model="selectedClients" :value="client.id"/><span class="checkmark"></span></label></span></td>
                        <td>{{ (firstListItemNumber + index) }}</td>
                        <td><img :src="client.profile.image"/></td>
                        <td  class="width-fix"><a class="width-fix-content" v-if="client.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + client.profile.instagram_handle" target="_blank">{{ '@' + client.profile.instagram_handle }}</a></td>
                        <td  class="width-fix"><div  class="width-fix-content"><a :href="'/profile/'+client.id" class="link-to-profile">{{ client.full_name }}</a></div></td>
                        <td  class="width-fix"><div  class="width-fix-content">{{ client.email }}</div></td>
                        <td >{{ client.profile.mobile_phone }}</td>
                        <td  v-html="getClientGenresList(client.genres)"></td>
                        <td >
                            <span class="btn btn-notify" @click="notifyClient(client)" v-if="client.profile.notification_methods.length>0">Notify</span>
                            <span class="btn btn-danger" @click="deleteClient(client)">Delete</span>
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
			clients: null,
			clientsMeta: {},
			maxInvitesEnabled : Number,
			countInvitationsSent : Number,
			alternativeInviteInputPlaceholder : String
        },
		components : {
			Paginate,
            ConfirmationPopup,
		},
		data() {
			return {
				selectedClients: [],
                searchString : '',
                allSelected: false,
                indeterminate: false,
                pagination : {
					total : 0,
                    total_pages : 0,
                    current_page : 0,
                    per_page : 25
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
                this.selectedClients = [];

                if (this.allSelected) {
                    this.indeterminate = false;
                    for (let user in this.listItems) {
                        this.selectedClients.push(this.listItems[user].id.toString());
                    }
                }
            },
            select: function() {
                if(this.listItems.length == this.selectedClients.length ) {
                    this.allSelected = 1;
                    this.indeterminate = false;
                } else if(this.selectedClients.length === 0) {
                    this.allSelected = 0;
                    this.indeterminate = false;
                } else {
                    this.allSelected = 0;
                    this.indeterminate = true;
                }

            },
			getClients() {
				let queryParams = {};
				if (this.pagination.current_page != undefined)
					queryParams.page = this.pagination.current_page;
				else
					queryParams.page = 1;

                queryParams.s = this.searchString;

				this.updateUrlQueryParams(queryParams);

				this.apiGet('/api/instructor/clients', {
					params: queryParams
				});
			},
			deleteClient(client){
                this.confirmDelete("Are you sure you want to delete this client?", () => {
//				    this.autoHideLoader = false;
				    this.apiDelete('/api/instructor/client/' + client.id);
//				    this.autoHideLoader = true;
                });
			},
            deleteManyClients(){
                this.confirmDelete("Are you sure you want to delete selected clients?", () => {
                    this.allSelected=false;
                    this.indeterminate=false;
                    this.apiPost('/api/instructor/clients/remove', {clients : this.selectedClients});
                });
            },
			notifyManyClients(){
                let clientSelect = [];
                this.clients.forEach((item) => {
                    this.selectedClients.forEach((itemId)=> {
                        if(item.id == itemId) {
                            clientSelect.push(item);
                        }
                    })
                });
				this.$root.$emit('initNotificationsForm', clientSelect);
            },
			notifyClient(client){
				this.$root.$emit('initNotificationsForm', [client]);
			},
			componentHandleDeleteResponse(responseData){
				if (this.pagination.count == 1 && this.pagination.current_page>1){ // last client on page deleted > go to prev page
					this.pagination.current_page -= 1;
                }
				this.getClients();
			},
			componentHandlePostResponse(responseData){
				if (this.pagination.count == parseInt(responseData.data) && this.pagination.current_page>1){ // last client on page deleted > go to prev page
					this.pagination.current_page -= 1;
				}
				this.selectedClients = [];
				this.getClients();
			},
			paginatorClickCallback(pageNum) {
				this.pagination.current_page = pageNum;
				this.getClients();
			},
			onChangePerPage() {
				this.pagination.current_page = 1;
				Cookies.set('instructorClientsPerPage', this.pagination.per_page);
				this.getClients();
			},
            searchClients(){
				this.pagination.current_page = 1;
				this.getClients();
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
			this.listItems = this.clients;
			if (this.clientsMeta!=undefined && this.clientsMeta.pagination!=undefined){
				this.pagination.count           = this.clientsMeta.pagination.count;
				this.pagination.total           = this.clientsMeta.pagination.total;
				this.pagination.total_pages     = this.clientsMeta.pagination.total_pages;
				this.pagination.current_page    = this.clientsMeta.pagination.current_page;
				this.pagination.per_page        = this.clientsMeta.pagination.per_page;
			}
			if (this.getUrlParameter('s'))
				this.searchString = this.getUrlParameter('s');

			this.debouncedGetClients = _.debounce(this.searchClients, 500)
        },
		watch: {
			searchString: function (newSearchString, oldSearchString) {
				this.debouncedGetClients()
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
			this.$root.$on('instructorClientsAdded', () => {
				this.searchClients();
			});
		}
	}
</script>