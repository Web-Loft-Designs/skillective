@if (!Auth::guest())
    <profile-menu
        img="{{ Auth::user()->profile->getImageUrl() }}"
        text="{!! Auth::user()->first_name !!} {!! Auth::user()->last_name !!}"
        subtext="{{ $loggedUserRole==\App\Models\User::ROLE_INSTRUCTOR ? '$' . number_format($totalAmountInEscrow, 2, '.', ',') : '' }} "
    >
        @if ($loggedUserRole==\App\Models\User::ROLE_ADMIN)
            @include('include/menu/admin-profile-menu')
        @else
            @if (!isDashboardPage())
                <li><a href="{{ getDashboardUrl() }}" >Dashboard</a></li>
            @endif
            <li><a href="{{ route('profile') }}" >Profile</a></li>
            @if ($loggedUserRole==\App\Models\User::ROLE_STUDENT)
                <li><a href="{{ route('student.library') }}" >My Library</a></li>
            @endif
            @if ($loggedUserRole==\App\Models\User::ROLE_INSTRUCTOR)
                <li><a href="{{ route('instructor.invites') }}">Invite instructor <span class="count-sent-instructor-invitations circle-badge">{{ Auth::user()->getMaxAllowedInstructorInvites() - $countInstructorInvitationsSent }}</span></a></li>
                <li><a href="{{ route('instructor.discount-management') }}">Discount Management</a></li>
                <li><a href="{{ route('instructor.my-shop') }}">My Shop</a></li>
                <li><a href="{{ route('instructor.incomes') }}">Incomes</a></li>
                <li><a href="{{ route('instructor.payouts') }}">Payouts</a></li>
            @endif
            <li><a href="{{ route('profile.edit') }}">Settings</a></li>
            <li>
                <a href="{!! url('/logout') !!}" class="logout red"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sign out
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        @endif
    </profile-menu>
@endif