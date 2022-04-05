@extends('layouts.app-frontend')

@section('content')
<div class="wrapper">
        <instructors-search-results-list
                v-bind:instructors="{{ json_encode($instructors['data']) }}"
                v-bind:instructors-meta="{{ isset($instructors['meta'])?json_encode($instructors['meta']):json_encode([]) }}"
                v-bind:site-genres="{{  json_encode($genres) }}"
        ></instructors-search-results-list>
</div>
@endsection
