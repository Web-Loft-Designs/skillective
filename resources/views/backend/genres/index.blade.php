@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit genres-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12" sticky-container>
                    @include('include.backend-content-menu')
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        {{-- fake emails generated when registration via instagram --}}
                        <div class="form-wrap table-page admin-dashboard p-15">
                            {{--<p class="login-box-msg">Genres</p>--}}
                            {{--<div class="p-10">--}}
                                {{--<div class="d-flex responsive-mobile justify-content-between align-items-center">--}}
                                    {{--<div class="d-flex search-input responsive-mobile align-items-center">--}}
                                        {{--<input type="text" placeholder="Search genre"/>--}}
                                    {{--</div>--}}
                                    {{--<div>--}}
                                        {{--<a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('backend.genres.create') !!}">--}}
                                            {{--<img width="17" style="margin-right: 3px;" src="{{ asset('images/ic_person_add-ic_add.png') }}" alt=""> Add New</a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--@include('flash::message')--}}
{{--                                @include('backend.genres.table')--}}
                            {{--</div>--}}
                            <backend-genres-list
                                    :genres="{{ json_encode($genres['data']) }}"
                                    :genres-meta="{{ isset($genres['meta'])?json_encode($genres['meta']):'[]' }}"></backend-genres-list>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

