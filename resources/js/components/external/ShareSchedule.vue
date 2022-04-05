<template>
       <div>
           <div class="block-form">
               <h2>Share schedule</h2>
               <div class="d-flex flex-wrap w-100">
                   <div class="form-group has-feedback" >
                   <label>Email address</label>
                   <input  class="form-control" name="" placeholder="Email" v-model="fields.email">
                       <span class="help-block" v-if="errors.email">
                       <strong>{{ errors.email[0] }}</strong>
                       </span>
                   </div>
                   <!--<div class="form-group has-feedback" >-->
                       <!--<label>Google</label>-->
                       <!--<input  class="form-control" name="" placeholder="Google email" v-model="google">-->
                       <!--&lt;!&ndash;<span class="help-block" v-if="errors.google">&ndash;&gt;-->
                        <!--&lt;!&ndash;<strong>{{ errors.google[0] }}</strong>&ndash;&gt;-->
                        <!--&lt;!&ndash;</span>&ndash;&gt;-->
                   <!--</div>-->
                   <!--<div class="form-group has-feedback" >-->
                       <!--<label>Apple</label>-->
                       <!--<input  class="form-control" name="" placeholder="iCloud email" v-model="apple">-->
                       <!--&lt;!&ndash;<span class="help-block" v-if="errors.apple">&ndash;&gt;-->
                        <!--&lt;!&ndash;<strong>{{ errors.apple[0] }}</strong>&ndash;&gt;-->
                        <!--&lt;!&ndash;</span>&ndash;&gt;-->
                   <!--</div>-->
                   <!--<div class="form-group has-feedback" >-->
                       <!--<label>Outlook</label>-->
                       <!--<input  class="form-control" name="" placeholder="Email" v-model="outlook">-->
                       <!--&lt;!&ndash;<span class="help-block" v-if="errors.outlook">&ndash;&gt;-->
                        <!--&lt;!&ndash;<strong>{{ errors.outlook[0] }}</strong>&ndash;&gt;-->
                        <!--&lt;!&ndash;</span>&ndash;&gt;-->
                   <!--</div>-->
                   <div v-if="errorText" class="has-error form-group">{{ errorText }}</div>
                   <div v-if="successText" class="has-success form-group">{{ successText }}</div>
                   <div class="form-group">
                       <span @click="onSubmit" class="btn btn-default btn-block">Share</span>
                   </div>
                   <!--<div class="col-xs-4">-->
                   <!--<span class="btn btn-danger" @click="clearFormAndClosePopup">Close Form</span>-->
                   <!--</div>-->
               </div>


           </div>
       </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';

	export default {
        props: ['modalWindow'],
		mixins : [siteAPI, skillectiveHelper],
		data() {
			return {
				fields: {
					email: '',
				},
//                google: '',
//                apple: '',
//                outlook: '',
//                errorText: '',
//                successText: '',
			}
		},
		components: {

		},
		methods: {
            close() {
            	this.clearFormAndClosePopup();
                this.modalWindow.close();
            },
			onSubmit() {
				this.apiPost('/api/student/bookings/share', this.fields);
			},
			clearFormAndClosePopup() {
				this.clearSubmittedForm();
				this.successText = null;
				this.formSubmitted = false;
//				this.$refs.modal.close();
			},
			clearSubmittedForm() {
				this.fields.email = '';
			},
			componentHandlePostResponse(responseData) {
				this.formSubmitted = true;
				this.clearSubmittedForm();
				setTimeout(() => {
					this.formSubmitted = false;
					this.successText = null;
				},5000);
			},
		},
		mounted() {

		}
	}
</script>