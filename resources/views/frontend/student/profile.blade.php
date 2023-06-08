@extends('layouts.app-frontend')

@section('content')
	<?php
	$authUserIsAdmin = (Auth::user() && Auth::user()->hasRole('Admin'));
	?>
    <div class="profile-section student-profile-page">
        <div class="inner">
            <div class="profile-info">
                <div id="card-personal-info" class="container">
                    <div class="aligner-top row">
                        <div class="card-section-info col-12 col-lg-12 padding-r-30">
                            <div class="form-wrap">
                                @include('frontend.student.partials.profile-data-form')
                                {{--  --}}
                                <!--
                                @if(Auth::user() && (Auth::user()->id==$userProfileData['id'] || $authUserIsAdmin) )
                                    <student-media-gallery
                                            class="dashboard-gallery-page"
                                            v-bind:user-media="{{ json_encode($userMedia) }}"
                                            v-bind:instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}"
                                            v-bind:description="'{{ isset($currentPage) ? addslashes($currentPage->content) : '' }}'"
                                            @if( $authUserIsAdmin && $userProfileData['id']!=Auth::user()->id )v-bind:user-id="{{ $userProfileData['id'] }}" @endif
                                    ></student-media-gallery>
                                @else
                                    <profile-simple-gallery class="dashboard-gallery-page" v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:instagram-media-queue="{{ (Auth::user() && Auth::user()->id==$userProfileData['id'] && isset($loadingInstagramProfileImagesInQueue))?'true':'false' }}"></profile-simple-gallery>

                                @endif
                                 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection