
<?php
//dd($ppClientToken, $ppMerchantId, ppBnCode);
//?>

<!-- Load the client component. -->
<script src="https://js.braintreegateway.com/web/3.57.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.57.0/js/hosted-fields.min.js"></script>
<!-- Load PayPal's checkout.js Library. -->
<script src="https://www.paypalobjects.com/api/checkout.js" data-version-4 log-level="warn"></script>
<script src="https://js.braintreegateway.com/web/3.57.0/js/data-collector.min.js"></script>

<script src="https://js.braintreegateway.com/web/dropin/1.22.0/js/dropin.min.js"></script>

<!-- Load the PayPal Checkout component. -->
<script src="https://js.braintreegateway.com/web/3.57.0/js/paypal-checkout.min.js"></script>
<!-- Load the Venmo component. -->
<script src="https://js.braintreegateway.com/web/3.57.0/js/venmo.min.js"></script>

{{--
client-id - id нашого додатку
merchant-id - id продавця
--}}

{{--<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields,funding-eligibility,applepay--}}
{{--        &client-id={{$ppClientToken}}--}}
{{--        &merchant-id={{$ppMerchantId}}--}}
{{--        &currency=USD--}}
{{--        &enable-funding=venmo"--}}
{{--        data-partner-attribution-id="<BN_CODE>"--}}
{{--        data-client-token="<CLIENT_TOKEN>"></script>--}}

{{--<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields,funding-eligibility,applepay--}}
{{--        &client-id={{$ppClientToken}}--}}
{{--        &merchant-id={{$ppMerchantId}}--}}
{{--        &currency=USD">--}}
{{--</script>--}}
