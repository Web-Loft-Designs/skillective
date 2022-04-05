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
                            @include('adminlte-templates::common.errors')
                            {!! Form::open(['route' => 'backend.testimonials.store', 'files' => true]) !!}
                                <div class="d-flex flex-wrap">
                                    @include('backend.testimonials.fields')
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            {{--<section class="content-header">--}}
                {{--<h2 class="card-title">{{ $page_title }}</h2>--}}
            {{--</section>--}}
            {{--<div class="content">--}}
                {{--@include('adminlte-templates::common.errors')--}}
                {{--<div class="box box-primary">--}}

                    {{--<div class="box-body">--}}
                        {{--<div class="row">--}}
                            {{--{!! Form::open(['route' => 'backend.testimonials.store', 'files' => true]) !!}--}}

                            {{--@include('backend.testimonials.fields')--}}

                            {{--{!! Form::close() !!}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
@endsection