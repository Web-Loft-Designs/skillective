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
                        <div class="form-wrap">
                            <p class="login-box-msg">Testimonials <span>({{ $items->total() }})</span></p>
                            <div class="p-10">
                                <div class="d-flex responsive-mobile justify-content-between align-items-center">
                                    <div class="d-flex search-input responsive-mobile align-items-center">
                                        {{--<input type="text" placeholder="Search client"/>--}}
                                    </div>
                                    <div>
                                        <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('backend.testimonials.create') !!}">
                                            <img width="17" style="margin-right: 3px;" src="{{ asset('images/ic_person_add-ic_add.png') }}" alt=""> Add New</a>
                                    </div>
                                </div>
                                @include('flash::message')
                                @include('backend.testimonials.table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--<section class="content-header">--}}
        {{--<h1 class="pull-left">Testimonials <span class="label-info">({{ $items->total() }})</span></h1>--}}
        {{--<h1 class="pull-right">--}}
            {{--<a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('backend.testimonials.create') !!}">Add New</a>--}}
        {{--</h1>--}}
    {{--</section>--}}
    {{--<div class="content">--}}
        {{--<a href="{{ route(Route::current()->getName(), $filterValues) }}" id="current-list-url"></a>--}}
        {{--<div class="clearfix"></div>--}}

        {{--@include('flash::message')--}}

        {{--<div class="clearfix"></div>--}}
        {{--<div class="box box-primary">--}}
            {{--<div class="box-body">--}}
                {{--@include('backend.testimonials.table')--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="text-center">--}}

        {{--</div>--}}
    {{--</div>--}}

@endsection