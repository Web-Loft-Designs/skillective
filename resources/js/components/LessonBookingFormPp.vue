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
          v-if='user != null && userPaymentMethods[paymentMethod] != undefined'
          class='form-group checkbox-wrapper mb-5 has-feedback'
        >
          <div class='field'>
            <label for='stored-payment-information'>
              <input
                id='stored-payment-information'
                v-model='useSavedMethod'
                type='checkbox'
              />
              <span class='checkmark'></span>
              Use stored payment information
            </label>
          </div>
        </div>
        <div>
          <div
            :class="{ active: paymentMethod === 'CreditCard' }"
            class='payment-option'
          >
            <div>
              <div class='payment-option-header'>
                <img alt src='/images/card-icon.png'/>
              </div>
              <div class='card_container'>
                <form id='checkout-form'>
                  <div
                    v-if="paymentMethod === 'CreditCard'"
                    class='payment-option-body d-flex flex-wrap'
                  >
                    <div class='form-group has-feedback'>
                      <label>Card number</label>
<!--                      v-if='this.selectedPaymentMethodObj == null'-->
                      <div id='card-number-field-container'></div>
<!--                      <input-->
<!--                        v-if='this.selectedPaymentMethodObj != null'-->
<!--                        :value='-->
<!--                      getSavedCardNumberVal(this.selectedPaymentMethodObj.last4)-->
<!--                    '-->
<!--                        class='form-control'-->
<!--                        disabled-->
<!--                        placeholder='____ ____ ____ ____'-->
<!--                        type='text'-->
<!--                      />-->
                    </div>
                    <div class='form-group w-50 has-feedback'>
                      <label>Cardholder name</label>
                      <div id='card-name-field-container'></div>
<!--                      <input-->
<!--                        v-if='this.selectedPaymentMethodObj == null'-->
<!--                        id='cardholder-name'-->
<!--                        v-model='fields.cardholderName'-->
<!--                        class='form-control'-->
<!--                        name='cardholderName'-->
<!--                        placeholder='Name'-->
<!--                        required-->
<!--                        type='text'-->
<!--                        value-->
<!--                      />-->
<!--                      <input-->
<!--                        v-if='this.selectedPaymentMethodObj != null'-->
<!--                        :value='this.selectedPaymentMethodObj.cardholderName'-->
<!--                        class='form-control'-->
<!--                        disabled-->
<!--                        type='text'-->
<!--                      />-->
                      <!--<span class="help-block" v-if="errors.cardholderName"><strong>{{ errors.cardholderName[0] }}</strong></span>-->
                    </div>
                    <div class='form-group w-25 has-feedback'>
                      <label>Expiry date</label>
                      <div
                        v-if='this.selectedPaymentMethodObj == null'
                        id='card-expiry-field-container'
                        class='card-item'
                      ></div>
<!--                      <input-->
<!--                        v-if='this.selectedPaymentMethodObj != null'-->
<!--                        :value='this.selectedPaymentMethodObj.expirationDate'-->
<!--                        class='form-control'-->
<!--                        disabled-->
<!--                        placeholder='MM / YY'-->
<!--                        type='text'-->
<!--                      />-->
                    </div>
                    <div
                      v-if='this.selectedPaymentMethodObj == null'
                      class='form-group w-25 has-feedback'
                    >
                      <label>CVC/CVV</label>
                      <div id='card-cvv-field-container' class='card-item'></div>
                    </div>

                    <div
                      v-if="errorText && paymentMethod === 'CreditCard'"
                      class='has-error'
                      v-html='errorText'
                    ></div>

                    <div class='form-group'>
                      <button
                        v-if='this.selectedPaymentMethodObj == null'
                        id='multi-card-field-button'
                        class='btn btn-block'
                        disabled
                        type='button'
                        @click='onSubmitStepCreditCard2()'
                      >
                        Submit Payment
                      </button>
                      <button
                        v-if='this.selectedPaymentMethodObj != null'
                        id='multi-card-field-button'
                        class='btn btn-block'
                        type='button'
                        @click='book()'
                      >
                        Submit Payment
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!--          <div-->
          <!--            class="payment-option"-->
          <!--            :class="{ active: paymentMethod === 'VenmoAccount' }"-->
          <!--          >-->
          <!--            <div>-->
          <!--              <div-->
          <!--                class="payment-option-header"-->
          <!--                @click="paymentMethod = 'VenmoAccount'"-->
          <!--              >-->
          <!--                <label>Venmo</label>-->
          <!--                <img src="/images/venmo.png" alt />-->
          <!--              </div>-->
          <!--              <div-->
          <!--                class="payment-option-body"-->
          <!--                v-if="paymentMethod === 'VenmoAccount'"-->
          <!--              >-->
          <!--                <div v-if="this.selectedPaymentMethodObj == null">-->
          <!--                  <p>Pay with</p>-->
          <!--                  <div-->
          <!--                    id="venmo-button"-->
          <!--                    class="btn btn-block"-->
          <!--                    style="-->
          <!--                      background: rgb(61, 149, 206)-->
          <!--                        url('/images/venmo_logo_white.png') repeat scroll 0% 0%;-->
          <!--                      display: block;-->
          <!--                      background-repeat: no-repeat;-->
          <!--                      background-size: 110px 21px;-->
          <!--                      background-position: center;-->
          <!--                    "-->
          <!--                    v-if="venmoNotSupported == false"-->
          <!--                  ></div>-->
          <!--                </div>-->
          <!--                <button-->
          <!--                  v-if="this.selectedPaymentMethodObj != null"-->
          <!--                  type="button"-->
          <!--                  class="btn btn-block"-->
          <!--                  @click="book"-->
          <!--                >-->
          <!--                  Submit Payment-->
          <!--                </button>-->
          <!--                <div-->
          <!--                  v-if="errorText && paymentMethod === 'VenmoAccount'"-->
          <!--                  class="has-error"-->
          <!--                  v-html="errorText"-->
          <!--                ></div>-->
          <!--                <div-->
          <!--                  v-if="-->
          <!--                    this.selectedPaymentMethodObj == null &&-->
          <!--                    venmoNotSupported == true-->
          <!--                  "-->
          <!--                >-->
          <!--                  Browser does not support Venmo-->
          <!--                </div>-->
          <!--              </div>-->
          <!--            </div>-->
          <!--          </div>-->
        </div>
      </div>
    </div>

<!--    <div class='card_container'>-->
<!--      <form id='checkout-form'>-->
<!--        <div id='card-name-field-container'></div>-->
<!--&lt;!&ndash;        <div id='card-number-field-container'></div>&ndash;&gt;-->
<!--        <div id='card-expiry-field-container'></div>-->
<!--        <div id='card-cvv-field-container'></div>-->
<!--        <button id='multi-card-field-button' type='button'>Pay now with Card Fields</button>-->
<!--      </form>-->
<!--    </div>-->
    <div id='paypal-button-container'></div>
  </div>
</template>

<script>
import MaskedInput from 'vue-masked-input'
import { loadScript } from '@paypal/paypal-js'
import { mapActions } from 'vuex'
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
    userPaymentMethods: Array,
    user: Object,
    total: Object,
    ppClientToken: String
  },
  // props: [
  //   'user',
  //   'categorizedGenres',
  //   'userPaymentMethods',
  //   'paymentEnvironment',
  //   'clientToken',
  //   'confirmationText',
  //   'lessonsCount',
  // ],
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
    this.getVaultSetupToken()
  },
  methods: {
    ...mapActions({
      fetchCartItems: 'fetchCartItems'
    }),
    async getVaultSetupToken() {
      try {
        const response = await fetch('/api/cart/vault-setup-token', {
          method: 'GET'
        })
        this.setupToken = await response.json()
        console.log(this.setupToken)
      } catch (error) {
        console.error('Помилка отримання токену:', error)
      }
    },
    async initializePaypal() {
      try {
        this.paypal = await loadScript({
          clientId: this.ppClientToken,
          buyerCountry: 'US',
          locale: 'en_US',
          components: ['buttons', 'card-fields'],
          // merchantId: ['KSLFGLWLXG79G'],
          currency: 'USD'
        })
        this.setupPaypalButtons()
        this.setupPaypalInput()
      } catch (error) {
        console.error('Failed to load the PayPal JS SDK script', error)
      }
    },
    setupPaypalButtons() {
      this.paypal.Buttons({
        createOrder: () => {
          // ... (Your existing createOrder logic)
        },
        onApprove: (data) => {
          this.captureOrder(data.orderID)
        }
      }).render('#paypal-button-container')
    },
    setupPaypalInput() {
      const styleObject = {
        input: {
          'font-size': '16 px',
          'font-family': 'monospace',
          'font-weight': 'lighter',
          color: 'blue'
        },
        '.invalid': {
          color: 'purple'
        },
        '.purple': {
          color: 'purple'
        }
      }
      const cardField = this.paypal.CardFields({
        style: styleObject,
        createOrder: function (data, actions) {
          console.log(data, 'data')
          console.log(actions, 'actions')
          // return fetch('/api/paypal/order/create/', {
          //   method: 'post'
          // })
          //   .then((res) => {
          //     console.log(res.json(), 'res.json()')
          //     return res.json()
          //   })
          //   .then((orderData) => {
          //     console.log(orderData.id, 'orderData.id')
          //     return orderData.id
          //   })
        },
        onApprove: function (data, actions) {
          const { orderID } = data
          return fetch('/api/paypal/orders/${orderID}/capture/', {
            method: 'post'
          })
            .then((res) => {
              return res.json()
            })
            .then((orderData) => {
              // Redirect to success page
            })
        }
        // inputEvents: {
        //   onChange: function (data) {
        //     // Handle a change event in any of the fields
        //   },
        //   onFocus: function (data) {
        //     // Handle a focus event in any of the fields
        //   },
        //   onBlur: function (data) {
        //     // Handle a blur event in any of the fields
        //   },
        //   onInputSubmitRequest: function (data) {
        //     // Handle an attempt to submit the entire card form
        //     // while focusing any of the fields
        //   }
        // }
      })
      // Define the container for each field and the submit button
      const cardNameContainer = document.getElementById('card-name-field-container') // Optional field
      const cardNumberContainer = document.getElementById('card-number-field-container')
      const cardCvvContainer = document.getElementById('card-cvv-field-container')
      const cardExpiryContainer = document.getElementById('card-expiry-field-container')
      const multiCardFieldButton = document.getElementById('multi-card-field-button')
      // Render each field after checking for eligibility
      if (cardField.isEligible()) {
        const nameField = cardField.NameField()
        nameField.render(cardNameContainer)
        const numberField = cardField.NumberField()
        numberField.render(cardNumberContainer)
        const cvvField = cardField.CVVField()
        cvvField.render(cardCvvContainer)
        const expiryField = cardField.ExpiryField()
        expiryField.render(cardExpiryContainer)
        // Add click listener to the submit button and call the submit function on the CardField component
        multiCardFieldButton.addEventListener('click', () => {
          cardField
            .submit()
            .then(() => {
              console.log('submit')
            })
            .catch((err) => {
              // Handle an unsuccessful payment
            })
        })
      }

    },
    captureOrder(orderId) {
      // ... (Your existing captureOrder logic)
    },
    // old methods
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
      if (this.bookingStep == 1) {
        this.bookingStep = 2
        this.initializePaypal()
      } else if (this.bookingStep == 2) {
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
    }
  }
}
</script>

<style>
/* Add your styles if needed */
</style>
