@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="sticky-nav" >
                        <h2>Site Email/SMS Notifications</h2>
                        <ul >
                            <li><a href="{{ route('backend.settings') }}">General</a></li>
                            <li class="active"><a href="#">Notifications</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        {{-- fake emails generated when registration via instagram --}}
                        <div class="form-wrap">
                            <div class="row table-row table-simplified table-admin table-users-list">
                                <p class="login-box-msg col-12">Title</p>
                                <table class="table-custom col-12 table">


                                    <tbody>

                                    <tr class="filter-row">

                                    @forelse ($collection as $resource)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $resource->tag)) }}</td>
                                            <td>
                                                <div class="row buttons-cell" style="justify-content: flex-end">
                                                    <a href="{{ route('backend.notifications.edit', $resource) }}" class="icon icon-edit">EDIT</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">
                                                <h5> No items found</h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $collection->links() }}

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--<div class="inner inner-space">--}}
        {{--<div class="card card-admin">--}}
            {{--<div class="card-header">--}}
                {{--<h2 class="card-title">Site Email/SMS Notifications</h2>--}}
            {{--</div>--}}

            {{--<div class="form-wrap">--}}
                {{--<div class="row table-row table-simplified table-admin table-users-list">--}}
                {{--<table>--}}

                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th><lable>Title</lable></th>--}}
                        {{--<th width="15%"></th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}

                    {{--<tbody>--}}

                    {{--<tr class="filter-row">--}}

                    {{--@forelse ($collection as $resource)--}}
                        {{--<tr>--}}
                            {{--<td>{{ ucfirst(str_replace('_', ' ', $resource->tag)) }}</td>--}}
                            {{--<td>--}}
                                {{--<div class="row buttons-cell" style="justify-content: flex-end">--}}
                                    {{--<a href="{{ route('backend.notifications.edit', $resource) }}" class="icon icon-edit">EDIT</a>--}}
                                {{--</div>--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                    {{--@empty--}}
                        {{--<tr>--}}
                            {{--<td colspan="2">--}}
                                {{--<h5> No items found</h5>--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                    {{--@endforelse--}}
                    {{--</tbody>--}}
                {{--</table>--}}
                {{--</div>--}}
                {{--{{ $collection->links() }}--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection