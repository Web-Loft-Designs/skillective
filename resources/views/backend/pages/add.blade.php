@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit genres-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    @include('include.backend-content-menu')
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        {{-- fake emails generated when registration via instagram --}}
                        <div class="form-wrap">
                            <p class="login-box-msg">{{ $page_title }}</p>
                            @include('common.errors')

                            <form action="{{ route('backend.page.create') }}" method="POST" enctype="multipart/form-data">
                                {{ csrf_field() }}

                               <div class="d-flex flex-wrap">
                                   <div class="form-group">
                                       <div class="field">
                                           <label for="page-title" >Title</label>
                                           <input type="text" class="form-control" name="title" id="page-title" value="{{ old('title') }}" required autocomplete="off">
                                       </div>
                                   </div>
                                   <div class="form-group">
                                       <div class="field">
                                           <label for="page-name">Name</label>
                                           <input type="text" class="form-control" name="name" id="page-name" value="{{ old('name') }}" required autocomplete="off">
                                       </div>
                                   </div>
                                   <div class="form-group">
                                       <div class="field">
                                           <label for="page-content" >Content</label>
                                           <textarea name="content" class="form-control" id="page-content" class="add-wysiwyg" autocomplete="off">{{ old('content') }}</textarea>
                                       </div>
                                   </div>

                                   <div class="form-group">
                                       <button type="submit" class="btn btn-primary btn-submit">
                                           Create
                                       </button>
                                       <a href="{{ route('backend.pages')}}" class="btn btn-secondary">
                                           Cancel
                                       </a>

                                   </div>
                               </div>


                                {{--<div class="col-sm-4">--}}
                                {{--<label for="page-parent" >Parent</label>--}}
                                {{--<select name="parent" id="page-parent" class="form-control">--}}
                                {{--<option value="">Select</option>--}}
                                {{--@foreach($tree as $page)--}}
                                {{--<option value="{{ $page->id }}" @if(old('parent') && old('parent')==$page->id){{ ' selected ' }}@endif>--}}
                                {{--{{ $page->title }}--}}
                                {{--@if(count($page->children))--}}
                                {{--@include('backend.pages.page-child-options',['children' => $page->children, 'margin'=>'-'])--}}
                                {{--@endif--}}
                                {{--</option>--}}
                                {{--@endforeach--}}
                                {{--</select>--}}
                                {{--</div>--}}



                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--<div class="inner inner-space">--}}
        {{--<div class="card card-admin">--}}
            {{--<div class="card-header">--}}
                {{--<h2 class="card-title">{{ $page_title }}</h2>--}}
                {{--@include('common.errors')--}}
            {{--</div>--}}
            {{--<div class="form-wrap form-admin">--}}
                {{--<form action="{{ route('backend.page.create') }}" method="POST" enctype="multipart/form-data">--}}
                    {{--{{ csrf_field() }}--}}

                    {{--<div class="row row-align-start">--}}
                        {{--<div class="column-half left">--}}
                            {{--<div class="row">--}}
                                {{--<div class="field">--}}
                                    {{--<label for="page-title" >Title</label>--}}
                                    {{--<input type="text" name="title" id="page-title" value="{{ old('title') }}" required autocomplete="off">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="column-half right">--}}
                                {{--<div class="field">--}}
                                    {{--<label for="page-name">Name</label>--}}
                                    {{--<input type="text" name="name" id="page-name" value="{{ old('name') }}" required autocomplete="off">--}}
                                {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="row">--}}
                        {{--<div class="field">--}}
                            {{--<label for="page-content" >Content</label>--}}
                            {{--<textarea name="content" id="page-content" class="add-wysiwyg" autocomplete="off">{{ old('content') }}</textarea>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                                {{--<div class="col-sm-4">--}}
                                {{--<label for="page-parent" >Parent</label>--}}
                                {{--<select name="parent" id="page-parent" class="form-control">--}}
                                {{--<option value="">Select</option>--}}
                                {{--@foreach($tree as $page)--}}
                                {{--<option value="{{ $page->id }}" @if(old('parent') && old('parent')==$page->id){{ ' selected ' }}@endif>--}}
                                {{--{{ $page->title }}--}}
                                {{--@if(count($page->children))--}}
                                {{--@include('backend.pages.page-child-options',['children' => $page->children, 'margin'=>'-'])--}}
                                {{--@endif--}}
                                {{--</option>--}}
                                {{--@endforeach--}}
                                {{--</select>--}}
                                {{--</div>--}}


                    {{--<div class="row bottom-row">--}}
                        {{--<div class="right-buttons">--}}
                            {{--<a href="{{ route('backend.pages')}}" class="link link-grey">--}}
                                {{--Cancel--}}
                            {{--</a>--}}
                            {{--<button type="submit" class="btn btn-submit">--}}
                                {{--Create--}}
                            {{--</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}


                {{--</form>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection

@section('pageSpecificJS')
    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script>
        var options = {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
        };
        CKEDITOR.replace( 'page-content', options );
    </script>
@endsection