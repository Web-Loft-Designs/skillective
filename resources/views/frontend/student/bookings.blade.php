@extends('layouts.app-frontend')

@section('content')
    <div class="table-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <request-lesson-form
                            :show-create-btn="false"
                            :site-genres="{{  json_encode($siteGenres) }}"
                            :logged-in-student="true"
                    ></request-lesson-form>

                    <send-notification-form :is-student="true" class="notification-form-simple" :mode="'simple'" v-bind:available-notification-methods="{{  json_encode($availableNotificationMethods) }}"></send-notification-form>

                    <student-bookings-list v-bind:bookings="{{ json_encode($bookings['data']) }}" v-bind:bookings-meta="{{ isset($bookings['meta'])?json_encode($bookings['meta']):'[]' }}"></student-bookings-list>
                </div>
            </div>
        </div>
    </div>
@endsection
