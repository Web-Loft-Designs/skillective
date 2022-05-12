@extends('layouts.app-frontend')

@section('content')

<?php
$pageMeta = $currentPage->getAllMeta();

$invitation_form_title= isset($pageMeta['invitation_form_title']) ? $pageMeta['invitation_form_title'] : '';
$invitation_form_description= isset($pageMeta['invitation_form_description']) ? $pageMeta['invitation_form_description'] : '';
$booking_fees_description= isset($pageMeta['booking_fees_description']) ? preg_replace('/\n|\r/', '', $pageMeta['booking_fees_description']) : '';

?>

    <div class="dashboard-page">
        <div class="container">
            <div class="row">

                @if (isset($upcomingLesson))
                    <div class="col-12" id="upcoming-lesson-notification">
                        <div class="top-alert">
                            <div class="avatar-stack">
                                <?php $countToShow = 7 ?>
                                @foreach ($upcomingLesson['students'] as $i=>$student)
                                    @if ($i<$countToShow)
                                        <span>
                                            <img src="{{ $student['profile']['image'] }}" alt="{{ addslashes($student['full_name']) }}" title="{{ addslashes($student['full_name']) }}">
                                        </span>
                                        @if ( ($i+1)==($countToShow) && ($totalCountStudents = count($upcomingLesson['students']))>$countToShow )
                                            <span><img src="{{ asset('images/ava_1.png') }}" alt=""><span>{{ $totalCountStudents-$i-1 }}+</span></span>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <p>Next <strong>{{ $upcomingLesson['genre']['title'] }}</strong> @if($upcomingLesson['lesson_type']=='virtual') virtual @endif lesson starts at {{ \Carbon\Carbon::createFromTimeString($upcomingLesson['start'])->format('h:i A') . ' ' . $upcomingLesson['timezone_id'] }} @if($upcomingLesson['lesson_type']!='virtual') at <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($upcomingLesson['location']) }}" target="_blank">{{ $upcomingLesson['location'] }}</a> @endif </p>
                            <span onclick="hideUpcomingLessonNotification({{ $upcomingLesson['id'] }})" class="close-it"></span>
                        </div>
                    </div>
                @endif

                <div class="col-lg-5 col-md-6 col-12">
                    <chart-dashboard
                            :initial-goal-value="{{ Auth::user()->profile->goal_value }}"
                            :initial-goal-color="'{{ Auth::user()->profile->goal_color }}'"
                            :date-chart="null"
                            :is-dashboard="true"
                            :year-of-registration="{{ Auth::user()->created_at->format('Y') }}"
                    ></chart-dashboard>
                </div>
                <div class="col-lg-7 col-md-6 col-12">
                    <colendar-add-lesson
                            :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"
                            :instructor-id="{{ Auth::user()->id }}"
                            v-bind:user-genres="{{  json_encode($userGenres) }}"
                            v-bind:site-genres="{{  json_encode($siteGenres) }}"
                    />
                </div>

                <div class="col-12">
                    <request-lesson-form
                            :show-create-btn="false"
                            :site-genres="{{  json_encode($siteGenres) }}"
                            :logged-in-student="false"
                            :booking-fees-description="'{{ isset($booking_fees_description) ? preg_replace('/\n|\r/', '', addslashes($booking_fees_description)) : '' }}'"
                    ></request-lesson-form>
                    <instructor-dashboard-bookings-clients :clients="{{ json_encode($clients['data']) }}" :bookings="{{ json_encode($bookings) }}" :bookings-meta="{{ isset($bookings['meta'])?json_encode($bookings['meta']):'[]' }}"></instructor-dashboard-bookings-clients>

                    <send-notification-form :is-student="false" v-bind:available-notification-methods="{{  json_encode($availableNotificationMethods) }}"></send-notification-form>
                </div>

                <div class="col-12">
                    <div class="dashboard-gallery">
                        <dashboard-media-gallery v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}"></dashboard-media-gallery>
                        <a href="{{ route('instructor.gallery') }}" class="btn btn-secondary btn-block">View Gallery</a>
                    </div>
                </div>

                <div class="col-12">
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
