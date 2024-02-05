<div class="form-wrap profile-tab-content"  data-tab="merchants">

        <p class="login-box-msg">Merchants/Payments/Bookings</p>

        <div class="d-flex flex-wrap">


            <div class="form-group has-feedback">
                <div class=field">
                    <label>Skillective pre recorded fee ($)</label>
                    <input type="number"
                           step=".01"
                           min="0"
                           required
                           class="form-control"
                           name="settings[skillective_service_pre_r_fixed]"
                           value="@if (isset($settings['skillective_service_pre_r_fixed'])){{ $settings['skillective_service_pre_r_fixed'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Skillective pre recorded fee (%)</label>
                    <input type="number"
                           step=".01"
                           min="0"
                           required
                           class="form-control"
                           name="settings[skillective_service_pre_r_percent]"
                           value="@if (isset($settings['skillective_service_pre_r_percent'])){{ $settings['skillective_service_pre_r_percent'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Skillective service fee ($)</label>
                    <input type="number"
                           step=".01"
                           min="0"
                           required
                           class="form-control"
                           name="settings[skillective_service_fee_fixed]"
                           value="@if (isset($settings['skillective_service_fee_fixed'])){{ $settings['skillective_service_fee_fixed'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Skillective service fee (%)</label>
                    <input type="number"
                           step=".01"
                           min="0"
                           required
                           class="form-control"
                           name="settings[skillective_service_fee_percent]"
                           value="@if (isset($settings['skillective_service_fee_percent'])){{ $settings['skillective_service_fee_percent'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>PayPal processing fee (%)</label>
                    <input type="number"
                           step=".01"
                           min="0.1"
                           required
                           class="form-control"
                           name="settings[braintree_processing_fee]"
                           value="@if (isset($settings['braintree_processing_fee'])){{ $settings['braintree_processing_fee'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>PayPal transaction fee ($)</label>
                    <input type="number"
                           step=".01"
                           min="0.1"
                           required
                           class="form-control"
                           name="settings[braintree_transaction_fee]"
                           value="@if (isset($settings['braintree_transaction_fee'])){{ $settings['braintree_transaction_fee'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Twilio "Small Group Room" price per participant per minute (for virtual lessons) ($)</label>
                    <input type="number" step=".001" min="0.004" required class="form-control" name="settings[twilio_small_group_fee]" value="@if (isset($settings['twilio_small_group_fee'])){{ $settings['twilio_small_group_fee'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Twilio "Group Room" price per participant per minute (for virtual lessons) ($)</label>
                    <input type="number" step=".001" min="0.01" required class="form-control" name="settings[twilio_group_fee]" value="@if (isset($settings['twilio_group_fee'])){{ $settings['twilio_group_fee'] }}@endif"/>
                </div>
            </div>

            <div class="form-group  has-feedback">
                <label>Booking Confirmation Block(after successfull reservation)</label>
                <textarea class="form-control" id="booking-confirmation-text" name="settings[booking_confirmation_text]" >@if (isset($settings['booking_confirmation_text'])){{ $settings['booking_confirmation_text'] }}@endif</textarea>
            </div>
            <div class="form-group has-feedback">
                <div class=field">
                    <label>Time to approve booking request in hours before it will be automatically cancelled</label>
                    <input required type="number" class="form-control" name="settings[time_to_approve_booking]" value="@if (isset($settings['time_to_approve_booking'])){{ (int)$settings['time_to_approve_booking'] }}@else{{ 24 }}@endif"/>
                </div>
            </div>

            <div class="form-group  has-feedback">
                <div class="field">
                    <label>Header notification: Instructor Braintree onboarding required</label>
                    <input type="text" class="form-control" name="settings[onboard_as_merchant]" value="@if (isset($settings['onboard_as_merchant'])){{ $settings['onboard_as_merchant'] }}@endif"/>
                </div>
            </div>
            <div class="form-group  has-feedback">
                <div class="field">
                    <label>Header notification: Instructor merchant account on review(by Braintree)</label>
                    <input type="text" class="form-control" name="settings[merchant_account_on_review]" value="@if (isset($settings['merchant_account_on_review'])){{ $settings['merchant_account_on_review'] }}@endif"/>
                </div>
            </div>
            <div class="form-group  has-feedback">
                <div class="field">
                    <label>Header notification: Instructor merchant account declined(by Braintree)</label>
                    <input type="text" class="form-control" name="settings[merchant_account_declined]" value="@if (isset($settings['merchant_account_declined'])){{ $settings['merchant_account_declined'] }}@endif"/>
                </div>
            </div>

            <hr class="hr">

            <div class="form-group  has-feedback">
                <div class="field">
                    <label>Lesson Request Created Confirmation Message</label>
                    <input type="text" class="form-control" name="settings[lesson_request_created]" value="@if (isset($settings['lesson_request_created'])){{ $settings['lesson_request_created'] }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Time(in hours) to approve the Lesson Request before it will be automatically cancelled</label>
                    <input required type="number" class="form-control" name="settings[time_to_approve_lesson_request]" value="@if (isset($settings['time_to_approve_lesson_request'])){{ (int)$settings['time_to_approve_lesson_request'] }}@else{{ 24 }}@endif"/>
                </div>
            </div>

            <div class="form-group has-feedback">
                <div class=field">
                    <label>Time(in hours) to book the Lesson created from Lesson Request before it will be automatically cancelled</label>
                    <input required type="number" class="form-control" name="settings[time_to_book_lesson_request]" value="@if (isset($settings['time_to_book_lesson_request'])){{ (int)$settings['time_to_book_lesson_request'] }}@else{{ 24 }}@endif"/>
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
