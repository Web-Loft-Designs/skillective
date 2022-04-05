<div class="table-responsive">
    <table class="table table-custom table-btn-drop" id="genres-table">
        <thead>
            <tr>
                <th>#</th>
                <th></th>
                <th>Genre name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($genres as $genre)
            <tr>
                <td>{!! $genre->id !!}</td>
                <td><img width="50px" src="{{ $genre->getImageUrl() }}" width="100px"></td>
                <td class="white-nowrap">{!! $genre->title !!}</td>
{{--            <td>{!! $genre->is_featured?'Yes':'No' !!}</td>--}}
                <td class="action text-right">
                    {!! Form::open(['route' => ['backend.genres.destroy', $genre->id], 'method' => 'delete']) !!}
                    <div class="dropdown">
                        {{--<a href="{!! route('backend.genres.show', [$genre->id]) !!}" class="btn btn-left">View</a>--}}
                        <a href="{!! route('backend.genres.edit', [$genre->id]) !!}" class="btn btn-left">Edit</a>
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            {{--<a class="dropdown-item" href="{!! route('backend.genres.edit', [$genre->id]) !!}">Edit</a>--}}
                            <button type="submit" onclick="return confirm('Are you sure?')" class="dropdown-item red" href="#">Disable</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
