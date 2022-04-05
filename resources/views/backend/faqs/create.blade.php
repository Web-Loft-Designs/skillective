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
                        {{-- fake emails generated when registration via instagram --}}
                        <div class="form-wrap">
                            <p class="login-box-msg">Create FAQ</p>
                            @include('adminlte-templates::common.errors')
                            {!! Form::open(['route' => 'backend.faqs.store', 'files' => true]) !!}
                            <div class="d-flex flex-wrap">
                                @include('backend.faqs.fields')
                            </div>


                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        if (document.getElementById('faq-content')){
            CKEDITOR.replace( 'faq-content', options );
        }
    </script>
@endsection