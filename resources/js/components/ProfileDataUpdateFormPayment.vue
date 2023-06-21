<template>
    <div id="profile-form-container">
        <form method="post" @submit.prevent="onSubmit">

            <div class="row d-flex flex-wrap" >
                <div class="form-group mb-1 d-flex justify-content-between">
                    <h4>Billing Details</h4>
                    <div class="checkbox-wrapper">
                        <div class="field">
                            <label for="use-personal-info">
                                <input v-model="fields.usePersonalInfo" type="checkbox" id="use-personal-info" >
                                <span class="checkmark"></span>
                                Use personal info
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group f-name w-50 has-feedback" :class="{ 'has-error' : errors.first_name,'color': !isStudent}">
                    <label>Complete name</label>
                    <input type="text" class="form-control" required name="first_name" value="" v-model="fields.first_name" placeholder="First Name">
                    <span class="help-block" v-if="errors.first_name">
                    <strong>{{ errors.first_name[0] }}</strong>
                </span>
                </div>

                <div class="form-group l-name w-50 has-feedback" :class="{ 'has-error' : errors.last_name,'color': !isStudent }">
                    <label style="opacity: 0;visability: hidden;">Complete name</label>
                    <input type="text" class="form-control" required name="last_name" value="" v-model="fields.last_name" placeholder="Last Name">
                    <span class="help-block" v-if="errors.last_name">
                    <strong>{{ errors.last_name[0] }}</strong>
                </span>
                </div>

                <div class="form-group has-feedback" :class="{ 'has-error' : errors.company_name }">
                    <label>Company name (optional)</label>
                    <input type="text" class="form-control" name="address" value="" v-model="fields.company_name" placeholder="">
                    <span class="help-block" v-if="errors.company_name">
                <strong>{{ errors.company_name[0] }}</strong>
                </span>
                </div>


                <div class="form-group has-feedback" :class="{ 'has-error' : errors.address }">
                    <label for="">Street Address</label>
                <input type="text" class="form-control" name="address" value="" v-model="fields.address" placeholder="House number and street name">
                <span class="help-block" v-if="errors.address">
                <strong>{{ errors.address[0] }}</strong>
                </span>
                </div>

                <div class="form-group has-feedback" :class="{ 'has-error' : errors.state }">
                    <label>State</label>
                    <select class="form-control" name="state" v-model="fields.state">
                        <option value="">Select State</option>
                        <option v-for="state in usStates" :value='state.code'>{{ state.name }}</option>
                    </select>
                    <span class="help-block" v-if="errors.state">
                    <strong>{{ errors.state[0] }}</strong>
                </span>
                </div>


                <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.city }">
                    <label>Town / City</label>
                    <input type="text" class="form-control" name="city" value="" v-model="fields.city" placeholder="City">
                    <span class="help-block" v-if="errors.city">
                    <strong>{{ errors.city[0] }}</strong>
                </span>
                </div>


                <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.zip }">
                    <label>ZIP</label>
                    <input type="text" class="form-control" name="zip" value="" v-model="fields.zip" placeholder="ZIP code">
                    <span class="help-block" v-if="errors.zip">
                    <strong>{{ errors.zip[0] }}</strong>
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
                    <masked-input :class="'form-control'" v-model="fields.mobile_phone" :placeholder="'(___) ___ ____'" mask="(111) 111 1111" />
                    <span class="help-block" v-if="errors.mobile_phone">
                    <strong>{{ errors.mobile_phone[0] }}</strong>
                </span>
                </div>


                <div v-if="errorText" class="form-group has-error">{{ errorText }}</div>
            </div>

        </form>

    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import MaskedInput from 'vue-masked-input';

	export default {
		components: {
			MaskedInput
		},
		mixins : [siteAPI],
		props : ["userProfileData", "categorizedGenres",'isStudent'],
		data() {
			return {
				fields: {
					first_name : '',
				    last_name : '',
				    email : '',
                    address : '',
                    city : '',
                    state : '',
                    zip : '',
                    dob : '',
					mobile_phone : '',
                    genres : [],
                    about_me : '',
					gender : '',
                    company_name: '',
                    usePersonalInfo: ''
                }
			}
		},
		methods: {
			onSubmit() {
                if (moment(this.fields.dob))
                    this.fields.dob = moment(this.fields.dob).format('YYYY-MM-DD');


                this.apiPut('/api/user/profile', this.fields)
			}
		},
	}
</script>