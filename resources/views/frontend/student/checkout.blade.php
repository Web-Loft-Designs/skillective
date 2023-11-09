@extends('layouts.app-frontend')
@section('content')
{{--  TODO надо удаляти   --}}
    <div class="dashboard-cart-page">
        <div class="container dashboard-cart">
            <div class="row">

                <div class="col-12 register-banner-wrapper" style="box-shadow: none;">
                    <p>Not found</p>
{{--                    <checkout :total="{{ json_encode($total) }}">--}}
{{--                        <lesson-booking-form--}}
{{--                            :lessons-count="{{ json_encode($lessonsCount) }}"--}}
{{--                            :us-states="{{ json_encode($usStates) }}"--}}
{{--                            :user="{{ json_encode($user) }}"--}}
{{--                            :user-payment-methods="{{ json_encode($userPaymentMethods) }}"--}}
{{--                            :client-token="'{{ $clientToken }}'" :payment-environment="'{{ $paymentEnvironment }}'"--}}
{{--                            :categorized-genres="{{ json_encode($categorizedGenres) }}"--}}
{{--                            :site-genres="{{ json_encode($siteGenres) }}"--}}
{{--                            :confirmation-text="'{{ str_replace(["\r", "\n"], '', $settings['booking_confirmation_text']) }}'">--}}
{{--                        </lesson-booking-form>--}}
{{--                    </checkout>--}}
                </div>

            </div>
        </div>
    </div>
@endsection

{{--@section('scripts')--}}
{{--    @include('frontend.partials.payment-processing-scripts')--}}
{{--@endsection--}}
