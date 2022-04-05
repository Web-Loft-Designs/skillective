<div class="form-wrap profile-tab-content"  data-tab="menus">


        <p class="login-box-msg">Menus</p>

        <div class="d-flex flex-wrap">

            <div class="form-group  has-feedback">
                <div class="field">
                    @include('backend.settings.menu_field', ['settingTitle'=>'Header Menu', 'settingName'=>'header-menu'])
                </div>
            </div>
            <div class="form-group  has-feedback">
                <div class="field">
                    @include('backend.settings.menu_field', ['settingTitle'=>'Footer Menu', 'settingName'=>'footer-menu'])
                </div>
            </div>
            <div class="form-group  has-feedback">
                <div class="field">
                    @include('backend.settings.menu_field', ['settingTitle'=>'Social Media', 'settingName'=>'social-menu'])
                </div>
            </div>

            <hr class="hr">

            <div class="form-group  has-feedback bottom-row">
                <div class="right-buttons">
                    <input type="submit" class="btn btn-primary btn-submit" value="Save Settings">
                </div>
            </div>
        </div>



</div>
