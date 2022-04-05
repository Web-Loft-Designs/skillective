<div class="col-sm-12">
    <h3>Additional Page Attributes</h3>
</div>

<div class="col-sm-12">
    <hr/>
    <h4>Notifications types</h4>
</div>
<div class="form-group col-sm-6">
    <label>Description for clients</label>
    <textarea class="form-control" id="student-notifications-block-description" name="page_meta[student_notifications_block_description]">{{ $currentitem->getMetaValue('student_notifications_block_description') }}</textarea>
</div>
<div class="form-group col-sm-6">
    <label>Description for instructors</label>
    <textarea class="form-control" id="instructor-notifications-block-description" name="page_meta[instructor_notifications_block_description]">{{ $currentitem->getMetaValue('instructor_notifications_block_description') }}</textarea>
</div>


<div class="col-sm-12">
    <hr/>
    <h4>Geo Locations</h4>
</div>
<div class="form-group col-sm-6">
    <label>Block Title</label>
    <input class="form-control" id="student-geolocation-block-title" name="page_meta[student_geolocation_block_title]" value="{{ $currentitem->getMetaValue('student_geolocation_block_title') }}"/>
</div>
<div class="form-group col-sm-6">
    <label>Block Description</label>
    <textarea class="form-control" id="student-geolocation-block-description" name="page_meta[student_geolocation_block_description]" class="add-wysiwyg">{{ $currentitem->getMetaValue('student_geolocation_block_description') }}</textarea>
</div>