<!-- Title Field -->
<div class="form-group ">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => 'form-control']) !!}
</div>

<!-- Image Field -->
<div class="form-group ">
    <div class="field field-files field-images">
        {!! Form::label('image', 'Image:') !!}
        <span class="wrapper-file-input">
        <span class="input-file">
            @if (isset($genre) && $genre->id)
                <span class="name"><img src="@if (isset($genre) && $genre->id){{ $genre->getImageUrl() }}@endif" /> {{ $genre->getImageUrl() }}</span>
            @else
                <span class="name"></span>
            @endif
        </span>
            {!! Form::file('image', null, ['class' => 'form-control']) !!}
    </span>
    </div>
    {{--@if (isset($genre) && $genre->id)--}}
        {{--<div class="row-uploads">--}}
            {{--<div class="uploads">--}}
                {{--<img src="@if (isset($genre) && $genre->id){{ $genre->getImageUrl() }}@endif" />--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--@endif--}}
</div>

<div class="form-group w-50">
    <label for="page-parent" >Category</label>
    <select name="genre_category_id" id="genre-category" class="form-control">
        @if(!isset($genre) || $genre->genre_category_id==null)
            <option value="">Select</option>
        @endif
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @if( (old('genre_category_id') && old('genre_category_id')==$category->id) || ( !old('genre_category_id') && isset($genre) && $genre->genre_category_id==$category->id) ){{ ' selected ' }}@endif>{{ $category->title }}</option>
        @endforeach
    </select>
</div>

{{--<div class="form-group col-sm-6">--}}
    {{--{!! Form::label('image', 'Image:') !!}--}}
    {{--{!! Form::file('image', null, ['class' => 'form-control']) !!}--}}
    {{--@if (isset($genre) && $genre->id)--}}
        {{--<img src="{{ $genre->getImageUrl() }}">--}}
    {{--@endif--}}
{{--</div>--}}

<!-- Is Featured Field -->
<div class="form-group">
    <div class="field mt-2 field-checkbox checkbox-wrapper">
        {!! Form::hidden('is_featured', 0) !!}
        <label>
            {!! Form::checkbox('is_featured', '1', null) !!}
            <span class="checkmark"></span>
            Is Featured?
        </label>
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('backend.genres.index') !!}" class="btn btn-secondary">Cancel</a>
</div>
