<template>
  <div id='password-payment-account'>
    <form id='payment-method-form' method='post' @submit.prevent='onSubmit'>

      <p class='login-box-msg'>Payment Methods123</p>
      <div class='form-group has-feedback mb-5'>
        <div class='radio-wrapper'>
          <label class='radio-item' for='isCard'>
            <input id='isCard' v-model='paymentMethod' name='payment_system' type='radio' value='CreditCard'>
            <span class='checkmark'></span>
            Credit Card <img alt='Credit Card' class='ml-2' src='/images/card-icon.png'>
          </label>
          <!--          <label class="radio-item" for="paypal">-->
          <!--            <input v-model="paymentMethod" name="payment_system" type="radio" id="paypal" value="PayPalAccount">-->
          <!--            <span class="checkmark"></span>-->
          <!--            <img src="/images/payPal.svg" alt="Paypal">-->
          <!--          </label>-->
          <!--                    <label class="radio-item" for="venmo">-->
          <!--                        <input v-model="paymentMethod" name="payment_system" type="radio" id="venmo" value="VenmoAccount">-->
          <!--                        <span class="checkmark"></span>-->
          <!--                        <img src="/images/venmo.png" alt="Venmo">-->
          <!--                    </label>-->
        </div>
      </div>

      <div v-if="paymentMethod === 'CreditCard'" >
          <div class='payment-option mt-5 pt-5 active'>
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
                    id='onSubmitStepCreditCard'
                    class='btn btn-primary btn-flat'
                    type='button'
                    value='submit'
                  >
                    Save
                  </button>
                  <button
                    v-show='isSelectedPaymentMethod'
                    class='btn btn-primary btn-flat'
                    type='button'
                  >
                    Delete payment method
                  </button>
                </div>
              </div>
            </div>

        </div>
      </div>
      <div v-show="paymentMethod === 'PayPalAccount'" id='paypal-buttons-container'></div>
      <div v-if="paymentMethod === 'PayPalAccount'" class='d-flex flex-wrap'>
        <div v-if='(this.selectedPaymentMethodObj==null)'>
          Connect PayPal Account
          <div id='paypal-button'></div>
<!--          <div v-if="waitPaypalInitialization==true">Wait while PayPal initializes the button</div>-->
        </div>
        <div class='form-group'>
<!--          <button v-if="(this.selectedPaymentMethodObj!=null && this.selectedPaymentMethodObj.is_default==false)" type="button" class="btn btn-primary btn-flat" @click="setAsDefaultPaymentMethod()">Set as default method</button>-->
          <button
            v-if='(this.selectedPaymentMethodObj!=null)' class='btn btn-primary btn-flat' type='button'
            @click='deletePaymentMethod()'
          >Delete payment method
          </button>
        </div>
      </div>


      <div v-if='errorText' class='has-error form-group'>{{ errorText }}</div>
      <div v-if='successText' class='has-success form-group'>{{ successText }}</div>
    </form>
  </div>
</template>

<script>
import { loadScript } from '@paypal/paypal-js'
import siteAPI from '../mixins/siteAPI.js'

export default {
  mixins: [siteAPI],
  props: [
    'clientToken',
    'userPaymentMethods'
  ],
  data() {
    return {
      paymentMethod: 'CreditCard',
      selectedPaymentMethodObj: null,
      paymentMethods: [],
      fields: {
        cardholderName: '',
        payment_method_nonce: null,
      },
      device_data: '',
      waitPaypalInitialization: false,
      venmoNotSupported: false,
      paypal: null,
      isSelectedPaymentMethod: false,
      lastFour: '**** **** **** 1234'
    }
  },
  methods: {

    async initializePaypal() {
      try {
        this.paypal = await loadScript({
          clientId: this.clientToken,
          buyerCountry: 'US',  // удалити при запуску на продакшені !!!!!!!
          locale: 'en_US',
          components: ['buttons', 'funding-eligibility', 'marks', 'card-fields'],
          vault: true,
          disableFunding: ['venmo,paylater'],
          // dataClientToken: this.user.pp_customer_id,
        })

        this.initPaymentMethod()
      } catch (error) {
        console.error('Failed to load the PayPal JS SDK script', error)
      }

    },
    initPaymentMethod() {
      if (this.paypal.FUNDING.CARD) this.renderCardForm()
      // if (this.paypal.FUNDING.PAYPAL) this.renderPayPalButton()
    },
    renderPayPalButton() {
      this.paypal.Buttons({
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
          const result = await axios.get('api/cart/vault-setup-token?method=card', {
              baseURL: "https://skillective.dvl.to/"
          })
            return result.data.vaultSetupToken;
        },
        onApprove: async (data) => {
            console.log(data, "onApprove")
            await axios.post('/api/student/payment-method',
                { payment_method_nonce: data.vaultSetupToken},
                { baseURL: "https://skillective.dvl.to/"} )
        },
        onError: (error) => console.log('Something went wrong:', error)
      })

      if (cardFields.isEligible()) {
        cardFields.NameField().render('#card-holder-name')
        cardFields.NumberField().render('#card-number')
        cardFields.ExpiryField().render('#expiration-date')
        cardFields.CVVField().render('#cvv')
      } else {
        console.log("обробити нормальний вивод помилки")
      }

      const submitButton = document.getElementById('onSubmitStepCreditCard')
      submitButton.addEventListener('click', () => {
        cardFields.submit()
          .then(() => {
            this.successText = 'Payment method saved'
          })
          .catch((error) => {
            this.errorText = 'Can\'t process your data.'
            console.log(error, "обробити нормальний вивод помилки")
          })
      })
    },

    deletePaymentMethod() {
      this.apiDelete('/api/student/payment-method/' + this.selectedPaymentMethodObj.token)
    },
    getPaymentMethods() {
      this.apiGet('/api/student/payment-methods')
    },

    componentHandleGetResponse(responseData) {
        console.log(responseData, " HandleGetResponse")
      this.paymentMethods = responseData.data
      this.setSelectedPaymentMethodObj(this.paymentMethod)
    },

    componentHandleDeleteResponse(responseData) {
      this.fields.cardholderName = ''
      this.getPaymentMethods()
    },

    setSelectedPaymentMethodObj(_paymentMethod) {
      console.log(this.paymentMethods[_paymentMethod], " setSelectedPaymentMethodObj")

    },


  },
  created() {

    this.paymentMethods = this.userPaymentMethods
      console.log(this.paymentMethods)
    // this.setSelectedPaymentMethodObj(this.paymentMethod)
    this.initializePaypal()
  },
  // watch: {
  //   paymentMethod: function (newPaymentMethod, oldPaymentMethod) {
  //     this.venmoNotSupported = false
  //     this.setSelectedPaymentMethodObj(newPaymentMethod)
  //   }
  // }
}
</script>
