@extends('layouts.app-backend')

@section('content')
    <div class="table-page admin-dashboard instructor-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <send-notification-form :is-student="true" class="notification-form-simple" :mode="'simple'" v-bind:available-notification-methods="null"></send-notification-form>

                    <backend-instructors-list
                            :instructors="{{ json_encode($instructors['data']) }}"
                            :instructors-meta="{{ isset($instructors['meta'])?json_encode($instructors['meta']):'[]' }}"
                            @if(isset($invitedByInstagramHandle))
                            :invited-by-instagram-handle="'{{ $invitedByInstagramHandle }}'"
                            @endif
                    ></backend-instructors-list>
                </div>
            </div>
        </div>
    </div>
@endsection

