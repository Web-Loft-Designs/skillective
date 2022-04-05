@extends('layouts.app-frontend')

@section('content')
    <?php
    $dashboardPage = getCurrentPage('instructor/dashboard');
    $booking_fees_description = getCurrentPageMetaValue($dashboardPage, 'booking_fees_description');
    ?>

    <div class="b--table-page">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    <request-lesson-form :show-create-btn="false" :site-genres="{{ json_encode($siteGenres) }}"
                        :logged-in-student="false"
                        :booking-fees-description="'{{ isset($booking_fees_description) ? preg_replace('/\n|\r/', '', addslashes($booking_fees_description)) : '' }}'">
                    </request-lesson-form>

                    <instructor-bookings-list :bookings="{{ json_encode($bookings) }}"
                        v-bind:bookings-meta="{{ isset($bookings['meta']) ? json_encode($bookings['meta']) : '[]' }}">
                    </instructor-bookings-list>


                    <send-notification-form :is-student="false"
                        :available-notification-methods="{{ json_encode($availableNotificationMethods) }}">
                    </send-notification-form>
                </div>
            </div>
        </div>
    </div>
@endsection
