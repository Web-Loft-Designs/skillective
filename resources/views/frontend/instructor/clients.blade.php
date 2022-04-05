@extends('layouts.app-frontend')

@section('content')
	<?php
	$pageMeta = getCurrentPage('instructor/dashboard')->getAllMeta();

	$invitation_form_title= isset($pageMeta['invitation_form_title']) ? $pageMeta['invitation_form_title'] : '';
	$invitation_form_description= isset($pageMeta['invitation_form_description']) ? $pageMeta['invitation_form_description'] : '';
	?>


    <div class="table-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <send-notification-form
                        :is-student="false"
                        :mode="'full'"
                        :available-notification-methods="{{  json_encode($availableNotificationMethods) }}"
                        :instructor-id="{{ Auth::user()->id }}"
                    ></send-notification-form>
                    <instructor-clients-list
                            v-bind:clients="{{ json_encode($clients['data']) }}"
                            v-bind:clients-meta="{{ isset($clients['meta'])?json_encode($clients['meta']):'[]' }}"
                            :count-invitations-sent="{{ Auth::user()->studentInvitations()->count() }}"
                            :max-invites-enabled="{{ $settings['max_allowed_student_invites'] }}"
                            :alternative-invite-input-placeholder="'Or invite by email address or phone number…'"
                    ></instructor-clients-list>


                    <div class="invite-block" style="background-image: url('{{asset('images/invide-bg.jpg')}}')">
                        <div>
                            @if($invitation_form_title)
                                <h2>{{ $invitation_form_title }}</h2>
                            @endif
                            @if($invitation_form_description)
                                <p>{{ $invitation_form_description }}</p>
                            @endif
                            <client-invitation-form :count-invitations-sent="{{ Auth::user()->studentInvitations()->count() }}" :max-invites-enabled="{{ $settings['max_allowed_student_invites'] }}"></client-invitation-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
