@extends('layouts.app-frontend')
@section('content')
    <div class="dashboard-cart-page">
        <div class="container dashboard-cart">
            <div class="row">

                <div class="col-12">
                    <cart :total="{{ json_encode($total) }}"></cart>
                </div>
                
            </div>
        </div>
    </div>
@endsection