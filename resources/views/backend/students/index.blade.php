@extends('layouts.app-backend')

@section('content')
    <div class="table-page admin-dashboard">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <send-notification-form :is-student="true" class="notification-form-simple" :mode="'simple'" v-bind:available-notification-methods="null"></send-notification-form>

                    <backend-students-list
                            :students="{{ json_encode($students['data']) }}"
                            :students-meta="{{ isset($students['meta'])?json_encode($students['meta']):'[]' }}"></backend-students-list>
                </div>
            </div>
        </div>
    </div>
@endsection

