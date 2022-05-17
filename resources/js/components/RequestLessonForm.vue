<template>
  <div id='lesson-form-container'>
    <div class='d-grid gap-2 d-md-block'>
      <button
        class='btn btn-success btn-sm text-wrap'
        type='button'
        data-toggle='tooltip'
        v-if='showCreateBtn'
        data-placement='bottom'
        @click='showJoinModal'
        :title='tooltipContent()'
      >
        Add me to {{ instructorName }}'s Client List
      </button>
    </div>
    <!--    modal1-->
    <div
      class='modal'
      id='joinClient'
      tabindex='0'
      role='dialog'
      aria-labelledby='exampleModalCenterTitle'
      aria-hidden='true'
    >
      <div
        class='modal-dialog modal-dialog-centered'
        role='document'
      >
        <div class='modal-content'>
          <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
          <div class='modal-body'>
            <span>
              Join {{ instructorName }}'s client list to be updated when training classes, clinics, workshops, or new tutorials become available.
            </span>
          </div>
          <div class='modal-footer'>
            <button
              type='button'
              aria-label='Close'
              class='btn btn-success btn-sm'
              @click='successJoinedToClient'
            >
              Join client list
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--    modal3-->
    <div
      class='modal'
      tabindex='-1'
      role='dialog'
      id='registeredModal'
    >
      <div class='modal-dialog' role='document'>
        <div class='modal-content'>
          <div class='modal-body'>
            <form novalidate class='needs-validation'>
              <h3 class='form-title'>Join {{ instructorName }}'s Client List</h3>
              <div class='d-flex flex-wrap'>
                <div class='label-w-100'>
                  <label>Complete name</label>
                </div>
                <div
                  class='form-group first-name has-feedback'
                  :class="{'has-error': ($v.modalData.first_name.$dirty && !$v.modalData.first_name.required)}"
                >
                  <input
                    type='text'
                    class='form-control'
                    name='first_name'
                    v-model='modalData.first_name'
                    placeholder='First Name'
                    :class="{'is-invalid': ($v.modalData.first_name.$dirty && !$v.modalData.first_name.required)}"
                  />
                  <span v-if='$v.modalData.first_name.$dirty && !$v.modalData.first_name.required'>First name can't be empty</span>
                </div>
                <div class='invalid-feedback'>
                  Please choose a username.
                </div>
                <div
                  class='form-group last-name has-feedback'
                  :class="{'has-error': ($v.modalData.last_name.$dirty && !$v.modalData.last_name.required)}"
                >
                  <input
                    type='text'
                    class='form-control'
                    name='last_name'
                    v-model='modalData.last_name'
                    placeholder='Last Name'
                    :class="{'is-invalid': ($v.modalData.last_name.$dirty && !$v.modalData.last_name.required)}"
                  />
                  <span v-if='$v.modalData.last_name.$dirty && !$v.modalData.last_name.required'>Last name can't be empty</span>
                </div>
                <div class='form-group'>
                  <label>Instagram Handle</label>
                  <input
                    type='text'
                    class='form-control'
                    name='instagram_handle'
                    v-model='modalData.instagram_handle'
                    placeholder='@instagram_name'
                  />
                </div>
                <div
                  class='form-group input-zip has-feedback'
                  :class="{'has-error':
                  ($v.modalData.zip.$dirty && !$v.modalData.zip.required)
                  ||
                  ($v.modalData.zip.$dirty && !$v.modalData.zip.numeric)
                  ||
                  ($v.modalData.zip.$dirty && !$v.modalData.zip.valid)
                }"
                >
                  <label>ZIP</label>
                  <input
                    type='text'
                    class='form-control'
                    name='zip'
                    v-model.number='modalData.zip'
                    placeholder='ZIP code'
                    :class="{'is-invalid':
                    ($v.modalData.zip.$dirty && !$v.modalData.zip.required)
                    ||
                    ($v.modalData.zip.$dirty && !$v.modalData.zip.numeric)
                    ||
                    ($v.modalData.zip.$dirty && !$v.modalData.zip.valid)
                    }"
                  />
                  <span v-if='$v.modalData.zip.$dirty && !$v.modalData.zip.required'>ZIP code can't be empty</span>
                  <span v-else-if='$v.modalData.zip.$dirty && !$v.modalData.zip.numeric'>ZIP code must be numeric</span>
                  <span v-else-if='$v.modalData.zip.$dirty && !$v.modalData.zip.valid'>The zip format is invalid</span>
                </div>
                <div
                  class='form-group w-50'
                  :class="{'has-error':
                  ($v.modalData.mobile_phone.$dirty && !$v.modalData.mobile_phone.required)
                  ||
                  ($v.modalData.mobile_phone.$dirty && !$v.modalData.mobile_phone.valid)
                }"
                >
                  <label>Phone number</label>
                  <masked-input
                    class='form-control'
                    v-model='modalData.mobile_phone'
                    :placeholder="'+1 (___) ___ ____'"
                    mask='\+1 (111) 111 1111'
                    :class="{'is-invalid':
                    ($v.modalData.mobile_phone.$dirty && !$v.modalData.mobile_phone.required
                    ||
                    ($v.modalData.mobile_phone.$dirty && !$v.modalData.mobile_phone.valid))
                    }"
                  />
                  <span v-if='$v.modalData.mobile_phone.$dirty && !$v.modalData.mobile_phone.required'>Phone number can't be empty</span>
                  <span v-else-if='$v.modalData.mobile_phone.$dirty && !$v.modalData.mobile_phone.valid'>The mobile phone format is invalid</span>
                </div>
                <div
                  class='form-group'
                  :class="{'has-error':
                  ($v.modalData.email.$dirty && !$v.modalData.email.required)
                  ||
                  ($v.modalData.email.$dirty && !$v.modalData.email.email)
                  ||
                   storeErrors.email
                }"
                >
                  <label>Email</label>
                  <input
                    type='email'
                    name='email'
                    v-model.trim='modalData.email'
                    @input='changeEmail'
                    placeholder='Email'
                    class='form-control'
                    :class="{'is-invalid':
                    ($v.modalData.email.$dirty && !$v.modalData.email.required)
                    ||
                    ($v.modalData.email.$dirty && !$v.modalData.email.email)
                    ||
                    storeErrors.email
                    }"
                  />
                  <span v-if='$v.modalData.email.$dirty && !$v.modalData.email.required'>Email can't be empty</span>
                  <span v-else-if='$v.modalData.email.$dirty && !$v.modalData.email.email'>Error email format</span>
                  <span v-else-if='storeErrors.email'>{{ storeErrors.email[0] }}</span>
                </div>
                <div class='form-group checkbox-wrapper'>
                  <span v-if='$v.modalData.mobile_phone.$dirty && !modalData.accept_terms'>Please accept terms of service</span>
                </div>
                <div
                  class='form-group checkbox-wrapper has-feedback'
                  :class="{'has-error': $v.modalData.mobile_phone.$dirty && !modalData.accept_terms}"
                >
                  <div class='field'>
                    <label for='accept-terms'>
                      <input
                        v-model='modalData.accept_terms'
                        type='checkbox'
                        id='accept-terms'
                        :value='1'
                      />
                      <span class='checkmark'></span>
                      I agree to the
                      <a href='/terms' target='_blank'>terms of service</a>
                    </label>
                  </div>
                  <span v-if='$v.modalData.mobile_phone.$dirty && !modalData.accept_terms'>Please accept terms of service</span>
                </div>
                <div class='form-group'>
                  <loader-button
                    :isLoading='loadingBtn'
                    text='ADD ME TO THEIR LIST :-)'
                    @click='joinClientList'
                  />
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!--    modal4-->
    <div
      class='modal'
      id='successAdded'
      tabindex='-1'
      role='dialog'
      aria-labelledby='exampleModalCenterTitle'
      aria-hidden='true'
    >
      <div
        class='modal-dialog modal-dialog-centered'
        role='document'
      >
        <div class='modal-content'>
          <div class='modal-header'>
            <h5
              class='modal-title'
              id='exampleModalLongTitle'
            >
              Welcome!
            </h5>
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
          <div class='modal-body text-center h3'>
            Thank you for joining my client list. I'll be in touch with upcoming lessons and events. Talk to you soon!
            <br>
            <strong>- {{ instructorName }}</strong>
            <br>
            You should have received an email from Skillective with more details.
          </div>
          <div class='modal-footer'>
            <button
              type='button'
              aria-label='Close'
              class='btn btn-success btn-sm'
              data-dismiss='modal'
              @click='openAllInstructorsModal'
            >
              ОК
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class='modal' id='instructorsModal' tabindex='-1' role='dialog' aria-labelledby='instructorsModalLabel'
         aria-hidden='true'>
      <div class='modal-dialog' role='document'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title' id='instructorsModalLabel'>
              Would you like to be added to other instructor's client lists? Check the boxes below
            </h5>
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
          <div class='modal-body'>
            <ul class='list-group'>
              <li class='list-group-item'>
                <div class='input-group align-items-center flex-nowrap'>
                  <div class='input-group-prepend'>
                    <div class='form-group checkbox-wrapper'>
                      <div class='field'>
                        <label for='select-all-instructors'>
                          <input
                            v-model='selectAll'
                            type='checkbox'
                            id='select-all-instructors'
                            @input='onSelectAll'
                          />
                          <span class='checkmark'></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class='d-flex justify-content-start align-items-center pt-4 m-0 px-5 h5'>
                    <span>Select all instructors</span>
                  </div>
                </div>
              </li>
              <li
                class='list-group-item'
                v-for='instructor in instructors'
                :key='instructor.id'
              >
                <div class='input-group align-items-center flex-nowrap'>
                  <div class='input-group-prepend'>
                    <div class='form-group checkbox-wrapper'>
                      <div class='field'>
                        <label :for='`accept-terms-instructors${instructor.id}`'>
                          <input
                            v-model='selectedInstructors'
                            type='checkbox'
                            :id='`accept-terms-instructors${instructor.id}`'
                            :value='`${instructor.id}`'
                          />
                          <span class='checkmark'></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class='d-flex justify-content-between align-items-center px-5 m-0 w-100'>
                    <div class='instructor-avatar'>
                      <img :src='instructor.profile.image' alt='instructor avatar'>
                    </div>
                    <div class='d-flex flex-column mt-0 align-items-center'>
                      <h3 class='title'>{{instructor.full_name}}</h3>
                      <span class='inst'>@{{instructor.profile.instagram_handle}}</span>
                      <div class='profile-genres d-flex flex-column align-items-center mt-1'>
                        <span v-if='instructor.genres.length > 0'>{{instructor.genres[0].title}}</span>
                        <div>
                          <span v-if='instructor.genres.length > 1'>{{instructor.genres[1].title}}</span>
                          <span v-if='instructor.genres.length > 2'>+{{instructor.genres.length-2}}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class='modal-footer'>
            <loader-button
              class='lesson__button'
              :disabled='!selectedInstructors.length'
              :is-loading='loadingAdd'
              text="Add me to these instructor's client lists"
              @click='addToInstructorsList'
            />
          </div>
        </div>
      </div>
    </div>
    <magnific-popup-modal
      class='ie-fix'
      @modalClosed='clearFormAndClosePopup'
      :show='false'
      :config='{
        closeOnBgClick: true,
        showCloseBtn: true,
        enableEscapeKey: false,
      }'
      ref='modal'
    >
      <div class='modal-add-lesson'>
        <form
          method='post'
          @keypress.enter.prevent
          @submit.prevent='onSubmit'
          v-if='
            lessonRequestCreated === false &&
            lessonRequestCancelled === false &&
            lessonRequestAccepted === false
          '
        >
          <input type='hidden' v-model='fields.instructor_id' />
          <div class='row'>
            <h2 class='login-box-msg col-12'>Request a Lesson</h2>
            <div
              class='col-12 form-group has-feedback'
              :class="{ 'has-error': errors.genre }"
            >
              <label>Genre</label>
              <select
                class='form-control'
                name='genre'
                v-model='fields.genre'
                :disabled='fieldsDisabled'
              >
                <option value></option>
                <option
                  v-for='genre in formGenres'
                  :key='genre.id'
                  :value='genre.id'
                >
                  {{ genre.title }}
                </option>
              </select>
              <span class='help-block' v-if='errors.genre'>
                <strong>{{ errors.genre[0] }}</strong>
              </span>
            </div>

            <div
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.date }"
            >
              <label>Date from</label>
              <!--<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" v-model="fields.date" name="date" :placeholder="'mm/dd/yyyy'" :format="'MM/dd/yyyy'"></datepicker>-->
              <dropdown-datepicker
                v-if='isDateInputInit'
                :disabled='fieldsDisabled'
                display-format='mdy'
                v-model='fields.date'
                submit-format='yyyy-mm-dd'
                :minYear='2021'
                ref='datepicker'
                maxDate='2030-01-01'
              ></dropdown-datepicker>

              <span class='help-block' v-if='errors.date'>
                <strong>{{ errors.date[0] }}</strong>
              </span>
            </div>
            <div
              class='col-lg-12 col-sm-12 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.date }"
            >
              <label>Date to </label>
              <!--<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" v-model="fields.date" name="date" :placeholder="'mm/dd/yyyy'" :format="'MM/dd/yyyy'"></datepicker>-->
              <dropdown-datepicker
                v-if='isDateInputInit'
                display-format='mdy'
                v-model='fields.date_to'
                submit-format='yyyy-mm-dd'
                key='sombun5'
                ref='datepickerTo'
                :minYear='2021'
                maxDate='2030-01-01'
              ></dropdown-datepicker>

              <span class='help-block' v-if='errors.date_to'>
                <strong>{{ errors.date_to[0] }}</strong>
              </span>
            </div>
            <div
              class='time-from col-lg-6 col-sm-6 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.time_from }"
            >
              <label>Time from</label>
              <vue-timepicker
                :minute-interval='15'
                v-model='fields.time_from'
                placeholder='Start Time'
                format='h:mm a'
                close-on-complete
                ref='timeFrom'
                hour-label='Hour'
                minute-label='Minute'
                apm-label='AM/PM'
                @change='timeFormChange'
                @open="clearTimepicker('timeFrom')"
              ></vue-timepicker>
              <span class='help-block' v-if='errors.time_from'>
                <strong>{{ errors.time_from[0] }}</strong>
              </span>
            </div>

            <div
              class='time-to col-lg-6 col-sm-6 col-12 form-group has-feedback'
              :class="{ 'has-error': errors.time_to }"
            >
              <label>Time to</label>
              <vue-timepicker
                :minute-interval='15'
                v-model='fields.time_to'
                placeholder='End Time'
                format='h:mm a'
                close-on-complete
                ref='timeTo'
                hour-label='Hour'
                minute-label='Minute'
                apm-label='AM/PM'
                @open="clearTimepicker('timeTo')"
              ></vue-timepicker>

              <span class='help-block' v-if='errors.time_to'>
                <strong>{{ errors.time_to[0] }}</strong>
              </span>
            </div>
            <div
              class='form-group col-4 has-feedback'
              :class="{ 'has-error': errors.lesson_type }"
            >
              <label>Type of Lesson</label>
              <select
                class='form-control'
                v-model='fields.lesson_type'
                :disabled='fieldsDisabled'
              >
                <option
                  v-for='(lessonTypeTitle, lessonTypeName) in lessonTypes'
                  :key='lessonTypeName'
                  :value='lessonTypeName'
                >
                  {{ lessonTypeTitle }}
                </option>
              </select>
              <span class='help-block' v-if='errors.lesson_type'>
                <strong>{{ errors.lesson_type[0] }}</strong>
              </span>
            </div>

            <div
              class='form-group col-8 has-feedback'
              :class="{ 'has-error': errors.location }"
              v-if="fields.lesson_type === 'in_person'"
            >
              <label>Location</label>
              <input
                type='text'
                class='form-control'
                name='location'
                value
                v-model='fields.location'
                ref='lessonLocation'
                :disabled='fieldsDisabled'
              />
              <span class='help-block' v-if='errors.location'>
                <strong>{{ errors.location[0] }}</strong>
              </span>
            </div>

            <div
              class='col-8 form-group has-feedback'
              :class="{ 'has-error': errors.timezone_id }"
              v-if="fields.lesson_type === 'virtual'"
            >
              <label>Time Zone</label>
              <select
                class='form-control'
                v-model='fields.timezone_id'
                :disabled='fieldsDisabled'
              >
                <option value disabled>Select...</option>
                <option
                  v-for='(value, key) in timeZomeOptions'
                  :value='key'
                  :key='key'
                >
                  {{ value }}
                </option>
              </select>
              <span class='help-block' v-if='errors.timezone_id'>
                <strong>{{ errors.timezone_id[0] }}</strong>
              </span>
            </div>

            <div
              class='form-group col-lg-9 col-sm-9 col-12 has-feedback align-end'
              :class="{ 'has-error': errors.lesson_price }"
            >
              <label v-if='priceError'>{{ priceError }}</label>
              <div class='d-flex'>
                <span class='dollar-wrapper'>
                  <!--<masked-input class="form-control" v-model="fields.lesson_price" mask="111.11" />-->
                  <input
                    type='number'
                    step='0.01'
                    class='form-control'
                    v-model='fields.lesson_price'
                    :disabled='fieldsDisabled'
                  />
                </span>
                <span class='per-lesson'>per lesson</span>
              </div>

              <span class='help-block' v-if='errors.lesson_price'>
                <strong>{{ errors.lesson_price[0] }}</strong>
              </span>
            </div>

            <div
              v-if="fields.lesson_type === 'in_person'"
              class='form-group col-lg-3 col-sm-3 col-12 has-feedback'
              :class="{ 'has-error': errors.count_participants }"
            >
              <span class='private-lesson'>
                <span v-if='fields.count_participants === 1'>
                  <img src='../../images/man-user.svg' alt />
                </span>
                <span v-if='fields.count_participants > 1'>
                  <img src='../../images/multiple-users-silhouette.svg' alt />
                </span>
              </span>
              <label>Max students</label>
              <input
                @input='replaceInput'
                class='form-control'
                min='1'
                max='100'
                v-model.number='fields.count_participants'
                type='number'
              />
              <span class='help-block' v-if='errors.count_participants'>
                <strong>{{ errors.count_participants[0] }}</strong>
              </span>
            </div>

            <div
              v-if='fields.id == null && loggedInStudent === true'
              class='form-group col-12 has-feedback'
              :class="{ 'has-error': errors.student_note }"
            >
              <label>Description</label>
              <textarea
                :disabled='fieldsDisabled'
                class='form-control'
                name='student_note'
                v-model='fields.student_note'
              ></textarea>
              <span class='help-block' v-if='errors.student_note'>
                <strong>{{ errors.student_note[0] }}</strong>
              </span>
            </div>
            <div v-else class='form-group col-12'>
              <label>Client Note</label>
              <p>{{ fields.student_note ? fields.student_note : '-' }}</p>
            </div>

            <div
              v-if='fields.id != null && loggedInStudent === false'
              class='form-group col-12 has-feedback'
              :class="{ 'has-error': errors.instructor_note }"
            >
              <label>Description</label>
              <textarea
                class='form-control'
                name='instructor_note'
                v-model='fields.instructor_note'
              ></textarea>
              <span class='help-block' v-if='errors.instructor_note'>
                <strong>{{ errors.instructor_note[0] }}</strong>
              </span>
            </div>

            <div class='col-12'>
              <div v-if='errorText' class='has-error'>{{ errorText }}</div>
              <div v-if='successText' class='has-success'>
                {{ successText }}
              </div>
            </div>

            <div class='col-12 mt-30' v-if='fields.id == null'>
              <button
                @keypress.enter.prevent
                type='submit'
                class='btn btn-primary btn-block'
              >
                Submit Request
              </button>
            </div>
            <div class='col-12 mt-30'>
              <div class='g-buttons-wrap'>
                <button
                  @keypress.enter.prevent
                  type='submit'
                  class='btn btn-primary btn-block'
                  v-if='fields.id != null && loggedInStudent === false'
                >
                  Accept Request
                </button>
                <button
                  @click='cancelLessonRequest'
                  type='button'
                  class='btn btn-danger btn-block'
                  v-if='fields.id != null'
                >
                  Cancel Request
                </button>
              </div>
            </div>
          </div>
        </form>
        <div
          id='lesson-request-sent'
          v-if='lessonRequestCreated === true'
          v-html='requestSentConfirmation'
        ></div>
        <div
          id='lesson-request-cancelled'
          v-if='lessonRequestCancelled === true'
        >
          Request has been cancelled.
        </div>
        <div id='lesson-request-accepted' v-if='lessonRequestAccepted === true'>
          Request has been accepted. Lesson created.
        </div>
      </div>
    </magnific-popup-modal>
  </div>
</template>

<script>

import MaskedInput from 'vue-masked-input'
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import MagnificPopupModal from './external/MagnificPopupModal'
import DropdownDatepicker from 'vue-dropdown-datepicker'
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue'
import {mapActions, mapMutations, mapState} from 'vuex'
import {email, numeric, required} from 'vuelidate/lib/validators'
import LoaderButton from './cart/LoaderButton/LoaderButton'
import ct from 'countries-and-timezones'

require('jquery.maskedinput/src/jquery.maskedinput')

$(function() {
  $('[data-toggle="tooltip"]')
  .tooltip()
})

export default {
  components: {
    LoaderButton,
    MaskedInput,
    MagnificPopupModal,
    DropdownDatepicker,
    VueTimepicker
  },
  mixins: [siteAPI, skillectiveHelper],
  props: [
    'lessonRequest',
    'userGenres',
    'siteGenres',
    'selectRange',
    'bookingFeesDescription',
    'instructorId',
    'requestSentConfirmation',
    'loggedInStudent',
    'showCreateBtn',
    'lessonBlockMinPrice',
    'instructorName',
    'studentId'
  ],
  validations: {
    modalData: {
      first_name: {required},
      last_name: {required},
      zip: {required, numeric, valid: v => /^[0-9]{5}(\-[0-9]{4})?$/.test(v)},
      email: {email, required},
      mobile_phone: {required, valid: v => /^((\+1)|(\+37))\s\([0-9]{3}\)\s[0-9]{3}\s[0-9]{4}$/.test(v)},
      accept_terms: {checked: v => v}
    }
  },
  data() {
    return {
      loadingAdd: false,
      selectedInstructors: [],
      loadingBtn: false,
      modalData: {
        first_name: '',
        last_name: '',
        email: '',
        zip: '',
        mobile_phone: '',
        instagram_handle: '',
        newsletter: true,
        accept_terms: false
      },
      fields: {
        id: null,
        instructor_id: null,
        genre: null,
        date: '',
        date_to: '',
        time_from: '',
        time_to: '',
        count_participants: '1',
        lesson_price: null,
        location: null,
        student_note: null,
        instructor_note: null,
        price_per: 'lesson',
        lesson_type: 'in_person',
        timezone_id: null
      },
      timeOptions: [],
      timeZomeOptions: [],
      formGenres: [],
      lessonTypes: [],
      instructorGenres: [],
      lessonRequestCreated: false,
      lessonRequestCancelled: false,
      lessonRequestAccepted: false,
      fieldsDisabled: false,
      isDateInputInit: false,
      openRegisteredModal: false,
      loadingOtherInstructors: false,
      selectAll: false,
    }
  },
  watch: {
    selectRange: function() {
      this.isDateInputInit = true

      if (this.selectRange !== null) {
        this.fields.date = this.selectRange.startStr
        this.fields.date_to = this.selectRange.startStr
        this.fields.time_from = moment(this.selectRange.startStr)
        .format(
          'H:mm:ss'
        )
        this.fields.time_to = moment(this.selectRange.endStr)
        .format('H:mm:ss')
        this.$refs.modal.open()
      }
    }
  },
  computed: {
    ...mapState(['storeErrors', 'storeErrorText', 'instructors']),
    priceError: function() {
      if (
        !this.fields.time_from ||
        !this.fields.time_to ||
        !this.lessonBlockMinPrice ||
        Number(this.lessonBlockMinPrice) === 0 ||
        !Number(this.fields.lesson_price) ||
        Number(this.fields.lesson_price) === 0
      ) {
        return ''
      }

      let countSpots =
        moment(moment(this.fields.time_from, ['h:mm A']))
        .diff(moment(this.fields.time_to, ['h:mm A'])) / 60000 / 30

      if (
        Math.abs(Number(this.lessonBlockMinPrice) * Number(countSpots)) >
        Number(this.fields.lesson_price)
      ) {
        return `The minimum amount this instructor will accept is $${ this.lessonBlockMinPrice } per half hour.`
      } else {
        return ''
      }
    }
  },
  methods: {
    ...mapActions(['addToClientList', 'createToClientList', 'addStudentToInstructorList', 'getInstructors']),
    ...mapMutations(['CLEAR_INPUT']),
    async addToInstructorsList() {
      this.loadingAdd = true
      const instructors = this.selectedInstructors.map(instructorId => +instructorId)
      const data = {
        required: this.studentId,
        instructors
      }
      await this.addStudentToInstructorList(data)
      this.loadingAdd = false
      // this.closeAllInstructorsModal()
    },
    onSelectAll() {
      this.selectAll
        ? this.selectedInstructors = []
        : this.instructors.forEach(instructor => this.selectedInstructors.push(+instructor.id))
    },
    changeEmail() {
      if (this.storeErrors) {
        this.CLEAR_INPUT()
      }
    },
    openAllInstructorsModal() {
      $('#instructorsModal').modal('show')
    },
    closeAllInstructorsModal() {
      $('#instructorsModal').modal('hide')
    },
    showJoinModal() {
      $('#joinClient').modal('show')
    },
    showRegisteredModal() {
      $('#registeredModal').modal('show')
    },
    showSuccessAddedModal() {
      $('#successAdded').modal('show')
    },
    showOtherInstructorsListModal() {
      $('#otherInstructorsList').modal('show')
    },
    closeJoinModal() {
      $('#joinClient').modal('hide')
    },
    closeRegisteredModal() {
      $('#registeredModal').modal('hide')
    },
    async joinClientList() {
      if (this.$v.$invalid) {
        this.$v.$touch()
        return
      }
      try {
        this.loadingBtn = true
        const data = {
          instructor_id: this.instructorId,
          first_name: this.modalData.first_name,
          last_name: this.modalData.last_name,
          instagram_handle: this.modalData.instagram_handle,
          zip: this.modalData.zip,
          email: this.modalData.email,
          mobile_phone: this.modalData.mobile_phone,
          newsletter: this.modalData.newsletter
        }
        await this.createToClientList(data)
        if (!this.storeErrors?.email?.length) {
          this.closeForm()
          this.showSuccessAddedModal()
        }
      } catch (e) {
        this.loadingBtn = false
      }
      this.loadingBtn = false
    },
    closeForm() {
      this.openRegisteredModal = false
      $('#registeredModal').modal('hide')
      this.modalData = {newsletter: true}
      this.$v.$reset()
    },
    tooltipContent() {
      return `Click here to add your contact info to ${ this.instructorName }'s Client List so you can be notified when classes, privates or workshops become available.`
    },
    async addingNotification() {
      this.apiPost('/api/student/instructor/geo-notifications/' + this.instructorId)
      this.apiPost('/api/student/instructor/virtual-lesson-notifications/' + this.instructorId)
    },
    async successJoinedToClient() {
      this.closeJoinModal()
      if (this.loggedInStudent) {
        try {
          let instructors = []
          instructors.push(this.instructorId)
          const data = {
            required: this.studentId,
            instructors: instructors
          }
          await this.addToClientList(this.instructorId)
          await this.addStudentToInstructorList(data)
          this.showSuccessAddedModal()
          await this.addingNotification()
        } catch (e) {
          console.log(e)
        }
      } else {
        this.showRegisteredModal()
      }
    },
    replaceInput() {
      if (this.fields.spots_count > 100) {
        setTimeout(() => {
          this.fields.spots_count = '100'
        }, 20)
      }
    },
    clearTimepicker(input) {
      if (!input) {
        return false
      }

      this.$refs[input].hour = ''
      this.$refs[input].minute = ''
      this.$refs[input].apm = ''
    },
    timeFormChange() {
      if (moment(this.fields.time_from)) {
        this.fields.time_to = moment(this.fields.time_from, ['h:mm a'])
        .add('30', 'minutes')
        .format('h:mm a')
      }
    },
    onSubmit() {
      if (moment(this.fields.date))
        this.fields.date = moment(this.fields.date)
        .format('YYYY-MM-DD')

      if (moment(this.fields.date_to))
        this.fields.date_to = moment(this.fields.date_to)
        .format('YYYY-MM-DD')

      if (this.fields.lesson_type === 'virtual') {
        this.fields.count_participants = 1
      }

      if (moment(this.fields.time_from)) {
        this.fields.time_from = moment(this.fields.time_from, [
          'h:mm A'
        ])
        .format('HH:mm:ss')
      }

      if (moment(this.fields.time_to)) {
        this.fields.time_to = moment(this.fields.time_to, ['h:mm A'])
        .format(
          'HH:mm:ss'
        )
      }
      if (this.fields.id > 0)
        this.apiPost(
          '/api/lesson-request/' + this.fields.id + '/accept',
          this.fields
        )
      else this.apiPost('/api/lesson-request', this.fields)
    },
    componentHandlePostResponse() {
      if (this.cancellingRequest === true) {
        this.cancellingRequest = false
        this.lessonRequestCancelled = true
        this.$root.$emit('lessonRequestUpdated', this.fields.id)
      } else {
        if (this.fields.id != null) {
          this.lessonRequestAccepted = true
          this.$root.$emit('lessonRequestUpdated', this.fields.id)
        } else {
          this.lessonRequestCreated = true
        }
      }
      this.clearFormAndClosePopup()
      //                this.$refs.modal.close()
    },
    openPopup() {
      this.isDateInputInit = true

      setTimeout(() => {
        this.$refs.datepicker.year = 2021
        this.$refs.datepickerTo.year = 2021
      }, 1)

      this.lessonRequestCreated = false
      this.lessonRequestCancelled = false
      this.lessonRequestAccepted = false

      this.fields.instructor_id = this.instructorId

      if (
        (this.loggedInStudent && this.fields.id == null) ||
        this.fields.id != null
      ) {
        if (this.userGenres !== undefined)
          this.formGenres = _.cloneDeep(this.userGenres)
        else if (this.fields.id != null)
          this.formGenres = _.cloneDeep(this.instructorGenres)

        if (this.fields.genre !== null) {
          var _inFormGenres = false
          for (var i = 0; i < this.formGenres.length; i++)
            if (this.fields.genre === this.formGenres[i].id) {
              _inFormGenres = true
              break
            }

          if (!_inFormGenres)
            for (i = 0; i < this.siteGenres.length; i++)
              if (this.fields.genre === this.siteGenres[i].id) {
                this.formGenres.push(this.siteGenres[i])
                break
              }
        }
        this.$refs.modal.open()
      } else {

        Cookies.set('backToRequestLesson', this.instructorId)
        window.location = '/login'

      }
    },
    clearFormAndClosePopup() {
      this.clearSubmittedForm()
      this.successText = null

      this.isDateInputInit = false

      // this.$refs.timeTo.hour = "";
      // this.$refs.timeTo.minute = "";
      // this.$refs.timeTo.apm = "";

      // this.$refs.timeFrom.hour = "";
      // this.$refs.timeFrom.minute = "";
      // this.$refs.timeFrom.apm = "";

      // this.selectRange = null;
      // this.$refs.modal.close()
    },
    initNewPlacesAutocomplete(_ref) {
      var thisComponent = this
      var autocomplete = this.initializeLocationField(this.$refs[_ref], [
        'address'
      ])
      google.maps.event.addListener(
        autocomplete,
        'place_changed',
        function() {
          thisComponent.fields.location = thisComponent.$refs[_ref].value
          console.log(thisComponent.$refs[_ref].value)
        }
      )
    },
    cancelLessonRequest() {
      this.cancellingRequest = true
      this.apiPost(
        '/api/lesson-request/' + this.fields.id + '/cancel',
        this.fields
      )
    }
  },

  async created() {
    this.timeOptions = this.getTimeOptions()
    this.timeZomeOptions = ct.getAllTimezones()

    const tzs = {
      'America/New_York': 'America/New_York UTC-05:00',
      'America/Indiana/Indianapolis': 'America/Indiana/Indianapolis UTC-05:00',
      'America/Chicago': 'America/Chicago UTC-06:00',
      'America/Denver': 'America/Denver UTC-07:00',
      'America/Phoenix': 'America/Phoenix UTC-07:00',
      'America/Los_Angeles': 'America/Los_Angeles UTC-08:00',
      'America/Anchorage': 'America/Anchorage UTC-09:00',
      'Pacific/Honolulu': 'Pacific/Honolulu UTC-10:00',
      'America/Chihuahua': 'America/Chihuahua UTC-07:00',
      'America/Guatemala': 'America/Guatemala UTC-06:00',
      'America/Regina': 'America/Regina UTC-06:00',
      'America/Mexico_City': 'America/Mexico_City UTC-06:00',
      'America/Bogota': 'America/Bogota UTC-05:00',
      'America/Caracas': 'America/Caracas UTC-04:30',
      'America/Halifax': 'America/Halifax UTC-04:00',
      'America/Asuncion': 'America/Asuncion UTC-04:00',
      'America/La_Paz': 'America/La_Paz UTC-04:00',
      'America/Cuiaba': '	America/Cuiaba UTC-04:00',
      // 'America/Santiago': '	America/Santiago UTC-04:00',
      'America/St_Johns': 'America/St_Johns UTC-03:30',
      'America/Sao_Paulo': '	America/Sao_Paulo UTC-03:00',
      'America/Santiago': '	America/Godthab UTC-03:00',
      'America/Cayenne': '	America/Cayenne UTC-03:00',
      'America/Argentina/Buenos_Aires':
        'America/Argentina/Buenos_Aires UTC-03:00',
      'America/Montevideo': '	America/Montevideo UTC-03:00',
      'Atlantic/Cape_Verde': 'Atlantic/Cape_Verde UTC-01:00',
      'Atlantic/Azores': 'Atlantic/Azores UTC-01:00',
      'Africa/Casablanca': 'Africa/Casablanca UTC+00:00',
      'Atlantic/Reykjavik': 'Atlantic/Reykjavik UTC+00:00',
      'Europe/London': 'Europe/London UTC+00:00',
      'Europe/Berlin': 'Europe/Berlin UTC+01:00',
      'Europe/Paris': 'Europe/Paris UTC+01:00',
      'Africa/Lagos': 'Africa/Lagos UTC+01:00',
      'Europe/Budapest': 'Europe/Budapest UTC+01:00',
      'Europe/Warsaw': 'Europe/Warsaw UTC+01:00',
      'Africa/Windhoek': 'Africa/Windhoek UTC+01:00',
      'Europe/Istanbul': 'Europe/Istanbul UTC+02:00',
      'Europe/Kiev': 'Europe/Kiev UTC+02:00',
      'Africa/Cairo': 'Africa/Cairo UTC+02:00',
      'Asia/Damascus': 'Asia/Damascus UTC+02:00',
      'Asia/Amman': 'Europe/Amman UTC+02:00',
      'Africa/Johannesburg': 'Africa/Johannesburg UTC+02:00',
      'Asia/Jerusalem': 'Asia/Jerusalem UTC+02:00',
      'Asia/Beirut': 'Asia/Beirut UTC+02:00',
      'Asia/Baghdad': 'Asia/Baghdad UTC+03:00',
      'Europe/Minsk': 'Europe/Minsk UTC+03:00',
      'Asia/Riyadh': 'Asia/Riyadh UTC+03:00',
      'Africa/Nairobi': 'Africa/Nairobi UTC+03:00',
      'Asia/Tehran': 'Asia/Tehran UTC+03:30',
      'Europe/Moscow': 'Europe/ Moscow UTC+04:00',
      'Asia/Tbilisi': 'Asia/Tbilisi UTC+04:00',
      'Asia/Yerevan': 'Asia/Yerevan UTC+04:00',
      'Asia/Dubai': 'Asia/Dubai UTC+04:00',
      'Asia/Baku': 'Asia/Baku UTC+04:00',
      'Indian/Mauritius': 'Indian/Mauritius UTC+04:00',
      'Asia/Kabul': 'Asia/Kabul UTC+04:30',
      'Asia/Tashkent': 'Asia/Tashkent UTC+05:00',
      'Asia/Karachi': '	Asia/Karachi UTC+05:00',
      'Asia/Colombo': 'Asia/Colombo UTC+05:30',
      'Asia/Kolkata': 'Asia/Kolkata UTC+05:30',
      'Asia/Kathmandu': 'Asia/Kathmandu UTC+05:45',
      'Asia/Almaty': 'Asia/Almaty UTC+06:00',
      'Asia/Dhaka': 'Asia/Dhaka UTC+06:00',
      'Asia/Yekaterinburg': 'Asia/Yekaterinburg UTC+06:00',
      'Asia/Yangon': 'Asia/Yangon UTC+06:30',
      'Asia/Bangkok': 'Asia/Bangkok UTC+07:00',
      'Asia/Novosibirsk': 'Asia/Novosibirsk UTC+07:00',
      'Asia/Krasnoyarsk': 'Asia/Krasnoyarsk UTC+08:00',
      'Asia/Ulaanbaatar': 'Asia/Ulaanbaatar UTC+08:00',
      'Asia/Shanghai': 'Asia/Shanghai UTC+08:00',
      'Australia/Perth': 'Australia/Perth UTC+08:00',
      'Asia/Singapore': 'Asia/Singapore UTC+08:00',
      'Asia/Taipei': 'Asia/Taipei UTC+08:00',
      'Asia/Irkutsk': 'Asia/Irkutsk UTC+09:00',
      'Asia/Seoul': 'Asia/Seoul UTC+09:00',
      'Asia/Tokyo': 'Asia/Tokyo UTC+09:00',
      'Australia/Darwin': '	Australia/Darwin UTC+09:30',
      'Australia/Adelaide': '	Australia/Adelaide UTC+09:30',
      'Australia/Hobart': '	Australia/Hobart UTC+10:00',
      'Asia/Yakutsk': 'Asia/Yakutsk UTC+10:00',
      'Australia/Brisbane': '	Australia/Brisbane UTC+10:00',
      'Pacific/Port_Moresby': 'Pacific/Port_Moresby UTC+10:00',
      'Australia/Sydney': 'Australia/Sydney UTC+10:00',
      'Asia/Vladivostok': 'Asia/Vladivostok UTC+11:00',
      'Pacific/Guadalcanal': 'Pacific/Guadalcanal UTC+11:00',
      'Pacific/Fiji': 'Pacific/Fiji UTC+12:00',
      'Asia/Magadan': 'Asia/Magadan UTC+12:00',
      'Pacific/Auckland': 'Pacific/Auckland UTC+12:00',
      'Pacific/Tongatapu': 'Pacific/Tongatapu UTC+13:00',
      'Pacific/Apia': 'Pacific/Apia UTC+13:00'
    }

    this.timeZomeOptions = tzs

    // window.usTimezones != undefined ? window.usTimezones : [];
    // getAllTimezones() getTimezonesForCountry('US'); ; example https://www.npmjs.com/package/countries-and-timezones#getalltimezones
    this.formGenres = this.userGenres
    // this.lessonTypes =
    //   window.lessonTypes != undefined ? window.lessonTypes : [];

    // console.log(this.lessonTypes)

    this.lessonTypes = {
      in_person: 'In Person',
      virtual: 'Virtual'
    }
    await this.getInstructors(this.instructorId)
  },
  mounted() {
    this.initNewPlacesAutocomplete('lessonLocation')
    this.tooltipContent()
    this.$root.$on('lessonRequestUpdateInit', (lessonRequest) => {
      this.instructorGenres = lessonRequest.instructor.genres

      this.isDateInputInit = true

      this.fields = {
        id: lessonRequest.id,
        genre: lessonRequest.genre_id,
        date: moment(lessonRequest.start, ['YYYY-MM-DD H:mm:ss'])
        .format(
          'YYYY-MM-DD'
        ),
        date_to: moment(lessonRequest.end, ['YYYY-MM-DD H:mm:ss'])
        .format(
          'YYYY-MM-DD'
        ),
        time_from: moment(lessonRequest.start)
        .format('h:mm a'),
        time_to: moment(lessonRequest.end)
        .format('h:mm a'),
        count_participants: lessonRequest.count_participants + '',
        lesson_price: lessonRequest.lesson_price,
        location: lessonRequest.location,
        timezone_id: lessonRequest.timezone_id_name,
        lesson_type: lessonRequest.lesson_type,
        student_note: lessonRequest.student_note,
        instructor_note: lessonRequest.instructor_note
      }

      setTimeout(() => {
        this.$refs.datepicker.year = Number(
          moment(lessonRequest.start, ['YYYY-MM-DD H:mm:ss'])
          .format('YYYY')
        )
        this.$refs.datepicker.month = Number(
          moment(lessonRequest.start, ['YYYY-MM-DD H:mm:ss'])
          .format('MM')
        )
        this.$refs.datepicker.day = Number(
          moment(lessonRequest.start, ['YYYY-MM-DD H:mm:ss'])
          .format('DD')
        )

        this.$refs.datepickerTo.year = Number(
          moment(lessonRequest.end, ['YYYY-MM-DD H:mm:ss'])
          .format('YYYY')
        )
        this.$refs.datepickerTo.month = Number(
          moment(lessonRequest.end, ['YYYY-MM-DD H:mm:ss'])
          .format('MM')
        )
        this.$refs.datepickerTo.day = Number(
          moment(lessonRequest.end, ['YYYY-MM-DD H:mm:ss'])
          .format('DD')
        )
      }, 10)

      this.fieldsDisabled =
        this.fields.id != null && this.loggedInStudent === true

      this.openPopup()
    })

    setTimeout(() => {
      if (
        this.instructorId != null &&
        Cookies.get('backToRequestLesson') === this.instructorId
      ) {
        Cookies.remove('backToRequestLesson')
        this.openPopup()
      }
      window.jQuery('.mask-input')
      .mask('99/99/9999')
    }, 100)
  }
}
</script>

<style lang='scss'>
.title {
  color: #444;
  font-family: Hind Vadodara;
  font-size: 16px;
  font-weight: 400;
}

.instructor-avatar {
  position: relative;
  width: 125px;
  height: 125px;
  img {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    border-radius: 50%;
  }
}

.profile-genres span {
  background-color: #f4f4f4;
  border-radius: 2px;
  color: #444;
  display: inline-block;
  font-family: Hind Vadodara, sans-serif;
  font-size: 14px;
  font-weight: 600;
  height: 31px;
  margin-bottom: 5px;
  margin-right: 5px;
  padding: 4px 5px;
  text-align: center;
}

.inst {
  font-family: Hind Vadodara, sans-serif;
  font-style: normal;
  font-weight: 500;
  font-size: 14px;
  line-height: 24px;
  color: #AAAAAA;
  display: flex;
  align-items: center;
  padding: 5px 0;
}

.modal-open, .modal {
  padding-right: 0 !important
}

.form-title {
  font-size: 25px;
  text-align: center;

}

.disabled-scroll {
  overflow: hidden;
}

.btn {
  font-size: 14px;
  margin-bottom: 8px;
}

.modal-body {
  font-size: 14px;
}

#successAdded {
  .modal-body {
    font-size: 20px;
  }
}

#otherInstructorsListTitle {
  font-size: 14px;
}

.tooltip {
  font-size: 10px;
}

#instructorsModal {
  .modal-footer {
    justify-content: center;

    button {
      width: 100%;
    }
  }
}
</style>
