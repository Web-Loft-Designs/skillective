@extends('layouts.app-frontend')

@section('content')
    <style>
        .panding-popup {
            display: none !important;
        }
    </style>
<div class="min-h-100">
    <div class="inner login-popup-v2">
        <h1 class="form-title">{{ config('app.name') }}</h1>
        <div id="password-form-container ">

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="post" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}
                <p class="login-box-msg" style="text-align: left;padding: 0 10px 15px;">Enter Email to reset password</p>
                <div class="d-flex flex-wrap">
                    <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
                        @if ($errors->has('email'))
                            <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn">
                            Send Password Reset Link
                        </button>
                    </div>
                </div>


            </form>

        </div>

    </div>
</div>
@endsection