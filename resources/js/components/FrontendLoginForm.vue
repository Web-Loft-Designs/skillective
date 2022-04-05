<template>
    <div>
        <button class="mfp-close"></button>
    <h3>Login</h3>
    <form method="post" action="/login" @submit.prevent="onSubmit">
        <div v-if="errorText" class="form-group has-error">{{ errorText }}</div>
        <div v-if="successText" class="form-group has-success">{{ successText }}</div>

        <div class="form-group has-feedback" :class="{ 'has-error' : errors.email }">
            <label>Email address</label>
            <input type="email" class="form-control" v-model="fields.email" name="email" value="" placeholder="your@email.com" tabindex="1">
            <span class="help-block" v-if="errors.email">
                <strong>{{ errors.email[0] }}</strong>
            </span>
        </div>

        <div class="form-group has-feedback" :class="{ 'has-error' : errors.password }">
            <label>Password <a href="/password/reset">Forgot password?</a></label>
            <input type="password" class="form-control" placeholder="Enter your password" name="password" v-model="fields.password" tabindex="2">
            <span class="help-block" v-if="errors.password">
                <strong>{{ errors.password[0] }}</strong>
            </span>
        </div>
        <div class="form-group">
            <button type="submit" class="btn">Login</button>
        </div>
        <div class="separator"></div>
        <div class="form-group">
            <!--<a :href="loginUsingInstagramUrl" class="btn-blue" ><img src="/images/instagram.png" alt=""> Login with Instagram</a>-->
            <a :href="loginUsingFacebookUrl" class="btn-blue" ><img src="/images/facebook.png" alt=""> Login with Facebook</a>
            <br>
            <a :href="loginUsingGoogleUrl" class="btn-blue" ><img src="/images/google.png" alt=""> Login with Google</a>
        </div>
        <div class="form-group">
            <p>Don`t have an account? <a href="/student/register">Register</a></p>
        </div>
        <!--<div class="d-flex flex-wrap">-->
            <!--<div class="col-xs-8">-->
            <!--<div class="checkbox icheck">-->
                <!--<label>-->
                    <!--<input type="checkbox" name="remember"> Remember Me-->
                    <!--</label>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->
    </form>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props : [
//			'loginUsingInstagramUrl',
			'loginUsingFacebookUrl',
			'loginUsingGoogleUrl'
        ],
		data() {
			return {
				fields : {
					email : null,
					password : null
				}
			}
		},
		methods: {
			onSubmit() {
                this.apiPost('/login', this.fields);
			},
			componentHandlePostResponse(responseData) {
				window.location = responseData.redirect;
			}
		}
	}
</script>