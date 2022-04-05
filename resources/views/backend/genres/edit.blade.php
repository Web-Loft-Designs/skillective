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
                            <p class="login-box-msg">Edit Genre</p>
                            @include('adminlte-templates::common.errors')
                            {!! Form::model($genre, ['route' => ['backend.genres.update', $genre->id], 'method' => 'patch', 'files' => true]) !!}
                            <div class="d-flex flex-wrap">
                                @include('backend.genres.fields')
                            </div>


                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection