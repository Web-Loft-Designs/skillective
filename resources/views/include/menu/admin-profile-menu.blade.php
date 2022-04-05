<li><a href="{{ route('backend.dashboard') }}" >Dashboard</a></li>
<li><a href="{{ route('admin.profile.edit') }}">Edit Profile</a></li>
<li>
    <a href="{!! url('/logout') !!}" class="logout"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Sign out
    </a>
    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
</li>