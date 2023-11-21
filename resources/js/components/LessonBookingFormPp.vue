<template>
    <div>
        <div id="paypal-button-container"></div>
        <div class="card_container">
            <form id="card-form">
                <label for="card-number">Card Number</label>
                <div ref="cardNumber" class="card_field"></div>
                <div>
                    <label for="expiration-date">Expiration Date</label>
                    <div ref="expirationDate" class="card_field"></div>
                </div>
                <div>
                    <label for="cvv">CVV</label>
                    <div ref="cvv" class="card_field"></div>
                </div>
                <label for="card-holder-name">Name on Card</label>
                <input type="text" id="card-holder-name" ref="cardHolderName" autocomplete="off" placeholder="card holder name"/>
                <div>
                    <label for="card-billing-address-street">Billing Address</label>
                    <input type="text" id="card-billing-address-street" ref="billingAddressStreet" autocomplete="off" placeholder="street address"/>
                </div>
                <div>
                    <label for="card-billing-address-unit">&nbsp;</label>
                    <input type="text" id="card-billing-address-unit" ref="billingAddressUnit" autocomplete="off" placeholder="unit"/>
                </div>
                <div>
                    <input type="text" id="card-billing-address-city" ref="billingAddressCity" autocomplete="off" placeholder="city"/>
                </div>
                <div>
                    <input type="text" id="card-billing-address-state" ref="billingAddressState" autocomplete="off" placeholder="state"/>
                </div>
                <div>
                    <input type="text" id="card-billing-address-zip" ref="billingAddressZip" autocomplete="off" placeholder="zip / postal code"/>
                </div>
                <div>
                    <input type="text" id="card-billing-address-country" ref="billingAddressCountry" autocomplete="off" placeholder="country code" />
                </div>
                <br/><br/>
                <button type="submit" class="btn">Pay</button>
            </form>
        </div>
    </div>
</template>

<script>
import { loadScript } from "@paypal/paypal-js";

export default {
    props: {
        total: Object,
        ppClientToken: String,
    },

    data() {
        return {
            orderId: null,
            paypal: null,
        };
    },
    mounted() {
        this.initializePaypal();
    },
    methods: {
        async initializePaypal() {
            try {
                this.paypal = await loadScript({
                    clientId: this.ppClientToken,
                    buyerCountry: 'US',
                    locale: "en_US",
                    components: ["buttons", "card-fields"],
                    merchantId: "KSLFGLWLXG79G",
                    vault:true,
                    currency: "USD"
                });
                this.setupPaypalButtons();
            } catch (error) {
                console.error("Failed to load the PayPal JS SDK script", error);
            }
        },

        setupPaypalButtons() {

            this.paypal.Buttons({
                createOrder: () => {
                    // ... (Your existing createOrder logic)
                },
                onApprove: (data) => {
                    this.captureOrder(data.orderID);
                },
            }).render("#paypal-button-container");

            const cardFields = paypal.CardFields({
                createVaultSetupToken: async () => {
                    // The merchant calls their server API to generate a setup token
                    // and return it here as a string
                    const result = await fetch("https://example.com/api/vault/token", {
                        method: "POST"
                    });
                    const { id } = await result.json();
                    return id;
                },
                onApprove: async (data) => {
                    return fetch(`https://example.com/api/vault/${data.vaultSetupToken}`, {
                        method: "POST"
                    });
                },
                onError: (error) => console.error('Something went wrong:', error)
            })
        },
        captureOrder(orderId) {
            // ... (Your existing captureOrder logic)
        },
    },
};
</script>

<style>
/* Add your styles if needed */
</style>
