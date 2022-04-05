<div class="form-wrap profile-tab-content" data-tab="google-analytics">


        <p class="login-box-msg">Google Analytics</p>

        <div class="d-flex flex-wrap">
            <div class="form-group has-feedback">
                <div class=field">
                    <label>Google Analytics</label>
                    <textarea name="settings[ga_tracking_code]" id="google_code" class="form-control">@if (isset($settings['ga_tracking_code'])){{ $settings['ga_tracking_code'] }}@endif</textarea>
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
