
{{--<?php--}}
{{--//dd($ppClientToken, $ppMerchantId, ppBnCode);--}}
{{--?>--}}

{{--
client-id - id нашого додатку
merchant-id - id продавця
--}}

<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields,funding-eligibility,applepay
    &client-id={{$ppClientToken}}
    &merchant-id=KSLFGLWLXG79G">
</script>
