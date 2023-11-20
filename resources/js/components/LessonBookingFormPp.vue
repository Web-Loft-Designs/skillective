<template>
  <div>
    <div id='paypal-button-container'>
      <div id='your-container-element' class='your-container-element'></div>
    </div>

    <div v-if='isHostedFieldsEligible' class='card_container'>
      <form id='card-form' @submit.prevent='submitCardForm'>
        <label for='card-number'>Card Number</label>
        <div id='card-number' class='card_field'></div>

        <div>
          <label for='expiration-date'>Expiration Date</label>
          <div id='expiration-date' class='card_field'></div>
        </div>

        <div>
          <label for='cvv'>CVV</label>
          <div id='cvv' class='card_field'></div>
        </div>

        <label for='card-holder-name'>Name on Card</label>
        <input
          id='card-holder-name'
          v-model='cardHolderName'
          autocomplete='off'
          name='card-holder-name'
          placeholder='card holder name'
          type='text'
        />

        <!-- Other billing address fields... -->

        <br/><br/>
        <button class='btn' type='submit'>Pay</button>
      </form>
    </div>
  </div>
</template>

<script>
import { loadScript } from '@paypal/paypal-js'

let paypal
export default {
  name: 'LessonBookingFormPp',
  data() {
    return {
      isHostedFieldsEligible: false,
      orderId: null,
      cardHolderName: '',
      vaultSetupToken: null
    }
  },
  props: [
    'ppClientToken', 'total'
  ],
  mounted() {
    console.log(this.ppClientToken, 'ppClientToken')
    console.log(this.total, 'total')
    this.initializePayPal()
  },
  methods: {
    async initializePayPal() {
      let paypal;

      try {
        paypal = await loadScript({ clientId: this.ppClientToken, components: ['buttons','hosted-fields','funding-eligibility'] });
      } catch (error) {
        console.error("failed to load the PayPal JS SDK script", error);
      }

      if (paypal) {
        try {
          await paypal.Buttons().render("#your-container-element");
        } catch (error) {
          console.error("failed to render the PayPal Buttons", error);
        }
      }
      console.log(1)
      console.log(paypal)
      console.log(paypal.HostedFields.isEligible(), 'paypal.HostedFields.isEligible()')
      console.log(paypal.HostedFields.render, 'paypal.HostedFields.render()')
        if (paypal.HostedFields.isEligible()) {
            this.isHostedFieldsEligible = true;

            paypal.HostedFields.render({
                createOrder: () => {
                    return this.createOrder();
                },
                styles: {
                    '.valid': {
                        color: 'green',
                    },
                    '.invalid': {
                        color: 'red',
                    },
                },
                fields: {
                    number: {
                        selector: '#card-number',
                        placeholder: '4111 1111 1111 1111',
                    },
                    cvv: {
                        selector: '#cvv',
                        placeholder: '123',
                    },
                    expirationDate: {
                        selector: '#expiration-date',
                        placeholder: 'MM/YY',
                    },
                },
            }).then((cardFields) => {
                this.cardFields = cardFields;
            });
        }
    },
    createOrder() {
        // Implement logic to create an order and return the order ID
        return fetch('/api/orders', {
            method: 'post',
            body: JSON.stringify({
                cart: [
                    {
                        sku: 'YOUR_PRODUCT_STOCK_KEEPING_UNIT',
                        quantity: 'YOUR_PRODUCT_QUANTITY',
                    },
                ],
            }),
        })
            .then((res) => res.json())
            .then((order) => {
                this.orderId = order.id;
                return order.id;
            });
    },
    captureOrder(orderId) {
        // Implement logic to capture the order
        return fetch(`/api/orders/${orderId}/capture`, {
            method: 'post',
        })
            .then((res) => res.json())
            .then((orderData) => {
                // Handle capture response
            });
    },
    submitCardForm() {
        this.cardFields
            .submit({
                cardholderName: this.cardHolderName,
                // Other billing address fields...
            })
            .then(() => {
                this.captureOrder(this.orderId);
            })
            .catch((err) => {
                console.error(err);
                // Handle errors
            });
    },

    handleApprove(vaultSetupToken) {
        // Викликається при схваленні оплати
        this.vaultSetupToken = vaultSetupToken;
        // Додайте вашу логіку для використання vaultSetupToken
    },
  }
}
</script>

<style>
/* Add your styles here */
</style>
