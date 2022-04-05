@extends('layouts.app-frontend')
@section('content')
    <div class="dashboard-gallery-page">
        <div class="container dashboard-gallery">
            <div class="row">
                <div class="col-12">

                    <a href="{{ $update_media_from_instagram_url }}">Load Recent Instagram Media</a>

                    <instructor-media-gallery v-bind:user-media="{{ json_encode($userMedia) }}"
                                              v-bind:instagram-media-queue="{{ isset($loadingInstagramProfileImagesInQueue)?'true':'false' }}"
                                              v-bind:description="'{{ isset($currentPage) ? addslashes($currentPage->content) : '' }}'">
                    </instructor-media-gallery>
                </div>
            </div>
        </div>
    </div>
@endsection