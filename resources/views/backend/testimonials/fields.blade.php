<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required' => true]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('instagram_handle', 'Instagram Handle:') !!}
    {!! Form::text('instagram_handle', null, ['class' => 'form-control']) !!}
</div>

<!-- Image Field -->
<div class="form-group col-sm-6">
    {{--{!! Form::label('image', 'Image:') !!}--}}

    <div class="field field-files field-images">
        {!! Form::label('image', 'Image:') !!}
        <span class="wrapper-file-input">
        <span class="input-file">
            @if (isset($testimonial) && $testimonial->id)
                <span class="name">
                    <img src="{{ $testimonial->getImageUrl() }}" /> {{ $testimonial->getImageUrl() }}</span>
            @else
                <span class="name"></span>
            @endif
        </span>
            {!! Form::file('image', null, ['class' => 'form-control', 'required' => true]) !!}
    </span>
    </div>
    {{--{!! Form::file('image', null, ['class' => 'form-control', 'required' => true]) !!}--}}
    {{--@if (isset($testimonial) && $testimonial->id)--}}
        {{--<img src="{{ $testimonial->getImageUrl() }}">--}}
    {{--@endif--}}
</div>

<div class="form-group col-sm-2">
    {!! Form::label('position', 'Position:') !!}
    {!! Form::number('position', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('content', 'Content:') !!}
    {!! Form::textarea('content', null, ['class' => 'form-control', 'id' => 'testimonial-content', 'rows' => 2]) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('backend.testimonials.index') !!}" class="btn btn-secondary">Cancel</a>
</div>
