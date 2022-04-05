@extends('layouts.app-backend')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/tabs.css') }}">
@endpush
@section('content')
    <div class="profile-section-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="sticky-nav" >
                        <h2>Site Email/SMS Notifications</h2>
                        <ul >
                            <li><a href="{{ route('backend.settings') }}">General</a></li>
                            <li class="active"><a href="#">Notifications</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        {{-- fake emails generated when registration via instagram --}}
                        <div class="form-wrap">
                            <p class="login-box-msg">{{ ucwords(str_replace('_', ' ', $resource->tag)) }}</p>
                            @include('common.errors')
                            <form action="{{ route('backend.notifications.update', $resource) }}" method="POST" class="form-vertical"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')

                                {{--<h2>{{ ucwords(str_replace('_', ' ', $resource->tag)) }}</h2>--}}

                                <div class="form-group row">
                                    <div class="col-sm-12" id="addTabContent">
                                        <div class="tabs">
                                            <div class="top-line">
                                                <ul class="tabs-nav" id="nav-tab" role="tablist">
                                                    <li class="" data-page="0">Email</li>
                                                    @if ( !in_array($resource->tag, $tagsWithoutSms) )
                                                        <li data-page="1" class="active">SMS</li>
                                                    @endif
                                                </ul>
                                            </div>

                                            <style>
                                                .d-none{
                                                    display: none;
                                                }
                                            </style>
                                            <div id="nav-tabContent"  class="tabs-wrap">
                                                <div class="tab" data-tab="general" style="display: none;">
                                                    <div class="field field-checkbox">
                                                        <input type="hidden" name="mail[checked]">
                                                        <input name="mail[checked]" type="checkbox" id="Emailcheckbox"
                                                               @if($resource->methods()->active()->pluck('method')->contains('mail') || (bool) old('mail.checked')) checked
                                                               @endif class="activatedInput email-editor">
                                                        <label for="Emailcheckbox">Active Email</label>
                                                    </div>
                                                    <div class="content-tabs">
                                                        <div class="form-group">
                                                            <input type="text" name="mail[subject]" class="form-control"
                                                                   value="{{ old('mail.subject') ?? $resource->methods->where('method', 'mail')->first()->data['subject'] ?? '' }}">
                                                        </div>
                                                        <div class="form-group">
                                    <textarea name="mail[content]" type="text"
                                              class="form-control TrumbowygEditor">{{ old('mail.content') ?? $resource->methods->where('method', 'mail')->first()->content ?? '' }}</textarea>
                                                            <small>
                                                                Available variables: {{ implode(', ', $resource->data['available_vars']) }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="tab" data-tab="footer" style="display: @if ( !in_array($resource->tag, $tagsWithoutSms) ){{ 'block' }}@else{{'none'}}@endif;">
                                                    <div class="field field-checkbox">
                                                        <input type="hidden" name="sms[checked]">
                                                        <input name="sms[checked]" type="checkbox" id="Smscheckbox"
                                                               @if(old('sms.checked') || $resource->methods()->active()->pluck('method')->contains('sms')) checked
                                                               @endif class="activatedInput">
                                                        <label for="Smscheckbox">Active SMS</label>
                                                    </div>
                                                    <div class="content-tabs">
                                                        <div class="form-group">
                                                            <textarea name="sms[content]" class="form-control" rows="30">{{ old('sms.content') ?? $resource->methods->where('method', 'sms')->first()->content ?? '' }}</textarea>
                                                            <small>
                                                                Available variables: {{ implode(', ', $resource->data['available_vars']) }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="hr">

                                <div class="form-group  has-feedback bottom-row">
                                    <div class="right-buttons">
                                        <button class="btn btn-primary btn-submit" type="submit">Update</button>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--<div class="inner inner-space">--}}
        {{--<div class="card card-admin">--}}
        {{--@include('common.errors')--}}

        {{--<form action="{{ route('backend.notifications.update', $resource) }}" method="POST" class="form-vertical"--}}
              {{--enctype="multipart/form-data">--}}
            {{--@csrf--}}
            {{--@method('put')--}}

            {{--<h2>{{ ucwords(str_replace('_', ' ', $resource->tag)) }}</h2>--}}

            {{--<div class="form-group row">--}}
                {{--<div class="col-sm-12" id="addTabContent">--}}
                    {{--<div class="tabs">--}}
                        {{--<div class="top-line">--}}
                            {{--<ul class="tabs-nav" id="nav-tab" role="tablist">--}}
                                {{--<li class="" data-page="0">Email</li>--}}
                                {{--@if ( !in_array($resource->tag, $tagsWithoutSms) )--}}
                                {{--<li data-page="1" class="active">SMS</li>--}}
                                {{--@endif--}}
                            {{--</ul>--}}
                        {{--</div>--}}

                        {{--<style>--}}
                            {{--.d-none{--}}
                                {{--display: none;--}}
                            {{--}--}}
                        {{--</style>--}}
                        {{--<div id="nav-tabContent"  class="tabs-wrap">--}}
                            {{--<div class="tab" data-tab="general" style="display: none;">--}}
                                {{--<div class="field field-checkbox">--}}
                                    {{--<input type="hidden" name="mail[checked]">--}}
                                    {{--<input name="mail[checked]" type="checkbox" id="Emailcheckbox"--}}
                                           {{--@if($resource->methods()->active()->pluck('method')->contains('mail') || (bool) old('mail.checked')) checked--}}
                                           {{--@endif class="activatedInput email-editor">--}}
                                    {{--<label for="Emailcheckbox">Active Email</label>--}}
                                {{--</div>--}}
                                {{--<div class="content-tabs">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<input type="text" name="mail[subject]" class="form-control"--}}
                                               {{--value="{{ old('mail.subject') ?? $resource->methods->where('method', 'mail')->first()->data['subject'] ?? '' }}">--}}
                                    {{--</div>--}}
                                    {{--<div class="form-group">--}}
                                    {{--<textarea name="mail[content]" type="text"--}}
                                              {{--class="form-control TrumbowygEditor">{{ old('mail.content') ?? $resource->methods->where('method', 'mail')->first()->content ?? '' }}</textarea>--}}
                                        {{--<small>--}}
                                            {{--Available variables: {{ implode(', ', $resource->data['available_vars']) }}--}}
                                        {{--</small>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}


                                {{--<div class="tab" data-tab="footer" style="display: @if ( !in_array($resource->tag, $tagsWithoutSms) ){{ 'block' }}@else{{'none'}}@endif;">--}}
                                    {{--<div class="field field-checkbox">--}}
                                        {{--<input type="hidden" name="sms[checked]">--}}
                                        {{--<input name="sms[checked]" type="checkbox" id="Smscheckbox"--}}
                                               {{--@if(old('sms.checked') || $resource->methods()->active()->pluck('method')->contains('sms')) checked--}}
                                               {{--@endif class="activatedInput">--}}
                                        {{--<label for="Smscheckbox">Active SMS</label>--}}
                                    {{--</div>--}}
                                    {{--<div class="content-tabs">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<textarea name="sms[content]" class="form-control" rows="30">{{ old('sms.content') ?? $resource->methods->where('method', 'sms')->first()->content ?? '' }}</textarea>--}}
                                            {{--<small>--}}
                                                {{--Available variables: {{ implode(', ', $resource->data['available_vars']) }}--}}
                                            {{--</small>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<button class="btn pull-right" type="submit">Update</button>--}}
        {{--</form>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection