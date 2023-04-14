<template>
    <div id="password-payment-account">
        <form method="post" id="payment-method-form" @submit.prevent="onSubmit">

            <p class="login-box-msg">Payment Methods</p>
            <div class="form-group has-feedback mb-5" >
                <div class="radio-wrapper">
                    <label class="radio-item" for="isCard">
                        <input v-model="paymentMethod" name="payment_system" type="radio" id="isCard" value="CreditCard">
                        <span class="checkmark"></span>
                        Credit Card <img class="ml-2" src="/images/card-icon.png" alt="Credit Card">
                    </label>
                    <!--<label class="radio-item" for="paypal">-->
                        <!--<input v-model="paymentMethod" name="payment_system" type="radio" id="paypal" value="PayPalAccount">-->
                        <!--<span class="checkmark"></span>-->
                        <!--<img src="/images/payPal.svg" alt="Paypal">-->
                    <!--</label>-->
<!--                    <label class="radio-item" for="venmo">-->
<!--                        <input v-model="paymentMethod" name="payment_system" type="radio" id="venmo" value="VenmoAccount">-->
<!--                        <span class="checkmark"></span>-->
<!--                        <img src="/images/venmo.png" alt="Venmo">-->
<!--                    </label>-->
                </div>
            </div>

            <div class="d-flex flex-wrap" v-if="paymentMethod == 'CreditCard'">
                <div class="col-lg-6 col-12">
                    <div class="payment-option mt-5 pt-5 active">
                        <div>
                            <div class="payment-option-body d-flex flex-wrap">
                                <div class="form-group has-feedback">
                                    <label>Card number</label>
                                    <div v-if="(this.selectedPaymentMethodObj==null)" class="card-item" id="card-number"></div>
                                    <input v-if="this.selectedPaymentMethodObj!=null" type="text" class="form-control" disabled :value="getSavedCardNumberVal(this.selectedPaymentMethodObj.last4)" placeholder="____ ____ ____ ____">
                                </div>
                                <div class="form-group has-feedback">
                                    <label>Cardholder name</label>
                                    <input v-if="(this.selectedPaymentMethodObj==null)" type="text" class="form-control" v-model="fields.cardholderName" required name="cardholderName" value="" placeholder="Cardholder Name" id="cardholder-name">
                                    <input v-if="this.selectedPaymentMethodObj!=null" type="text" class="form-control" disabled :value="this.selectedPaymentMethodObj.cardholderName">
                                    <!--<span class="help-block" v-if="errors.cardholderName"><strong>{{ errors.cardholderName[0] }}</strong></span>-->
                                </div>
                                <div class="form-group w-33 has-feedback">
                                    <label>Expiry date</label>
                                    <div    v-if="(this.selectedPaymentMethodObj==null)" class="card-item" id="expiration-date"></div>
                                    <input  v-if="this.selectedPaymentMethodObj!=null" type="text" class="form-control" disabled :value="this.selectedPaymentMethodObj.expirationDate" placeholder="MM / YY">
                                </div>
                                <div v-if="(this.selectedPaymentMethodObj==null)" class="form-group w-33 has-feedback">
                                    <label>CVC/CVV</label>
                                    <div  class="card-item" id="cvv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button v-if="(this.selectedPaymentMethodObj==null)" type="submit" class="btn btn-primary btn-flat" id="payment-method-form-submit" disabled>Save</button>
                        <!--<button v-if="(this.selectedPaymentMethodObj!=null && this.selectedPaymentMethodObj.is_default==false)" type="button" class="btn btn-primary btn-flat" @click="setAsDefaultPaymentMethod()">Set as default method</button>-->
                        <button v-if="(this.selectedPaymentMethodObj!=null)" type="button" class="btn btn-primary btn-flat" @click="deletePaymentMethod()">Delete payment method</button>
                    </div>
                </div>
            </div>

            <!--<div class="d-flex flex-wrap" v-if="paymentMethod == 'PayPalAccount'">-->
                <!--<div v-if="(this.selectedPaymentMethodObj==null)">-->
                    <!--Connect PayPal Account <div id="paypal-button"></div>-->
                    <!--&lt;!&ndash;<div v-if="waitPaypalInitialization==true">Wait while PayPal initializes the button</div>&ndash;&gt;-->
                <!--</div>-->
                <!--<div class="form-group">-->
                    <!--&lt;!&ndash;<button v-if="(this.selectedPaymentMethodObj!=null && this.selectedPaymentMethodObj.is_default==false)" type="button" class="btn btn-primary btn-flat" @click="setAsDefaultPaymentMethod()">Set as default method</button>&ndash;&gt;-->
                    <!--<button v-if="(this.selectedPaymentMethodObj!=null)" type="button" class="btn btn-primary btn-flat" @click="deletePaymentMethod()">Delete payment method</button>-->
                <!--</div>-->
            <!--</div>-->

<!--            <div class="d-flex flex-wrap" v-if="paymentMethod == 'VenmoAccount'">-->
<!--                <div class="form-group w-50 has-feedback" v-if="(this.selectedPaymentMethodObj==null)">-->
<!--                    <div id="venmo-button" class="btn btn-block" style="background: rgb(61, 149, 206) url('/images/venmo_logo_white.png') repeat scroll 0% 0%;display: block;background-repeat: no-repeat;background-size: 110px 21px;background-position:center;" v-if="venmoNotSupported==false"></div>-->
<!--                    <div v-if="venmoNotSupported==true">Browser does not support Venmo</div>-->
<!--                </div>-->
<!--                <div class="form-group w-50" v-if="(this.selectedPaymentMethodObj!=null)">-->
<!--                    {{ this.selectedPaymentMethodObj.username[0].toUpperCase() + this.selectedPaymentMethodObj.username.substring(1) }} account connected-->
<!--                    <button type="button" class="btn btn-primary btn-flat" @click="deletePaymentMethod()">Delete payment method</button>-->
<!--                </div>-->
<!--            </div>-->


            <div v-if="errorText" class="has-error form-group">{{ errorText }}</div>
            <div v-if="successText" class="has-success form-group">{{ successText }}</div>
        </form>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import $ from 'jquery'

	export default {
		mixins : [siteAPI],
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
					cardholderName : ''
                },
                braintreeClientInstance : null,
				device_data : '',
				waitPaypalInitialization: false,
				venmoNotSupported : false
			}
		},
		methods: {
			setAsDefaultPaymentMethod(){
				this.apiPut('/api/student/payment-method/set-as-default/' + this.selectedPaymentMethodObj.token);
            },
			deletePaymentMethod(){
				this.apiDelete('/api/student/payment-method/' + this.selectedPaymentMethodObj.token);
            },
			getPaymentMethods(){
				this.apiGet('/api/student/payment-methods');
            },
            sendNonceToServer(nonce){
				this.apiPost(
					'/api/student/payment-method',
					{
						payment_method_nonce: nonce,
						device_data : this.device_data
					}
				);
            },
			onSubmit(){
				var vueComponent = this;
				if (this.selectedPaymentMethodObj==null) {
					this.braintreeClientInstance.tokenize( {
						cardholderName: this.fields.cardholderName
					}, function( tokenizeErr, payload ) {
						if ( tokenizeErr ) {
							//                        console.error(tokenizeErr);
							vueComponent.errorText = tokenizeErr.message;
							//                        vueComponent.errorText = 'Error: Can\'t process your data'
							return;
						} else
							vueComponent.errorText = '';
						if ( payload.nonce != undefined ) {
							vueComponent.sendNonceToServer(payload.nonce);
						} else {
							vueComponent.errorText = 'Can\'t process your data.';
						}
					} );
				}else{
					this.errorText = 'Can\'t create new payment method.';
                }
            },
			componentHandleGetResponse(responseData) {
				this.paymentMethods = responseData.data;
				this.setSelectedPaymentMethodObj(this.paymentMethod);
			},
			componentHandleDeleteResponse(responseData) {
				this.fields.cardholderName = '';
				this.getPaymentMethods();
			},
			componentHandlePutResponse(responseData) {
				this.getPaymentMethods();
			},
			componentHandlePostResponse(responseData) {
				if (this.paymentMethod == 'CreditCard') {
					this.braintreeClientInstance.teardown( function( teardownErr ) {
						if ( teardownErr ) {
							console.error( 'Could not tear down the Hosted Fields form!' );
						} else {
							console.info( 'Hosted Fields form has been torn down!' );
						}
					} );
				}

				if ( responseData.success ) {
					this.successText = 'Payment method saved';
				} else {
					this.errorText = 'Can\'t process your data.';
				}
				this.getPaymentMethods();
			},
            initBraintreeClient() {
				var vueComponent = this;
				if (this.selectedPaymentMethodObj==null){ // needed only for creating new method
					braintree.client.create({
						authorization: this.clientToken
					}, function (clientErr, clientInstance) {
						if (clientErr) {
							vueComponent.errorText = clientErr.message;
							return;
						}else
							vueComponent.errorText = '';

						if (vueComponent.paymentMethod == 'CreditCard'){
							// Create a hostedFields component to initialize the form
//							if (vueComponent.selectedPaymentMethodObj!=null){
//								vueComponent.fields.cardholderName = vueComponent.selectedPaymentMethodObj.cardholderName;
//							}

							braintree.hostedFields.create({
								client: clientInstance,
								styles: {  // https://developers.braintreepayments.com/guides/hosted-fields/styling/javascript/v3
									'input': {
										'font-size': '14px',
										'border' : '1px solid #ccc'
									},
									'input.invalid': {
										'color': 'red'
									},
									'input.valid': {
										'color': 'green'
									}
								},
								// Configure which fields in your card form will be generated by Hosted Fields instead
								fields: {
									number: {
										selector: '#card-number',
										placeholder: '____ ____ ____ ____'
									},
									cvv: {
										selector: '#cvv',
										placeholder: '•••',
									},
									expirationDate: {
										selector: '#expiration-date',
										placeholder: 'MM / YYYY',
//										prefill : (vueComponent.selectedPaymentMethodObj!=null) ? (vueComponent.selectedPaymentMethodObj.expirationDate) :''
									}
								}
							}, function (hostedFieldsErr, instance) {
								if (hostedFieldsErr) {
									vueComponent.errorText = hostedFieldsErr.message;
									console.error(hostedFieldsErr);
									return;
								}else
									vueComponent.errorText = '';
								document.querySelector('#payment-method-form-submit').removeAttribute('disabled');
								vueComponent.braintreeClientInstance = instance;
							});
						}else if (vueComponent.paymentMethod == 'PayPalAccount'){
							var forPayPal = true;
							vueComponent.collectDeviceDataForBraintree(clientInstance, forPayPal);

							vueComponent.braintreeClientInstance = clientInstance;
                            $('#paypal-button').html('');

							// Create a PayPal Checkout component.
							braintree.paypalCheckout.create({
								client: clientInstance
							}, function (paypalCheckoutErr, paypalCheckoutInstance) {

								// Stop if there was a problem creating PayPal Checkout.
								// This could happen if there was a network error or if it's incorrectly
								// configured.
								if (paypalCheckoutErr) {
									vueComponent.errorText = paypalCheckoutErr.message;
									console.error('Error connecting to PayPal:', paypalCheckoutErr);
									return;
								}
								vueComponent.waitPaypalInitialization = true;
								// Set up PayPal with the checkout.js library
								paypal.Button.render({
									env: vueComponent.paymentEnvironment, //'production' or 'sandbox'
									style: {
										label: 'paypal',
										size : 'large',
//										tagline : false,
										shape : 'rect'
									},
									payment: function () {
										return paypalCheckoutInstance.createPayment({
											flow: 'vault',
											billingAgreementDescription: '',
											enableShippingAddress: false
										});
									},
									onAuthorize: function (data, actions) {
										return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
											if ( payload.nonce != undefined ) {
												vueComponent.sendNonceToServer(payload.nonce);
											} else {
												vueComponent.errorText = 'Can\'t process your data.';
											}
										});
									},
									onCancel: function (data) {
										console.log('Adding payment method cancelled', JSON.stringify(data, 0, 2));
									},

									onError: function (err) {
										console.error('checkout.js error', err);
									}
								}, '#paypal-button').then(function () {
									// The PayPal button will be rendered in an html element with the id
									// `paypal-button`. This function will be called when the PayPal button
									// is set up and ready to be used.
									vueComponent.waitPaypalInitialization = false;
								});

							});
            }
// 						else if (vueComponent.paymentMethod == 'VenmoAccount'){
// 							var forPayPal = true;
// 							vueComponent.collectDeviceDataForBraintree(clientInstance, forPayPal);
//
// 							braintree.venmo.create({
// 								client: clientInstance,
// 								allowNewBrowserTab: false
// 							}, function (venmoErr, venmoInstance) {
//
// 								// Stop if there was a problem creating Venmo. This could happen if there was a network error or if it's incorrectly configured.
// 								if (venmoErr) {
// 									vueComponent.errorText = venmoErr.message;
// 									console.error('Error creating Venmo:', venmoErr);
// 									return;
// 								}
//
// 								// Verify browser support before proceeding.
// 								if (!venmoInstance.isBrowserSupported()) {
// 									vueComponent.venmoNotSupported = true;
// 									return;
// 								}
//
// 								var venmoButton = document.getElementById('venmo-button');
// 								venmoButton.style.display = 'block'; // Assumes that venmoButton is initially display: none.
// 								venmoButton.addEventListener('click', function () {
// 									venmoButton.disabled = true;
// 									venmoInstance.tokenize(function (tokenizeErr, payload) {
// 										venmoButton.removeAttribute('disabled');
//
// 										if (tokenizeErr) {
// 											if (tokenizeErr.code === 'VENMO_CANCELED') {
// 												vueComponent.errorText = 'App is not available or user aborted payment flow';
// 											} else if (tokenizeErr.code === 'VENMO_APP_CANCELED') {
// 												vueComponent.errorText = 'User canceled payment flow';
// 											} else {
// 												vueComponent.errorText = 'An error occurred:'+ tokenizeErr.message;
// 											}
// 										} else {
// 											// Send payload.nonce to your server.
// //											console.log('Got a payment method nonce:', payload.nonce);
// 											// Display the Venmo username in your checkout UI.
// 											console.log('Venmo user:', payload.details.username);
// 											if ( payload.nonce != undefined ) {
// 												vueComponent.sendNonceToServer(payload.nonce);
// 											} else {
// 												vueComponent.errorText = 'Can\'t process your data.';
// 											}
// 										}
// 									});
// 								});
//
// 								// Check if tokenization results already exist. This occurs when your checkout page is relaunched in a new tab. This step can be omitted if allowNewBrowserTab is false.
// //								if (venmoInstance.hasTokenizationResult()) {
// //									venmoInstance.tokenize(function (tokenizeErr, payload) {
// //										if (err) {
// //											if (err.code === 'VENMO_CANCELED') {
// //												vueComponent.errorText = 'App is not available or user aborted payment flow';
// //											} else if (err.code === 'VENMO_APP_CANCELED') {
// //												vueComponent.errorText = 'User canceled payment flow';
// //											} else {
// //												vueComponent.errorText = 'An error occurred:'+ err.message;
// //											}
// //										} else {
// //											// Send payload.nonce to your server.
// ////											console.log('Got a payment method nonce:', payload.nonce);
// //											// Display the Venmo username in your checkout UI.
// //											console.log('Venmo user:', payload.details.username);
// //											if ( payload.nonce != undefined ) {
// //												vueComponent.sendNonceToServer(payload.nonce);
// //											} else {
// //												vueComponent.errorText = 'Can\'t process your data.';
// //											}
// //										}
// //									});
// //									return;
// //								}
// 							});
// 						}
					});
                }
            },
            collectDeviceDataForBraintree(clientInstance, forPayPal){
				var vueComponent = this;
				braintree.dataCollector.create({
					client: clientInstance,
					paypal: forPayPal
				}, function (err, dataCollectorInstance) {
					if (err) {
						vueComponent.errorText = err.message;
                        dataCollectorInstance.teardown();
						return;
					}
					// At this point, you should access the dataCollectorInstance.deviceData value and provide it
					// to your server, e.g. by injecting it into your form as a hidden input.
					vueComponent.device_data = dataCollectorInstance.deviceData;
					dataCollectorInstance.teardown();
				});
            },
            setSelectedPaymentMethodObj(_paymentMethod) {
				if (this.paymentMethods[_paymentMethod] != undefined){
					this.selectedPaymentMethodObj = this.paymentMethods[_paymentMethod];
				}else
				    this.selectedPaymentMethodObj = null;
				this.initBraintreeClient();
            },
            getSavedCardNumberVal(_last4){
				return ('•••• •••• •••• ' + _last4);
            }
		},
        created: function(){
			this.paymentMethods = this.userPaymentMethods;
			this.setSelectedPaymentMethodObj(this.paymentMethod);
        },
		watch: {
			paymentMethod : function (newPaymentMethod, oldPaymentMethod) {
				this.venmoNotSupported = false;
				this.setSelectedPaymentMethodObj(newPaymentMethod);
			}
		}
	}
</script>
