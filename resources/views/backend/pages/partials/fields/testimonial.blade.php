<div  class="form-group  mt-4">
    <h4>Testimonial</h4>
</div>
<div class="form-group col-sm-6">
    <label>Name</label>
    <input type="text" class="form-control" name="page_meta[testimonial_block_name]" value="{{ $currentitem->getMetaValue('testimonial_block_name') }}"/>
</div>
<div class="form-group col-sm-6">
    <label>Position</label>
    <input type="text" class="form-control" name="page_meta[testimonial_block_position]" value="{{ $currentitem->getMetaValue('testimonial_block_position') }}"/>
</div>
<div class="form-group col-sm-6">
    <label>Text</label>
    <textarea class="form-control" name="page_meta[testimonial_block_text]">{{ $currentitem->getMetaValue('testimonial_block_text') }}</textarea>
</div>
<div class="form-group col-sm-6">
    <div class="field field-files field-images">
        <label>Image</label>
        <span class="wrapper-file-input">
            <span class="input-file">
                @if (''!=$currentitem->getMetaValue('_image_testimonial_block_bg'))
                    <span class="name"><img src="{{ $currentitem->getMetaValue('_image_testimonial_block_bg') }}"  /> {{ $currentitem->getMetaValue('_image_testimonial_block_bg') }}</span>
                @else
                    <span class="name"></span>
                @endif
            </span>
                    <input id="testimonial-block-image" type="file" name="page_meta[_image_testimonial_block_bg]" title="Browse">

        </span>
        {{--<input id="testimonial-block-image" type="file" name="page_meta[_image_testimonial_block_bg]" title="Browse">--}}
        {{--<label for="testimonial-block-image"></label>--}}
    </div>
    @if (''!=$currentitem->getMetaValue('_image_testimonial_block_bg'))
        <div class=" row-uploads">
            {{--<div class="uploads">--}}
                {{--<img src="{{ $currentitem->getMetaValue('_image_testimonial_block_bg') }}" width="200" />--}}
            {{--</div>--}}
            <div class="field field-checkbox">
                <input type="checkbox" value="1" name="page_meta[_remove__image_testimonial_block_bg]" id="remove-testimonial-block-image"/>
                <label for="remove-testimonial-block-image">Remove Uploaded Image</label>
            </div>
        </div>
    @endif
</div>