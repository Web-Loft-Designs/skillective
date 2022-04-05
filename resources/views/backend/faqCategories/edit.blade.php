@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit faqs-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    @include('include.backend-content-menu')
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        <div class="form-wrap">
                            <p class="login-box-msg">Edit FAQ Category</p>
                            @include('adminlte-templates::common.errors')
                            {!! Form::model($faqCategory, ['route' => ['backend.faq-categories.update', $faqCategory->id], 'method' => 'patch', 'files' => true]) !!}
                            <div class="d-flex flex-wrap">
                                @include('backend.faqCategories.fields')
                            </div>


                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection