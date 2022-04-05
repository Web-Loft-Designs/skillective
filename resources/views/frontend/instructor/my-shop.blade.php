@extends('layouts.app-frontend')
@section('content')
    <instructor-my-shop 
        v-bind:user-genres="{{  json_encode($userGenres) }}"
    ></instructor-my-shop>
@endsection
