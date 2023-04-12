@extends('layouts.app-frontend')

@section('content')
    <div class="min-h-100">
    <div class="inner login-popup-v2">
        <h1 class="form-title">
            {{ ( 'Welcome, ' . $user['full_name'] . '!' ) }}
            <br/>
            <br/>
            Please set your Skillective password:
        </h1>
        <user-finish-registration-form></user-finish-registration-form>
    </div>
    <img src='http://i.ctnsnet.com/int/integration?pixel=69506234&nid=66354764&cont=i' width='1' height='1' border='0' alt=''>
    </div>
@endsection
