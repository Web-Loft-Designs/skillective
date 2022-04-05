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
                            <p class="login-box-msg">Create Genre Category</p>
                            @include('adminlte-templates::common.errors')
                            {!! Form::open(['route' => 'backend.genre-categories.store', 'files' => true]) !!}
                            <div class="d-flex flex-wrap">
                                @include('backend.genreCategories.fields')
                            </div>


                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--<section class="content-header">--}}
        {{--<h1>--}}
            {{--Genre--}}
        {{--</h1>--}}
    {{--</section>--}}
    {{--<div class="content">--}}
        {{--@include('adminlte-templates::common.errors')--}}
        {{--<div class="box box-primary">--}}

            {{--<div class="box-body">--}}
                {{--<div class="row">--}}
                    {{--{!! Form::open(['route' => 'backend.genres.store', 'files' => true]) !!}--}}

                        {{--@include('backend.genres.fields')--}}

                    {{--{!! Form::close() !!}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection
