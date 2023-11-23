<template>
    <div>

        <div id="paypal-buttons-container"></div>
        <!-- Advanced credit and debit card payments form -->
        <div class='card_container'>
            <div id='card-number'></div>
            <div id='expiration-date'></div>
            <div id='cvv'></div>
            <div id='card-holder-name'></div>
            <label>
                <input type='checkbox' id='vault' name='vault' /> Vault
            </label>
            <br><br>
            <button value='submit' id='submit' class='btn'>Pay</button>
        </div>
    </div>
</template>

<script>
import { loadScript } from "@paypal/paypal-js";
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from "../mixins/skillectiveHelper";
export default {
    props: {
        total: Object,
        ppClientToken: String,
    },
    mixins: [siteAPI, skillectiveHelper],
    data() {
        return {
            order: null,
            paypal: null,
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
                lesson_type: '',
                order: ''
            },
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
                    components: ["buttons","funding-eligibility","marks","card-fields"],
                    currency: "USD"
                });
                // console.log(this.paypal.cardFields.isEligible(), 'cardFields.isEligible()')
                // this.setupPaypalButtons();
                this.initPaymentMethod();
            } catch (error) {
                console.error("Failed to load the PayPal JS SDK script", error);
            }
        },


        initPaymentMethod() {
            console.log(this.paypal.isFundingEligible(this.paypal.FUNDING.PAYPAL), " is elig PAYPAL")
            console.log(this.paypal.isFundingEligible(this.paypal.FUNDING.CARD), " is elig CARD")
            console.log(this.paypal.isFundingEligible(this.paypal.FUNDING.VENMO), " is elig VENMO")
            console.log(this.paypal.isFundingEligible(this.paypal.FUNDING.CREDIT), " is elig CREDIT")

            // Loop over each payment method
            // this.paypal.getFundingSources().forEach((fundingSource) => {
            //     // // Initialize the buttons
            //     const button = this.paypal.Buttons({
            //         fundingSource: fundingSource,
            //     });
            //     // Check if the button is eligible
            //     if (button.isEligible()) {
            //         // Render the standalone button for that payment method
            //         button.render('#paypal-buttons-container')
            //     }
            //
            //
            //
            // })

        }


        //
        // setupPaypalButtons() {
        //
        //     // this.paypal.Buttons({
        //     //     createVaultSetupToken: async () => {
        //     //         // The merchant calls their server API to generate a setup token
        //     //         // and return it here as a string
        //     //         const result = await fetch("api/cart/vault-setup-token", {
        //     //             method: "GET",
        //     //         })
        //     //         return result.vaultSetupToken
        //     //     },
        //     //     onApprove: async ({ vaultSetupToken }) => {
        //     //         return fetch("example.com/create/payment/token", { body: JSON.stringify({ vaultSetupToken }) })
        //     //     },
        //     //     onError: (error) => {
        //     //         console.log("An error occurred: ", error)
        //     //     }
        //     // }).render("#paypal-buttons-container");
        //
        //     const cardFields = paypal.CardFields({
        //         createVaultSetupToken: async () => {
        //             // отримати vaultSetupToken з нашого сервера
        //             const result = await fetch("api/cart/vault-setup-token", {
        //                 method: "GET"
        //             });
        //
        //             const { vaultSetupToken } = await result.json();
        //             return vaultSetupToken;
        //         },
        //         onApprove: async (data) => {
        //             // запуск процес оплати
        //             // payment_method_token
        //             this.fields.payment_method_nonce = data.vaultSetupToken;
        //             this.fields.order = this.total;
        //             await this.apiPost('/api/cart/checkout', {
        //                 ...this.fields,
        //             })
        //
        //         },
        //         onError: (error) => console.error('Something went wrong:', error)
        //     })
        //
        //
        //     // Check eligibility and display advanced credit and debit card payments
        //
        //     console.log(cardFields.isEligible(), 'isEligible()')
        //     if (cardFields.isEligible()) {
        //         cardFields.NameField().render("#card-holder-name");
        //         cardFields.NumberField().render("#card-number");
        //         cardFields.ExpiryField().render("#expiration-date");
        //         cardFields.CVVField().render("#cvv");
        //     } else {
        //         // Handle the workflow when credit and debit cards are not available
        //     }
        //     const submitButton = document.getElementById("submit"); submitButton.addEventListener("click", () => {
        //         cardFields
        //             .submit()
        //             .then(() => {
        //                 console.log("submit was successful");
        //             })
        //             .catch((error) => {
        //                 console.error("submit erred:", error);
        //             });
        //     });
        // },



    },
};
</script>

<style>
/* Add your styles if needed */
</style>
