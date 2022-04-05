<p class="profile-label">Invited instructors:
    <a href="{{ route('backend.instructors.index') . '?invited_by=' . $userProfileData['id'] }}">(Edit)</a>
</p>
<div class="avatar-stack">
	<?php $countToShow = 7 ?>
    @foreach ($invitedInstructors as $i=>$instructor)
        @if ($i<$countToShow)
            <span>
                <img src="{{ $instructor['profile']['image'] }}" alt="{{ addslashes($instructor['full_name']) }}" title="{{ addslashes($instructor['full_name']) }}">
            </span>
            @if ( ($i+1)==($countToShow) && ($totalCountInstructors = count($invitedInstructors))>$countToShow )
                <span><img src="{{ asset('images/ava_1.png') }}" alt=""><span>{{ $totalCountInstructors-$i-1 }}+</span></span>
            @endif
        @endif
    @endforeach
</div>