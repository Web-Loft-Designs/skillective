@extends('layouts.app-frontend')

@section('content')
    <?php
	$loggedUserIsAdmin = ($loggedUserRole==\App\Models\User::ROLE_ADMIN);
	$pageMeta = $currentPage->getAllMeta();
	$instructor_notifications_block_description= isset($pageMeta['instructor_notifications_block_description']) ? $pageMeta['instructor_notifications_block_description'] : '';
    ?>
    <div class="profile-section-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12" sticky-container>
                    <div class="sticky-nav"  v-sticky="true" sticky-offset="{top: 100, bottom: 20}">
                        <h2>Settings</h2>
                        <ul id="top-menu">
                            @if( !$loggedUserIsAdmin )
                                <li class="active"><a class="scroll-to" href="#social-media-account-trigger">Social Media</a></li>
                            @endif
                            <li><a class="scroll-to" href="#data-update-trigger">Profile</a></li>
                            <li><a class="scroll-to" href="#notifications-update-trigger">Notifications</a></li>
                            <li><a class="scroll-to" href="#merchant-account-trigger">Payouts Account</a></li>
                            <li><a class="scroll-to" href="#password-change-trigger">Change Password</a></li>
                            @if( $loggedUserIsAdmin )
                                <li><a class="scroll-to" href="#invitations-limit">Invitations limit</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        @if( !$loggedUserIsAdmin )
                            <div class="form-wrap" id="social-media-account-trigger">
                                @include('frontend.partials.social-accounts-form')
                            </div>
                        @endif
                        {{-- fake emails generated when registration via instagram --}}
                        <div class="form-wrap" id="data-update-trigger">
                            <profile-data-update-form
                                    v-bind:us-states="{{  json_encode($usStates) }}"
                                    v-bind:categorized-genres="{{  json_encode($categorizedGenres) }}"
                                    v-bind:user-profile-data="{{  json_encode($userProfileData) }}"
                                    @if( $loggedUserIsAdmin )v-bind:is-admin-form="true" @endif
                            ></profile-data-update-form>
                        </div>
                        @if( !Auth::user()->hasFakeEmail() )
                            <div class="form-wrap" id="notifications-update-trigger">
                                <profile-notifications-update-form
                                        v-bind:available-notification-methods="{{  json_encode($availableNotificationMethods) }}"
                                        v-bind:user-notification-methods="{{  json_encode($userProfileData['profile']['notification_methods']) }}"
                                        v-bind:description="'{{ addslashes(preg_replace( "/\r|\n/", "", $instructor_notifications_block_description )) }}'"
                                        @if( $loggedUserIsAdmin )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
                                ></profile-notifications-update-form>
                            </div>
                                    {{-- PayPal  $ppMerchantAccount this is array --}}
                                <div class="form-wrap" id="paypal-account-trigger">
                                    <div dir="ltr" style="text-align: left;" trbidi="on">
                                        <p class="login-box-msg">PayPal</p>
                                        <p class="login-box">Status: {{$ppMerchantAccount['status']}}</p>
                                        <script>
                                            (function(d, s, id) {
                                                let js, ref = d.getElementsByTagName[s](0);
                                                if (!d.getElementById(id)) {
                                                    js = d.createElement(s);
                                                    js.id = id;
                                                    js.async = true;
                                                    js.src = "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
                                                    ref.parentNode.insertBefore(js, ref);
                                                }
                                            }(document, "script", "paypal-js"));
                                        </script>

                                        @isset($ppMerchantAccount['actionUrl'])
                                            <button  class="btn btn-primary">
                                                <a data-paypal-button="true"
                                                   href="{{$ppMerchantAccount['actionUrl']}}&displayMode=minibrowser"
                                                   target="PPFrame">
                                                    Connect with PayPal
                                                </a>
                                            </button>
                                        @endisset

                                    </div>
                                </div>
{{--                            <div class="form-wrap" id="merchant-account-trigger">--}}
{{--                                <braintree-merchant-form--}}
{{--                                        :saved-merchant-account-details="{{ json_encode($savedMerchantAccountDetails) }}"--}}
{{--                                        v-bind:us-states="{{  json_encode($usStates) }}"--}}
{{--                                        v-bind:is-admin-form="{{ $loggedUserIsAdmin ? 'true' : 'false' }}"--}}
{{--                                ></braintree-merchant-form>--}}
{{--                            </div>--}}
                            <div class="form-wrap" id="password-change-trigger">
                                <profile-password-change
                                        @if( $loggedUserIsAdmin )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
                                        @if( $userProfileData['noPassword']==true )v-bind:no-password="true" @endif
                                ></profile-password-change>
                            </div>
                            @if( $loggedUserIsAdmin )
                                <div class="form-wrap" id="invitations-limit">
                                    <profile-invitations-limit
                                            :default-max-allowed-instructor-invites="{{ (int)$defaultMaxAllowedInstructorInvites }}"
                                            v-bind:user-id="{{ $userProfileData['id'] }}"
                                            :total-sent="{{ (int)$countInstructorInvitationsSent }}"
                                            :total-applied="{{ (int)$countInstructorInvitationsApplied }}"
                                            @if($userProfileData['profile']['max_allowed_instructor_invites']!=null) v-bind:current-value="{{ $userProfileData['profile']['max_allowed_instructor_invites'] }}" @endif
                                    ></profile-invitations-limit>
                                </div>
                            @endif
                        @endif
                        {{--<profile-simple-gallery v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}"></profile-simple-gallery>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageSpecificHeadJS')
    <script src="https://js.braintreegateway.com/web/dropin/1.20.4/js/dropin.min.js"></script>
@endsection
