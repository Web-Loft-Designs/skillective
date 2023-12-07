<template>
  <div class='checkout-outer'>
    <div v-if='total?.count === 0 && !checkoutSuccess' class='no-lessons-wrap'>
      <span> No items in cart </span>
      <p>Maybe someone booked the lesson before you</p>
      <a href='/lessons'> Book lessons </a>
    </div>
    <div v-else id='booking-form-container'>
      <!-- User profile data -->
      <div v-if='bookingStep !== 3'>
        <div class='user-info-title'>
          <h3 class='login-box-msg'>Profile</h3>
          <button
              v-if='bookingStep === 2'
              class='prev-step-btn'
              @click='editUserInformation'
          >
            Edit
          </button>
        </div>
        <form
            v-if='bookingStep === 1'
            method='post'
            @submit.prevent='onSubmitStep1'
        >
          <div v-if='user === null'>
            <p class='custom-padding'>
              Input information or
              <a href='/login'>Login to your account</a>
            </p>
          </div>

          <div class='d-flex flex-wrap'>
            <div class='label-w-100'>
              <label>Your name</label>
            </div>
            <div
                :class="{ 'has-error': errors.first_name }"
                class='form-group first-name has-feedback'
            >

              <input
                  v-model='fields.first_name'
                  :readonly='formReadonly === true'
                  class='form-control'
                  name='first_name'
                  placeholder='First Name'
                  required
                  type='text'
                  value
              />
              <span v-if='errors.first_name' class='help-block'>
                <strong>{{ errors.first_name[0] }}</strong>
              </span>
            </div>

            <div
                :class="{ 'has-error': errors.last_name }"
                class='form-group last-name has-feedback'
            >
              <input
                  v-model='fields.last_name'
                  :readonly='formReadonly === true'
                  class='form-control'
                  name='last_name'
                  placeholder='Last Name'
                  required
                  type='text'
                  value
              />
              <span v-if='errors.last_name' class='help-block'>
                <strong>{{ errors.last_name[0] }}</strong>
              </span>
            </div>

            <div
                :class="{ 'has-error': errors.zip }"
                class='form-group w-50 has-feedback'
            >
              <label>ZIP</label>
              <input
                  v-model='fields.zip'
                  :readonly='formReadonly === true'
                  class='form-control'
                  name='zip'
                  placeholder='ZIP code'
                  type='text'
                  value
              />
              <span v-if='errors.zip' class='help-block'>
                <strong>{{ errors.zip[0] }}</strong>
              </span>
            </div>

            <div
                :class="{ 'has-error': errors.dob }"
                class='form-group w-50 has-feedback'
            >
              <label>Date of Birth</label>
              <dropdown-datepicker
                  v-model='fields.dob'
                  :default-date='fields.dob'
                  :max-year='2021'
                  :min-year='1940'
                  :required='true'
                  display-format='mdy'
                  submit-format='yyyy-mm-dd'
              ></dropdown-datepicker>

              <span v-if='errors.dob' class='help-block'>
                <strong>{{ errors.dob[0] }}</strong>
              </span>
            </div>

            <div
                :class="{ 'has-error': errors.email }"
                class='form-group w-50 has-feedback'
            >
              <label>Email</label>
              <input
                  v-model='fields.email'
                  :readonly='formReadonly === true'
                  class='form-control'
                  name='email'
                  placeholder='Email'
                  required
                  type='email'
                  value
              />
              <span v-if='errors.email' class='help-block'>
                <strong>{{ errors.email[0] }}</strong>
              </span>
            </div>

            <div
                :class="{ 'has-error': errors.mobile_phone }"
                class='form-group w-50 has-feedback'
            >
              <label>Phone number</label>

              <masked-input
                  v-if='formReadonly === false'
                  v-model='fields.mobile_phone'
                  :class="'form-control'"
                  :placeholder="'(___) ___ ____'"
                  mask='(111) 111 1111'
              />
              <input
                  v-if='formReadonly === true'
                  v-model='fields.mobile_phone'
                  class='form-control'
                  readonly
                  type='text'
              />
              <span v-if='errors.mobile_phone' class='help-block'>
                <strong>{{ errors.mobile_phone[0] }}</strong>
              </span>
            </div>

            <div
                v-if="lesson_type === 'in_person_client'"
                :class="{ 'has-error': errors.location }"
                class='form-group has-feedback'
            >

              <label>Location</label>
              <input
                  ref='lessonLocation'
                  v-model='fields.location'
                  class='form-control'
                  name='location'
                  required
                  type='text'
                  value
              />
            </div>
            <div
                :class="{ 'has-error': errors.accept_terms }"
                class='checkout-terms form-group checkbox-wrapper has-feedback'
            >
              <div class='field'>
                <label for='accept-terms'>
                  <input
                      id='accept-terms'
                      v-model='fields.accept_terms'
                      :value='1'
                      type='checkbox'
                  />
                  <span class='checkmark'></span>
                  I agree to the
                  <a href='/terms' target='_blank'>terms of service</a>
                </label>
              </div>

              <span v-if='errors.accept_terms' class='help-block'>
                <strong>{{ errors.accept_terms[0] }}</strong>
              </span>
            </div>

            <!--            <div v-if="errorText" class="has-error" v-html="errorText"></div>-->
            <!--            <div v-if="successText" class="has-success" v-html="successText"></div>-->

            <div class='form-group'>
              <button
                  :disabled='!fields.accept_terms'
                  class='btn btn-block'
                  type='submit'
              >
                Continue to Payment
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Payment Method form -->
      <div v-if='bookingStep === 2' class='section-payments'>
        <div class='payment-information'>
          <h3 class='custom-padding'>Please Submit Payment</h3>
          <p>Step 2/2</p>
        </div>
        <div
            v-if='user != null && Object.entries(userPaymentMethods).length === 1'
            class='checkbox-wrapper mb-5 has-feedback'
        >
          <div class='mb-4'>Use stored payment information:</div>
          <div class='field'>
            <label class='mx-4' for='stored-payment-information'>
              <input
                  id='stored-payment-information'
                  v-model='useSavedMethod'
                  type='checkbox'
              />
              <span class='checkmark'></span>
              {{ userPaymentMethods.card.type }} {{ userPaymentMethods.card.brand || '' }}
            </label>
          </div>
        </div>
        <div
            v-if='user != null && Object.entries(this.userPaymentMethods).length > 1'
            class='checkbox-wrapper mb-5 has-feedback'
        >
          <div class='mb-4'>Use stored payment information:</div>
          <label v-for='(method, index) in userPaymentMethods' :key='index' class='mx-4'>
            <input
                v-model='selectedPaymentArr[index]'
                :value='method.payment_id'
                type='checkbox'
                @change='toggleUseSavedMethods(method.payment_id, index)'
            />
            {{ method.type }} {{ method.brand || '' }}
          </label>
        </div>
        <div>
          <div class='payment-option-header mb-4'>
            <img alt src='/images/card-icon.png'/>
          </div>
          <div class='card_container'>
            <div>
              <div>
                <label>Card number</label>
                <div v-show='!isSelectedPaymentMethod' id='card-number'></div>
                <input
                    v-show='isSelectedPaymentMethod'
                    :value='lastFour'
                    class='form-control-pp'
                    disabled
                    placeholder='____ ____ ____ ____'
                    type='text'
                />
              </div>

              <div>
                <label>Cardholder name</label>
                <div v-show='!isSelectedPaymentMethod' id='card-holder-name'></div>
                <input
                    v-show='isSelectedPaymentMethod'
                    class='form-control-pp'
                    disabled
                    type='text'
                    value='********** ************'
                />
              </div>

              <div>
                <label>Expiry date</label>
                <div v-show='!isSelectedPaymentMethod' id='expiration-date'></div>
                <input
                    v-show='isSelectedPaymentMethod'
                    class='form-control-pp'
                    disabled
                    type='text'
                    value='** / **'
                />
              </div>

              <div>
                <label>CVC/CVV</label>
                <div v-show='!isSelectedPaymentMethod' id='cvv'></div>
                <input
                    v-show='isSelectedPaymentMethod'
                    class='form-control-pp'
                    disabled
                    type='text'
                    value='***'
                />
              </div>

              <div class='form-group'>
                <button
                    v-show='!isSelectedPaymentMethod'
                    id='onSubmitStepCreditCard2'
                    class='btn btn-block'
                    type='button'
                    value='submit'
                >
                  Submit Payment
                </button>
                <button
                    v-show='isSelectedPaymentMethod'
                    class='btn btn-block'
                    type='button'
                    @click='book()'
                >
                  Submit Payment
                </button>
              </div>
            </div>
          </div>
        </div>
        <div id='paypal-buttons-container'></div>
      </div>

      <!-- Successfull Booking -->
      <div v-if='bookingStep === 3'>
        <div class='lesson-success' v-html='confirmationText'></div>
      </div>
    </div>

  </div>
</template>

<script>
import MaskedInput from 'vue-masked-input'
import {loadScript} from '@paypal/paypal-js'
import {mapActions} from 'vuex'
import DropdownDatepicker from 'vue-dropdown-datepicker'
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper'
import guestCartHelper from '../helpers/guestCartHelper'

require('jquery.maskedinput/src/jquery.maskedinput')

export default {
  components: {
    MaskedInput,
    DropdownDatepicker
  },
  mixins: [siteAPI, skillectiveHelper],
  props: {
    confirmationText: String,
    userPaymentMethods: Object,
    user: Object,
    total: Object,
    ppClientToken: String,
    bnCode: String,
    dataUserIdToken: String,
    masterMerchantId: String
  },
  data() {
    return {
      selectedPaymentMethodObj: null,
      paymentMethod: 'CreditCard',
      paymentMethods: [],
      lesson_type: '',
      fields: {
        first_name: '',
        last_name: '',
        email: '',
        address: '',
        zip: '',
        dob: '',
        gender: '',
        mobile_phone: '',
        accept_terms: false,
        payment_method_token: null,
        payment_method_nonce: null,
        device_data: '',
        lesson_type: ''
      },
      booking: null,
      formReadonly: false,
      useSavedMethod: false,
      selectedPaymentArr: [],
      selectedPaymentId: null,
      selectedPaymentIndex: null,
      venmoNotSupported: false,
      orderId: null,
      paypal: null,
      setupToken: null,
      checkoutSuccess: false,
      bookingStep: 1
    }
  },
  created() {
    if (
        window.location.hash.search(/venmoSuccess=/) !== -1 &&
        Cookies.get('currentOrderDetails') != undefined
    ) {
      let currentOrderDetails = Cookies.get('currentOrderDetails')
      if (typeof currentOrderDetails == 'string') currentOrderDetails = JSON.parse(currentOrderDetails)
      this.fields = currentOrderDetails.fields
      this.paymentMethod = currentOrderDetails.paymentMethod
      this.bookingStep = currentOrderDetails.bookingStep
      this.useSavedMethod = currentOrderDetails.useSavedMethod
      this.setSelectedPaymentMethodObj(this.paymentMethod)
    } else if (this.user !== null) {
      this.fields.first_name = this.user.first_name
      this.fields.last_name = this.user.last_name
      this.fields.email = this.user.email
      this.fields.address = this.user.profile.first_name
      this.fields.zip = this.user.profile.zip
      this.fields.dob = this.user.profile.dob
      this.fields.gender = this.user.profile.gender
      this.fields.mobile_phone = this.user.profile.mobile_phone
    }
    setTimeout(function () {
      window.jQuery('.mask-input').mask('99/99/9999')
    }, 200)
    this.paymentMethods = this.userPaymentMethods
  },
  mounted() {
    this.initNewPlacesAutocomplete('lessonLocation')
  },
  computed: {
    isSelectedPaymentMethod() {
      return this.useSavedMethod || this.selectedPaymentArr.includes(true)
    },
    lastFour() {
      return `**** **** **** ${this.userPaymentMethods?.card?.last_digits}`
    }
  },
  methods: {
    ...mapActions({
      fetchCartItems: 'fetchCartItems'
    }),
    async initializePaypal() {
      console.log(this.userPaymentMethods, 'userPaymentMethods')
      try {
        this.paypal = await loadScript({
          clientId: this.ppClientToken,
          merchantId: this.masterMerchantId,
          buyerCountry: 'US',  // удалити при запуску на продакшені !!!!!!!
          locale: 'en_US',
          components: ['buttons', 'funding-eligibility', 'marks', 'card-fields'],
          currency: 'USD',
          vault: true,
          disableFunding: ['paylater'],
          enableFunding: ['venmo'],
          dataUserIdToken: this.dataUserIdToken,
        })
        this.initPaymentMethod()
      } catch (error) {
        console.error('Failed to load the PayPal JS SDK script', error)
      }
    },
    initPaymentMethod() {
      if (this.userPaymentMethods.card) this.renderCardForm()
      if (this.userPaymentMethods.paypal) this.renderPayPalButton()
      if (!this.userPaymentMethods.card && !this.userPaymentMethods.paypal && !this.userPaymentMethods.venmo) {
        this.renderCardForm()
        this.renderPayPalButton()
      }
    },
    renderPayPalButton() {
      this.paypal.Buttons({
        createVaultSetupToken: async () => {
          const result = await axios.post('/api/cart/vault-setup-token?method=paypal')
          return result.data.vaultSetupToken;
        },
        onApprove: async (data) => {
          this.fields.payment_method_nonce = data.vaultSetupToken
          this.fields.order = this.total
          this.fields.payment_method_token = null
          await this.apiPost('/api/cart/checkout', {
            ...this.fields
          })
        },
        onError: (error) => console.log('Something went wrong:', error),
        style: {
          layout: 'vertical',
          color: 'gold',
          shape: 'pill',
          label: 'paypal'
        }
      }).render('#paypal-buttons-container')
    },
    renderCardForm() {
      const cardFields = this.paypal.CardFields({
        createVaultSetupToken: async () => {
          const result = await fetch('/api/cart/vault-setup-token?method=card', {
            method: 'POST'
          })
          const {vaultSetupToken} = await result.json()
          return vaultSetupToken
        },
        onApprove: async (data) => {
          this.fields.payment_method_nonce = data.vaultSetupToken
          this.fields.order = this.total
          this.fields.payment_method_token = null
          await this.apiPost('/api/cart/checkout', {
            ...this.fields
          })
        },
        onError: (error) => console.error('Something went wrong:', error)
      })
      if (cardFields.isEligible()) {
        cardFields.NameField().render('#card-holder-name')
        cardFields.NumberField().render('#card-number')
        cardFields.ExpiryField().render('#expiration-date')
        cardFields.CVVField().render('#cvv')
      } else {
        // Handle the workflow when credit and debit cards are not available
      }
      const submitButton = document.getElementById('onSubmitStepCreditCard2')
      submitButton.addEventListener('click', () => {
        cardFields.submit()
            .then(() => {
              console.log('submit was successful')
            })
            .catch((error) => {
              console.error('submit erred:', error)
            })
      })
    },
    async onSubmitStep1() {
      if (this.fields.dob) {
        this.fields.dob = moment(this.fields.dob).format('YYYY-MM-DD')
      }
      await this.fetchCartItems()
      this.apiPost('/api/cart/validate-user-info', this.fields)
    },
    initNewPlacesAutocomplete(_ref) {
      let thisComponent = this
      let autocomplete = this.initializeLocationField(this.$refs[_ref], [
        'address'
      ])
      google.maps.event.addListener(
          autocomplete,
          'place_changed',
          function (e) {
            thisComponent.fields.location = thisComponent.$refs[_ref].value
          }
      )
    },
    editUserInformation() {
      this.bookingStep = 1
    },
    componentHandlePostResponse(responseData) {
      if (this.bookingStep === 1) {
        this.bookingStep = 2
        this.initializePaypal()
      } else if (this.bookingStep === 2) {
        guestCartHelper.clearProducts()
        guestCartHelper.clearPromos()
        this.booking = responseData.data
        this.clearSubmittedForm()
        this.successText = null
        this.bookingStep = 3
        this.fetchCartTotal()
        this.fetchCartItems()
        this.checkoutSuccess = true
      }
    },
    book() {
      if (moment(this.fields.dob)) this.fields.dob = moment(this.fields.dob).format('YYYY-MM-DD')
      this.fields.payment_method_token = this.selectedPaymentId
      this.apiPost('/api/cart/checkout', {
        ...this.fields,
        guest_cart: guestCartHelper.getProducts(),
        promo_codes: guestCartHelper.getPromos()
      })
    },
    toggleUseSavedMethods(value, index) {
      this.selectedPaymentIndex = index
      const oldSelectedPaymentId = this.selectedPaymentId
      this.selectedPaymentArr = this.selectedPaymentArr.map((el, i) => (i === index ? el = true : el = false))
      this.selectedPaymentId = value
      if (oldSelectedPaymentId === value) {
        this.selectedPaymentArr = this.selectedPaymentArr.map((el, i) => (i === index ? el = false : el = false))
        this.selectedPaymentId = null
        this.selectedPaymentIndex = null
      }
    }
  },
  watch: {
    useSavedMethod() {
      if (this.useSavedMethod) {
        this.selectedPaymentId = this.userPaymentMethods[0].payment_id
        console.log(this.selectedPaymentId)
      } else {
        this.selectedPaymentId = null
        console.log(this.selectedPaymentId)
      }
    }
  }
}
</script>

<style>
/* Add your styles if needed */
.form-control-pp {
  border: 0.0625rem solid #909697;
  border-radius: 0.25rem;
  box-sizing: border-box;
  background: #ffffff;
  font-family: inherit;
  font-size: 2.125rem;
  line-height: 1.5rem;
  padding: 1.25rem 0.75rem;
  width: calc(100% - 12px);
  height: 67px;
  margin: 6px 6px 14px 6px;
}
</style>
