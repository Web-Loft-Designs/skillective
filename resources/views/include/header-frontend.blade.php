@if (!\Request::is('/') && !\Request::is('lessons*') && !\Request::is('globalshop*'))  
  <calendar-input
    show-fixed-search-by-default
    :logo-src="'{{ asset('images/logo.png') }}'"
  >
    <template v-slot:header-right>
        @if (!isDashboardPage())
          @include('include/menu/header-menu-guest')
        @elseif(Auth::user())
            @include('include/menu/header-menu-login')
        @endif
        @include('include/menu/profile-menu')
        <cart-icon
            v-if="{{ (!isset($loggedUserRole) || $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
            :guest-mode="{{ isset($loggedUserRole) ? 'false' : 'true' }}"
        ></cart-icon>
    </template>
</calendar-input>
@endif