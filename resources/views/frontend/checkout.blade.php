@extends('layouts.app-frontend')
@section('content')
{{--    <?php--}}
{{--    dd($total);--}}
{{--    ?>--}}
    <div class="dashboard-cart-page">
        <div class="container dashboard-cart">
            <div class="row">

                <div class="col-12 register-banner-wrapper" style="box-shadow: none;">
                    <checkout>
                        {{--          Braintree              --}}
{{--                        <lesson-booking-form--}}
{{--                            :lessons-count="{{ json_encode($lessonsCount) }}"--}}
{{--                            :us-states="{{ json_encode($usStates) }}"--}}
{{--                            :user="{{ json_encode($user) }}"--}}
{{--                            :user-payment-methods="{{ json_encode($userPaymentMethods) }}"--}}
{{--                            :client-token="'{{ $clientToken }}'"--}}
{{--                            :payment-environment="'{{ $paymentEnvironment }}'"--}}
{{--                            :confirmation-text="'{{ str_replace(["\r", "\n"], '', $settings['booking_confirmation_text']) }}'"--}}
{{--                            :ppAccessToken = "'{{$ppAccessToken}}'">--}}
{{--                        </lesson-booking-form>--}}
                        {{--          Paypal             --}}
                        <lesson-booking-form-pp
                            :clientId="'your-client-id'"
                            :merchantId="'your-merchant-id'"
                            :currency="'USD'"
                            :cartItems="[
                            { sku: 'product-1', quantity: 2 },
                            { sku: 'product-2', quantity: 1 }
                            ]">

                        </lesson-booking-form-pp>

                    </checkout>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{{--    @include('frontend.partials.payment-processing-scripts') --}}{{--          Braintree              --}}
    @include('frontend.partials.pp-payment-processing-scripts') {{--          Paypal             --}}
@endsection
