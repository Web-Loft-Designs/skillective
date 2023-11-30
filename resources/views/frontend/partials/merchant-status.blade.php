@if (Auth::user() && $loggedUserRole==\App\Models\User::ROLE_INSTRUCTOR)
    @if (Auth::user()->bt_submerchant_id==null)
        <div class="flash-message" id="onboard-message">
            <div class="alert alert-danger">
                @if (isset($settings['onboard_as_merchant']) && $settings['onboard_as_merchant'])
                    {!! $settings['onboard_as_merchant'] !!}
                @else
                    {{ 'You have to onboard as a merchant to sell your lessons.' }}
                @endif
                <a href="{{ route('profile.edit') }}#merchant-account-trigger">Onboard now?</a>
            </div>
        </div>
    @elseif(strtolower(Auth::user()->bt_submerchant_status)==Braintree\MerchantAccount::STATUS_PENDING)
        <div class="flash-message">
            <div class="alert alert-danger">@if (isset($settings['merchant_account_on_review']) && $settings['merchant_account_on_review'])
                    {!! $settings['merchant_account_on_review'] !!}
                @else
                    {{ 'Your merchant account is being verified. This message will disappear once it is approved and then funds may be disbursed to your account' }}
                @endif</div>
        </div>
    @elseif(strtolower(Auth::user()->bt_submerchant_status)==Braintree\MerchantAccount::STATUS_SUSPENDED)
        <div class="flash-message">
            <div class="alert alert-danger">
                @if (isset($settings['merchant_account_declined']) && $settings['merchant_account_declined'])
                    {!! $settings['merchant_account_declined'] !!}
                @else
                    {{ 'Your merchant account has been declined.' }}
                @endif
                @if(Auth::user()->bt_submerchant_status_reason)
                    Reason: {{ Auth::user()->bt_submerchant_status_reason }}.
                @endif
            </div>
        </div>
    @endif
@endif
