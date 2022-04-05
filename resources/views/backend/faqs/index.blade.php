@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit faqs-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12" sticky-container>
                    @include('include.backend-content-menu')
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        <div class="form-wrap table-page admin-dashboard p-15">
                            <backend-faqs-list
                                    :faqs="{{ json_encode($faqs['data']) }}"
                                    :faqs-meta="{{ isset($faqs['meta'])?json_encode($faqs['meta']):'[]' }}"></backend-faqs-list>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

