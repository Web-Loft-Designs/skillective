@extends('layouts.app-frontend')
@section('content')
    <my-library-player
        :is-instructor="{{ (isset($loggedUserRole) && $loggedUserRole==\App\Models\User::ROLE_INSTRUCTOR) ? 'true' : 'false' }}"
    ></my-library-player>
@endsection