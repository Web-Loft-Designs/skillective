@extends('layouts.app-frontend')
@section('content')
    <div class="dashboard-cart-page">
        <div class="container dashboard-cart">
            <div class="row">
                <div class="col-12 register-banner-wrapper" style="box-shadow: none;">
                    <checkout>
                        <lesson-booking-form-pp
                        :total="{{json_encode($total)}}"
                        :pp-client-token="'{{$ppClientToken}}'"
                        :user-payment-methods="{{ json_encode($ppUserPaymentMethods) }}"
                        :user="{{ json_encode($user) }}"
                        :confirmation-text="'{{ str_replace(["\r", "\n"], '', $settings['booking_confirmation_text']) }}'"
                        >
                        </lesson-booking-form-pp>
                    </checkout>
                </div>
            </div>
        </div>
    </div>
@endsection

