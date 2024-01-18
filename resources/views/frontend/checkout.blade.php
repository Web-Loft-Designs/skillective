@extends('layouts.app-frontend')

@php
 $ppUserPaymentMethods ? $methods = $ppUserPaymentMethods : $methods = (object)[];
@endphp

@section('content')
    <div class="dashboard-cart-page">
        <div class="container dashboard-cart">
            <div class="row">
                <div class="col-12 register-banner-wrapper" style="box-shadow: none;">
                    <checkout>
                        <lesson-booking-form-pp
                        :total="{{json_encode($total)}}"
                        :client-id="'{{$ppClientToken}}'"
                        :user-payment-methods="{{ json_encode($methods) }}"
                        :user="{{ json_encode($user) }}"
                        :confirmation-text="'{{ str_replace(["\r", "\n"], '', $settings['booking_confirmation_text']) }}'"
                        :bn-code="'{{$bnCode}}'"
                        :data-user-id-token="{{ json_encode($dataUserIdToken) }}"
                        :merchant-ids="{{ json_encode($merchantIds) }}"
                        >
                        </lesson-booking-form-pp>
                    </checkout>
                </div>
            </div>
        </div>
    </div>
@endsection

