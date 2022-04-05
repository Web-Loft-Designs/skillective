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

                    <form method="post" action="{{ url('/password/reset') }}">
                        {!! csrf_field() !!}
                        <p class="login-box-msg text-left" style="text-align: left;padding: 0 10px 15px;">Reset your password</p>
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="d-flex flex-wrap">
                            <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                                @endif
                            </div>

                            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" class="form-control" name="password" placeholder="Password">


                                @if ($errors->has('password'))
                                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                                @endif
                            </div>

                            <div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn ">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>

            </div>

        </div>
     </div>

@endsection