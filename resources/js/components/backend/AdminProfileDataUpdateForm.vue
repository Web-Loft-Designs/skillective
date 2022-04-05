<template>
    <div id="profile-form-container">
        <form method="post" @submit.prevent="onSubmit">

            <p class="login-box-msg">Personal info</p>
            <div class="d-flex flex-wrap" >
                <div class="form-group f-name  w-50 has-feedback" :class="{ 'has-error' : errors.first_name,'color': !isStudent}">
                    <label>Complete name</label>
                    <input type="text" class="form-control" required name="first_name" value="" v-model="fields.first_name" placeholder="First Name">
                    <span class="help-block" v-if="errors.first_name">
                    <strong>{{ errors.first_name[0] }}</strong>
                </span>
                </div>

                <div class="form-group l-name color w-50 has-feedback" :class="{ 'has-error' : errors.last_name,'color': !isStudent }">
                    <label style="opacity: 0;visability: hidden;">Complete name</label>
                    <input type="text" class="form-control" required name="last_name" value="" v-model="fields.last_name" placeholder="Last Name">
                    <span class="help-block" v-if="errors.last_name">
                    <strong>{{ errors.last_name[0] }}</strong>
                </span>
                </div>

                <div class="form-group  has-feedback" :class="{ 'has-error' : errors.email }">
                    <label>Email address</label>
                    <input type="email" class="form-control" required name="email" value="" v-model="fields.email" placeholder="Email">
                    <span class="help-block" v-if="errors.email">
                    <strong>{{ errors.email[0] }}</strong>
                </span>
                </div>

                <div class="form-group has-feedback" :class="{ 'has-error' : errors.mobile_phone }">
                    <label>Phone number</label>
                    <masked-input :class="'form-control'" v-model="fields.mobile_phone" :placeholder="'+1 (___) ___ ____'" mask="\+1 (111) 111 1111" />
                    <span class="help-block" v-if="errors.mobile_phone">
                    <strong>{{ errors.mobile_phone[0] }}</strong>
                </span>
                </div>

                <div v-if="errorText" class="form-group has-error">{{ errorText }}</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>

        </form>

    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import MaskedInput from 'vue-masked-input';

	export default {
		components: {
			MaskedInput
		},
		mixins : [siteAPI],
		props : {
			userProfileData : null,
		},
		data() {
			return {
				fields: {
					first_name : '',
				    last_name : '',
				    email : '',
					mobile_phone : '',
                }
			}
		},
		methods: {
			onSubmit() {
				var submitUrl = '/api/admin/profile';
				this.apiPut(submitUrl, this.fields);
			}
		},
		created: function(){
			this.fields =  {
				first_name : this.userProfileData.first_name,
				last_name : this.userProfileData.last_name,
				email : this.userProfileData.email,
				mobile_phone : this.userProfileData.profile.mobile_phone,
			};
		}
	}
</script>