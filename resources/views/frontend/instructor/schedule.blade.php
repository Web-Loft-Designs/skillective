@extends('layouts.app-frontend')

@section('content')
    <?php
    $dashboardPage = getCurrentPage('instructor/dashboard');
    $booking_fees_description = getCurrentPageMetaValue($dashboardPage, 'booking_fees_description');
    ?>
<div class="schedule-page instructor-schedule-page">
    <div class="container">
        <div class="row">
            <colendar-add-lesson
                    :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"
                    v-bind:user-genres="{{  json_encode($userGenres) }}"
                    v-bind:site-genres="{{  json_encode($siteGenres) }}"
                    :available-notification-methods="{{  json_encode($availableNotificationMethods) }}"
                    :instructor-id="{{ Auth::user()->id }}"
            ></colendar-add-lesson>
        </div>
    </div>
</div>
@endsection
