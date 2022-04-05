<template>
    <div class="password-form-container">
        <form method="post" @submit.prevent="onSubmit">

            <div class="row w-100">
                <div class="col-lg-6 col-12">
                    <div class="form-group has-feedback" :class="{ 'has-error' : errors.max_allowed_instructor_invites }">
                        <label>Maximum allowed Instructor invitations (total sent: {{ totalSent }}; total applied : {{ totalApplied }} )</label>
                        <input type="number" min="0" class="form-control" v-model="fields.max_allowed_instructor_invites" placeholder="">
                        <span class="help-block" v-if="errors.max_allowed_instructor_invites">
                            <strong>{{ errors.max_allowed_instructor_invites[0] }}</strong>
                        </span>
                        <p v-if="fields.max_allowed_instructor_invites==null || fields.max_allowed_instructor_invites==''">Now equal to default system value ({{ defaultMaxAllowedInstructorInvites }})</p>
                    </div>
                </div>
            </div>
            <div v-if="errorText" class="has-error form-group">{{ errorText }}</div>
            <div v-if="successText" class="has-success form-group">{{ successText }}</div>

            <div class="form-group">
               <button type="submit" class="btn btn-primary btn-flat">Update</button>
            </div>
        </form>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : ['userId', 'currentValue', 'defaultMaxAllowedInstructorInvites', 'totalSent', 'totalApplied'],
		data() {
			return {
				formUserId : null,
				fields: {
					max_allowed_instructor_invites : ''
                },
			}
		},
		methods: {
			onSubmit() {
				var submitUrl = '/api/admin/users/count-invites-allowed';
				if (this.formUserId!=null){
					submitUrl += ('/' + this.formUserId);
					this.apiPut(submitUrl, this.fields);
                }
			},
			componentHandlePutResponse(responseData) {
				setTimeout(() => {
					this.successText = '';
					this.errorText = '';
				},1000)
			},
		},
		created : function(){
			this.fields.max_allowed_instructor_invites = this.currentValue==undefined ? null : this.currentValue;
			this.formUserId = this.userId;
		},
	}
</script>