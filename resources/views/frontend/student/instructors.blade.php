@extends('layouts.app-frontend')

@section('content')
    <div class="table-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    {{--<p class="login-box-msg">Client Instructors</p>--}}
                    <student-instructors-list v-bind:instructors="{{ json_encode($instructors['data']) }}" v-bind:instructors-meta="{{ isset($instructors['meta'])?json_encode($instructors['meta']):'[]' }}"></student-instructors-list>
                </div>
            </div>
        </div>
    </div>
@endsection
