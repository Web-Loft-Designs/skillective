<!-- Title Field -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('title', 'Title:') !!}
            {!! Form::text('title', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group ">
            <label for="page-parent" >Category</label>
            <select name="faq_category_id" id="faq-category" class="form-control">
                @if(!isset($faq) || $faq->faq_category_id==null)
                    <option value="">Select</option>
                @endif
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @if( (old('faq_category_id') && old('faq_category_id')==$category->id) || ( !old('faq_category_id') && isset($faq) && $faq->faq_category_id==$category->id) ){{ ' selected ' }}@endif>{{ $category->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('video_url', 'Video URL:') !!}
            {!! Form::text('video_url', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="field field-files field-images">
                {!! Form::label('file', 'Attachment:') !!}
                <span class="wrapper-file-input">
                    <span class="input-file">
                        <span class="name"></span>
                    </span>
                    {!! Form::file('file', null, ['class' => 'form-control']) !!}
                </span>
                @if(isset($faq) && ($file=$faq->getAttachment())!=null)
                    <a href="{{ $file->getUrl() }}" target="_blank">{{ $file->file_name }}</a>

                    <div class="field mt-2 field-checkbox checkbox-wrapper">
                        <label for="remove-uploaded">
                            <input type="checkbox" value="1" name="remove_uploaded" id="remove-uploaded"/>
                            <span class="checkmark"></span>
                            Remove Uploaded File
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="page-content" >Content</label>
            <textarea class="form-control" name="content" id="faq-content" class="add-wysiwyg">{{ old('content')?old('content'):isset($faq)?$faq->content:'' }}</textarea>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('position', 'Position:') !!}
            {!! Form::number('position', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('backend.faqs.index') !!}" class="btn btn-secondary">Cancel</a>
    </div>
</div>
