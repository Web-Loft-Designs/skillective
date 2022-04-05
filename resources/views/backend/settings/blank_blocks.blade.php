<ul id="blank-menu-link" style="display:none;">
    <li class="d-flex flex-wrap align-items-center mb-3 mobile-fix">
        <div class="col-1 col-move drag-div icon icon-drag">
            <img width="20px" class="ml-auto mr-auto d-block" src="{{ asset('images/move.png') }}" alt="">
        </div>
        <div class="col-4">
            <input type="text" placeholder="Title" name="settings[%name%][link_title][]" class="form-control title-inp" autocomplete="off" required>
        </div>
        <div class="col-3 col-link">
            <input type="text" placeholder="URL" name="settings[%name%][link_url][]" class="form-control title-inp" autocomplete="off" required>
        </div>
        <div class="col-3">
            <input type="text" placeholder="Class" name="settings[%name%][link_class][]" class="form-control title-inp" autocomplete="off">
        </div>
        <div class="col-1">
            <button class="btn btn-default delete-field-row" style="position:relative;"><img src="{{ asset('images/remove.png') }}" alt=""></button>
        </div>
    </li>
</ul>