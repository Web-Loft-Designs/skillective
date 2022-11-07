@extends('layouts.app-frontend')
@section('content')
    <discount-management :instructor-id="{{ $user['id'] }}"></discount-management>
@endsection
