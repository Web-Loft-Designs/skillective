@extends('layouts.app-backend')

@section('content')
    <div class="table-page admin-dashboard payment-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <backend-payments-list
                            v-bind:payments="{{ json_encode($bookings['data']) }}"
                            v-bind:payments-meta="{{ isset($bookings['meta'])?json_encode($bookings['meta']):'[]' }}"
                    ></backend-payments-list>
                </div>
            </div>
        </div>
    </div>
@endsection

