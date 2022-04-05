@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit report-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    @include('backend.reports.partials.menu')
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Geographics</h3>
                        <a href="{{ route('backend.reports.geographics.export') }}" class="btn-green">Export</a>
                    </div>
                    <map-report></map-report>
                    <backend-geographic-report
                            :report-data="{{ json_encode($reportData) }}"
                    ></backend-geographic-report>
                </div>
            </div>
        </div>
    </div>

@endsection

