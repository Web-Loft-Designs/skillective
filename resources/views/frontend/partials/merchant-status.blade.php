@if (Auth::user() && $loggedUserRole==\App\Models\User::ROLE_INSTRUCTOR)
@php

@endphp

    @if (Auth::user()->pp_merchant_id === null)
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

    @elseif(Auth::user()->pp_account_status != "active")
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
