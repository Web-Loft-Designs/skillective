<template>
    <div id="password-form-container ">

        <form method="post" @submit.prevent="onSubmit">

            <!--<p class="login-box-msg">Setup your password</p>-->

            <div class="form-group has-feedback" :class="{ 'has-error' : errors.password }">
                <label>Password</label>
                <input type="password" class="form-control" name="password" value="" v-model="fields.password" placeholder="Set Your Password" autocomplete="off">
                <span class="help-block" v-if="errors.password">
                    <strong>{{ errors.password[0] }}</strong>
                </span>
            </div>

            <div class="form-group has-feedback" :class="{ 'has-error' : errors.password_confirmation }">
                <label>Repeat New Password</label>
                <input type="password" class="form-control" name="password_confirmation" value="" v-model="fields.password_confirmation" placeholder="Confirm Password">
                <span class="help-block" v-if="errors.password_confirmation">
                    <strong>{{ errors.password_confirmation[0] }}</strong>
                </span>
            </div>

            <div v-if="errorText" class="has-error form-group">{{ errorText }}</div>
            <div v-if="successText" class="has-success form-group">{{ successText }}</div>
            <div class="form-group has-feedback">
                <button type="submit" class="btn  btn-block btn-flat">Finish Registration</button>
            </div>
        </form>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		data() {
			return {
				fields: {
					email : null,
                    token : null,
					password : '',
					password_confirmation : ''
                },
			}
		},
		methods: {
			onSubmit() {
				this.apiPost('/api/user/finish-registration', this.fields);
			},
			componentHandlePostResponse(responseData) {
				if (responseData.data.redirect != undefined)
				    window.location = responseData.data.redirect;
			},
		},
        created : function(){
			if (this.getUrlParameter('email'))
				this.fields.email = this.getUrlParameter('email');
			if (this.getUrlParameter('token'))
				this.fields.token = this.getUrlParameter('token');
        }
	}
</script>