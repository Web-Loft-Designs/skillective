@extends('layouts.app-frontend')

@section('content')
<calendar-input
    always-on-top
    :genres="{{ json_encode($genres) }}"
    ajax-event="lessonsLoadLessons"
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
<lessons
    :genres="{{ json_encode($genres) }}"
    :preloaded-lessons="{{ json_encode($lessons['data']) }}"
    :can-book="{{ (!isset($loggedUserRole) || $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
    :meta="{{ isset($lessons['meta'])?json_encode($lessons['meta']):json_encode([]) }}"
></lessons>
@endsection
