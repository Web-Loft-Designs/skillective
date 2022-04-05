<div class="col-sm-12">
    <h3>Additional Page Attributes</h3>
</div>

<div class="col-sm-12">
    <hr/>
    <h4>Invitation Form</h4>
</div>
<div class="form-group col-sm-6">
    <label>Form Title</label>
    <input type="text" class="form-control" name="page_meta[invitation_form_title]" value="{{ $currentitem->getMetaValue('invitation_form_title') }}"/>
</div>
<div class="form-group col-sm-6">
    <label>Form Description</label>
    <textarea class="form-control" name="page_meta[invitation_form_description]">{{ $currentitem->getMetaValue('invitation_form_description') }}</textarea>
</div>