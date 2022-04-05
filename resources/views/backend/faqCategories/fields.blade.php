<!-- Title Field -->
<div class="row">
    <div class="col-md-8">
    <div class="form-group">
        {!! Form::label('title', 'Title:') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
        {!! Form::label('position', 'Position:') !!}
        {!! Form::number('position', null, ['class' => 'form-control']) !!}
    </div>
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('backend.genre-categories.index') !!}" class="btn btn-secondary">Cancel</a>
</div>
