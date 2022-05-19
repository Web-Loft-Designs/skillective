@extends('layouts.app-frontend')
@section('content')

    <calendar-input
        :genres="{{ json_encode($siteGenres) }}"
        default-lesson-type="pre_recorded"
        ajax-event="globalShopLoadLessons"
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
    <global-shop
        :can-book="{{ (!isset($loggedUserRole) || $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
        :preloaded-lessons="{{ json_encode($lessons['data']) }}"
        :preloaded-pagination="{{ json_encode($lessons['meta']) }}"
    ></global-shop>
@endsection
