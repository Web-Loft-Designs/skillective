@extends('layouts.app-frontend')
@section('content')
    <div class="dashboard-gallery-page">
        <div class="container dashboard-gallery">
            <div class="row">
                <div class="col-12">
                    <student-media-gallery v-bind:user-media="{{ json_encode($userMedia) }}" v-bind:description="'{{ isset($currentPage) ? addslashes($currentPage->content) : '' }}'"></student-media-gallery>
                </div>
            </div>
        </div>
    </div>
@endsection