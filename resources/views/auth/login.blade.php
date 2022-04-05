@extends('layouts.app-backend')

@section('content')
    <div class="login-popup-v2">
        <h3>Admin Login</h3>
        <form method="post" action="{{ route('login') }}">
            {!! csrf_field() !!}

            <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                <label>Email address</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="your@email.com">

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <label>Password <a href="/password/reset">Forgot password?</a></label>
                <input type="password" class="form-control" placeholder="Enter your password" name="password">
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Login</button>
            </div>
            <div class="separator"></div>
            <div class="d-flex flex-wrap">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection