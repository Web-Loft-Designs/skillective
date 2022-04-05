<div class="profile-avatar">
    <div id="uploader-profile-image" style="background-image: url('{{ $userProfileData['profile']['image'] }}')"></div>
    <profile-avatar-upload
            @if( Auth::user()->hasRole('Admin') && $userProfileData['id']!=Auth::user()->id )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
    ></profile-avatar-upload>
</div>