@extends('layouts.app-frontend')
@section('content')
    <div class="payouts-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="page-title">Payouts</h2>
                    <div class="payouts-top">
                        <div>
                            <p>Total Payout YTD</p>
                            <p><strong>${{ number_format($totalAmountInEscrow, 2, '.', ',') }}</strong></p>
                        </div>
                            <div class="card-payouts">
                                @if($savedMerchantAccountDetails)
                                <div>
                                    {{--<img src="{{ asset('images/paypal.svg') }}" alt="">--}}
                                    {{--<a href="mailto:whatever@email.com">Whatever@email.com</a>--}}
                                    {{--<span>Added: 01 Mar 2018</span>--}}
                                    <span>Account Number: {{ $savedMerchantAccountDetails['funding_accountNumber'] }}</span>
                                    <span>Routing Number: {{ $savedMerchantAccountDetails['funding_routingNumber'] }}</span>
                                </div>
                                @endif
                                <div class="link-container"><a href="{{ route('profile.edit') }}#merchant-account-trigger"><img src="{{ asset('images/img.png') }}" alt="">Set Account</a></div>
                            </div>
                    </div>
                </div>
                <div class="col-12">
                    <instructor-payouts-list
                            :payouts="{{ json_encode($payouts['data']) }}"
                            :payouts-meta="{{ isset($payouts['meta'])?json_encode($payouts['meta']):'[]' }}"
                    ></instructor-payouts-list>
                </div>
            </div>
        </div>
    </div>
@endsection