<template>
    <div class="form-wrap">
        <form method="post" @submit.prevent="onSubmit" v-if="!formSubmitted">

            <div class="wrap">
                <div class="row">
                    <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.first_name }">
                            <label for="contact-first-name">First Name</label>
                            <input type="text" id="contact-first-name" class="form-control" required name="first_name" value="" v-model="fields.first_name" placeholder="First Name">
                            <span class="help-block" v-if="errors.first_name">
                            <strong>{{ errors.first_name[0] }}</strong>
                            </span>
                    </div>
                    <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.last_name }">
                            <label for="contact-last-name">Last Name</label>
                            <input type="text" id="contact-last-name" class="form-control" required name="last_name" value="" v-model="fields.last_name" placeholder="Last Name">
                            <span class="help-block" v-if="errors.last_name">
                            <strong>{{ errors.last_name[0] }}</strong>
                            </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.email }">
                        <label>Email</label>
                        <input type="email" class="form-control" required name="email" value="" v-model="fields.email" placeholder="Email">
                        <span class="help-block" v-if="errors.email">
                        <strong>{{ errors.email[0] }}</strong>
                        </span>
                    </div>

                    <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.mobile_phone }">
                        <label>Phone number</label>
                        <masked-input :class="'form-control'" v-model="fields.mobile_phone" :placeholder="'+1 (___) ___ ____'" mask="\+1 (111) 111 1111" />
                        <span class="help-block" v-if="errors.mobile_phone">
                        <strong>{{ errors.mobile_phone[0] }}</strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.address }">
                            <label for="contact-address">Address</label>
                            <input type="text" id="contact-address" class="form-control" name="address" value="" v-model="fields.address" placeholder="Address">
                            <span class="help-block" v-if="errors.address">
                                <strong>{{ errors.address[0] }}</strong>
                            </span>
                    </div>
                    <div class="form-group w-50 has-feedback" :class="{ 'has-error' : errors.reason }">
                        <label>Message</label>
                        <textarea class="form-control" name="reason"  v-model="fields.reason" placeholder="Your message"></textarea>
                        <span class="help-block" v-if="errors.reason">
                        <strong>{{ errors.reason[0] }}</strong>
                        </span>
                    </div>
                </div>

                <div class="row bottom-row">
                    <div class="submit form-group">
                        <input type="submit" class="btn" value="Send" id="contact-submit">
                    </div>
                </div>
            </div>
        </form>

        <div v-if="errorText" class="has-error">{{ errorText }}</div>
        <div v-if="formSubmitted" class="has-success">{{ successText }}</div>
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
        props : ['currentUserData'],
		data() {
			return {
				formSubmitted: false,
				fields: {
					first_name : '',
				    last_name : '',
				    email : '',
                    address : '',
					mobile_phone : '',
                    reason : ''
                }
			}
		},
		methods: {
			onSubmit() {
				this.apiPost('/api/contact-us', this.fields);
			},
			componentHandlePostResponse(responseData) {
				this.clearSubmittedForm();
				this.formSubmitted = true;
			}
		},
		created: function(){
			this.fields = {
				first_name : this.currentUserData.first_name!=undefined ? this.currentUserData.first_name : '',
				last_name : this.currentUserData.last_name!=undefined ? this.currentUserData.last_name : '',
				email : this.currentUserData.email!=undefined ? this.currentUserData.email : '',
				address : this.currentUserData.address!=undefined ? this.currentUserData.address : '',
				mobile_phone : this.currentUserData.mobile_phone!=undefined ? this.currentUserData.mobile_phone : '',
				reason : ''
			}
		}
	}
</script>