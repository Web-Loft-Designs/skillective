<template>
  <div class="checkout-outer">
    <div v-if="getTotal.count == 0 && !checkoutSuccess" class="no-lessons-wrap">
      <span> No items in cart </span>
      <p>Maybe someone booked the lesson before you</p>
      <a href="/lessons"> Book lessons </a>
    </div>
    <div v-else id="booking-form-container">
      <!--<ol id="process-log" style="border:1px solid red;">-->
      <!--<li v-for="(logMsg) in logItems">{{ logMsg }}</li>-->
      <!--</ol>-->

      <!-- Successfull Booking -->

      <div v-if="bookingStep == 3">
        <!--#{{ booking.id }}-->
        <div class="lesson-success" v-html="confirmationText"></div>
      </div>

      <!-- Payment Method form -->

      <div class="section-payments" v-if="bookingStep == 2">
        <h3 class="custom-padding">
          Please Submit Payment to Reserve your spot
        </h3>
        <div
          class="form-group checkbox-wrapper mb-5 has-feedback"
          v-if="user != null && userPaymentMethods[paymentMethod] != undefined"
        >
          <div class="field">
            <label for="stored-payment-information">
              <!--@change="toggleStoredPaymentInfo()"-->
              <input
                type="checkbox"
                id="stored-payment-information"
                v-model="useSavedMethod"
              />
              <span class="checkmark"></span>
              Use stored payment information
            </label>
          </div>
        </div>

        <div
          class="payment-option"
          :class="{ active: paymentMethod === 'CreditCard' }"
        >
          <div>
            <div
              class="payment-option-header"
              @click="paymentMethod = 'CreditCard'"
            >
              <label>Credit cards</label>
              <img src="/images/card-icon.png" alt />
            </div>
            <div
              class="payment-option-body d-flex flex-wrap"
              v-if="paymentMethod === 'CreditCard'"
            >
              <div class="form-group has-feedback">
                <label>Card number</label>
                <div
                  v-if="this.selectedPaymentMethodObj == null"
                  class="card-item"
                  id="card-number"
                ></div>
                <input
                  v-if="this.selectedPaymentMethodObj != null"
                  type="text"
                  class="form-control"
                  disabled
                  :value="
                    getSavedCardNumberVal(this.selectedPaymentMethodObj.last4)
                  "
                  placeholder="____ ____ ____ ____"
                />
              </div>
              <div class="form-group w-50 has-feedback">
                <label>Cardholder name</label>
                <input
                  v-if="this.selectedPaymentMethodObj == null"
                  type="text"
                  class="form-control"
                  v-model="fields.cardholderName"
                  required
                  name="cardholderName"
                  value
                  placeholder="Cardholder Name"
                  id="cardholder-name"
                />
                <input
                  v-if="this.selectedPaymentMethodObj != null"
                  type="text"
                  class="form-control"
                  disabled
                  :value="this.selectedPaymentMethodObj.cardholderName"
                />
                <!--<span class="help-block" v-if="errors.cardholderName"><strong>{{ errors.cardholderName[0] }}</strong></span>-->
              </div>
              <div class="form-group w-25 has-feedback">
                <label>Expiry date</label>
                <div
                  v-if="this.selectedPaymentMethodObj == null"
                  class="card-item"
                  id="expiration-date"
                ></div>
                <input
                  v-if="this.selectedPaymentMethodObj != null"
                  type="text"
                  class="form-control"
                  disabled
                  :value="this.selectedPaymentMethodObj.expirationDate"
                  placeholder="MM / YY"
                />
              </div>
              <div
                v-if="this.selectedPaymentMethodObj == null"
                class="form-group w-25 has-feedback"
              >
                <label>CVC/CVV</label>
                <div class="card-item" id="cvv"></div>
              </div>

              <div
                v-if="errorText && paymentMethod === 'CreditCard'"
                class="has-error"
                v-html="errorText"
              ></div>

              <div class="form-group">
                <button
                  v-if="this.selectedPaymentMethodObj == null"
                  type="button"
                  @click="onSubmitStepCreditCard2()"
                  class="btn btn-block"
                  id="payment-method-form-submit"
                  disabled
                >
                  Submit Payment
                </button>
                <button
                  v-if="this.selectedPaymentMethodObj != null"
                  type="button"
                  @click="book()"
                  class="btn btn-block"
                  id="payment-method-form-submit"
                >
                  Submit Payment
                </button>
              </div>
            </div>
          </div>
        </div>
        <!--<div class="payment-option"  :class="{'active': paymentMethod === 'PayPalAccount'}">-->
        <!--<div>-->
        <!--<div class="payment-option-header" @click="paymentMethod = 'PayPalAccount'">-->
        <!--<label>PayPal</label>-->
        <!--<img src="/images/payPal.png" alt="">-->
        <!--</div>-->
        <!--<div v-if="errorText && paymentMethod === 'PayPalAccount'" class="has-error" v-html="errorText"></div>-->
        <!--<div class="payment-option-body" v-if="paymentMethod === 'PayPalAccount'">-->
        <!--<div v-if="(this.selectedPaymentMethodObj==null)">-->
        <!--<div id="paypal-button"></div>-->
        <!--</div>-->
        <!--<button v-if="(this.selectedPaymentMethodObj!=null)" type="button" class="btn btn-block" @click="book">Submit Payment</button>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
        <div
          class="payment-option"
          :class="{ active: paymentMethod === 'VenmoAccount' }"
        >
          <div>
            <div
              class="payment-option-header"
              @click="paymentMethod = 'VenmoAccount'"
            >
              <label>Venmo</label>
              <img src="/images/venmo.png" alt />
            </div>
            <div
              class="payment-option-body"
              v-if="paymentMethod === 'VenmoAccount'"
            >
              <div v-if="this.selectedPaymentMethodObj == null">
                <p>Pay with</p>
                <div
                  id="venmo-button"
                  class="btn btn-block"
                  style="
                    background: rgb(61, 149, 206)
                      url('/images/venmo_logo_white.png') repeat scroll 0% 0%;
                    display: block;
                    background-repeat: no-repeat;
                    background-size: 110px 21px;
                    background-position: center;
                  "
                  v-if="venmoNotSupported == false"
                ></div>
              </div>
              <button
                v-if="this.selectedPaymentMethodObj != null"
                type="button"
                class="btn btn-block"
                @click="book"
              >
                Submit Payment
              </button>
              <div
                v-if="errorText && paymentMethod === 'VenmoAccount'"
                class="has-error"
                v-html="errorText"
              ></div>
              <div
                v-if="
                  this.selectedPaymentMethodObj == null &&
                  venmoNotSupported == true
                "
              >
                Browser does not support Venmo
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User profile data -->

      <form
        method="post"
        @submit.prevent="onSubmitStep1"
        v-if="bookingStep == 1"
      >
        <h3 class="login-box-msg">User Information</h3>
        <div v-if="user == null">
          <p class="custom-padding">
            Input information or
            <a href="/login">Login to your account</a>
          </p>
        </div>

        <div class="d-flex flex-wrap">
          <div class="label-w-100">
            <label>Complete name</label>
          </div>
          <div
            class="form-group first-name has-feedback"
            :class="{ 'has-error': errors.first_name }"
          >
            <input
              type="text"
              class="form-control"
              required
              name="first_name"
              value
              v-model="fields.first_name"
              placeholder="First Name"
              :readonly="formReadonly == true"
            />
            <span class="help-block" v-if="errors.first_name">
              <strong>{{ errors.first_name[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group last-name has-feedback"
            :class="{ 'has-error': errors.last_name }"
          >
            <input
              type="text"
              class="form-control"
              required
              name="last_name"
              value
              v-model="fields.last_name"
              placeholder="Last Name"
              :readonly="formReadonly == true"
            />
            <span class="help-block" v-if="errors.last_name">
              <strong>{{ errors.last_name[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group has-feedback"
            :class="{ 'has-error': errors.instagram_handle }"
          >
            <label>Instagram Handle</label>
            <input
              type="text"
              class="form-control"
              name="instagram_handle"
              value
              v-model="fields.instagram_handle"
              placeholder="@instagram_name"
              :readonly="formReadonly == true"
            />
            <span class="help-block" v-if="errors.instagram_handle">
              <strong>{{ errors.instagram_handle[0] }}</strong>
            </span>
          </div>

          <!--<div class="form-group has-feedback" :class="{ 'has-error' : errors.address }">-->
          <!--<input type="text" class="form-control" name="address" value="" v-model="fields.address" placeholder="Address">-->
          <!--<span class="help-block" v-if="errors.address">-->
          <!--<strong>{{ errors.address[0] }}</strong>-->
          <!--</span>-->
          <!--</div>-->

          <div
            class="form-group input-city has-feedback"
            :class="{ 'has-error': errors.city }"
          >
            <label>City</label>
            <input
              type="text"
              class="form-control"
              name="city"
              value
              v-model="fields.city"
              placeholder="City"
              :readonly="formReadonly == true"
            />
            <span class="help-block" v-if="errors.city">
              <strong>{{ errors.city[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group input-state has-feedback"
            :class="{ 'has-error': errors.state }"
          >
            <label>State</label>
            <select
              class="form-control"
              name="state"
              v-bind:class="{ 'select-empty': fields.state === '' }"
              v-model="fields.state"
              :readonly="formReadonly == true"
            >
              <option value>State</option>
              <option
                v-for="state in usStates"
                :key="state.name"
                :value="state.code"
              >
                {{ state.name }}
              </option>
            </select>
            <span class="help-block" v-if="errors.state">
              <strong>{{ errors.state[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group input-zip has-feedback"
            :class="{ 'has-error': errors.zip }"
          >
            <label>ZIP</label>
            <input
              type="text"
              class="form-control"
              name="zip"
              value
              v-model="fields.zip"
              placeholder="ZIP code"
              :readonly="formReadonly == true"
            />
            <span class="help-block" v-if="errors.zip">
              <strong>{{ errors.zip[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group w-50 has-feedback"
            :class="{ 'has-error': errors.dob }"
          >
            <label>Date of Birth</label>

            <dropdown-datepicker
              :max-year="2021"
              :min-year="1940"
              display-format="mdy"
              v-model="fields.dob"
              submit-format="yyyy-mm-dd"
            ></dropdown-datepicker>

            <span class="help-block" v-if="errors.dob">
              <strong>{{ errors.dob[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group w-50 has-feedback"
            :class="{ 'has-error': errors.gender }"
          >
            <label>Gender</label>
            <div class="radio-wrapper">
              <label class="radio-item" for="male">
                <input
                  v-model="fields.gender"
                  name="gender"
                  type="radio"
                  id="male"
                  value="male"
                  :readonly="formReadonly == true"
                />
                <span class="checkmark"></span>
                Male
              </label>
              <label class="radio-item" for="female">
                <input
                  v-model="fields.gender"
                  name="gender"
                  type="radio"
                  id="female"
                  value="female"
                  :readonly="formReadonly == true"
                />
                <span class="checkmark"></span>
                Female
              </label>
            </div>

            <span class="help-block" v-if="errors.gender">
              <strong>{{ errors.gender[0] }}</strong>
            </span>
          </div>

          <div
            v-if="formReadonly == false"
            class="form-group has-feedback"
            :class="{ 'has-error': errors.genres }"
          >
            <label>Interested Genres</label>
            <multiselect
              @input="changeIt"
              v-model="genresTemp"
              :options="siteGenres"
              label="title"
              track-by="id"
              :preserve-search="true"
              :close-on-select="false"
              :clear-on-select="false"
              :multiple="true"
              placeholder="Select Genre"
            >
              <template slot="selection" slot-scope="{ values, isOpen }">
                <span
                  class="multiselect__single"
                  v-if="values.length && !isOpen"
                  >{{ values.length }} options selected</span
                >
              </template>
              <template slot="option" slot-scope="props">
                <div class="option__checkbox">{{ props.option.title }}</div>
              </template>
            </multiselect>
            <input type="hidden" name="genres" v-model="fields.genres" />

            <span class="help-block" v-if="errors.genres">
              <strong>{{ errors.genres[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group w-50 has-feedback"
            :class="{ 'has-error': errors.email }"
          >
            <label>Email</label>
            <input
              type="email"
              class="form-control"
              required
              name="email"
              value
              v-model="fields.email"
              placeholder="Email"
              :readonly="formReadonly == true"
            />
            <span class="help-block" v-if="errors.email">
              <strong>{{ errors.email[0] }}</strong>
            </span>
          </div>

          <div
            class="form-group w-50 has-feedback"
            :class="{ 'has-error': errors.mobile_phone }"
          >
            <label>Phone number</label>

            <masked-input
              v-if="formReadonly == false"
              :class="'form-control'"
              v-model="fields.mobile_phone"
              :placeholder="'+1 (___) ___ ____'"
              mask="\+1 (111) 111 1111"
            />
            <input
              v-if="formReadonly == true"
              type="text"
              class="form-control"
              v-model="fields.mobile_phone"
              readonly
            />

            <span class="help-block" v-if="errors.mobile_phone">
              <strong>{{ errors.mobile_phone[0] }}</strong>
            </span>
          </div>

          <div
            v-if="lesson_type == 'in_person_client'"
            class="form-group has-feedback"
            :class="{ 'has-error': errors.location }"
          >
            <label>Location</label>
            <input
              type="text"
              class="form-control"
              name="location"
              required
              value
              v-model="fields.location"
              ref="lessonLocation"
            />
          </div>
          <div
            class="checkout-terms form-group checkbox-wrapper has-feedback"
            :class="{ 'has-error': errors.accept_terms }"
          >
            <div class="field">
              <label for="accept-terms">
                <input
                  v-model="fields.accept_terms"
                  type="checkbox"
                  id="accept-terms"
                  :value="1"
                />
                <span class="checkmark"></span>
                I agree to the
                <a href="/terms" target="_blank">terms of service</a> and that
                by booking, I’ll be registered as a user
              </label>
            </div>

            <span class="help-block" v-if="errors.accept_terms">
              <strong>{{ errors.accept_terms[0] }}</strong>
            </span>
          </div>

          <div v-if="errorText" class="has-error" v-html="errorText"></div>
          <div
            v-if="successText"
            class="has-success"
            v-html="successText"
          ></div>

          <div class="form-group">
            <button
              type="submit"
              :disabled="!fields.accept_terms"
              class="btn btn-block"
            >
              Submit Booking Request
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import guestCartHelper from '../helpers/guestCartHelper'
import MaskedInput from 'vue-masked-input'
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
// import VueAutonumeric from '../../../node_modules/vue-autonumeric/src/components/VueAutonumeric.vue';
import $ from 'jquery'
require('jquery.maskedinput/src/jquery.maskedinput')
import DropdownDatepicker from 'vue-dropdown-datepicker'
export default {
  components: {
    MaskedInput,
    DropdownDatepicker,
    // 			VueAutonumeric,
  },
  mixins: [siteAPI, skillectiveHelper],
  props: [
    'user',
    'categorizedGenres',
    'userPaymentMethods',
    'paymentEnvironment',
    'clientToken',
    'confirmationText',
    'lessonsCount',
  ],
  data() {
    return {
      //				route: '',
      //				logItems : [],
      //				focusLoop: false,
      paymentMethod: 'CreditCard',
      paymentMethods: [],
      lesson_type: '',
      fields: {
        first_name: '',
        last_name: '',
        instagram_handle: '',
        email: '',
        address: '',
        city: '',
        state: '',
        zip: '',
        dob: '',
        gender: '',
        mobile_phone: '',
        genres: [],
        accept_terms: false,
        payment_method_token: null,
        payment_method_nonce: null,
        device_data: '',
        lesson_type: '',
      },
      booking: null,
      genresTemp: [],
      formReadonly: false,
      bookingStep: 1,
      useSavedMethod: false,
      venmoNotSupported: false,
      checkoutSuccess: false,
    }
  },
  methods: {
    ...mapActions({
      fetchCartTotal: 'fetchCartTotal',
      fetchCartItems: 'fetchCartItems',
    }),
    ...mapGetters({
      getCartTotal: 'getCartTotal',
    }),
    ...mapMutations(['SET_CHECK_OUT_STEP']),
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this
      var autocomplete = this.initializeLocationField(this.$refs[_ref], [
        'address',
      ])
      google.maps.event.addListener(
        autocomplete,
        'place_changed',
        function (e) {
          thisComponent.fields.location = thisComponent.$refs[_ref].value
        }
      )
    },
    fetchBrainthreeToken() {
      var vueComponent = this

      return new Promise((resolve, reject) => {
        this.braintreeClientInstance.tokenize(
          {
            cardholderName: this.fields.cardholderName,
          },
          function (tokenizeErr, payload) {
            if (tokenizeErr) {
              //                        console.error(tokenizeErr);
              vueComponent.errorText = tokenizeErr.message
              //                        vueComponent.errorText = 'Error: Can\'t process your data'
              return
            } else vueComponent.errorText = ''
            if (payload.nonce != undefined) {
              if (
                vueComponent.fields.payment_method_token == null &&
                payload.nonce == null
              ) {
                vueComponent.errorText = 'No payment method provided'
                return
              }
              resolve(payload.nonce)
            } else {
              vueComponent.errorText = "Can't process your data."
            }
          }
        )
      })
    },
    async onSubmitStepCreditCard2() {
      if (this.getTotal.count > 0) {
        if (this.selectedPaymentMethodObj == null) {
          let i = 0
          const payment_method_nonce = []

          while (i < this.getTotal.count) {
            let token = await this.fetchBrainthreeToken()
            payment_method_nonce.push(token)
            i++
          }

          this.fields.payment_method_nonce = payment_method_nonce

          setTimeout(() => {
            this.book()
          }, 1)
        } else {
          this.book()
        }
      }
    },
    onSubmitStep1() {
      if (moment(this.fields.dob))
        this.fields.dob = moment(this.fields.dob).format('YYYY-MM-DD')

      this.fetchCartTotal()
      this.fetchCartItems()
      this.apiPost('/api/cart/validate-user-info', this.fields)
      this.SET_CHECK_OUT_STEP(2)
    },
    book() {
      if (moment(this.fields.dob))
        this.fields.dob = moment(this.fields.dob).format('YYYY-MM-DD')

      this.apiPost('/api/cart/checkout', {
        ...this.fields,
        guest_cart: guestCartHelper.getProducts(),
        promo_codes: guestCartHelper.getPromos(),
      })
    },
    componentHandlePostResponse(responseData) {
      if (this.bookingStep == 1) {
        this.bookingStep = 2
      } else if (this.bookingStep == 2) {
        guestCartHelper.clearProducts()
        guestCartHelper.clearPromos()

        this.booking = responseData.data
        this.clearSubmittedForm()
        this.successText = null
        this.bookingStep = 3
        this.SET_CHECK_OUT_STEP(3)
        this.fetchCartTotal()
        this.fetchCartItems()
        this.checkoutSuccess = true
      }
    },
    componentHandleGetResponse(responseData) {
      if (this.bookingStep == 2) {
        this.paymentMethodsData = responseData.data
      }
    },
    changeIt: function () {
      var tempArry = []
      this.genresTemp.forEach((item) => {
        tempArry.push(item.id)
      })
      this.fields.genres = tempArry
    },
    initBraintreeClient() {
      var vueComponent = this
      this.fields.payment_method_nonce = null
      if (this.selectedPaymentMethodObj == null) {
        // needed only for creating new method

        braintree.client.create(
          {
            authorization: this.clientToken,
          },
          function (clientErr, clientInstance) {
            if (clientErr) {
              vueComponent.errorText = clientErr.message
              return
            } else vueComponent.errorText = ''

            if (vueComponent.paymentMethod == 'CreditCard') {
              // Create a hostedFields component to initialize the form
              //							if (vueComponent.selectedPaymentMethodObj!=null){
              //								vueComponent.fields.cardholderName = vueComponent.selectedPaymentMethodObj.cardholderName;
              //							}

              braintree.hostedFields.create(
                {
                  client: clientInstance,
                  styles: {
                    // https://developers.braintreepayments.com/guides/hosted-fields/styling/javascript/v3
                    input: {
                      'font-size': '14px',
                      border: '1px solid #ccc',
                    },
                    'input.invalid': {
                      color: 'red',
                    },
                    'input.valid': {
                      color: 'green',
                    },
                  },
                  // Configure which fields in your card form will be generated by Hosted Fields instead
                  fields: {
                    number: {
                      selector: '#card-number',
                      placeholder: '____ ____ ____ ____',
                    },
                    cvv: {
                      selector: '#cvv',
                      placeholder: '•••',
                    },
                    expirationDate: {
                      selector: '#expiration-date',
                      placeholder: 'MM / YYYY',
                      //										prefill : (vueComponent.selectedPaymentMethodObj!=null) ? (vueComponent.selectedPaymentMethodObj.expirationDate) :''
                    },
                  },
                },
                function (hostedFieldsErr, instance) {
                  if (hostedFieldsErr) {
                    vueComponent.errorText = hostedFieldsErr.message
                    console.error(hostedFieldsErr)
                    return
                  } else vueComponent.errorText = ''
                  document
                    .querySelector('#payment-method-form-submit')
                    .removeAttribute('disabled')
                  vueComponent.braintreeClientInstance = instance
                }
              )
            } else if (vueComponent.paymentMethod == 'PayPalAccount') {
              var forPayPal = true
              vueComponent.collectDeviceDataForBraintree(
                clientInstance,
                forPayPal
              )

              // Create a PayPal Checkout component.
              braintree.paypalCheckout.create(
                {
                  client: clientInstance,
                },
                function (paypalCheckoutErr, paypalCheckoutInstance) {
                  // Stop if there was a problem creating PayPal Checkout.
                  // This could happen if there was a network error or if it's incorrectly
                  // configured.
                  if (paypalCheckoutErr) {
                    vueComponent.errorText = paypalCheckoutErr.message
                    console.error(
                      'Error connecting to PayPal:',
                      paypalCheckoutErr
                    )
                    return
                  }

                  // Set up PayPal with the checkout.js library
                  paypal.Button.render(
                    {
                      env: vueComponent.paymentEnvironment, //'production' or 'sandbox'
                      style: {
                        label: 'pay',
                        size: 'large',
                        //										tagline : false,
                        shape: 'rect',
                      },
                      payment: function () {
                        return paypalCheckoutInstance.createPayment({
                          flow: 'vault',
                          billingAgreementDescription: '',
                          enableShippingAddress: false,
                        })
                      },
                      onAuthorize: function (data, actions) {
                        return paypalCheckoutInstance.tokenizePayment(
                          data,
                          function (err, payload) {
                            if (payload.nonce != undefined) {
                              vueComponent.fields.payment_method_nonce =
                                payload.nonce
                              vueComponent.book()
                            } else {
                              vueComponent.errorText =
                                "Can't process your data."
                            }
                          }
                        )
                      },
                      onCancel: function (data) {
                        console.log(
                          'Payment cancelled',
                          JSON.stringify(data, 0, 2)
                        )
                      },

                      onError: function (err) {
                        console.error('checkout.js error', err)
                      },
                    },
                    '#paypal-button'
                  ).then(function () {
                    // The PayPal button will be rendered in an html element with the id
                    // `paypal-button`. This function will be called when the PayPal button
                    // is set up and ready to be used.
                  })
                }
              )
            } else if (vueComponent.paymentMethod == 'VenmoAccount') {
              var forPayPal = true
              vueComponent.collectDeviceDataForBraintree(
                clientInstance,
                forPayPal
              )

              //							vueComponent.logItems.push('create method');

              braintree.venmo.create(
                {
                  client: clientInstance,
                  allowNewBrowserTab: true,
                },
                function (venmoErr, venmoInstance) {
                  // Stop if there was a problem creating Venmo. This could happen if there was a network error or if it's incorrectly configured.
                  if (venmoErr) {
                    vueComponent.errorText = venmoErr.message
                    console.error('Error creating Venmo:', venmoErr)
                    return
                  }

                  // Verify browser support before proceeding.
                  if (!venmoInstance.isBrowserSupported()) {
                    vueComponent.venmoNotSupported = true
                    return
                  }

                  //								vueComponent.logItems.push('create button');

                  var venmoButton = document.getElementById('venmo-button')
                  venmoButton.style.display = 'block' // Assumes that venmoButton is initially display: none.
                  venmoButton.addEventListener('click', function () {
                    venmoButton.disabled = true

                    Cookies.set('currentOrderDetails', {
                      fields: vueComponent.fields,
                      paymentMethod: vueComponent.paymentMethod,
                      bookingStep: vueComponent.bookingStep,
                      useSavedMethod: vueComponent.useSavedMethod,
                    })
                    //									vueComponent.logItems.push('button clicked');
                    //									vueComponent.focusLoop = true;
                    //									vueComponent.focusWindow();

                    venmoInstance.tokenize(function (tokenizeErr, payload) {
                      //										vueComponent.logItems.push('tokenized');
                      venmoButton.removeAttribute('disabled')
                      if (tokenizeErr) {
                        //											vueComponent.logItems.push(tokenizeErr.message);
                        if (tokenizeErr.code === 'VENMO_CANCELED') {
                          vueComponent.errorText =
                            'App is not available or user aborted payment flow'
                        } else if (tokenizeErr.code === 'VENMO_APP_CANCELED') {
                          vueComponent.errorText = 'User canceled payment flow'
                        } else {
                          vueComponent.errorText =
                            'An error occurred:' + tokenizeErr.message
                        }
                      } else {
                        // Send payload.nonce to your server.
                        //											console.log('Got a payment method nonce:', payload.nonce);
                        // Display the Venmo username in your checkout UI.
                        //											vueComponent.logItems.push(payload.nonce);
                        //											console.log('Venmo user:', payload.details.username);
                        if (payload.nonce != undefined) {
                          vueComponent.fields.payment_method_nonce =
                            payload.nonce
                          vueComponent.book()
                        } else {
                          vueComponent.errorText = "Can't process your data."
                        }
                      }
                    })
                  })

                  // Check if tokenization results already exist. This occurs when your checkout page is relaunched in a new tab. This step can be omitted if allowNewBrowserTab is false.
                  if (venmoInstance.hasTokenizationResult()) {
                    //									vueComponent.logItems.push('hasTokenizationResult');

                    venmoInstance.tokenize(function (tokenizeErr, payload) {
                      //										vueComponent.logItems.push('tokenized 2');
                      if (tokenizeErr) {
                        if (tokenizeErr.code === 'VENMO_CANCELED') {
                          vueComponent.errorText =
                            'App is not available or user aborted payment flow'
                        } else if (tokenizeErr.code === 'VENMO_APP_CANCELED') {
                          vueComponent.errorText = 'User canceled payment flow'
                        } else {
                          vueComponent.errorText =
                            'An error occurred:' + tokenizeErr.message
                        }
                      } else {
                        // Send payload.nonce to your server.
                        //											console.log('Got a payment method nonce:', payload.nonce);
                        // Display the Venmo username in your checkout UI.
                        console.log('Venmo user:', payload.details.username)
                        if (payload.nonce != undefined) {
                          vueComponent.fields.payment_method_nonce =
                            payload.nonce
                          vueComponent.book()
                        } else {
                          vueComponent.errorText = "Can't process your data."
                        }
                      }
                    })
                    return
                  }
                }
              )
            }
          }
        )
      }
    },
    collectDeviceDataForBraintree(clientInstance, forPayPal) {
      var vueComponent = this
      braintree.dataCollector.create(
        {
          client: clientInstance,
          paypal: forPayPal,
        },
        function (err, dataCollectorInstance) {
          if (err) {
            vueComponent.errorText = err.message
            dataCollectorInstance.teardown()
            return
          }
          // At this point, you should access the dataCollectorInstance.deviceData value and provide it
          // to your server, e.g. by injecting it into your form as a hidden input.
          vueComponent.fields.device_data = dataCollectorInstance.deviceData
          dataCollectorInstance.teardown()
        }
      )
    },
    setSelectedPaymentMethodObj(_paymentMethod) {
      if (
        this.useSavedMethod &&
        this.paymentMethods[_paymentMethod] != undefined
      ) {
        this.selectedPaymentMethodObj = this.paymentMethods[_paymentMethod]
        this.fields.payment_method_token =
          this.paymentMethods[_paymentMethod].token
      } else this.selectedPaymentMethodObj = null
      this.initBraintreeClient()
    },
    getSavedCardNumberVal(_last4) {
      return '•••• •••• •••• ' + _last4
    },
    //			focusWindow: function () {
    //				setInterval( () => {
    //					if (this.focusLoop==true
    //						&& window.location.hash!=this.route
    //						&& window.location.hash.search(/venmoSuccess=/)!==-1
    //						&& Cookies.get('currentOrderDetails')!=undefined
    //					){
    //						this.logItems.push('focus');
    //						window.blur();
    //						window.focus();
    //						this.focusLoop = false;
    //					}
    //				}, 100 );
    //			},
  },
  computed: {
    ...mapState(['checkOutStep']),
    computedFields: function () {
      return Object.assign({}, this.fields)
    },
    getTotal() {
      return this.getCartTotal()
    },
  },
  mounted: function () {
    this.initNewPlacesAutocomplete('lessonLocation')
  },
  watch: {
    checkOutStep() {
      if (this.checkOutStep == 1) this.bookingStep = 1
      if (this.checkOutStep == 3) this.bookingStep = 3
    },
    useSavedMethod: function (newValue, oldValue) {
      this.setSelectedPaymentMethodObj(this.paymentMethod)
    },
    paymentMethod: function (newPaymentMethod, oldPaymentMethod) {
      this.useSavedMethod = false
      this.venmoNotSupported = false
      this.setSelectedPaymentMethodObj(newPaymentMethod)
    },
    bookingStep: function (newBookingStep, oldBookingStep) {
      if (newBookingStep == 2) {
        this.errorText = ''
        this.setSelectedPaymentMethodObj(this.paymentMethod)
      }
    },
    computedFields: {
      handler(value, oldValue) {
        if (value.lesson_type) {
          if (!oldValue || value.lesson_type !== oldValue.lesson_type) {
            if (value.lesson_type == 'in_person_client') {
              setTimeout(() => {
                var thisComponent = this
                var autocomplete = this.initializeLocationField(
                  this.$refs['lessonLocation'],
                  ['address']
                )
                google.maps.event.addListener(
                  autocomplete,
                  'place_changed',
                  function (e) {
                    thisComponent.fields.location =
                      thisComponent.$refs['lessonLocation'].value
                  }
                )
              }, 1)
            }
          }
        }
      },
      deep: true,
    },
  },
  created: function () {
    if (
      window.location.hash.search(/venmoSuccess=/) !== -1 &&
      Cookies.get('currentOrderDetails') != undefined
    ) {
      var currentOrderDetails = Cookies.get('currentOrderDetails')
      if (typeof currentOrderDetails == 'string')
        currentOrderDetails = JSON.parse(currentOrderDetails)
      //				console.log(Cookies.get('currentOrderDetails'));
      //				console.log(currentOrderDetails);
      this.fields = currentOrderDetails.fields
      this.paymentMethod = currentOrderDetails.paymentMethod
      this.bookingStep = currentOrderDetails.bookingStep
      this.useSavedMethod = currentOrderDetails.useSavedMethod
      this.setSelectedPaymentMethodObj(this.paymentMethod)
    } else if (this.user !== null) {
      this.fields.first_name = this.user.first_name
      this.fields.last_name = this.user.last_name
      this.fields.instagram_handle = this.user.profile.instagram_handle
      this.fields.email = this.user.email
      this.fields.address = this.user.profile.first_name
      this.fields.city = this.user.profile.city
      this.fields.state = this.user.profile.state
      this.fields.zip = this.user.profile.zip
      this.fields.dob = this.user.profile.dob
      this.fields.gender = this.user.profile.gender
      this.fields.mobile_phone = this.user.profile.mobile_phone
      this.fields.genres = this.user.genres

      // console.log("g", this.user.genres)
      this.genresTemp = this.siteGenres.filter((v) => {
        return this.user.genres.includes(v.id)
      })
      //				this.formReadonly = this.user.email!='';
    }
    setTimeout(function () {
      window.jQuery('.mask-input').mask('99/99/9999')
    }, 200)
    this.paymentMethods = this.userPaymentMethods
    //			this.route = window.location.hash;
  },
}
</script>