@extends('layouts.app-frontend')

@section('content')

    <?php
    $pageMeta = $currentPage->getAllMeta();

    $invitation_form_title= isset($pageMeta['invitation_form_title']) ? $pageMeta['invitation_form_title'] : '';
    $invitation_form_description= isset($pageMeta['invitation_form_description']) ? $pageMeta['invitation_form_description'] : '';
    ?>
    <div class="dashboard-page dashboard-page-student">
        <div class="container">
            <div class="row">
                @if (isset($upcomingBooking))
                    <div class="col-12" id="upcoming-booking-notification">
                        <div class="top-alert">
                            <div class="avatar-stack">
                                <?php 
                                $countToShow = 7;
                                $date = Carbon\Carbon::createFromTimeString($upcomingBooking['lesson']['start'])->toFormattedDateString();
                                ?>
                                @foreach ($upcomingBooking['lesson']['students'] as $i=>$student)
                                    @if ($i<$countToShow)
                                        <span>
                                            <img src="{{ $student['profile']['image'] }}" alt="{{ addslashes($student['full_name']) }}" title="{{ addslashes($student['full_name']) }}">
                                        </span>
                                        @if ( ($i+1)==($countToShow) && ($totalCountStudents = count($upcomingBooking['lesson']['students']))>$countToShow )
                                            <span><img src="{{ asset('images/ava_1.png') }}" alt=""><span>{{ $totalCountStudents-$i-1 }}+</span></span>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <p>Next
                                <strong>{{ $upcomingBooking['lesson']['genre']['title'] }}</strong>
                                @if($upcomingBooking['lesson']['lesson_type']=='virtual')
                                 virtual @endif lesson starts at 
                                 {{$date}}
                                 {{ \Carbon\Carbon::createFromTimeString($upcomingBooking['lesson']['start'])->format('h:i A') . ' ' . $upcomingBooking['lesson']['timezone_id'] }}
                                  @if($upcomingBooking['lesson']['lesson_type']!='virtual')
                                    at <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($upcomingBooking['lesson']['location']) }}" target="_blank">
                                         {{ $upcomingBooking['lesson']['location'] }}
                                    </a> @endif </p>
                            <span onclick="hideUpcomingBookingNotification({{ $upcomingBooking['id'] }})" class="close-it"></span>
                        </div>
                    </div>
                @endif

                <div class="col-lg-6 col-12">
                    <div class="table-page">
                        <student-instructors-dashboard-list v-bind:instructors="{{ json_encode($instructors['data']) }}" v-bind:instructors-meta="{{ isset($instructors['meta'])?json_encode($instructors['meta']):'[]' }}"></student-instructors-dashboard-list>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="table-page">
                        <calendar-booked-lessons
                            :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"
                        ></calendar-booked-lessons>
                    </div>
                </div>

                <div class="col-12">
                    <div class="table-page">
                        <request-lesson-form
                            :show-create-btn="false"
                            :site-genres="{{  json_encode($siteGenres) }}"
                            :logged-in-student="true"
                        ></request-lesson-form>
                        <send-notification-form
                            @if($loggedUserRole==\App\Models\User::ROLE_STUDENT)
                                :is-student="true"
                            @else
                                :is-student="false"
                            @endif
                            class="notification-form-simple"
                            :mode="'simple'"
                            v-bind:available-notification-methods="{{  json_encode($availableNotificationMethods) }}"></send-notification-form>
                        <student-bookings-dashboard-list v-bind:bookings="{{ json_encode($bookings['data']) }}" v-bind:bookings-meta="{{ isset($bookings['meta'])?json_encode($bookings['meta']):'[]' }}"></student-bookings-dashboard-list>
                    </div>
                </div>

<!--                 <div class="col-12">
                    <div class="dashboard-gallery">
                        <h2 class="page-title">Gallery123123</h2>
                        <dashboard-media-gallery v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}"></dashboard-media-gallery>
                        <a href="{{ route('student.gallery') }}" class="btn btn-secondary btn-block">View Gallery</a>
                    </div>
                </div>
-->

                <div class="col-12">
                    <div class="table-page">
                        <div class="table-component">
                            <div class="table-component-top">
                                <h2>Skills and Learning</h2>
                                <a href="/lessons">Book More Training</a>
                            </div>
                            <div class="table-component-body">
                                <table>
                                    <tr>
                                        <th>Genre</th>
                                        <th class="text-center">Lessons</th>
                                        <th class="text-center">Hours</th>
                                    </tr>
                                    @foreach ($bookedGenresStatistics as $stat)
                                        <?php
                                        $genre = null;
                                        foreach ($siteGenres as $k => $g) {
                                            if ($g->id == $stat['genre_id']) {
                                                $genre = $g;
                                                break;
                                            }
                                        }
                                        foreach ($studentGenres as $k => $g) {
                                            if ($g->id == $stat['genre_id']) {
                                                $studentGenres->forget($k);
                                                break;
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td>@if($genre)<img src="{{ $genre->getImageUrl() }}" alt="" style="width:50px;"><span>{{ $genre->title }}</span>@endif</td>
                                            <td class="text-center">{{ $stat['lessons'] }}</td>
                                            <td class="text-center">{{ round( ($stat['minutes']/60) , 1 ) }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach ($studentGenres as $k => $g)
                                        <tr>
                                            <td>@if($g)<img src="{{ $g->getImageUrl() }}" alt="" style="width:50px;"><span>{{ $g->title }}</span>@endif</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
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
                            <favorite-instructor-invitation-form
                                :count-invitations-sent="{{ Auth::user()->instructorInvitations()->count() }}"
                                :max-invites-enabled="{{ Auth::user()->getMaxAllowedInstructorInvites() }}"
                            ></favorite-instructor-invitation-form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
