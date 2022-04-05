@extends('layouts.app-backend')

@section('content')
<div class="admin-dashboard">
    <div class="container">
        <div class="row">

            <div class="col-lg-5 col-12">
                <backend-overview-report
                        :report-data="{{ json_encode($overviewWidgetData) }}"
                ></backend-overview-report>
            </div>
            <div class="col-lg-7 col-12">
                <chart-admin-dashboard :is-dashboard="true"></chart-admin-dashboard>
            </div>
            <div class="col-12">
                <div class="clients-table">
                    <backend-lessons-dashboard-list

                    ></backend-lessons-dashboard-list>
                    <a href="/backend/lessons" class="btn btn-block btn-secondary">View all</a>
                </div>
            </div>
            <div class="col-12">
                <div class="clients-table">
                    <send-notification-form :is-student="true" class="notification-form-simple" :mode="'simple'" v-bind:available-notification-methods="{{  json_encode($availableNotificationMethods) }}"></send-notification-form>
                    <backend-students-dashboard-list
                            :students="{{ json_encode($students['data']) }}"
                            :students-meta="{{ isset($students['meta'])?json_encode($students['meta']):'[]' }}"
                    ></backend-students-dashboard-list>
                </div>
            </div>
            <div class="col-12">
                <div class="clients-table">
                    <backend-instructors-dashboard-list
                            :instructors="{{ json_encode($instructors['data']) }}"
                            :instructors-meta="{{ isset($instructors['meta'])?json_encode($instructors['meta']):'[]' }}"
                    ></backend-instructors-dashboard-list>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
