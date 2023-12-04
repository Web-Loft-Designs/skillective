<template>
  <div id='password-payment-account'>
    <form id='payment-method-form'>

      <p class='login-box-msg'>Payment Methods123</p>
      <div class='form-group has-feedback mb-5'>
        <div class='radio-wrapper'>
          <label class='radio-item' for='isCard'>
            <input id='isCard' v-model='paymentMethod' name='payment_system' type='radio' value='CreditCard'>
            <span class='checkmark'></span>
            Credit Card <img alt='Credit Card' class='ml-2' src='/images/card-icon.png'>
          </label>
        </div>
      </div>

      <div id='paypal-buttons-container'></div>

      <div v-if="paymentMethod === 'CreditCard'">
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


      <div v-if='errorText' class='has-error form-group'>{{ errorText }}</div>
      <div v-if='successText' class='has-success form-group'>{{ successText }}</div>
    </form>
  </div>
</template>

<script>
import {loadScript} from '@paypal/paypal-js'
import siteAPI from '../mixins/siteAPI.js'

export default {
  mixins: [siteAPI],
  props: [
    'clientToken',
    'userPaymentMethods',
    'dataUserIdToken',
    'masterMerchantId'
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
      lastFour: '**** **** **** 1234',
      errorText: "",
      successText: ''
    }
  },
  methods: {

    async initializePaypal() {
      try {
        this.paypal = await loadScript({
          clientId: this.clientToken,
          merchantId: this.masterMerchantId,
          buyerCountry: 'US',  // удалити при запуску на продакшені !!!!!!!
          locale: 'en_US',
          components: ['buttons', 'card-fields'],
          vault: true,
          disableFunding: ['paylater'],
          dataUserIdToken: this.dataUserIdToken,
        })

        this.initPaymentMethod()
      } catch (error) {
        console.error('Failed to load the PayPal JS SDK script', error)
      }

    },
    initPaymentMethod() {
      this.renderCardForm()
      // this.renderPayPalButton()
    },
    renderPayPalButton() {
      this.paypal.Buttons({
        createVaultSetupToken: async () => {
          const result = await axios.post('/api/cart/vault-setup-token?method=paypal')
          return result.data.vaultSetupToken;
        },
        onApprove: async (data) => {
          console.log(data, "onApprove PayPal")
          await axios.post('/api/student/payment-method',
              {payment_method_nonce: data.vaultSetupToken})
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
          const result = await axios.post('/api/cart/vault-setup-token?method=card')
          return result.data.vaultSetupToken;
        },
        onApprove: async (data) => {
          console.log(data, "onApprove")
          await axios.post('/api/student/payment-method',
              {payment_method_nonce: data.vaultSetupToken})
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
    console.log(this.paymentMethods, "userPaymentMethods")
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
