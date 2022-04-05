@extends('layouts.app-fullscreen-content')

@section('content')
    <div id="chat-page-container">
        @if(isset($roomError) && $roomError)
            <h1>{{$roomError}}</h1>
        @else
            <Room :lesson-id="{{ $lessonId }}"></Room>
        @endif
    </div>
@endsection

