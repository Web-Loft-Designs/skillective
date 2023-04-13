<div class="profile-info-wrapper">

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
        <p><span>{{ $userProfileData['full_name'] }}</span> <!-- | <span>
                {{ $userProfileData['profile']['full_address'] }}
            </span> --></p>
                    <h2>
                        @if ($userProfileData['profile']['instagram_handle'])
                            <a href="https://instagram.com/{{ $userProfileData['profile']['instagram_handle'] }}" target="_blank">{{ '@' . $userProfileData['profile']['instagram_handle'] }}</a>
                        @endif
                            @if(Auth::user() && (Auth::user ()->id==$userProfileData['id'] || Auth::user()->hasRole('Admin')))
                                <a href="{{ $editRoute }}" class="edit">(Edit)</a>
                            @endif
                    </h2>
        <div class="d-flex align-items-center">
            <p><strong>{{ $userProfileData['total_booked_lessons'] }}</strong> Lessons</p>
{{--            <p><strong>{{ number_format($userProfileData['profile']['instagram_followers_count']) }}</strong> Followers</p>--}}
        </div>
    </div>
    <div class="profile-info-middle">
        <div class="profile-info-about-us">
            <p class="profile-label">About me:
                @if(Auth::user() && (Auth::user ()->id==$userProfileData['id'] || Auth::user()->hasRole('Admin')))
                    <a href="{{ $editRoute }}">(Edit)</a>
                @endif
            </p>
            <content-viewer content="{{ $userProfileData['profile']['about_me'] }}"></content-viewer>
        </div>
        <div class="d-flex flex-wrap w-100">
            <div class="profile-info-genres @if(count($invitedInstructors)>0 && Auth::user()->hasRole('Admin')) col-md-6 @endif col-12 p-0">
                <p class="profile-label">Genres:
                    @if(Auth::user() && (Auth::user ()->id==$userProfileData['id'] || Auth::user()->hasRole('Admin')))
                        <a href="{{ $editRoute }}">(Edit)</a>
                    @endif
                </p>
                @foreach ($userProfileData['genres'] as $genre)
                    <span>{{ $genre['title'] }} </span>
                @endforeach
            </div>
            @if(count($invitedInstructors)>0 && Auth::user() && Auth::user()->hasRole('Admin'))
            <div class="profile-info-invated col-md-6 col-12 p-0">
                @include('frontend.partials.profile.invited-instructors')
            </div>
            @endif
        </div>
    </div>
</div>