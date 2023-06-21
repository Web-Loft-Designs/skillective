<template>
  <div id="registration-form-container">
    <form method="post" @submit.prevent="onSubmit" v-if="!formSubmitted">
      <h3 class="form-title">Profile Information</h3>
      <div class="d-flex flex-wrap">
        <div class="label-w-100">
          <label>Full name</label>
        </div>
        <div
          class="form-group first-name has-feedback"
          :class="{ 'has-error': errors.first_name }"
        >
          <input
            type="text"
            class="form-control"
            required
            name="first_name"
            value=""
            v-model="fields.first_name"
            placeholder="First Name"
          />
          <span class="help-block" v-if="errors.first_name">
            <strong>{{ errors.first_name[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group last-name has-feedback"
          :class="{ 'has-error': errors.last_name }"
        >
          <input
            type="text"
            class="form-control"
            required
            name="last_name"
            value=""
            v-model="fields.last_name"
            placeholder="Last Name"
          />
          <span class="help-block" v-if="errors.last_name">
            <strong>{{ errors.last_name[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group has-feedback"
          :class="{ 'has-error': errors.instagram_handle }"
        >
          <label>Instagram Handle</label>
          <input
            type="text"
            class="form-control"
            name="instagram_handle"
            value=""
            v-model="fields.instagram_handle"
            placeholder="@instagram_name"
          />
          <span class="help-block" v-if="errors.instagram_handle">
            <strong>{{ errors.instagram_handle[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group input-city has-feedback"
          :class="{ 'has-error': errors.city }"
        >
          <label>City</label>
          <input
            type="text"
            class="form-control"
            name="city"
            value=""
            v-model="fields.city"
            placeholder="City"
          />
          <span class="help-block" v-if="errors.city">
            <strong>{{ errors.city[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group input-state has-feedback"
          :class="{ 'has-error': errors.state }"
        >
          <label>State</label>
          <select
            class="form-control"
            name="state"
            v-bind:class="{ 'select-empty': fields.state === '' }"
            v-model="fields.state"
          >
            <option value="">State</option>
            <option v-for="state in usStates" :value="state.code" :key="state.code" >
              {{ state.name }}
            </option>
          </select>
          <span class="help-block" v-if="errors.state">
            <strong>{{ errors.state[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group input-zip has-feedback"
          :class="{ 'has-error': errors.zip }"
        >
          <label>ZIP</label>
          <input
            type="text"
            class="form-control"
            name="zip"
            value=""
            v-model="fields.zip"
            placeholder="ZIP code"
          />
          <span class="help-block" v-if="errors.zip">
            <strong>{{ errors.zip[0] }}</strong>
          </span>
        </div>

<!--        <div-->
<!--          class="form-group w-50 has-feedback"-->
<!--          :class="{ 'has-error': errors.dob }"-->
<!--        >-->
<!--          <label>Date of Birth</label>-->
<!--          &lt;!&ndash;<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" :placeholder="'mm/dd/yyyy'" v-model="fields.dob" name="dob" :format="'MM/dd/yyyy'"></datepicker>&ndash;&gt;-->
<!--          <dropdown-datepicker-->
<!--            :max-year="2021"-->
<!--            :minYear="1940"-->
<!--            display-format="mdy"-->
<!--            v-model="fields.dob"-->
<!--            submit-format="yyyy-mm-dd"-->
<!--          ></dropdown-datepicker>-->

<!--          <span class="help-block" v-if="errors.dob">-->
<!--            <strong>{{ errors.dob[0] }}</strong>-->
<!--          </span>-->
<!--        </div>-->

<!--        <div-->
<!--          class="form-group w-50 has-feedback"-->
<!--          :class="{ 'has-error': errors.gender }"-->
<!--        >-->
<!--          <label>Gender</label>-->
<!--          <div class="radio-wrapper">-->
<!--            <label class="radio-item" for="male">-->
<!--              <input-->
<!--                v-model="fields.gender"-->
<!--                name="gender"-->
<!--                type="radio"-->
<!--                id="male"-->
<!--                value="male"-->
<!--              />-->
<!--              <span class="checkmark"></span>-->
<!--              Male-->
<!--            </label>-->
<!--            <label class="radio-item" for="female">-->
<!--              <input-->
<!--                v-model="fields.gender"-->
<!--                name="gender"-->
<!--                type="radio"-->
<!--                id="female"-->
<!--                value="female"-->
<!--              />-->
<!--              <span class="checkmark"></span>-->
<!--              Female-->
<!--            </label>-->
<!--          </div>-->

<!--          <span class="help-block" v-if="errors.gender">-->
<!--            <strong>{{ errors.gender[0] }}</strong>-->
<!--          </span>-->
<!--        </div>-->

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.email }"
        >
          <label>Email</label>
          <input
            type="email"
            class="form-control"
            required
            name="email"
            value=""
            v-model="fields.email"
            placeholder="Email"
          />
          <span class="help-block" v-if="errors.email">
            <strong>{{ errors.email[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.mobile_phone }"
        >
          <label>Phone number</label>
          <masked-input
            :class="'form-control'"
            v-model="fields.mobile_phone"
            :placeholder="'(___) ___ ____'"
            mask="(111) 111 1111"
          />
          <span class="help-block" v-if="errors.mobile_phone">
            <strong>{{ errors.mobile_phone[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group has-feedback"
          :class="{ 'has-error': errors.genres }"
        >
          <label>Skills I would like to follow …</label>
          <!--<div class="field" v-for="genre in siteGenres">-->
          <!--<input v-model="fields.genres" type="checkbox" :id="'genre-'+genre.id" :value="genre.id">-->
          <!--<label :for="'genre-'+genre.id">{{ genre.title }}</label>-->
          <!--</div>-->

          <multiselect
            @input="changeIt"
            v-model="genresTemp"
            :options="siteGenres"
            label="title"
            track-by="id"
            :preserve-search="true"
            :close-on-select="false"
            :clear-on-select="false"
            :multiple="true"
            placeholder="Select Skill"
          >
            <template slot="selection" slot-scope="{ values, isOpen }"
              ><span class="multiselect__single" v-if="values.length && !isOpen"
                >{{ values.length }} options selected</span
              ></template
            >
            <template slot="option" slot-scope="props"
              ><div class="option__checkbox">
                {{ props.option.title }}
              </div></template
            >
          </multiselect>
          <input type="hidden" name="genres" v-model="fields.genres" />

          <!--<select class="form-control" name="state" v-bind:class="{ 'select-empty': fields.genres === ''}" v-model="fields.genres" multiple>-->
          <!--&lt;!&ndash;<option value="">Select Genre</option>&ndash;&gt;-->
          <!--<option v-for="genre in siteGenres" label="title" track-by="title" :value='genre.id'>{{ genre.title }}</option>-->
          <!--</select>-->

          <span class="help-block" v-if="errors.genres">
            <strong>{{ errors.genres[0] }}</strong>
          </span>
        </div>

        <!--<div class="form-group has-feedback" :class="{ 'has-error' : errors.password }">-->
        <!--<input type="password" class="form-control" name="password" value="" v-model="fields.password" placeholder="Set Your Password">-->
        <!--<span class="help-block" v-if="errors.password">-->
        <!--<strong>{{ errors.password[0] }}</strong>-->
        <!--</span>-->
        <!--</div>-->

        <!--<div class="form-group has-feedback" :class="{ 'has-error' : errors.password_confirmation }">-->
        <!--<input type="password" class="form-control" name="password_confirmation" value="" v-model="fields.password_confirmation" placeholder="Confirm Password">-->
        <!--<span class="help-block" v-if="errors.password_confirmation">-->
        <!--<strong>{{ errors.password_confirmation[0] }}</strong>-->
        <!--</span>-->
        <!--</div>-->

        <div
          class="form-group checkbox-wrapper has-feedback"
          :class="{ 'has-error': errors.accept_terms }"
        >
          <div class="field">
            <label for="accept-terms">
              <input
                v-model="fields.accept_terms"
                type="checkbox"
                id="accept-terms"
                :value="1"
              />
              <span class="checkmark"></span>
              I agree to the
              <a href="/terms" target="_blank">terms of service</a>
            </label>
          </div>

          <span class="help-block" v-if="errors.accept_terms">
            <strong>{{ errors.accept_terms[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group checkbox-wrapper has-feedback"
        >
          <div class="field">
            <label for="sms-notification">
              <input
                v-model="fields.sms_notification"
                type="checkbox"
                id="sms-notification"
                :value="false"
              />
              <span class="checkmark"></span>
              By clicking this box, you represent that you have the authority to agree to receive SMS messages on the telephone number that you provided to us. Message frequency depends upon your activity. Standard message and data rates may apply. SMS messaging is not available in all areas. Not all mobile devices or handsets may be supported. Skillective and the mobile carriers are not liable for delayed or undelivered messages.
            </label>
          </div>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-block">Get Started</button>
          <!--<a href="/login" class="text-center">I already have a membership</a>-->
        </div>
      </div>

      <div v-if="errorText" class="has-error">{{ errorText }}</div>
    </form>

    <div v-if="formSubmitted" class="has-success">{{ successText }}</div>
  </div>
</template>

<script>
import siteAPI from "../mixins/siteAPI.js";
import MaskedInput from "vue-masked-input";
import DropdownDatepicker from "vue-dropdown-datepicker";
require("jquery.maskedinput/src/jquery.maskedinput");
export default {
  components: {
    MaskedInput,
    DropdownDatepicker,
  },
  mixins: [siteAPI],
  props: ["initialFormData", "categorizedGenres"],
  data() {
    return {
      formSubmitted: false,
      genresTemp: [],
      fields: {
        first_name: "",
        last_name: "",
        email: "",
        address: "",
        city: "",
        state: "",
        zip: "",
        // dob: "",
        // genres: [],
        // gender: "",
        mobile_phone: "",
        about_me: "",
        //                    password : '',
        //                    password_confirmation : '',
        accept_terms: false,
        sms_notification: false,
        instagram_handle: "",
      },
    };
  },
  methods: {
    onSubmit() {
      // if (moment(this.fields.dob))
        // this.fields.dob = moment(this.fields.dob).format("YYYY-MM-DD");
      this.apiPost("/api/student/register", this.fields);
    },
    componentHandlePostResponse(responseData) {
      this.formSubmitted = true;
      $([document.documentElement, document.body]).animate(
        {
          scrollTop: $("#registration-form-container").offset().top - 200,
        },
        2000
      );
    },
    changeIt: function () {
      var tempArry = [];
      this.genresTemp.forEach((item) => {
        tempArry.push(item.id);
      });
      this.fields.genres = tempArry;
    },
  },
  created: function () {
    setTimeout(function () {
      window.jQuery(".mask-input").mask("99/99/9999");
    }, 200);

    if (this.initialFormData.email != undefined)
      this.fields.email = this.initialFormData.email;
    if (this.initialFormData.mobile_phone != undefined)
      this.fields.mobile_phone = this.initialFormData.mobile_phone;
    if (this.initialFormData.invitation != undefined) {
      this.fields.invitation = this.initialFormData.invitation;
    }
  },
};
</script>