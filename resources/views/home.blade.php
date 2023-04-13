@extends('layouts.app-frontend')

@section('content')
<?php
$pageMeta = $currentPage->getAllMeta();

$text_above_filter             = isset($pageMeta['text_above_filter']) ? $pageMeta['text_above_filter'] : '';
$filter_form_title             = isset($pageMeta['filter_form_title']) ? $pageMeta['filter_form_title'] : '';
$instructor_filter_form_title  = isset($pageMeta['instructor_filter_form_title']) ? $pageMeta['instructor_filter_form_title'] : '';
$benefits_title                = isset($pageMeta['benefits_title']) ? $pageMeta['benefits_title'] : '';
$_image_filter_block_image = isset($pageMeta['_image_filter_block_image']) ? $pageMeta['_image_filter_block_image'] : '';
$benefits               = isset($pageMeta['benefits']) ? $pageMeta['benefits'] : [];
?>

<calendar-input
    :genres="{{ json_encode($siteGenres) }}"
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
<home
        :can-book="{{ (!isset($loggedUserRole) || $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
        :preloaded-lessons="{{ json_encode($lessons['data']) }}"
></home>


@endsection

@section ('scripts')
{{--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.api_key') }}&libraries=places"></script>--}}
@endsection