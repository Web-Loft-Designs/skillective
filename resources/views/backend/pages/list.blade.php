@extends('layouts.app-backend')

@section('content')
    <div class="profile-section-edit genres-edit">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12" sticky-container>
                    @include('include.backend-content-menu')
                </div>
                <div class="col-lg-9  col-md-8 col-12">
                    <div class="profile-info">
                        <div class="form-wrap">
                            <p class="login-box-msg">Pages<span class="ml-1">({{ $pages->total() }})</span></p>
                            <div class="p-10">
                                <div class="d-flex responsive-mobile justify-content-between align-items-center">
                                    <div class="d-flex search-input responsive-mobile align-items-center">
                                        <input type="text" placeholder="Search page"/>
                                    </div>
                                    <div>
                                        <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('backend.page.create') !!}">
                                            <img width="17" style="margin-right: 3px;" src="{{ asset('images/ic_person_add-ic_add.png') }}" alt=""> Add New</a>
                                    </div>
                                </div>
                                @include('flash::message')
                                <div class="table-responsive">
                                    <table class="table table-custom">
                                        <thead>
                                        <tr>
                                            <th>
                                                <label for="page-title">Title</label>

                                            </th>
                                            <th>
                                                <label for="page-name">Name</label>

                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($pages) > 0)
                                            @foreach ($pages as $item)
                                                <tr>
                                                    <td class="table-text white-nowrap">
                                                        <div>{{ $item->title }}</div>
                                                    </td>
                                                    <td class="table-text white-nowrap">
                                                        <div>{{ $item->name }}</div>
                                                    </td>
                                                    <td class="action text-right">
                                                        <div class="row buttons-cell justify-content-end">
                                                            <a href="{{ route('backend.page.edit', $item->id) }}" class="icon icon-edit">Edit</a>
                                                            @if ($item->id>15)
                                                                <form action="{{ route('backend.page.delete', $item->id) }}" method="POST" class="delete-item-form delete-item-form-{{ $item->id }}">
                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                    @include('backend.includes.delete-button-with-confirmation', ['onConfirmSelector'=>'.delete-item-form-' . $item->id])
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="3"><h5> No items found</h5></td></tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                @include('backend.pagination.default', ['paginator' => $pages, 'sortVars'=>$filterValues]) </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection