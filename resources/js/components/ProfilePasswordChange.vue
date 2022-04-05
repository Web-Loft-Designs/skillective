<template>
    <div id="password-form-container" class="password-form-container">
        <form method="post" @submit.prevent="onSubmit">

            <p class="login-box-msg">Change Password</p>
            <div class="row w-100">
                <div class="col-lg-6 col-12">
                    <div v-if="(formUserId==null && hasFakePassword==false)" class="form-group has-feedback" :class="{ 'has-error' : errors.current_password }">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password" value="" v-model="fields.current_password" placeholder="Set Your Password">
                        <span class="help-block" v-if="errors.current_password">
                            <strong>{{ errors.current_password[0] }}</strong>
                        </span>
                    </div>

                    <div class="form-group has-feedback" :class="{ 'has-error' : errors.new_password }">
                        <label v-if="hasFakePassword==true">Password</label>
                        <label v-else>New Password</label>
                        <input type="password" class="form-control" name="new_password" value="" v-model="fields.new_password" placeholder="Set Your Password" autocomplete="off">
                        <span class="help-block" v-if="errors.new_password">
                            <strong>{{ errors.new_password[0] }}</strong>
                        </span>
                    </div>

                    <div class="form-group has-feedback" :class="{ 'has-error' : errors.new_password_confirmation }">
                        <label v-if="hasFakePassword==true">Repeat Password</label>
                        <label v-else>Repeat New Password</label>
                        <input type="password" class="form-control" name="new_password_confirmation" value="" v-model="fields.new_password_confirmation" placeholder="Confirm Password">
                        <span class="help-block" v-if="errors.new_password_confirmation">
                            <strong>{{ errors.new_password_confirmation[0] }}</strong>
                        </span>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <p>Passwords must be at least 8 characters long.
                        The password must contain at least three character categories among the following: Uppercase characters (A-Z), Lowercase characters (a-z), Special character or digit
                    </p>
                </div>
            </div>
            <div v-if="errorText" class="has-error form-group">{{ errorText }}</div>
            <div v-if="successText" class="has-success form-group">{{ successText }}</div>

            <div class="form-group">
               <button type="submit" class="btn btn-primary btn-flat" v-if="hasFakePassword==true">Save</button>
               <button type="submit" class="btn btn-primary btn-flat" v-else>Change</button>
            </div>
        </form>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : ['userId', 'noPassword'],
		data() {
			return {
				hasFakePassword : false,
				formUserId : null,
				fields: {
					current_password : '',
					new_password : '',
					new_password_confirmation : ''
                },
			}
		},
		methods: {
			onSubmit() {
				var submitUrl = '/api/user/password';
				if (this.formUserId!=null)
					submitUrl += ('/' + this.formUserId);
				this.apiPut(submitUrl, this.fields);
			},
			componentHandlePutResponse(responseData) {
				this.hasFakePassword = false;
				this.clearSubmittedForm();
				setTimeout(() => {
					this.successText = '';
					this.errorText = '';
				},1000)
			},
		},
		created : function(){
			this.hasFakePassword = this.noPassword==undefined ? this.hasFakePassword : this.noPassword;
			this.formUserId = this.userId==undefined ? this.formUserId : this.userId;
		},
	}
</script>