<script src='../../../../../../../../Стільниця/example_js_sdk.js'>
</script>
<template>
  <div id='password-payment-account'>
    <form id='payment-method-form'>

      <p class='login-box-msg'>Payment Methods</p>
      <div v-show='isRadioButton' class='form-group has-feedback mb-5'>
        <div class='radio-wrapper'>
          <label class='radio-item' for='isCard'>
            <input id='isCard' v-model='paymentMethod' name='payment_system' type='radio' value='CreditCard'>
            <span class='checkmark'></span>
            Credit Card <img alt='Credit Card' class='ml-2' src='/images/card-icon.png'>
          </label>
          <label class='radio-item' for='btn'>
            <input id='btn' v-model='paymentMethod' name='payment_system' type='radio' value='PayPalButton'>
            <span class='checkmark'></span>
              <img alt='Pay Pal' class='ml-2' src='/images/payPal.svg'>
          </label>
        </div>
      </div>

      <div v-show="paymentMethod === 'PayPalButton'" id='paypal-buttons-container'></div>
      <button
        v-show="paymentMethod === 'PayPalButton' && isSelectedPaymentMethod"
        class='btn btn-primary btn-flat cursor-pointer'
        type='button'
        @click='deletePaymentMethod()'
      >
        Delete payment method
      </button>

      <div v-show="paymentMethod === 'CreditCard'">
        <div class='payment-option pt-1 active'>
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
                <label>Expiration date</label>
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
                  class='btn btn-primary btn-flat cursor-pointer'
                  type='button'
                  value='submit'
                >
                  Save
                </button>
                <button
                  v-show='isSelectedPaymentMethod'
                  class='btn btn-primary btn-flat cursor-pointer'
                  type='button'
                  @click='deletePaymentMethod()'
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
import { loadScript } from '@paypal/paypal-js'
import siteAPI from '../mixins/siteAPI.js'

export default {
  mixins: [siteAPI],
  props: [
    'clientId',
    'userPaymentMethods',
    'dataUserIdToken',
  ],
  data() {
    return {
      paymentMethod: 'CreditCard',
      selectedPaymentMethodObj: null,
      paymentMethods: [],
      fields: {
        cardholderName: '',
        payment_method_nonce: null
      },
      device_data: '',
      waitPaypalInitialization: false,
      venmoNotSupported: false,
      paypal: null,
      isSelectedPaymentMethod: false,
      lastFour: '',
      errorText: '',
      successText: '',
      isRadioButton: true,
      currentPaymentId: null
    }
  },
  methods: {
    async initializePaypal() {

      try {
        this.paypal = await loadScript({
          clientId: this.clientId,
          buyerCountry: 'US',  // удалити при запуску на продакшені !!!!!!!
          locale: 'en_US',
          components: ['buttons', 'card-fields'],
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
      this.renderCardForm()
      this.renderPayPalButton()
    },
    renderPayPalButton() {
      this.paypal.Buttons({
        createVaultSetupToken: async () => {
          const result = await axios.post('/api/cart/vault-setup-token?method=paypal')
          return result.data.vaultSetupToken
        },
        onApprove: async (data) => {
          await axios.post('/api/student/payment-method',
            { payment_method_nonce: data.vaultSetupToken })
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
          return result.data.vaultSetupToken
        },
        onApprove: async (data) => {
          await axios.post('/api/student/payment-method',
            { payment_method_nonce: data.vaultSetupToken })
        },
        onError: (error) => console.log('Something went wrong:', error)
      })

      if (cardFields.isEligible()) {
        cardFields.NameField().render('#card-holder-name')
        cardFields.NumberField().render('#card-number')
        cardFields.ExpiryField().render('#expiration-date')
        cardFields.CVVField().render('#cvv')
      } else {
        console.log('Error')
      }

      const submitButton = document.getElementById('onSubmitStepCreditCard')
      submitButton.addEventListener('click', () => {
        cardFields.submit()
          .then(() => {
            this.getPaymentMethods()
            this.successText = 'Payment method saved'
          })
          .catch((error) => {
            this.errorText = 'Can\'t process your data.'
            console.log(error, 'Error')
          })
      })
    },

    deletePaymentMethod() {
      this.apiDelete('/api/student/payment-method/' + this.currentPaymentId)
    },
    getPaymentMethods() {
      this.apiGet('/api/student/payment-methods')
    },

    componentHandleGetResponse(responseData) {
      this.paymentMethods = responseData.data
      if (!Object.keys(this.paymentMethods).length) {
        this.isSelectedPaymentMethod = false
        this.isRadioButton = true
        let card = document.getElementById('card-number')
        card.innerHTML = ''
        let cardHolderName = document.getElementById('card-holder-name')
        cardHolderName.innerHTML = ''
        let expirationDate = document.getElementById('expiration-date')
        expirationDate.innerHTML = ''
        let cvv = document.getElementById('cvv')
        cvv.innerHTML = ''
        this.renderCardForm()

      }
      if (this.paymentMethods.card) {
        this.isSelectedPaymentMethod = true
        this.lastFour = '**** **** **** ' + this.paymentMethods.card.last_digits
        this.currentPaymentId = this.paymentMethods.card.payment_id
        this.isRadioButton = false
      }
    },
    componentHandleDeleteResponse() {
      this.fields.cardholderName = ''
      this.getPaymentMethods()
    },
    isPaymentMethodsCard() {
      if (this.paymentMethods.card) {
        this.paymentMethod = 'CreditCard'
        this.lastFour = '**** **** **** ' + this.paymentMethods.card.last_digits
        this.isRadioButton = false
        this.currentPaymentId = this.paymentMethods.card.payment_id
        this.isSelectedPaymentMethod = true
      }
      if (this.paymentMethods.paypal){
        this.paymentMethod = 'PayPalButton'
        this.isRadioButton = false
        this.currentPaymentId = this.paymentMethods.paypal.payment_id
        this.isSelectedPaymentMethod = true
      }
    }
  },
  created() {
    this.paymentMethods = this.userPaymentMethods
    this.isPaymentMethodsCard()
    this.initializePaypal()
  },
}
</script>
<style lang='scss' scoped>
.radio-wrapper {
  display: flex;
  flex-direction: column;
}
</style>
