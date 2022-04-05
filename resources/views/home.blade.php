@extends('layouts.app-frontend')

@section('content')
<?php
$pageMeta = $currentPage->getAllMeta();

$text_above_filter             = isset($pageMeta['text_above_filter']) ? $pageMeta['text_above_filter'] : '';
$filter_form_title             = isset($pageMeta['filter_form_title']) ? $pageMeta['filter_form_title'] : '';
$instructor_filter_form_title             = isset($pageMeta['instructor_filter_form_title']) ? $pageMeta['instructor_filter_form_title'] : '';
$how_it_works_title            = isset($pageMeta['how_it_works_title']) ? $pageMeta['how_it_works_title'] : '';
$benefits_title                = isset($pageMeta['benefits_title']) ? $pageMeta['benefits_title'] : '';
$how_it_works_text             = isset($pageMeta['how_it_works_text']) ? $pageMeta['how_it_works_text'] : '';
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
<home></home>

<section class="how-it-works">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 col-12">
                @if($how_it_works_title)
                <h2 class="section-title">{!! $how_it_works_title !!}</h2>
                @endif
                @if($how_it_works_text)
                <p>{!! $how_it_works_text !!}</p>
                @endif
            </div>
            <div class="col-md-6 col-12">
                @if (count($testimonials))
                <div class="slider">
                    <div class="item">
                        <div class="item-wrapper">
                            <slick :options="{
                                    arrows: false,
                                    dots: true,
                                    infinite: true,
                                    autoplay: true,
                                    autoplaySpeed: 4000
                                }">
                                @foreach ($testimonials as $t)
                                <div class="slick-slide">
                                    <div class="item-body">
                                        <p>{{ $t->content }}</p>
                                    </div>
                                    <div class="item-footer">
                                        <span>
                                            @if ($t->getImageUrl()!='')
                                            <img class="img" src="{{ $t->getImageUrl() }}" />
                                            @endif
                                            <span>– {{ $t->name }}@if($t->instagram_handle), <a href="https://www.instagram.com/{{ $t->instagram_handle }}" target="_blank">{{ '@' . $t->instagram_handle }}</a>@endif</span>
                                        </span>
                                    </div>
                                </div>
                                @endforeach

                            </slick>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<upcoming-lessons-near-you
    :can-book="{{ (!isset($loggedUserRole) || $loggedUserRole==\App\Models\User::ROLE_STUDENT) ? 'true' : 'false' }}"
></upcoming-lessons-near-you>

@include('include/benefit', ['title_block' => $benefits_title, 'benefits'=>$benefits])

@endsection

@section ('scripts')
{{--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.api_key') }}&libraries=places"></script>--}}
@endsection