@extends('layouts.app-frontend')

@section('content')
<div class="schedule-page student-schedule-page">
    <div class="container">
        <div class="row">
            <calendar-booked-lessons
                    :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"
            ></calendar-booked-lessons>
        </div>
    </div>
</div>
@endsection
