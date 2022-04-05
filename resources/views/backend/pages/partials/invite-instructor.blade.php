<div class="col-sm-12">
    <h3>Additional Page Attributes</h3>
</div>

<div class="col-sm-12">
    <hr/>
    <h4>How this works</h4>
</div>
<div class="form-group col-sm-6">
    <label>Block Title</label>
    <input type="text" class="form-control" name="page_meta[how_this_works_title]" value="{{ $currentitem->getMetaValue('how_this_works_title') }}"/>
</div>
<div class="form-group col-sm-12">
    <how-this-works-list v-bind:how-this-works="{{ ( ''!=($benefits = $currentitem->getMetaValue('how_this_works_blocks')) ) ? json_encode($benefits) : json_encode([]) }}"></how-this-works-list>
</div>