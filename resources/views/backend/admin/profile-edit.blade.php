@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12" sticky-container>
                    <div class="sticky-nav"  v-sticky="true" sticky-offset="{top: 100, bottom: 20}">
                        <h2>Settings</h2>
                        <ul >
                            <li class="active"><a class="scroll-to" href="#data-update-trigger">Personal info</a></li>
                            <li><a class="scroll-to" href="#password-change-trigger">Change password</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        <div class="form-wrap" id="data-update-trigger">
                            <admin-profile-data-update-form
                                    v-bind:us-states="{{  json_encode($usStates) }}"
                                    v-bind:user-profile-data="{{  json_encode($userProfileData) }}"
                            ></admin-profile-data-update-form>
                        </div>
                        <div class="form-wrap" id="password-change-trigger">
                            <profile-password-change></profile-password-change>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
