@extends('layouts.app-backend')

@section('content')
    <div class="table-page admin-dashboard">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <users-list
                            :users="{{ json_encode($users['data']) }}"
                            :users-meta="{{ isset($users['meta'])?json_encode($users['meta']):'[]' }}"></users-list>
                </div>
            </div>
        </div>
    </div>
@endsection

