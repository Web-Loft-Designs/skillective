@extends('layouts.app-frontend')

@section('content')
    <div class="login-popup-v2">
        <front-login-form
                {{--:login-using-instagram-url="'{{ route('social.redirect',['provider' => 'ig']) }}'"--}}
                :login-using-google-url="'{{ route('social.redirect',['provider' => 'google']) }}'"
                :login-using-facebook-url="'{{ route('social.redirect',['provider' => 'facebook']) }}'"
        ></front-login-form>

    </div>
@endsection