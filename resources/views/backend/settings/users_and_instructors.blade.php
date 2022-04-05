<div class="form-wrap profile-tab-content"  data-tab="users-instructors">


        <p class="login-box-msg">Users & Instructors</p>

        <div class="d-flex flex-wrap">
            <div class="form-group has-feedback">
                <label>Instructor registration without invitation</label>
                <div class="radio-wrapper">
                    <label class="radio-item" for="free-instructor-registration-disabled">
                        <input type="radio" id="free-instructor-registration-disabled"  name="settings[free_instructor_registration_enabled]" @if ( (isset($settings['free_instructor_registration_enabled']) && $settings['free_instructor_registration_enabled']==0) || !isset($settings['free_instructor_registration_enabled']) ) checked @endif value="0"/>
                        <span class="checkmark"></span>
                        Disabled
                    </label>
                    <label class="radio-item" for="free-instructor-registration-enabled">

                        <input type="radio" id="free-instructor-registration-enabled"  name="settings[free_instructor_registration_enabled]" @if (isset($settings['free_instructor_registration_enabled']) && $settings['free_instructor_registration_enabled']==1) checked @endif value="1"/>
                        <span class="checkmark"></span>
                        Enabled
                    </label>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Maximum allowed Instructor invites to send</label>
                    <input type="number" class="form-control" name="settings[max_allowed_instructor_invites]" value="@if (isset($settings['max_allowed_instructor_invites'])){{ $settings['max_allowed_instructor_invites'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class="field">
                    <label>Maximum allowed Client invites to send</label>
                    <input type="number" class="form-control" name="settings[max_allowed_student_invites]" value="@if (isset($settings['max_allowed_student_invites'])){{ $settings['max_allowed_student_invites'] }}@endif"/>
                </div>
            </div>

            <div class="form-group  has-feedback">
                <div class="field">
                    <label>Instructor registration confirmation(in popup)</label>
                    <input type="text" class="form-control" name="settings[instructor_registration_confirmation_text]" value="@if (isset($settings['instructor_registration_confirmation_text'])){{ $settings['instructor_registration_confirmation_text'] }}@endif"/>
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
