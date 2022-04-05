<template>
    <div id="clients-add-form-container">
        <a href="#" class="btn-green" @click.prevent="openPopup"><img src="/images/add-client.png" alt=""> Add Clients</a>

        <magnific-popup-modal @close='clearFormAndClosePopup' class="add-client-form" :show="false" :config="{closeOnBgClick:true,showCloseBtn:true,enableEscapeKey:false}" ref="modal">
            <div class="modal-top">
                <h2>Add clients</h2>
                <input v-model="searchString" placeholder="Search by name , instagram, email or phone number"/>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <!--<thead>-->
                        <!--<tr>-->
                            <!--<th scope="col"></th>-->
                            <!--<th scope="col"></th>-->
                            <!--<th scope="col">Instagram</th>-->
                            <!--<th scope="col">Name</th>-->
                            <!--<th scope="col">Email</th>-->
                            <!--<th scope="col">Phone</th>-->
                        <!--</tr>-->
                        <!--</thead>-->
                        <tbody>
                        <tr v-for="(student, index) in listItems">
                            <td><span class="checkbox-wrapper"><label><input type="checkbox" v-model="selectedStudents" :value="student.id"/><span class="checkmark"></span></label></span></td>
                            <td><img :src="student.profile.image"/></td>
                            <td><a v-if="student.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + student.profile.instagram_handle" target="_blank">{{ student.profile.instagram_handle }}</a></td>
                            <td><a :href="'/profile/'+student.id" class="link-to-profile">{{ student.full_name }}</a></td>
                            <td>{{ student.email }}</td>
                            <td>{{ student.profile.mobile_phone }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>


                <div class="row">
                    <div class="col-12">
                        <span @click="addClients" :class="{'disabled': selectedStudents.length === 0 }" class="btn btn-block btn-default">Add {{ selectedStudents.length }} clients</span>
                    </div>
                    <!--<div class="col-xs-4">-->
                        <!--<span class="btn btn-danger" @click="clearFormAndClosePopup">Close Form</span>-->
                    <!--</div>-->
                </div>

                <div v-if="errorText" class="has-error">{{ errorText }}</div>
                <div v-if="successText" class="has-success">{{ successText }}</div>
            </div>
            <div class="modal-footer">
                <div class="group-input">
                    <client-invitation-form :clearForm="clearForm" :count-invitations-sent="countInvitationsSent" :max-invites-enabled="maxInvitesEnabled" :alternative-invite-input-placeholder="alternativeInviteInputPlaceholder"></client-invitation-form>
                </div>

            </div>
        </magnific-popup-modal>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';
	import MagnificPopupModal from './external/MagnificPopupModal'

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
			maxInvitesEnabled : Number,
			countInvitationsSent : Number,
			alternativeInviteInputPlaceholder : String
		},
		data() {
			return {
				selectedStudents: [],
                searchString : '',
                clearForm: false,
			}
		},
		components: {
			MagnificPopupModal
		},
		methods: {
			getStudents() {
				let queryParams = {};
                queryParams.s = this.searchString;

				this.apiGet('/api/students', {
					params: queryParams
				});
			},
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data.data;
			},
			addClients() {
                this.apiPost('/api/instructor/clients', {students : this.selectedStudents});
			},
			componentHandlePostResponse(responseData) {
				this.clearFormAndClosePopup();
				this.$root.$emit('instructorClientsAdded');
			},
			openPopup(){
				this.$refs.modal.open();
                this.clearForm = false;
				this.getStudents();
			},
			clearFormAndClosePopup(){

				this.selectedStudents = [];
				this.searchString = null;
				this.successText = null;
				this.clearForm = true;
				this.$refs.modal.close()
			}
		},
        created : function(){
			this.debouncedGetStudents = _.debounce(this.getStudents, 500)
        },
		watch: {
			searchString : function (newSearchString, oldSearchString) {
				if (newSearchString!==null)
				    this.debouncedGetStudents();
			}
		}
	}
</script>