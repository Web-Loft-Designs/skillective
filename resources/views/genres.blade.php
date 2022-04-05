@extends('layouts.app-frontend')

@section('content')
<div class="container">
    <div class="row">

        <h3>All Genres</h3>
        <ul>
            @foreach($genres as $genre)
                <li>{{ $genre['title'] }} <img src="{{ $genre['image'] }}" width="40"/></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
