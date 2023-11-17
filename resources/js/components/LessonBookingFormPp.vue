<template>
    <div>
        <div id="paypal-button-container"></div>

        <div v-if="isHostedFieldsEligible" class="card_container">
            <form id="card-form" @submit.prevent="submitCardForm">
                <label for="card-number">Card Number</label>
                <div id="card-number" class="card_field"></div>

                <div>
                    <label for="expiration-date">Expiration Date</label>
                    <div id="expiration-date" class="card_field"></div>
                </div>

                <div>
                    <label for="cvv">CVV</label>
                    <div id="cvv" class="card_field"></div>
                </div>

                <label for="card-holder-name">Name on Card</label>
                <input type="text" v-model="cardHolderName" id="card-holder-name" name="card-holder-name" autocomplete="off" placeholder="card holder name" />

                <!-- Other billing address fields... -->

                <br /><br />
                <button type="submit" class="btn">Pay</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isHostedFieldsEligible: false,
            orderId: null,
            cardHolderName: '',
            vaultSetupToken: null,
        };
    },
    mounted() {
        this.initializePayPal();
    },
    methods: {
        initializePayPal() {
            paypal
                .Buttons({
                    createOrder: () => {
                        return this.createOrder();
                    },
                    onApprove: (data) => {
                        return this.captureOrder(data.orderID);
                    },
                })
                .render('#paypal-button-container');

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
    },
};
</script>

<style>
/* Add your styles here */
</style>
