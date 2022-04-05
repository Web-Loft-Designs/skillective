@extends('layouts.app')

@section('content')
    {{--<div class="table-page">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12">--}}
                    {{--@include('backend.users.show_fields')--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<section class="content-header">--}}
        {{--<h1>--}}
            {{--User--}}
        {{--</h1>--}}
    {{--</section>--}}
    {{--<div class="content">--}}
        {{--<div class="box box-primary">--}}
            {{--<div class="box-body">--}}
                {{--<div class="row" style="padding-left: 20px">--}}

                    {{--<a href="{!! route('backend.users.index') !!}" class="btn btn-default">Back</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="profile-section student-profile-page">--}}
        {{--<div class="inner">--}}
            {{--<div class="profile-info">--}}
                {{--<div id="card-personal-info" class="container">--}}
                    {{--@if(Auth::user() && Auth::user()->id==$userProfileData['id'])--}}
                    {{--<a href="{{ route('profile.edit') }}">(Edit)</a>--}}
                    {{--@endif--}}
                    {{--<div class="aligner-top row">--}}
                        {{--<div class="card-section-info col-12 col-lg-12 padding-r-30">--}}
                            {{--<div class="form-wrap">--}}
                                {{--@include('frontend.student.partials.profile-data-form')--}}
                                {{--@if(Auth::user() && Auth::user()->id==$userProfileData['id'])--}}
                                    {{--<student-media-gallery class="dashboard-gallery-page" v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}" v-bind:description="'{{ isset($currentPage) ? $currentPage->content : '' }}'"></student-media-gallery>--}}
                                {{--@else--}}
                                    {{--<profile-simple-gallery class="dashboard-gallery-page" v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ (Auth::user() && Auth::user()->id==$userProfileData['id'] && isset($loadingInstagramProfileImagesInQueue))?'true':'false' }}"></profile-simple-gallery>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

@endsection
