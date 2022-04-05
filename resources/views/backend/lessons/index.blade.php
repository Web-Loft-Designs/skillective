@extends('layouts.app-backend')

@section('content')
        <div class="table-page admin-dashboard lessons-page">
                <div class="container">
                        <div class="row">
                                <div class="col-12">
                                        <backend-lessons-list
                                                v-bind:lessons="{{ json_encode($lessons['data']) }}"
                                                v-bind:lessons-meta="{{ isset($lessons['meta'])?json_encode($lessons['meta']):'[]' }}"
                                        ></backend-lessons-list>
                                </div>
                        </div>
                </div>
        </div>
@endsection

