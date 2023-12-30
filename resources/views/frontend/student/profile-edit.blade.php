@extends('layouts.app-frontend')

@section('content')
	<?php
    $paymentMethods ? $methods = $paymentMethods : $methods = (object)[];
	$loggedUserIsAdmin = ($loggedUserRole==\App\Models\User::ROLE_ADMIN);
	$pageMeta = $currentPage->getAllMeta();
	$student_notifications_block_description= isset($pageMeta['student_notifications_block_description']) ? $pageMeta['student_notifications_block_description'] : '';
	$student_geolocation_block_title= isset($pageMeta['student_geolocation_block_title']) ? $pageMeta['student_geolocation_block_title'] : '';
	$student_geolocation_block_description= isset($pageMeta['student_geolocation_block_description']) ? $pageMeta['student_geolocation_block_description'] : '';
	?>
    <div class="profile-section-edit student-profile">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-12" sticky-container>
                    <div class="sticky-nav"  v-sticky="true" sticky-offset="{top: 100, bottom: 20}">
                        <h2>Settings</h2>
                        <ul id="top-menu">
                            @if( !$loggedUserIsAdmin )
                                <li class="active"><a class="scroll-to" href="#social-media-account-trigger">Social media accounts</a></li>
                            @endif
                            <li class="@if( $loggedUserIsAdmin ){{'active'}}@endif"><a class="scroll-to" href="#data-update-trigger">Profile Information</a></li>
                            <li><a class="scroll-to" href="#notifications-update-trigger">Notifications</a></li>
                            @if( Auth::user()->hasRole('Student') )
                                <li><a class="scroll-to" href="#payment-account-trigger">Payment Methods</a></li>
                            @endif
                            <li><a class="scroll-to" href="#password-change-trigger">Change Password</a></li>
                            @if( $loggedUserIsAdmin )
                                <li><a class="scroll-to" href="#invitations-limit">Invitations limit</a></li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="col-lg-10  col-md-9 col-12">
                    <div class="profile-info">
                        @if( !$loggedUserIsAdmin )
                            <div class="form-wrap" id="social-media-account-trigger">
                                @include('frontend.partials.social-accounts-form')
                            </div>
                        @endif
                        <div class="form-wrap" id="data-update-trigger">
                         <profile-data-update-form
                                    @if( Auth::user()->hasRole('Student') )v-bind:is-student="true" @endif
                                    v-bind:us-states="{{  json_encode($usStates) }}"
                                    v-bind:categorized-genres="{{  json_encode($categorizedGenres) }}"
                                    v-bind:user-profile-data="{{  json_encode($userProfileData) }}"
                                    @if( $loggedUserIsAdmin )v-bind:is-admin-form="true" @endif
                         ></profile-data-update-form>
                        </div>
                        <div class="form-wrap" id="notifications-update-trigger">
                            <profile-notifications-update-form
                                    v-bind:available-notification-methods="{{  json_encode($availableNotificationMethods) }}"
                                    v-bind:user-notification-methods="{{  json_encode($userProfileData['profile']['notification_methods']) }}"
                                    v-bind:description="'{{ addslashes(preg_replace( "/\r|\n/", "", $student_notifications_block_description)) }}'"
                                    @if( $loggedUserIsAdmin )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
                            ></profile-notifications-update-form>
                            <profile-geo-locations-form
                                    v-bind:us-states="{{  json_encode($usStates) }}"
                                    v-bind:available-limits="{{  json_encode($availableLimits) }}"
                                    v-bind:user-geo-locations="{{  json_encode($userGeoLocations) }}"
                                    v-bind:title="'{{ addslashes(preg_replace( "/\r|\n/", "", $student_geolocation_block_title)) }}'"
                                    v-bind:description="'{{ preg_replace( "/\r|\n/", "", $student_geolocation_block_description) }}'"
                                    @if( $loggedUserIsAdmin )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
                            ></profile-geo-locations-form>
                        </div>
                        @if( !$loggedUserIsAdmin )
                            <div class="form-wrap" id="payment-account-trigger">
                                <profile-payment-account-pp
                                        :client-id="'{{ $clientToken }}'"
                                        :user-payment-methods="{{ json_encode($methods) }}"
                                        :data-user-id-token="{{ json_encode($dataUserIdToken) }}"
                                >
                                </profile-payment-account-pp>
                            </div>
                        @endif
                         @if( !Auth::user()->hasFakeEmail() )
                            <div class="form-wrap" id="password-change-trigger">
                                <profile-password-change
                                        @if( $loggedUserIsAdmin )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
                                ></profile-password-change>
                            </div>
                        @endif
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
                    </div>


                </div>


            </div>
        </div>
    </div>
@endsection
