<div class="table-responsive">
    <table class="table" id="profiles-table">
        <thead>
            <tr>
                <th>User Id</th>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Instagram Handle</th>
                <th>Mobile Phone</th>
                <th>Avatar</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
            <td>{!! $user->id !!}</td>
            <td>{!! $user->email !!}</td>
            <td>{!! $user->first_name !!}</td>
            <td>{!! $user->last_name !!}</td>
            <td><a href="https://www.instagram.com/{{ $user->profile->instagram_handle }}" target="_blank">{{ $user->profile->instagram_handle }}</a></td>
            <td>{!! $user->profile->mobile_phone !!}</td>
            <td><img src="{{ $user->profile->getImageUrl() }}" class="img-circle" alt="User Image" width="40"/></td>
                <td class="action">
                    <div class="dropdown">
                        <a href="{!! route('backend.users.show', [$user->id]) !!}" class='btn btn-left'>View</a>
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{!! route('backend.users.edit', [$user->id]) !!}" >Edit profile</a>
                            <a class="dropdown-item" href="#">Contact</a>
                            <a class="dropdown-item" href="#">Lessons</a>
                            <a class="dropdown-item" href="#">Invited instructors</a>
                            <a class="dropdown-item red" href="#">Suspend</a>
                        </div>
                    </div>

                    {{--<a href="{!! route('backend.users.create-merchant', [$user->id]) !!}" class='btn btn-default btn-xs'>Create Merchant Account</a>--}}

                    {{--@if($user->status!='on_review')--}}
                        {{--{!! Form::open(['route' => ['backend.users.destroy', $user->id], 'method' => 'delete']) !!}--}}
                            {{--{!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}--}}
                        {{--{!! Form::close() !!}--}}
                    {{--@else--}}
                        {{--{!! Form::open(['route' => ['backend.users.approve', $user->id], 'method' => 'patch']) !!}--}}
                            {{--{!! Form::button('<i class="glyphicon glyphicon-ok"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}--}}
                        {{--{!! Form::close() !!}--}}

                        {{--{!! Form::open(['route' => ['backend.users.deny', $user->id], 'method' => 'patch']) !!}--}}
                            {{--{!! Form::button('<i class="glyphicon glyphicon-remove"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}--}}
                        {{--{!! Form::close() !!}--}}
                    {{--@endif--}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
