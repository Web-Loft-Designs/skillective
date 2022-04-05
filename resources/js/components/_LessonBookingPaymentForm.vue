<template>
    <div class="section-payments">

        <form method="post">
            <h3 class="custom-padding">Please Submit Payment to Reserve your spot</h3>
            <div class="form-group checkbox-wrapper mb-5 has-feedback">
                <div class="field">

                    <label for="stored-payment-information">
                        <input  type="checkbox" id="stored-payment-information" >
                        <span class="checkmark"></span>
                        Use stored payment information
                    </label>
                </div>
            </div>

            <div class="payment-option" :class="{'active': selectedPaymentMethod === 1}">
                <div>
                    <div class="payment-option-header" @click="selectedPaymentMethod = 1">
                        <label>Credit cards</label>
                        <img :src="publickFolder+'images/card-icon.png'" alt="">
                    </div>
                    <div class="payment-option-body d-flex flex-wrap" v-if="selectedPaymentMethod === 1">
                        <div class="form-group has-feedback">
                            <label>Card number</label>
                            <input type="text" class="form-control mastercard" required name="first_name" value="" placeholder="____ ____ ____ ____">
                            <span class="help-block" v-if="errors.first_name"><strong>{{ errors.first_name[0] }}</strong></span>
                        </div>
                        <div class="form-group w-50 has-feedback">
                            <label>Cardholder name</label>
                            <input type="text" class="form-control" required name="first_name" value="" placeholder="Full Name">
                            <span class="help-block" v-if="errors.first_name"><strong>{{ errors.first_name[0] }}</strong></span>
                        </div>
                        <div class="form-group w-25 has-feedback">
                            <label>Expiry date</label>
                            <input type="text" class="form-control" required name="first_name" value="" placeholder="MM / YY">
                            <span class="help-block" v-if="errors.first_name"><strong>{{ errors.first_name[0] }}</strong></span>
                        </div>
                        <div class="form-group w-25 has-feedback">
                            <label>CVC/CVV</label>
                            <input type="text" class="form-control" required name="first_name" value="" placeholder="3 digits">
                            <span class="help-block" v-if="errors.first_name"><strong>{{ errors.first_name[0] }}</strong></span>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-block">Submit Payment</button>
                            <!--<a href="/login" class="text-center">I already have a membership</a>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="payment-option"  :class="{'active': selectedPaymentMethod === 2}">
                <div>
                    <div class="payment-option-header" @click="selectedPaymentMethod = 2">
                        <label>PayPal</label>
                        <img :src="publickFolder+'images/payPal.png'" alt="">
                    </div>
                    <div class="payment-option-body" v-if="selectedPaymentMethod === 2">
                       content PayPal
                    </div>
                </div>
            </div>
            <div class="payment-option"  :class="{'active': selectedPaymentMethod === 3}">
                <div>
                    <div class="payment-option-header" @click="selectedPaymentMethod = 3">
                        <label>Venmo</label>
                        <img :src="publickFolder+'images/venmo.png'" alt="">
                    </div>
                    <div class="payment-option-body" v-if="selectedPaymentMethod === 3">
                        content Venmo
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
//	import MaskedInput from 'vue-masked-input';
    import siteAPI from '../mixins/siteAPI.js';
    import skillectiveHelper from '../mixins/skillectiveHelper.js';


	export default {

		mixins : [siteAPI, skillectiveHelper],
		props: ['lessonId', 'user'],
		data() {
			return {
				formSubmitted: false,
				fields: {
					first_name : '',
					last_name : '',
					instagram_handle : '',
					email : '',
					address : '',
					city : '',
					state : '',
					zip : '',
					dob : '',
					gender : '',
					mobile_phone : '',
					genres : [],
					special_request : '',
					accept_terms : false
				},
                booking: null,
                formReadonly : false,
                publickFolder: window.location.protocol+'//'+window.location.host+'/',
				selectedPaymentMethod: 1
			}
		},
		methods: {
			onSubmit() {

                if (moment(this.fields.dob))
                    this.fields.dob = moment(this.fields.dob).format('YYYY-MM-DD');

                this.apiPost('/api/instructor/lesson/'+this.lessonId+'/book', this.fields);
			},
			componentHandlePostResponse(responseData) {
				this.booking = responseData.data;
				this.clearSubmittedForm();
				this.successText = null;
			}
		},
        created : function(){
			if (this.user !== null){
                this.fields.first_name = this.user.first_name;
                this.fields.last_name = this.user.last_name;
                this.fields.instagram_handle = this.user.profile.instagram_handle;
                this.fields.email = this.user.email;
                this.fields.address = this.user.profile.first_name;
                this.fields.city = this.user.profile.city;
                this.fields.state = this.user.profile.state;
                this.fields.zip = this.user.profile.zip;
                this.fields.dob = this.user.profile.dob;
                this.fields.gender = this.user.profile.gender;
                this.fields.mobile_phone = this.user.profile.mobile_phone;
                this.fields.genres = this.user.genres;

				this.formReadonly = this.user.email!='';
			}
        }
	}
</script>