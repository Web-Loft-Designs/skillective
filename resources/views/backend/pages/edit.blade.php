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
                            <p class="login-box-msg">Edit "{{ $currentitem->title }}" page</p>
                            @include('adminlte-templates::common.errors')


                            <form action="{{ route('backend.page.update', $currentitem->id) }}" method="POST" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="d-flex flex-wrap">
                                    <div class="form-group col-sm-6">
                                        <label for="page-title" >Title</label>
                                        <input class="form-control" type="text" name="title" id="page-title" value="{{ old('title')?old('title'):$currentitem->title }}" autocomplete="off" required>
                                    </div>
                                    {{--<div class="col-sm-4">--}}
                                    {{--<label for="page-parent" >Parent</label>--}}
                                    {{--<select name="parent" id="page-parent" class="form-control">--}}
                                    {{--<option value="">Select</option>--}}
                                    {{--@foreach($tree as $page)--}}
                                    {{--<option value="{{ $page->id }}" @if( (old('parent') && old('parent')==$page->id) || (isset($currentitem) && $currentitem->parent_id==$page->id)) {{ ' selected ' }}@endif>--}}
                                    {{--{{ $page->title }}--}}
                                    {{--@if(count($page->children))--}}
                                    {{--@include('backend.pages.page-child-options',['children' => $page->children, 'margin'=>'-'])--}}
                                    {{--@endif--}}
                                    {{--</option>--}}
                                    {{--@endforeach--}}
                                    {{--</select>--}}
                                    {{--</div>--}}


                                    <div class="form-group col-sm-6">
                                        <label for="page-name" >Name</label>
                                        <input class="form-control" @if ($currentitem->id<=15) readonly @endif type="text" name="name" id="page-name" value="{{ old('name')?old('name'):$currentitem->name }}" autocomplete="off" required>
                                    </div>

                                    @if ($currentitem
                                    && $currentitem->id!=App\Models\Page::HOME_PAGE_ID
                                    && $currentitem->id!=App\Models\Page::ABOUT_US_PAGE_ID
                                    && $currentitem->name!='instructor/register'
                                    && $currentitem->name!='student/register'
                                    && $currentitem->name!='instructor/dashboard'
                                    && $currentitem->name!='student/dashboard'
                                    && $currentitem->name!='profile/edit'
                                    )
                                        <div class="form-group col-sm-12">
                                            <label for="page-content" >Content</label>
                                            <textarea class="form-control" name="content" id="page-content" class="add-wysiwyg">{{ old('content')?old('content'):$currentitem->content }}</textarea>
                                        </div>
                                    @endif

                                    @include('backend.pages.page_meta')

                                    <div class="form-group col-sm-12">
                                        <div class="right-buttons">
                                            @if ($currentitem->id>15)
                                                @include('backend.includes.delete-link-with-confirmation')
                                            @endif
                                            <button type="submit" class="btn btn-primary btn-submit" >Save</button>
                                            <a href="{{ route('backend.pages')}}" class="btn btn-secondary">
                                                Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>


                            </form>

                            @if ($currentitem->id>15)
                                <div class="form-group col-sm-12 hide">
                                    <form action="{{ route('backend.page.delete', $currentitem->id) }}" method="POST" class="form-delete-current-page-item">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button class="btn btn-danger navbar-btn">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--<section class="content-header">--}}
        {{--<h1>--}}
            {{--Edit "{{ $currentitem->title }}" page--}}
        {{--</h1>--}}
    {{--</section>--}}
    {{--<div class="content">--}}
        {{--@include('adminlte-templates::common.errors')--}}
        {{--<div class="box box-primary">--}}

            {{--<div class="box-body">--}}
                {{--<div class="row">--}}
                    {{--<form action="{{ route('backend.page.update', $currentitem->id) }}" method="POST" enctype="multipart/form-data">--}}
                        {{--{{ csrf_field() }}--}}

                                    {{--<div class="form-group col-sm-6">--}}
                                        {{--<label for="page-title" >Title</label>--}}
                                        {{--<input class="form-control" type="text" name="title" id="page-title" value="{{ old('title')?old('title'):$currentitem->title }}" autocomplete="off" required>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-4">--}}
                                    {{--<label for="page-parent" >Parent</label>--}}
                                    {{--<select name="parent" id="page-parent" class="form-control">--}}
                                    {{--<option value="">Select</option>--}}
                                    {{--@foreach($tree as $page)--}}
                                    {{--<option value="{{ $page->id }}" @if( (old('parent') && old('parent')==$page->id) || (isset($currentitem) && $currentitem->parent_id==$page->id)) {{ ' selected ' }}@endif>--}}
                                    {{--{{ $page->title }}--}}
                                    {{--@if(count($page->children))--}}
                                    {{--@include('backend.pages.page-child-options',['children' => $page->children, 'margin'=>'-'])--}}
                                    {{--@endif--}}
                                    {{--</option>--}}
                                    {{--@endforeach--}}
                                    {{--</select>--}}
                                    {{--</div>--}}


                            {{--<div class="form-group col-sm-6">--}}
                                {{--<label for="page-name" >Name</label>--}}
                                {{--<input class="form-control" @if ($currentitem->id<=12) readonly @endif type="text" name="name" id="page-name" value="{{ old('name')?old('name'):$currentitem->name }}" autocomplete="off" required>--}}
                            {{--</div>--}}

                        {{--@if ($currentitem && $currentitem->id!=App\Models\Page::HOME_PAGE_ID && $currentitem->id!=App\Models\Page::ABOUT_US_PAGE_ID && $currentitem->name!='instructor/register' && $currentitem->name!='student/register' && $currentitem->name!='instructor/dashboard' && $currentitem->name!='student/dashboard')--}}
                            {{--<div class="form-group col-sm-12">--}}
                                {{--<label for="page-content" >Content</label>--}}
                                {{--<textarea class="form-control" name="content" id="page-content" class="add-wysiwyg">{{ old('content')?old('content'):$currentitem->content }}</textarea>--}}
                            {{--</div>--}}
                        {{--@endif--}}

                            {{--@include('backend.pages.page_meta')--}}

                            {{--<div class="form-group col-sm-12">--}}
                                {{--<div class="right-buttons">--}}
                                    {{--@if ($currentitem->id>12)--}}
                                        {{--@include('backend.includes.delete-link-with-confirmation')--}}
                                    {{--@endif--}}
                                    {{--<a href="{{ route('backend.pages')}}" class="link link-grey">--}}
                                        {{--Cancel--}}
                                    {{--</a>--}}
                                    {{--<button type="submit" class="btn btn-submit" >Save</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                    {{--</form>--}}

                    {{--@if ($currentitem->id>12)--}}
                    {{--<div class="form-group col-sm-12 hide">--}}
                    {{--<form action="{{ route('backend.page.delete', $currentitem->id) }}" method="POST" class="form-delete-current-page-item">--}}
                        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                        {{--<input type="hidden" name="_method" value="DELETE">--}}
                        {{--<button class="btn btn-danger navbar-btn">Delete</button>--}}
                    {{--</form>--}}
                    {{--</div>--}}
                    {{--@endif--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection

@section('scripts')
    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script>
        var options = {
            allowedContent:true,
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
        };
		if (document.getElementById('page-content')){
            CKEDITOR.replace( 'page-content', options );
		}
        if (document.getElementById('cancellation-confirmation')){
            CKEDITOR.replace( 'cancellation-confirmation', options );
        }
		if (document.getElementById('cancellation-block-footer')){
			CKEDITOR.replace( 'cancellation-block-footer', options );
		}
		if (document.getElementById('student-geolocation-block-description')){
			CKEDITOR.replace( 'student-geolocation-block-description', options );
		}
        if (document.getElementById('booking-fees-description')){
            CKEDITOR.replace( 'booking-fees-description', options );
        }
    </script>
@endsection
