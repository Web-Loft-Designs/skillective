@extends('layouts.app-frontend')
@section('content')
    <my-library
        :logged-in-as-student="{{ isset($loggedUserRole) && $loggedUserRole == \App\Models\User::ROLE_STUDENT ? 'true' : 'false' }}">
    ></my-library>
@endsection