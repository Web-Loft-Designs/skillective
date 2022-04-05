@if ($loggedUserRole==\App\Models\User::ROLE_INSTRUCTOR)
    <nav-links>
        <li><add-lesson-time-button></add-lesson-time-button></li>
        <li><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
        <li><a href="{{ route('instructor.schedule') }}" >Schedule</a></li>
        <li><a href="{{ route('instructor.bookings') }}" >Bookings @if($countInstructorPendingBookings>0)<span class="circle-badge update-value">{{$countInstructorPendingBookings}}</span>@endif</a></li>
        <li><a href="{{ route('instructor.clients') }}" >Clients</a></li>
        <li><a href="{{ route('instructor.gallery') }}" >Gallery</a></li>
    </nav-links>
@elseif ($loggedUserRole==\App\Models\User::ROLE_STUDENT)
    <nav-links>
        <li><a href="{{ route('student.dashboard') }}" >Dashboard</a></li>
        <li><a href="{{ route('student.schedule') }}" >Schedule</a></li>
        <li><a href="{{ route('student.bookings') }}" >Bookings</a></li>
        <li><a href="{{ route('student.instructors') }}" >Instructors</a></li>
        <li><a href="{{ route('student.gallery') }}" >Gallery</a></li>
    </nav-links>
@endif
