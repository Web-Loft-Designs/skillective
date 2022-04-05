
<div class="col-sm-12">
    <h4>Form Description</h4>
    <hr/>
</div>

<div class="form-group col-sm-6">
    <label>Title</label>
    <input type="text" class="form-control" name="page_meta[form_block_welcome]" value="{{ $currentitem->getMetaValue('form_block_welcome') }}"/>
</div>
<div class="form-group col-sm-6">
    <label>Text</label>
    <textarea class="form-control" id="registration-form-block-text" name="page_meta[form_block_text]">{{ $currentitem->getMetaValue('form_block_text') }}</textarea>
</div>
<div class="form-group col-sm-6">
        <div class="field field-files field-images">
            <label>Background Image</label>
            <span class="wrapper-file-input">
            <span class="input-file">
                @if (''!=$currentitem->getMetaValue('_image_form_block_bg'))
                    <span class="name"><img src="{{ $currentitem->getMetaValue('_image_form_block_bg') }}"  /> {{ $currentitem->getMetaValue('_image_form_block_bg') }}</span>
                @else
                    <span class="name"></span>
                @endif
            </span>
                         <input id="form-block-image" type="file" name="page_meta[_image_form_block_bg]" title="Browse">

        </span>
            {{--<input id="form-block-image" type="file" name="page_meta[_image_form_block_bg]" title="Browse">--}}
            {{--<label for="form-block-image"></label>--}}
        </div>
        @if (''!=$currentitem->getMetaValue('_image_form_block_bg'))
            <div class="row-uploads">
                {{--<div class="uploads">--}}
                    {{--<img src="{{ $currentitem->getMetaValue('_image_form_block_bg') }}" width="200" />--}}
                {{--</div>--}}
                <div class="field field-checkbox">
                    <input type="checkbox" value="1" name="page_meta[_remove__image_form_block_bg]" id="remove-form-block-image"/>
                    <label for="remove-form-block-image">Remove Uploaded Image</label>
                </div>
            </div>
        @endif
</div>
<div class="form-group col-sm-12">
    <form-benefits v-bind:benefits="{{ ( ''!=($benefits = $currentitem->getMetaValue('form_benefits')) ) ? json_encode($benefits) : json_encode([]) }}"></form-benefits>
</div>


@include('backend.pages.partials.fields.testimonial')


<div class="col-sm-12">
    <hr/>
    <h4>Benefits List</h4>
</div>
<div class="form-group col-sm-6">
    <label>Block Title</label>
    <input type="text" class="form-control" name="page_meta[benefits_title]" value="{{ $currentitem->getMetaValue('benefits_title') }}"/>
</div>
<div class="form-group col-sm-12">
    <benefits-list v-bind:benefits="{{ ( ''!=($benefits = $currentitem->getMetaValue('benefits')) ) ? json_encode($benefits) : json_encode([]) }}"></benefits-list>
</div>