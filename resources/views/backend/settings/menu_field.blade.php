<div class="col-sm-6 form-group section-row" id="{{ $settingName }}-links">
    <label class="control-label">{{ $settingTitle }}</label>
    <input type="hidden" name="{{ $settingName }}_order" value="" class="section-fields-inp">
    <ol id="{{ $settingName }}-links-list">
        <?php
        if (isset($settings[$settingName]) && $settings[$settingName])
            $menu = $settings[$settingName];
        else
            $menu = [];
        ?>
        @foreach ($menu as $index=>$link)
            <li class="d-flex flex-wrap align-items-center mb-3 mobile-fix" id="main-menu-links-list-sortable-{{ $index }}">
                <div class="col-1 col-move drag-div icon icon-drag">
                    <img width="20px" class="ml-auto mr-auto d-block" src="{{ asset('images/move.png') }}" alt="">
                </div>
                <div class="col-4">
                    <input type="text" placeholder="Title" name="settings[{{ $settingName }}][link_title][{{ $index }}]" class="form-control title-inp" value="{{ isset($link['link_title'])?$link['link_title']:'' }}" autocomplete="off" required>
                </div>
                <div class="col-3 col-link">
                    <input type="text" placeholder="URL" name="settings[{{ $settingName }}][link_url][{{ $index }}]" class="form-control title-inp" value="{{ isset($link['link_url'])?$link['link_url']:'' }}" autocomplete="off" required>
                </div>
                <div class="col-3">
                    <input type="text" placeholder="Class" name="settings[{{ $settingName }}][link_class][{{ $index }}]" class="form-control title-inp" value="{{ isset($link['link_class'])?$link['link_class']:'' }}" autocomplete="off">
                </div>
                <div class="col-1">
                    <button class="btn btn-default delete-field-row" style="position:relative;"><img src="{{ asset('images/remove.png') }}" alt=""></button>
                </div>
            </li>
        @endforeach
    </ol>
    <button type="button" class="btn btn-primary left add-field-row">
        Add Link
    </button>
</div>

@section('scripts')
    @parent

    <script src="{{ asset('js/jquery.mjs.nestedSortable.js') }}"></script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#{{ $settingName }}-links-list').nestedSortable({
            handle: 'div.drag-div',
            items: 'li',
            toleranceElement: '> div',
            maxLevels: 1,
            relocate : function () {
                serializeSortableOrder($(this));
            }
        });
        serializeSortableOrder($('#{{ $settingName }}-links-list'));

        $('#{{ $settingName }}-links').on('click', '.add-field-row', function(){
            _clonedRow = $('#blank-menu-link li:first').clone();
            _clonedRow.find('input').each(function(){
                $(this).attr('name', $(this).attr('name').replace('%name%', '{{ $settingName }}'));
            })
            var fieldsContainer = $(this).parents('#{{ $settingName }}-links').find('#{{ $settingName }}-links-list');
            fieldsContainer.append(_clonedRow);
            reindexSectionFieldsNames( fieldsContainer );
            serializeSortableOrder(fieldsContainer);
        });

        $('#{{ $settingName }}-links').on('click', '.delete-field-row', function(e){
            e.preventDefault();
            var fieldsContainer = $(this).parents('.links-list');
            $(this).parents('li').remove();
            reindexSectionFieldsNames( fieldsContainer );
            serializeSortableOrder(fieldsContainer);
            return false;
        });
    });
</script>

@stop