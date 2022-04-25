<div class="profile-info-wrapper">
    <?php
    $dashboardPage = getCurrentPage('instructor/dashboard');
    $booking_fees_description = getCurrentPageMetaValue($dashboardPage, 'booking_fees_description');
    $lesson_request_created = 'Your Lesson Request has been sent to Instructor.';
    ?>

    <?php
        if (Auth::user() && !Auth::user()->hasRole('Admin'))
	        $editRoute = route('profile.edit');
        else
			$editRoute = route('profile.edituser', ['user'=>$userProfileData['id']]);
    ?>

    @if(Auth::user() && (Auth::user ()->id==$userProfileData['id'] || Auth::user()->hasRole('Admin') ) )
        @include('frontend.partials.profile.avatar-upload')
    @else
        @include('frontend.partials.profile.avatar')
    @endif

    <div class="profile-info-text">
        <h2>
        @if ($userProfileData['profile']['instagram_handle'])
            <a href="https://instagram.com/{{ $userProfileData['profile']['instagram_handle'] }}" target="_blank">{{ '@' . $userProfileData['profile']['instagram_handle'] }}</a>
        @endif

        @if ($userProfileData['isInstructor']==true && Auth::user() && Auth::user()->hasRole('Student'))
            <favorite-instructor v-bind:instructor-id="{{ $userProfileData['id'] }}" v-bind:is-favorite="{{ Auth::user()->hasOwnFavoriteInstructor($userProfileData['id']) ? 'true' : 'false' }}"></favorite-instructor>
        @endif
        </h2>
        <p>{{ $userProfileData['full_name'] }} <br/>
           {{ $userProfileData['profile']['city'] }} <br/>
           {{ $userProfileData['profile']['state'] }}
        </p>
        <div class="d-flex align-items-center">
            <p><strong>{{ $userProfileData['total_count_lessons'] }}</strong> Lessons Held</p>
            <?php
            $loggedInStudent = ( Auth::user() && Auth::user()->hasRole('Student') );
            ?>
            @if ($userProfileData['isInstructor']==true && ( !Auth::user() || $loggedInStudent ) )
                <request-lesson-form
                        :instructor-name="{{ json_encode($userProfileData['full_name']) }}"
                        :show-create-btn="true"
                        :user-genres="{{  json_encode($userProfileData['genres']) }}"
                        :site-genres="{{  json_encode($siteGenres) }}"
                        :instructor-id="{{ $userProfileData['id'] }}"
                        {{--:select-range="selectRangeTrigger"--}}
                        :logged-in-student="{{ $loggedInStudent ? 'true' : 'false' }}"
                        {{--:booking-fees-description="'{{ isset($booking_fees_description) ? preg_replace('/\n|\r/', '', addslashes($booking_fees_description)) : '' }}'"--}}
                        :request-sent-confirmation="'{{ preg_replace('/\n|\r/', '', addslashes($lesson_request_created)) }}'"
                        :lesson-block-min-price="{{ $userProfileData['profile']['lesson_block_min_price'] }}"
                ></request-lesson-form>
            @endif
{{--            <p><strong>{{ number_format($userProfileData['profile']['instagram_followers_count']) }}</strong> Followers</p>--}}
        </div>
    </div>
    <div class="profile-info-middle">
        <div class="profile-info-about-us">
            <p class="profile-label">About instructor:
                @if(Auth::user() && (Auth::user ()->id==$userProfileData['id'] || Auth::user()->hasRole('Admin')))
                 <a href="{{ $editRoute }}">(Edit)</a>
                @endif
            </p>
            <content-viewer content="{{ $userProfileData['profile']['about_me'] }}"></content-viewer>
        </div>
        @if($userProfileData['lessons_rate_min']>0 || $userProfileData['lessons_rate_max']>0)
        <div class="profile-info-rates">
            <p class="profile-label">Lesson Rates:</p>
            <p><strong>${{ number_format($userProfileData['lessons_rate_min'], 2) }}@if($userProfileData['lessons_rate_min']<$userProfileData['lessons_rate_max']) – ${{ number_format($userProfileData['lessons_rate_max'], 2) }}@endif</strong></p>
        </div>
        @endif
        <div class="profile-info-genres">
            <p class="profile-label">Genres:
                @if( Auth::user() && (Auth::user ()->id==$userProfileData['id'] || Auth::user()->hasRole('Admin')))
                    <a href="{{ $editRoute }}">(Edit)</a>
                @endif
            </p>
            @foreach ($userProfileData['genres'] as $genre)
                <span>{{ $genre['title'] }} </span>
            @endforeach
        </div>
        @if(count($invitedInstructors)>0 && Auth::user() && Auth::user()->hasRole('Admin'))
        <div class="profile-info-invated">
            @include('frontend.partials.profile.invited-instructors')
        </div>
        @endif
    </div>
</div>