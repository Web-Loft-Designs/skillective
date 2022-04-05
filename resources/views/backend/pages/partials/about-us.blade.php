{{--<div class="col-sm-12">--}}
    {{--<h3>Additional Page Attributes</h3>--}}
{{--</div>--}}

<div class="form-group  mt-4">
    <h4>About us Block</h4>
    <hr/>
</div>

<div class="form-group col-sm-6">
    <label>Block Title</label>
    <input type="text" class="form-control" name="page_meta[about_us_block_title]" value="{{ $currentitem->getMetaValue('about_us_block_title') }}"/>
</div>
<div class="form-group col-sm-6">
    <div class="field field-files field-images">
        <label>Background Image</label>
        <span class="wrapper-file-input">
            <span class="input-file">
                @if (''!=$currentitem->getMetaValue('_image_about_us_block_bg'))
                    <span class="name"><img src="{{ $currentitem->getMetaValue('_image_about_us_block_bg') }}"  /> {{ $currentitem->getMetaValue('_image_about_us_block_bg') }}</span>
                @else
                    <span class="name"></span>
                @endif
            </span>
           <input id="about-us-block-image" type="file" name="page_meta[_image_about_us_block_bg]" title="Browse">
        </span>
    </div>
    @if (''!=$currentitem->getMetaValue('_image_about_us_block_bg'))
        <div class="row-uploads">
            {{--<div class="uploads">--}}
                {{--<img src="{{ $currentitem->getMetaValue('_image_about_us_block_bg') }}" />--}}
            {{--</div>--}}
            <div class="field mt-2 field-checkbox checkbox-wrapper">
                <label for="remove-about-us-block-image">
                    <input type="checkbox" value="1" name="page_meta[_remove__image_about_us_block_bg]" id="remove-about-us-block-image"/>
                    <span class="checkmark"></span>
                    Remove Uploaded Image
                </label>
            </div>
            {{--<div class="field field-checkbox">--}}
                {{--<label for="remove-about-us-block-image">Remove Uploaded Image</label>--}}
            {{--</div>--}}
        </div>
    @endif
</div>
<div class="form-group col-sm-12">
    <label>Text</label>
    <textarea class="form-control" id="about-us-block-text" name="page_meta[about_us_block_text]">{{ $currentitem->getMetaValue('about_us_block_text') }}</textarea>
</div>



<div class="form-group  mt-4">

    <h4>Booking Steps Block</h4>
    <hr/>
</div>
<div class="form-group col-sm-6">
    <label>Block Title</label>
    <input type="text" class="form-control" name="page_meta[booking_steps_block_title]" value="{{ $currentitem->getMetaValue('booking_steps_block_title') }}"/>
</div>
<div class="form-group col-sm-12">
    <booking-steps-list v-bind:booking-steps="{{ ( ''!=($benefits = $currentitem->getMetaValue('booking_steps')) ) ? json_encode($benefits) : json_encode([]) }}"></booking-steps-list>
</div>


<div class="form-group  mt-4">

    <h4>Our Experience Block</h4>
    <hr/>
</div>
<div class="form-group col-sm-12">
    <label>Text</label>
    <textarea class="form-control" id="our-experience-block-text" name="page_meta[our_experience_block_text]">{{ $currentitem->getMetaValue('our_experience_block_text') }}</textarea>
</div>
<div class="form-group col-sm-6">
    <div class="field field-files field-images">
        <label>Background Image</label>
        <span class="wrapper-file-input">
        <span class="input-file">
             @if (''!=$currentitem->getMetaValue('_image_our_experience_block_bg'))
                <span class="name"><img src="{{ $currentitem->getMetaValue('_image_our_experience_block_bg') }}"  /> {{ $currentitem->getMetaValue('_image_our_experience_block_bg') }}</span>
            @else
                <span class="name"></span>
            @endif
      </span>
        <input id="our-experience-block-image" type="file" name="page_meta[_image_our_experience_block_bg]" title="Browse">
        </span>
    </div>
    @if (''!=$currentitem->getMetaValue('_image_our_experience_block_bg'))
        <div class="row-uploads">
            {{--<div class="uploads">--}}
                {{--<img src="{{ $currentitem->getMetaValue('_image_our_experience_block_bg') }}" width="200" />--}}
            {{--</div>--}}
            <div class="field field-checkbox">
                <label for="remove-our-experience-block-image">
                    <input type="checkbox" value="1" name="page_meta[_remove__image_our_experience_block_bg]" id="remove-our-experience-block-image"/>

                    <span class="checkmark"></span>
                    Remove Uploaded Image
                </label>
            </div>
        </div>
    @endif
</div>

<div class="col-sm-12">
    <h4>Start Now Block</h4>
    <hr/>
</div>
<div class="form-group col-sm-12">
    <label>Text</label>
    <textarea class="form-control" id="start-now-block-text" name="page_meta[start_now_block_text]">{{ $currentitem->getMetaValue('start_now_block_text') }}</textarea>
</div>
<div class="form-group col-sm-6">
    <div class="field field-files field-images">
        <label>Background Image</label>
        <span class="wrapper-file-input">
        <span class="input-file">
             @if (''!=$currentitem->getMetaValue('_image_our_experience_block_bg'))
                <span class="name"><img src="{{ $currentitem->getMetaValue('_image_start_now_block_bg') }}" /> {{ $currentitem->getMetaValue('_image_our_experience_block_bg') }}</span>
            @else
                <span class="name"></span>
            @endif
      </span>
        <input id="start-now-block-image" type="file" name="page_meta[_image_start_now_block_bg]" title="Browse">
        </span>
        {{--<label for="start-now-block-image"></label>--}}
    </div>
    @if (''!=$currentitem->getMetaValue('_image_start_now_block_bg'))
        <div class="row-uploads">
            {{--<div class="uploads">--}}
                {{--<img src="{{ $currentitem->getMetaValue('_image_start_now_block_bg') }}" />--}}
            {{--</div>--}}
            <div class="field field-checkbox">
                <input type="checkbox" value="1" name="page_meta[_remove__image_start_now_block_bg]" id="remove-start-now-block-image"/>
                <label for="remove-start-now-block-image">Remove Uploaded Image</label>
            </div>
        </div>
    @endif
</div>

@section('scripts')
    @parent
    <script>
		CKEDITOR.replace( 'about-us-block-text', options );
		CKEDITOR.replace( 'our-experience-block-text', options );
		CKEDITOR.replace( 'start-now-block-text', options );
    </script>
@endsection