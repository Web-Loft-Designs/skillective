
<div class="form-group  mt-4">
    <h4>Filter Block</h4>
    <hr/>
</div>

<div class="form-group col-sm-6">
    <label>Text above filter</label>
    <input type="text" class="form-control" name="page_meta[text_above_filter]" value="{{ $currentitem->getMetaValue('text_above_filter') }}"/>
</div>

<div class="form-group col-sm-6">
        <div class="field field-files field-images">
            <label>Background Image</label>
            <span class="wrapper-file-input">
            <span class="input-file">
                @if (''!=$currentitem->getMetaValue('_image_filter_block_image'))
                    <span class="name"><img src="{{ $currentitem->getMetaValue('_image_filter_block_image') }}"  /> {{ $currentitem->getMetaValue('_image_filter_block_image') }}</span>
                @else
                    <span class="name"></span>
                @endif
            </span>
           <input id="filter-block-image" type="file" name="page_meta[_image_filter_block_image]" title="Browse">
        </span>
            {{--<input id="filter-block-image" type="file" name="page_meta[_image_filter_block_image]" title="Browse">--}}
            {{--<label for="filter-block-image"></label>--}}
        </div>
        @if (''!=$currentitem->getMetaValue('_image_filter_block_image'))
            <div class="row-uploads">
                {{--<div class="uploads">--}}
                    {{--<img src="{{ $currentitem->getMetaValue('_image_filter_block_image') }}" width="200" />--}}
                {{--</div>--}}
                <div class="field field-checkbox">
                    <input type="checkbox" value="1" name="page_meta[_remove__image_filter_block_image]" id="remove-filter-block-image"/>
                    <label for="remove-filter-block-image">Remove Uploaded Image</label>
                </div>
            </div>
        @endif
</div>

<div class="form-group col-sm-6">
    <label>Lesson Search Form Title</label>
    <input type="text" class="form-control" name="page_meta[filter_form_title]" value="{{ $currentitem->getMetaValue('filter_form_title') }}"/>
</div>

<div class="form-group col-sm-6">
    <label>Instructor Search Form Title</label>
    <input type="text" class="form-control" name="page_meta[instructor_filter_form_title]" value="{{ $currentitem->getMetaValue('instructor_filter_form_title') }}"/>
</div>

<div class="form-group  mt-4">
    <h4>Text under filter block</h4>
    <hr/>
</div>

<div class="form-group col-sm-6">
    <label>Title</label>
    <input type="text" class="form-control" name="page_meta[how_it_works_title]" value="{{ $currentitem->getMetaValue('how_it_works_title') }}"/>
</div>

<div class="form-group col-sm-6">
    <label>Text</label>
    <textarea class="form-control" name="page_meta[how_it_works_text]">{{ $currentitem->getMetaValue('how_it_works_text') }}</textarea>
</div>

<div class="form-group  mt-4">
    <h4>Benefits List</h4>
    <hr/>
</div>

<div class="form-group col-sm-6">
    <label>Block Title</label>
    <input type="text" class="form-control" name="page_meta[benefits_title]" value="{{ $currentitem->getMetaValue('benefits_title') }}"/>
</div>

<div class="form-group col-sm-12">
    <benefits-list v-bind:benefits="{{ ( ''!=($benefits = $currentitem->getMetaValue('benefits')) ) ? json_encode($benefits) : json_encode([]) }}"></benefits-list>
</div>