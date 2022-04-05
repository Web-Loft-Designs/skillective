@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12" sticky-container>
                    <div class="sticky-nav" v-sticky="true" sticky-offset="{top: 100, bottom: 20}">
                        <h2>Site Settings</h2>
                        <ul >
                            <li class="tab-trigger active"><a data-tab="general-settings" href="#general-settings">General</a></li>
                            <li class="tab-trigger"><a data-tab="users-instructors" href="#users-instructors">Users & Instructors</a></li>
                            <li class="tab-trigger"><a data-tab="merchants" href="#merchants">Merchants/Payments/Bookings</a></li>
                            <li class="tab-trigger"><a data-tab="google-analytics" href="#google-analytics">Google Analytics</a></li>
                            <li class="tab-trigger"><a data-tab="menus" href="#menus">Menus</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        <form action="{{ route('backend.settings.update') }}" method="POST" class="form-vertical" enctype="multipart/form-data">
                        <div class="form-wrap profile-tab-content active" data-tab="general-settings">

                                {{ csrf_field() }}
                                <p class="login-box-msg">General settings</p>

                                <div class="d-flex flex-wrap">
                                    <div class="form-group  has-feedback">
                                        <label for="meta-1" >Website Name</label>
                                        <input type="text" class="form-control" name="settings[sitename]" id="meta-1" value="@if (isset($settings['sitename'])){{ $settings['sitename'] }}@endif"/>
                                    </div>

                                    <div class="form-group  has-feedback">
                                        <div class="field field-files field-images">
                                            <label>Favicon</label>
                                            <span class="wrapper-file-input">
                                                <span class="input-file">
                                                    @if (isset($settings['favicon']) && ''!=$settings['favicon'])


                                                        <span class="name"><img src="@if (isset($settings['favicon'])){{ $settings['favicon'] }}@endif" />{{ $settings['favicon'] }}</span>
                                                    @else
                                                        <span class="name"></span>
                                                    @endif
                                                </span>
                                                <input id="website-logo" type="file" name="settings[favicon]" title="Browse">
                                            </span>
                                        </div>
                                        @if (isset($settings['favicon']) && ''!=$settings['favicon'])
                                            <div class="row-uploads">
                                                <div class="field mt-2 field-checkbox checkbox-wrapper">
                                                    <label for="remove-favicon">
                                                        <input type="checkbox" value="1" name="page_meta[_remove_favicon]" id="remove-favicon"/>
                                                        <span class="checkmark"></span>
                                                        Remove Uploaded Image
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{--<div class="form-group  has-feedback">--}}
                                        {{--<div class="field field-files field-images">--}}
                                            {{--<label>Website Logo</label>--}}
                                            {{--<span class="wrapper-file-input">--}}
                                                {{--<span class="input-file">--}}
                                                    {{--@if (isset($settings['sitelogo']) && ''!=$settings['sitelogo'])--}}


                                                        {{--<span class="name"><img src="@if (isset($settings['sitelogo'])){{ $settings['sitelogo'] }}@endif" />{{ $settings['sitelogo'] }}</span>--}}
                                                    {{--@else--}}
                                                        {{--<span class="name"></span>--}}
                                                    {{--@endif--}}
                                                {{--</span>--}}
                                                {{--<input id="website-logo" type="file" name="settings[sitelogo]" title="Browse">--}}
                                            {{--</span>--}}
                                        {{--</div>--}}
                                        {{--@if (isset($settings['sitelogo']) && ''!=$settings['sitelogo'])--}}
                                            {{--<div class="row-uploads">--}}
                                                {{--<div class="uploads">--}}
                                                    {{--<img src="@if (isset($settings['sitelogo'])){{ $settings['sitelogo'] }}@endif" />--}}
                                                {{--</div>--}}
                                                {{--<div class="field mt-2 field-checkbox checkbox-wrapper">--}}
                                                    {{--<label for="remove-sitelogo">--}}
                                                        {{--<input type="checkbox" value="1" name="page_meta[_remove_sitelogo]" id="remove-sitelogo"/>--}}
                                                        {{--<span class="checkmark"></span>--}}
                                                        {{--Remove Uploaded Image--}}
                                                    {{--</label>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--@endif--}}
                                    {{--</div>--}}


                                    {{--<div class="form-group  has-feedback">--}}
                                            {{--<div class="field field-files field-images">--}}
                                                {{--<label>Website Logo (Mobile)</label>--}}
                                                {{--<span class="wrapper-file-input">--}}
                                                {{--<span class="input-file">--}}
                                                    {{--@if (isset($settings['sitelogo-mobile']) && ''!=$settings['sitelogo-mobile'])--}}

                                                        {{--<span class="name"><img src="@if (isset($settings['sitelogo-mobile'])){{ $settings['sitelogo-mobile'] }}@endif" />{{ $settings['sitelogo-mobile'] }}</span>--}}
                                                    {{--@else--}}
                                                        {{--<span class="name"></span>--}}
                                                    {{--@endif--}}
                                                {{--</span>--}}
                                                {{--<input id="website-logo" type="file" name="settings[sitelogo-mobile]" title="Browse">--}}
                                                {{--</span>--}}
                                            {{--</div>--}}
                                            {{--@if (isset($settings['sitelogo-mobile']) && ''!=$settings['sitelogo-mobile'])--}}
                                                {{--<div class="row-uploads">--}}
                                                    {{--<div class="uploads">--}}
                                                        {{--<img src="@if (isset($settings['sitelogo-mobile'])){{ $settings['sitelogo-mobile'] }}@endif" />--}}
                                                    {{--</div>--}}
                                                    {{--<div class="field field-checkbox">--}}
                                                        {{--<input type="checkbox" value="1" name="page_meta[_remove_sitelogo-mobile]" id="remove-sitelogo-mobile"/>--}}
                                                        {{--<label for="remove-sitelogo-mobile">Remove Uploaded Image</label>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="field mt-2 field-checkbox checkbox-wrapper">--}}
                                                        {{--<label for="remove-sitelogo-mobile">--}}
                                                            {{--<input type="checkbox" value="1" name="page_meta[_remove_sitelogo-mobile]" id="remove-sitelogo-mobile"/>--}}
                                                            {{--<span class="checkmark"></span>--}}
                                                            {{--Remove Uploaded Image--}}
                                                        {{--</label>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--@endif--}}
                                    {{--</div>--}}

                                    <div class="form-group  has-feedback">
                                        <div class="">
                                            <div class="field field-files field-images">
                                                <label>Default Profile Image</label>
                                                <span class="wrapper-file-input">
                                                <span class="input-file">
                                                    @if (isset($settings['default_profile_image']) && ''!=$settings['default_profile_image'])


                                                        <span class="name"><img src="@if (isset($settings['default_profile_image'])){{ $settings['default_profile_image'] }}@endif" />{{ $settings['default_profile_image'] }}</span>
                                                    @else
                                                        <span class="name"></span>
                                                    @endif
                                                </span>
                                                    <input id="default-profile-image" type="file" name="settings[default_profile_image]" title="Browse">
                                                </span>
                                            </div>
                                            @if (isset($settings['default_profile_image']) && ''!=$settings['default_profile_image'])
                                                <div class="row-uploads">
                                                    {{--<div class="uploads">--}}
                                                        {{--<img src="@if (isset($settings['default_profile_image'])){{ $settings['default_profile_image'] }}@endif" />--}}
                                                    {{--</div>--}}
                                                    <div class="field mt-2 field-checkbox checkbox-wrapper">
                                                        <label for="remove-default-profile-image">
                                                            <input type="checkbox" value="1" name="page_meta[_remove_default_profile_image]" id="remove-default-profile-image"/>
                                                            <span class="checkmark"></span>
                                                            Remove Uploaded Image
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="form-group  has-feedback">
                                        <div class="column-half right">
                                            <div class="field field-files field-images">
                                                <label>Default Genre Image</label>
                                                <span class="wrapper-file-input">
                                                <span class="input-file">
                                                    @if (isset($settings['default_genre_image']) && ''!=$settings['default_genre_image'])


                                                        <span class="name"><img src="@if (isset($settings['default_genre_image'])){{ $settings['default_genre_image'] }}@endif" />{{ $settings['default_genre_image'] }}</span>
                                                    @else
                                                        <span class="name"></span>
                                                    @endif
                                                </span>
                                                <input id="default-genre-image" type="file" name="settings[default_genre_image]" title="Browse">
                                                </span>
                                            </div>
                                            @if (isset($settings['default_genre_image']) && ''!=$settings['default_genre_image'])
                                                <div class="row-uploads">
                                                    {{--<div class="uploads">--}}
                                                        {{--<img src="@if (isset($settings['default_genre_image'])){{ $settings['default_genre_image'] }}@endif" />--}}
                                                    {{--</div>--}}
                                                    <div class="field mt-2 field-checkbox checkbox-wrapper">
                                                        <label for="remove-default-genre-image">
                                                            <input type="checkbox" value="1" name="page_meta[_remove_default_genre_image]" id="remove-default-genre-image"/>
                                                            <span class="checkmark"></span>
                                                            Remove Uploaded Image
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group  has-feedback">
                                            <label>This email will get submitted "Contact Us" form data</label>
                                            <input type="text" class="form-control" required name="settings[contact_form_recepients]" value="@if (isset($settings['contact_form_recepients'])){{ $settings['contact_form_recepients'] }}@endif"/>
                                    </div>

                                    <div class="form-group  has-feedback">
                                        <div class="field">
                                            <label >Copyright</label>
                                            <input type="text" class="form-control" name="settings[copyright]" value="@if (isset($settings['copyright'])){{ $settings['copyright'] }}@endif"/>
                                        </div>
                                    </div>

                                    <hr class="hr">

                                    <div class="form-group  has-feedback bottom-row">
                                        <div class="right-buttons">
                                            <input type="submit" class="btn btn-primary btn-submit" value="Save Settings">
                                        </div>
                                    </div>
                                </div>



                        </div>

                        @include('backend.settings.users_and_instructors')
                        @include('backend.settings.merchants')
                        @include('backend.settings.google_analytics')
                        @include('backend.settings.menus')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backend.settings.blank_blocks')

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
	if (document.getElementById('booking-confirmation-text')){
		CKEDITOR.replace( 'booking-confirmation-text', options );
	}
</script>
@endsection

