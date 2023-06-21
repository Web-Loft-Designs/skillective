<template>
	<div id="profile-form-container" class="">
		<div v-if="isAdminForm && merchantAccountDetails.id == null">
			<h4>Payouts Account not yet created</h4>
		</div>

		<div
			v-if="
				merchantAccountDetails.id != null &&
				merchantAccountDetails.status == 'pending'
			"
		>
			<h4>Payouts Account created and is being verified now</h4>
		</div>
		<form
			v-if="
				merchantAccountDetails.status != 'pending' &&
				(!isAdminForm || (isAdminForm && merchantAccountDetails.id != null))
			"
			method="post"
			@submit.prevent="onSubmit"
		>
			<div class="d-flex flex-wrap">
				<div class="form-group mb-1 d-flex justify-content-between">
					<h4>
						Payouts Account Details<span v-if="isAdminForm">
							(Status: {{ merchantAccountDetails.status }})</span
						>
					</h4>
					<p>We will use this information to deposit funds in your account</p>
					<div
						class="checkbox-wrapper"
						v-if="!isAdminForm && merchantAccountDetails.id == null"
					>
						<div class="field">
							<label for="use-personal-info">
								<input
									v-model="usePersonalInfo"
									type="checkbox"
									id="use-personal-info"
								/>
								<span class="checkmark"></span>
								Use personal info
							</label>
						</div>
					</div>
				</div>

				<div
					class="form-group f-name w-50 has-feedback"
					:class="{ 'has-error': errors.individual_firstName }"
				>
					<label>Your name</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						required
						v-model="merchantAccountDetails.individual_firstName"
						placeholder="First Name"
					/>
					<span class="help-block" v-if="errors.individual_firstName">
						<strong>{{ errors.individual_firstName[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group l-name w-50 has-feedback"
					:class="{ 'has-error': errors.individual_lastName }"
				>
					<label style="opacity: 0; visability: hidden">Your name</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						required
						v-model="merchantAccountDetails.individual_lastName"
						placeholder="Last Name"
					/>
					<span class="help-block" v-if="errors.individual_lastName">
						<strong>{{ errors.individual_lastName[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group has-feedback"
					:class="{ 'has-error': errors.individual_email }"
				>
					<label>Email address</label>
					<input
						:disabled="isAdminForm"
						type="email"
						class="form-control"
						required
						v-model="merchantAccountDetails.individual_email"
						placeholder="Email"
					/>
					<span class="help-block" v-if="errors.individual_email">
						<strong>{{ errors.individual_email[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group has-feedback"
					:class="{ 'has-error': errors.individual_phone }"
				>
					<label>Phone number</label>
					<masked-input
						:id="'individual-phone'"
						:disabled="isAdminForm"
						:class="'form-control'"
						v-model="merchantAccountDetails.individual_phone"
						:placeholder="'(___) ___ ____'"
						mask="(111) 111 1111"
					/>
					<span class="help-block" v-if="errors.individual_phone">
						<strong>{{ errors.individual_phone[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group has-feedback"
					:class="{ 'has-error': errors.individual_streetAddress }"
				>
					<label>Street Address</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						v-model="merchantAccountDetails.individual_streetAddress"
						placeholder="House number and street name"
					/>
					<span class="help-block" v-if="errors.individual_streetAddress">
						<strong>{{ errors.individual_streetAddress[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group has-feedback"
					:class="{ 'has-error': errors.individual_region }"
				>
					<label>State</label>
					<select
						:disabled="isAdminForm"
						class="form-control"
						v-model="merchantAccountDetails.individual_region"
					>
						<option value="">Select State</option>
						<option v-for="state in usStates" :value="state.code">
							{{ state.name }}
						</option>
					</select>
					<span class="help-block" v-if="errors.individual_region">
						<strong>{{ errors.individual_region[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group w-50 has-feedback"
					:class="{ 'has-error': errors.individual_locality }"
				>
					<label>Town / City</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						v-model="merchantAccountDetails.individual_locality"
						placeholder="City"
					/>
					<span class="help-block" v-if="errors.individual_locality">
						<strong>{{ errors.individual_locality[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group w-50 has-feedback"
					:class="{ 'has-error': errors.individual_dateOfBirth }"
				>
					<label>Date of Birth</label>
					<!--<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" :disabled="isAdminForm" :placeholder="'mm/dd/yyyy'" v-model="merchantAccountDetails.individual_dateOfBirth" :format="'MM/dd/yyyy'"></datepicker>-->
					<dropdown-datepicker
						:minYear="1940"
						:maxYear="currentYear"
						display-format="mdy"
						v-model="merchantAccountDetails.individual_dateOfBirth"
						submit-format="yyyy-mm-dd"
						ref="datepicker"
					></dropdown-datepicker>
					<span class="help-block" v-if="errors.individual_dateOfBirth">
						<strong>{{ errors.individual_dateOfBirth[0] }}</strong>
					</span>
				</div>

				<div
					class="form-group w-50 has-feedback"
					:class="{ 'has-error': errors.individual_postalCode }"
				>
					<label>ZIP</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						name="zip"
						value=""
						v-model="merchantAccountDetails.individual_postalCode"
						placeholder="ZIP code"
					/>
					<span class="help-block" v-if="errors.individual_postalCode">
						<strong>{{ errors.individual_postalCode[0] }}</strong>
					</span>
				</div>

				<div class="form-group has-feedback">
					<label>Bank Account Number</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						:required="
							merchantAccountDetails.id == null ||
							(merchantAccountDetails.funding_accountNumber_to_show == '' &&
								merchantAccountDetails.funding_routingNumber != '')
						"
						v-model="merchantAccountDetails.funding_accountNumber"
						:placeholder="
							merchantAccountDetails.funding_accountNumber_to_show != null &&
							merchantAccountDetails.funding_accountNumber_to_show != ''
								? merchantAccountDetails.funding_accountNumber_to_show
								: ''
						"
					/>
					<!--<span class="current-value" v-if="merchantAccountDetails.funding_accountNumber_to_show!=''">{{ merchantAccountDetails.funding_accountNumber_to_show }}</span>-->
					<span class="help-block" v-if="errors.funding_accountNumber"
						><strong>{{ errors.funding_accountNumber[0] }}</strong></span
					>
				</div>

				<div id="re-enter-bank" class="form-group has-feedback">
					<label>Re-enter Bank Account Number for Confirmation</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						:required="
							merchantAccountDetails.id == null ||
							(merchantAccountDetails.funding_accountNumber_confirmation_to_show ==
								'' &&
								merchantAccountDetails.funding_routingNumber != '')
						"
						v-model="merchantAccountDetails.funding_accountNumber_confirmation"
					/>
					<span class="help-block" v-if="errors.funding_accountNumber"
						><strong>{{ errors.funding_accountNumber[0] }}</strong></span
					>
				</div>

				<div class="form-group has-feedback">
					<label>Bank Routing Number</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control"
						required
						v-model="merchantAccountDetails.funding_routingNumber"
					/>
					<span class="help-block" v-if="errors.first_name"
						><strong>{{ errors.funding_routingNumber[0] }}</strong></span
					>
				</div>
				<div class="form-group has-feedback">
					<label>Tax Identification</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control mb-2"
            :required=" merchantAccountDetails.id == null || taxPlaceHolder == '' "
						:placeholder="taxPlaceHolder"
						v-model="merchantAccountDetails.taxId"
					/>
					<span
						data-toggle="modal"
						data-target="#taxInfoPopup"
						class="btn-grn-link"
						>Why do we need this?</span
					>
					<TaxIdPopup />
					<span class="help-block" v-if="errors.taxId">
						<strong>{{ errors.taxId[0] }}</strong>
					</span>
				</div>

				<div class="form-group has-feedback">
					<label>Legal Name</label>
					<input
						:disabled="isAdminForm"
						type="text"
						class="form-control mb-2"
						required
						v-model="merchantAccountDetails.legalName"
					/>
					<span class="help-block" v-if="errors.legalName">
						<strong>{{ errors.legalName[0] }}</strong>
					</span>
				</div>

				<div
					v-if="!isAdminForm && merchantAccountDetails.id == null"
					class="form-group checkbox-wrapper has-feedback"
					:class="{ 'has-error': errors.tosAccepted }"
				>
					<div class="field">
						<label for="accept-terms">
							<input
								v-model="merchantAccountDetails.tosAccepted"
								type="checkbox"
								id="accept-terms"
								:value="1"
							/>
							<span class="checkmark"></span>
							I agree to the
							<a href="/terms" target="_blank">terms of service</a>
						</label>
					</div>
					<span class="help-block" v-if="errors.tosAccepted">
						<strong>{{ errors.tosAccepted[0] }}</strong>
					</span>
				</div>
			</div>

			<div
				v-if="errorText"
				class="form-group has-error"
				v-html="errorText"
			></div>
			<div
				v-if="successText"
				class="has-success form-group"
				v-html="successText"
			></div>

			<div class="form-group" v-if="!isAdminForm">
				<button
					type="submit"
					v-if="merchantAccountDetails.id == null"
					class="btn btn-primary btn-flat"
				>
					Create Payouts Account
				</button>
				<button
					type="submit"
					v-if="merchantAccountDetails.id != null"
					class="btn btn-primary btn-flat"
				>
					Update Payouts Account
				</button>
			</div>
		</form>
	</div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import MaskedInput from 'vue-masked-input'
import $ from 'jquery'

require('jquery.maskedinput/src/jquery.maskedinput')
import DropdownDatepicker from 'vue-dropdown-datepicker'
import TaxIdPopup from './instructor/TaxIdPopups/TaxIdPopup'

export default {
	components: {
		MaskedInput,
		DropdownDatepicker,
		TaxIdPopup,
	},
	mixins: [siteAPI],
	props: ['usStates', 'savedMerchantAccountDetails', 'isAdminForm'],
	data() {
		return {
			usePersonalInfo: false,
			taxPlaceHolder: '',
			merchantAccountDetails: {
				taxId: null,
				legalName: null,
				id: null,
				status: '',
				funding_accountNumber_to_show: '',
				funding_accountNumber_confirmation_to_show: '',
				individual_firstName: '',
				individual_lastName: '',
				individual_email: '',
				individual_phone: '',
				individual_dateOfBirth: '',
				individual_streetAddress: '',
				individual_locality: '',
				individual_region: '',
				individual_postalCode: '',

				funding_email: '',
				funding_mobilePhone: '',
				funding_accountNumber_confirmation: '',
				funding_accountNumber: '',
				funding_routingNumber: '',

				tosAccepted: false,
			},
		}
	},
	computed: {
		isTaxVerify() {
			return !!this.savedMerchantAccountDetails.taxId
		},
		taxIdMask() {
			let inVisibleSymbol = this.savedMerchantAccountDetails.taxId
				.split('')
				.map((el) => {
					return el !== '-' ? (el = '•') : el
				})
				.join('')
			return `${inVisibleSymbol.slice(0, inVisibleSymbol.length - 4)}${
				this.visibleSymbol
			}`
		},
		visibleSymbol() {
			return this.savedMerchantAccountDetails.taxId.split('').slice(-4).join('')
		},
	},
	methods: {
		updateMerchantAccountInput(userProfileData) {
			this.merchantAccountDetails.individual_firstName =
				userProfileData != undefined ? userProfileData.first_name : ''
			this.merchantAccountDetails.individual_lastName =
				userProfileData != undefined ? userProfileData.last_name : ''
			this.merchantAccountDetails.individual_email =
				userProfileData != undefined ? userProfileData.email : ''
			this.merchantAccountDetails.individual_phone =
				userProfileData != undefined ? userProfileData.profile.mobile_phone : ''
			this.merchantAccountDetails.individual_dateOfBirth =
				userProfileData != undefined ? userProfileData.profile.dob : ''
			this.merchantAccountDetails.individual_locality =
				userProfileData != undefined ? userProfileData.profile.city : ''
			this.merchantAccountDetails.individual_region =
				userProfileData != undefined ? userProfileData.profile.state : ''
			this.merchantAccountDetails.individual_postalCode =
				userProfileData != undefined ? userProfileData.profile.zip : ''
			this.merchantAccountDetails.legalName =
				userProfileData != undefined ? userProfileData.profile.legalName : ''
		},
		profileDataToMerchantAccountDetails() {
			this.apiGet('/api/user')
		},
		componentHandleGetResponse(responseData) {
			if (this.usePersonalInfo) this.updateMerchantAccountInput(responseData)
		},
		onSubmit() {
			if (!this.isAdminForm) {
				if (moment(this.merchantAccountDetails.individual_dateOfBirth))
					this.merchantAccountDetails.individual_dateOfBirth = moment(
						this.merchantAccountDetails.individual_dateOfBirth
					).format('YYYY-MM-DD')

				if (this.merchantAccountDetails.id == null) {
					this.apiPost('/api/instructor/merchant', this.merchantAccountDetails)
				} else {
					this.apiPut('/api/instructor/merchant', this.merchantAccountDetails)
				}
			}
		},
		componentHandlePostResponse(responseData) {
			this.merchantAccountDetails = responseData.data
			$('#onboard-message').remove()
			if (this.merchantAccountDetails.id == null) {
				$([document.documentElement, document.body]).animate(
					{
						scrollTop: $('#merchant-account-trigger').offset().top - 100,
					},
					2000
				)
			}
		},
		componentHandlePutResponse(responseData) {
			this.merchantAccountDetails.funding_accountNumber =
				responseData.data.funding_accountNumber
			this.updateSavedToShowParams()
		},
		updateSavedToShowParams() {
			this.merchantAccountDetails.funding_accountNumber_to_show =
				this.merchantAccountDetails.funding_accountNumber != null &&
				this.merchantAccountDetails.funding_accountNumber.length >= 4
					? '•••••••••' +
					  this.merchantAccountDetails.funding_accountNumber.substr(-4)
					: ''
			this.merchantAccountDetails.funding_accountNumber = ''
		},
	},
	watch: {
		usePersonalInfo: function (newVal, oldVal) {
			if (newVal == true) this.profileDataToMerchantAccountDetails()
			else this.updateMerchantAccountInput()
		},
		isTaxVerify() {
			this.isTaxVerify
				? ((this.taxPlaceHolder = this.taxIdMask),
				  (this.merchantAccountDetails.taxId = null))
				: (this.taxPlaceHolder = '')
		},
	},
	created: function () {
		this.currentYear = moment().format('YYYY')

		this.currentYear = moment().format('YYYY')

		setTimeout(() => {
			if (this.$refs.datepicker) {
				this.$refs.datepicker.day = Number(
					moment(this.merchantAccountDetails.individual_dateOfBirth).format(
						'DD'
					)
				)
				this.$refs.datepicker.month = Number(
					moment(this.merchantAccountDetails.individual_dateOfBirth).format(
						'MM'
					)
				)
				this.$refs.datepicker.year = Number(
					moment(this.merchantAccountDetails.individual_dateOfBirth).format(
						'YYYY'
					)
				)
			}
		}, 1)

		setTimeout(function () {
			window.jQuery('.mask-input').mask('99/99/9999')
		}, 200)

		if (
			this.savedMerchantAccountDetails == undefined ||
			this.savedMerchantAccountDetails == null
		)
			this.updateMerchantAccountInput()
		else {
			this.merchantAccountDetails = this.savedMerchantAccountDetails
			this.updateSavedToShowParams()
		}
	},
	mounted() {
		this.isTaxVerify
			? ((this.taxPlaceHolder = this.taxIdMask),
			  (this.merchantAccountDetails.taxId = null))
			: (this.taxPlaceHolder = '')
	},
}
</script>

<style scoped lang="scss">
.btn-grn-link {
	color: #0aab14;
	font-family: 'Hind Vadodara', serif;
	font-size: 16px;
	font-weight: 600;
	cursor: pointer;
	margin: 5px 0 0 5px;
}
</style>
