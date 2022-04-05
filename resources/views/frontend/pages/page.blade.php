@extends('layouts.app-frontend')

@section('content')
    @if ($page->template!='')
        @include('frontend.pages.' . $page->template)
    @else
        @include('frontend.pages.content-default')
    @endif
@endsection
