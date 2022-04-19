@extends('layouts.app-frontend')

@section('content')
    <div class="profile-section">
        <div class="inner">
            <div class="profile-info">
                <div id="card-personal-info" class="container">
                    <div class="aligner-top row">
                        <div class="card-section-info col-12 col-lg-5 padding-r-30">
                            <div class="">
                                @include('frontend.instructor.partials.profile-data-form')
                                {{-- admin-table lessons --}}
                                @if(!$authUserIsAdmin)
                                    <profile-upcoming-locations v-bind:upcoming-locations="{{ json_encode($userProfileData['upcoming_locations']) }}" v-bind:instructor-name="'{{ addslashes($userProfileData['full_name']) }}'"></profile-upcoming-locations>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-7 col-12 padding-l-30">
                            @if(Auth::user() && Auth::user()->id==$userProfileData['id'])
                                <colendar-add-lesson :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}" v-bind:user-genres="{{  json_encode($userProfileData['genres']) }}" v-bind:site-genres="{{  json_encode($siteGenres) }}" v-bind:lessons="{}" :instructor-id="{{ $userProfileData['id'] }}" :booking-fees-description="'{{ isset($booking_fees_description) ? preg_replace('/\n|\r/', '', addslashes($booking_fees_description)) : '' }}'"></colendar-add-lesson>
                            @elseif($authUserIsAdmin)
                                <colendar-small
                                    :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"
                                    :is-admin="true" :student-list="false"
                                    :instructor-id="{{ $userProfileData['id'] }}"
                                    :logged-in-as-student="{{ (isset($loggedUserRole) && $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
                                ></colendar-small>
                            @else
                                <colendar-small
                                    :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"
                                    :student-list="'booking-button'"
                                    :is-admin="false"
                                    :instructor-id="{{ $userProfileData['id'] }}"
                                    :logged-in-as-student="{{ (isset($loggedUserRole) && $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
                                ></colendar-small>
                            @endif
                            @if(!$authUserIsAdmin)
                                <profile-upcoming-virtual-lessons v-bind:upcoming-dates="{{ json_encode($userProfileData['upcoming_virtual_lessons_dates']) }}" v-bind:instructor-name="'{{ addslashes($userProfileData['full_name']) }}'"></profile-upcoming-virtual-lessons>
                            @endif
                        </div>
                    </div>
                    @if($authUserIsAdmin)
                        <div class="aligner-top row">
                            <div class="card-section-info col-12 col-lg-12 padding-r-30">
                                <div class="admin-dashboard">
                                    <div class="clients-table">
                                        <backend-lessons-dashboard-list :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}" :instructor="{{ $userProfileData['id'] }}"></backend-lessons-dashboard-list>
                                        <a href="/backend/lessons?instructor={{ $userProfileData['id'] }}" class="btn btn-block btn-secondary">View all</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="aligner-top row">
                        <div class="card-section-info col-12 col-lg-12 padding-r-30 padding-r-30">
                            <instructor-video-lessons
                                :instructor-id="'{{ addslashes($userProfileData['id']) }}'"
                                :can-book="{{ $loggedUserRole==\App\Models\User::ROLE_STUDENT ? 'true' : 'false' }}"
                                :user-role = "{{ json_encode($loggedUserRole) }}"
                            ></instructor-video-lessons>
                        </div>
                    </div>

                    <div class="aligner-top row">
                        <div class="card-section-info col-12 col-lg-12 padding-r-30">
                            @if(Auth::user() && (Auth::user()->id==$userProfileData['id'] || $authUserIsAdmin) )
                                <student-media-gallery
                                    class="dashboard-gallery-page"
                                    :user-media="{{ json_encode($userMedia) }}"
                                    :instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}"
                                    :description="'{{ isset($currentPage) ? addslashes($currentPage->content) : '' }}'"
                                    @if( $authUserIsAdmin && $userProfileData['id']!=Auth::user()->id )
                                        v-bind:user-id="{{ $userProfileData['id'] }}"
                                    @endif
                                ></student-media-gallery>
                            @else
                                <profile-simple-gallery v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ (Auth::user() && Auth::user()->id==$userProfileData['id'] && isset($loadingInstagramProfileImagesInQueue))?'true':'false' }}"></profile-simple-gallery>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
