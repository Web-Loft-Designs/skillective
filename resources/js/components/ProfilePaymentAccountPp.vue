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
                    id='onSubmitStepCreditCard2'
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
<!--                    @click='book()'-->
                    Delete payment method
                  </button>
                </div>
              </div>
            </div>
<!--            <div>-->
<!--              <div class='payment-option-body d-flex flex-wrap'>-->
<!--                <div class='form-group has-feedback'>-->
<!--                  <label>Card number</label>-->
<!--                  <div v-if='(this.selectedPaymentMethodObj==null)' id='card-number' class='card-item'></div>-->
<!--                  <input-->
<!--                    v-if='this.selectedPaymentMethodObj!=null' :value='getSavedCardNumberVal(this.selectedPaymentMethodObj.last4)' class='form-control' disabled-->
<!--                    placeholder='____ ____ ____ ____'-->
<!--                    type='text'-->
<!--                  >-->
<!--                </div>-->
<!--                <div class='form-group has-feedback'>-->
<!--                  <label>Cardholder name</label>-->
<!--                  <input-->
<!--                    v-if='(this.selectedPaymentMethodObj==null)' id='cardholder-name' v-model='fields.cardholderName'-->
<!--                    class='form-control' name='cardholderName' placeholder='Cardholder Name' required-->
<!--                    type='text' value=''-->
<!--                  >-->
<!--                  <input-->
<!--                    v-if='this.selectedPaymentMethodObj!=null' :value='this.selectedPaymentMethodObj.cardholderName' class='form-control' disabled-->
<!--                    type='text'-->
<!--                  >-->
<!--                  &lt;!&ndash;<span class="help-block" v-if="errors.cardholderName"><strong>{{ errors.cardholderName[0] }}</strong></span>&ndash;&gt;-->
<!--                </div>-->
<!--                <div class='form-group w-33 has-feedback'>-->
<!--                  <label>Expiry date</label>-->
<!--                  <div v-if='(this.selectedPaymentMethodObj==null)' id='expiration-date' class='card-item'></div>-->
<!--                  <input-->
<!--                    v-if='this.selectedPaymentMethodObj!=null' :value='this.selectedPaymentMethodObj.expirationDate' class='form-control' disabled-->
<!--                    placeholder='MM / YY' type='text'-->
<!--                  >-->
<!--                </div>-->
<!--                <div v-if='(this.selectedPaymentMethodObj==null)' class='form-group w-33 has-feedback'>-->
<!--                  <label>CVC/CVV</label>-->
<!--                  <div id='cvv' class='card-item'></div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div class='form-group'>-->
<!--            <button-->
<!--              v-if='(this.selectedPaymentMethodObj==null)' id='payment-method-form-submit' class='btn btn-primary btn-flat'-->
<!--              disabled type='submit'-->
<!--            >Save-->
<!--            </button>-->
<!--            &lt;!&ndash;<button v-if="(this.selectedPaymentMethodObj!=null && this.selectedPaymentMethodObj.is_default==false)" type="button" class="btn btn-primary btn-flat" @click="setAsDefaultPaymentMethod()">Set as default method</button>&ndash;&gt;-->
<!--            <button-->
<!--              v-if='(this.selectedPaymentMethodObj!=null)' class='btn btn-primary btn-flat' type='button'-->
<!--              @click='deletePaymentMethod()'-->
<!--            >Delete payment method-->
<!--            </button>-->
        </div>
      </div>
      <div v-show="paymentMethod === 'PayPalAccount'" id='paypal-buttons-container'></div>
      <div v-if="paymentMethod === 'PayPalAccount'" class='d-flex flex-wrap'>
        <div v-if='(this.selectedPaymentMethodObj==null)'>
          Connect PayPal Account
          <div id='paypal-button'></div>
          <!--<div v-if="waitPaypalInitialization==true">Wait while PayPal initializes the button</div>-->
        </div>
        <div class='form-group'>
          <!--<button v-if="(this.selectedPaymentMethodObj!=null && this.selectedPaymentMethodObj.is_default==false)" type="button" class="btn btn-primary btn-flat" @click="setAsDefaultPaymentMethod()">Set as default method</button>-->
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
import $ from 'jquery'

export default {
  mixins: [siteAPI],
  props: [
    'paymentEnvironment',
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
      braintreeClientInstance: null,
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
          currency: 'USD',
          vault: true,
          disableFunding: ['venmo,paylater'],
          // dataClientToken: this.user.pp_customer_id,
        })
        console.log(123)
        this.initPaymentMethod()
      } catch (error) {
        console.error('Failed to load the PayPal JS SDK script', error)
      }
      console.log(1)
    },
    initPaymentMethod() {
      if (this.paypal.FUNDING.CARD) this.renderCardForm()
      if (this.paypal.FUNDING.PAYPAL) this.renderPayPalButton()
    },
    renderPayPalButton() {
      this.paypal.Buttons({
        style: {
          layout: 'vertical',
          color: 'gold',
          shape: 'pill',
          label: 'paypal'
        }
        // createOrder() {
        //     console.log('Buttons create order')
        // }
      }).render('#paypal-buttons-container')
    },
    renderCardForm() {
      const cardFields = this.paypal.CardFields({
        createVaultSetupToken: async () => {
          // отримати vaultSetupToken з нашого сервера
          const result = await fetch('api/cart/vault-setup-token?method=card', {
            method: 'GET'
          })
          console.log(result)
          // const { vaultSetupToken } = await result.json()
          return vaultSetupToken
        },
        onApprove: async (data) => {
          // запуск процес оплати
          // payment_method_token
          this.fields.payment_method_nonce = data.vaultSetupToken
          console.log(this.fields.payment_method_nonce)
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


    setAsDefaultPaymentMethod() {
      this.apiPut('/api/student/payment-method/set-as-default/' + this.selectedPaymentMethodObj.token)
    },
    deletePaymentMethod() {
      this.apiDelete('/api/student/payment-method/' + this.selectedPaymentMethodObj.token)
    },
    getPaymentMethods() {
      this.apiGet('/api/student/payment-methods')
    },
    sendNonceToServer(nonce) {
      this.apiPost(
        '/api/student/payment-method',
        {
          payment_method_nonce: nonce,
          device_data: this.device_data
        }
      )
    },
    onSubmit() {
      var vueComponent = this
      if (this.selectedPaymentMethodObj == null) {
        this.braintreeClientInstance.tokenize({
          cardholderName: this.fields.cardholderName
        }, function (tokenizeErr, payload) {
          if (tokenizeErr) {
            //                        console.error(tokenizeErr);
            vueComponent.errorText = tokenizeErr.message
            //                        vueComponent.errorText = 'Error: Can\'t process your data'
            return
          } else
            vueComponent.errorText = ''
          if (payload.nonce != undefined) {
            vueComponent.sendNonceToServer(payload.nonce)
          } else {
            vueComponent.errorText = 'Can\'t process your data.'
          }
        })
      } else {
        this.errorText = 'Can\'t create new payment method.'
      }
    },
    componentHandleGetResponse(responseData) {
      this.paymentMethods = responseData.data
      this.setSelectedPaymentMethodObj(this.paymentMethod)
    },
    componentHandleDeleteResponse(responseData) {
      this.fields.cardholderName = ''
      this.getPaymentMethods()
    },
    componentHandlePutResponse(responseData) {
      this.getPaymentMethods()
    },
    componentHandlePostResponse(responseData) {
      if (this.paymentMethod == 'CreditCard') {
        this.braintreeClientInstance.teardown(function (teardownErr) {
          if (teardownErr) {
            console.error('Could not tear down the Hosted Fields form!')
          } else {
            console.info('Hosted Fields form has been torn down!')
          }
        })
      }

      if (responseData.success) {
        this.successText = 'Payment method saved'
      } else {
        this.errorText = 'Can\'t process your data.'
      }
      this.getPaymentMethods()
    },



    //  можливо непотрібна функціональність
    collectDeviceDataForBraintree(clientInstance, forPayPal) {
      var vueComponent = this
      braintree.dataCollector.create({
        client: clientInstance,
        paypal: forPayPal
      }, function (err, dataCollectorInstance) {
        if (err) {
          vueComponent.errorText = err.message
          dataCollectorInstance.teardown()
          return
        }
        // At this point, you should access the dataCollectorInstance.deviceData value and provide it
        // to your server, e.g. by injecting it into your form as a hidden input.
        vueComponent.device_data = dataCollectorInstance.deviceData
        dataCollectorInstance.teardown()
      })
    },


    setSelectedPaymentMethodObj(_paymentMethod) {
      console.log(this.paymentMethods[_paymentMethod])
      if (this.paymentMethods[_paymentMethod] != undefined) {
        this.selectedPaymentMethodObj = this.paymentMethods[_paymentMethod]
      } else {
        this.selectedPaymentMethodObj = null
      }

      this.initBraintreeClient()

    },
    getSavedCardNumberVal(_last4) {
      return ('•••• •••• •••• ' + _last4)
    }
  },
  created() {
    console.log(this.clientToken, 'clientToken')
    this.paymentMethods = this.userPaymentMethods

    console.log(this.paymentMethods, 'Saved methods')
    console.log(this.paymentMethod, 'paymentMethod')

    // this.setSelectedPaymentMethodObj(this.paymentMethod)
    this.initializePaypal()
  },
  watch: {
    paymentMethod: function (newPaymentMethod, oldPaymentMethod) {
      this.venmoNotSupported = false
      this.setSelectedPaymentMethodObj(newPaymentMethod)
    }
  }
}
</script>
