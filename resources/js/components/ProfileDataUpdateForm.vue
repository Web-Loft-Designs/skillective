<template>
  <div id="profile-form-container">
    <form method="post" @submit.prevent="onSubmit">
      <p class="login-box-msg">Personal info</p>
      <div class="d-flex flex-wrap">
        <div
          class="form-group f-name w-50 has-feedback"
          :class="{ 'has-error': errors.first_name, color: !isStudent }"
        >
          <label>Complete name</label>
          <input
            type="text"
            class="form-control"
            required
            name="first_name"
            value
            v-model="fields.first_name"
            placeholder="First Name"
          />
          <span class="help-block" v-if="errors.first_name">
            <strong>{{ errors.first_name[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group l-name color w-50 has-feedback"
          :class="{ 'has-error': errors.last_name, color: !isStudent }"
        >
          <label style="opacity: 0; visability: hidden">Complete name</label>
          <input
            type="text"
            class="form-control"
            required
            name="last_name"
            value
            v-model="fields.last_name"
            placeholder="Last Name"
          />
          <span class="help-block" v-if="errors.last_name">
            <strong>{{ errors.last_name[0] }}</strong>
          </span>
        </div>

        <div class="form-group color has-feedback">
          <label>Instagram Handle</label>
          <input
            type="text"
            :disabled="!isStudent"
            class="form-control"
            v-model="fields.instagram_handle"
            placeholder="Instagram Handle"
          />
        </div>

        <div
          class="form-group has-feedback"
          :class="{ 'has-error': errors.email }"
        >
          <label>Email address</label>
          <input
            type="email"
            class="form-control"
            required
            name="email"
            value
            v-model="fields.email"
            placeholder="Email"
          />
          <span class="help-block" v-if="errors.email">
            <strong>{{ errors.email[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group has-feedback"
          :class="{ 'has-error': errors.mobile_phone }"
        >
          <label>Phone number</label>
          <masked-input
            :class="'form-control'"
            v-model="fields.mobile_phone"
            :placeholder="'+1 (___) ___ ____'"
            mask="\+1 (111) 111 1111"
          />
          <span class="help-block" v-if="errors.mobile_phone">
            <strong>{{ errors.mobile_phone[0] }}</strong>
          </span>
        </div>

        <!--<div class="form-group has-feedback" :class="{ 'has-error' : errors.address }">-->
        <!---->
        <!--<input type="text" class="form-control" name="address" value="" v-model="fields.address" placeholder="Address">-->
        <!--<span class="help-block" v-if="errors.address">-->
        <!--<strong>{{ errors.address[0] }}</strong>-->
        <!--</span>-->
        <!--</div>-->

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.city }"
        >
          <label>City</label>
          <input
            type="text"
            class="form-control"
            name="city"
            value
            v-model="fields.city"
            placeholder="City"
          />
          <span class="help-block" v-if="errors.city">
            <strong>{{ errors.city[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-30 has-feedback"
          :class="{ 'has-error': errors.state }"
        >
          <label>State</label>
          <select class="form-control" name="state" v-model="fields.state">
            <option value>Select State</option>
            <option v-for="state in usStates" :value="state.code">
              {{ state.name }}
            </option>
          </select>
          <span class="help-block" v-if="errors.state">
            <strong>{{ errors.state[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-20 has-feedback"
          :class="{ 'has-error': errors.zip }"
        >
          <label>ZIP</label>
          <input
            type="text"
            class="form-control"
            name="zip"
            value
            v-model="fields.zip"
            placeholder="ZIP code"
          />
          <span class="help-block" v-if="errors.zip">
            <strong>{{ errors.zip[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.dob }"
        >
          <label>Date of Birth</label>
          <!--<datepicker :monday-first="false" :typeable="true" :input-class="'mask-input'" v-model="fields.dob" name="dob" :placeholder="'mm/dd/yyyy'"  :format="'MM/dd/yyyy'"></datepicker>-->
          <dropdown-datepicker
            display-format="mdy"
            v-model="fields.dob"
            submit-format="yyyy-mm-dd"
            ref="datepicker"
            :minYear="1940"
            :maxYear="currentYear"
          ></dropdown-datepicker>

          <span class="help-block" v-if="errors.dob">
            <strong>{{ errors.dob[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.gender }"
        >
          <label>Gender</label>
          <div class="radio-wrapper">
            <label class="radio-item" for="male">
              <input
                v-model="fields.gender"
                name="gender"
                type="radio"
                id="male"
                value="male"
              />
              <span class="checkmark"></span>
              Male
            </label>
            <label class="radio-item" for="female">
              <input
                v-model="fields.gender"
                name="gender"
                type="radio"
                id="female"
                value="female"
              />
              <span class="checkmark"></span>
              Female
            </label>
          </div>
          <span class="help-block" v-if="errors.gender">
            <strong>{{ errors.gender[0] }}</strong>
          </span>
        </div>
        <div
          class="form-group has-feedback"
          :class="{ 'has-error': errors.about_me }"
        >
          <label>About me</label>
          <!-- <textarea
            class="form-control"
            name="about_me"
            placeholder="about you"
            v-model="fields.about_me"
          ></textarea> -->

          <text-editor
            name="about_me"
            placeholder="About you"
            v-model="fields.about_me"
          />

          <span class="help-block" v-if="errors.about_me">
            <strong>{{ errors.about_me[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group has-feedback"
          :class="{ 'has-error': errors.genres }"
          v-if="fields.genres != undefined"
        >
          <label v-if="userProfileData.isInstructor">Genres</label>
          <label v-if="!userProfileData.isInstructor">Genres</label>
          <div class="scroll">
            <div class="genres-wrapper">
              <div class="item" v-for="(genres, catTitle) in categorizedGenres">
                <label class="title">{{ catTitle }}</label>
                <div class="checkbox-wrapper">
                  <div class="field" v-for="genre in genres">
                    <label :for="'genre-' + genre.id">
                      <input
                        v-model="fields.genres"
                        type="checkbox"
                        :id="'genre-' + genre.id"
                        :value="genre.id"
                      />
                      <span class="checkmark"></span>
                      {{ genre.title }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <span class="help-block" v-if="errors.genres">
            <strong>{{ errors.genres[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.lesson_block_min_price }"
          v-if="userProfileData.isInstructor"
        >
          <label>
            <strong style="display: block">Request a Lesson (In person) </strong>
            Minimum price allowed per half hour
          </label>
          <input
            type="text"
            class="form-control"
            name="lesson_block_min_price"
            value
            v-model="fields.lesson_block_min_price"
            placeholder="Min price per 30 minute lesson"
          />
          <span class="help-block" v-if="errors.lesson_block_min_price">
            <strong>{{ errors.lesson_block_min_price[0] }}</strong>
          </span>
        </div>

        <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.virtual_min_price }"
          v-if="userProfileData.isInstructor"
        >
          <label>
            <strong style="display: block">Request a Lesson (Virtual) </strong>
            Virtual lesson minimum price allowed per half hour
          </label>
          <input
            type="text"
            class="form-control"
            name="virtual_min_price"
            value
            v-model="fields.virtual_min_price"
            placeholder="Min price per 30 minute lesson"
          />
          <span class="help-block" v-if="errors.virtual_min_price">
            <strong>{{ errors.virtual_min_price[0] }}</strong>
          </span>
        </div>

        <!-- <div
          class="form-group w-50 has-feedback"
          :class="{ 'has-error': errors.virtual_min_price }"
          v-if="userProfileData.isInstructor"
        >
          <label>
            <strong style="display: block">Request a Lesson (Virtual) </strong>
            Virtual lesson minimum price allowed per half hour
          </label>
          <input
            type="text"
            class="form-control"
            name="virtual_min_price"
            value
            v-model="fields.virtual_min_price"
            placeholder="Min price per 30 minute lesson"
          />
          <span class="help-block" v-if="errors.virtual_min_price">
            <strong>{{ errors.virtual_min_price[0] }}</strong>
          </span>
        </div> -->

        <div v-if="errorText" class="form-group has-error">{{ errorText }}</div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import TextEditor from "./profile/TextEditor/TextEditor.vue";

import siteAPI from "../mixins/siteAPI.js";
import MaskedInput from "vue-masked-input";
require("jquery.maskedinput/src/jquery.maskedinput");
import DropdownDatepicker from "vue-dropdown-datepicker";
export default {
  components: {
    MaskedInput,
    DropdownDatepicker,
    TextEditor,
  },
  mixins: [siteAPI],
  props: {
    userProfileData: null,
    categorizedGenres: null,
    isStudent: false,
    isAdminForm: false,
  },
  data() {
    return {
      fields: {
        first_name: "",
        last_name: "",
        email: "",
        address: "",
        city: "",
        state: "",
        zip: "",
        dob: "",
        mobile_phone: "",
        genres: [],
        about_me: "",
        gender: "",
        instagram_handle: "",
        lesson_block_min_price: null,
        virtual_min_price: null
      },
      currentYear: null
    };
  },
  methods: {
    async onSubmit() {
      if (moment(this.fields.dob)) this.fields.dob = moment(this.fields.dob).format("YYYY-MM-DD");
      var submitUrl = "/api/user/profile";
      if (this.isAdminForm != null) submitUrl += "/" + this.userProfileData.id;
      await this.apiPut(submitUrl, this.fields);
    },
  },
  created: function () {
    this.currentYear = parseInt(moment().format("YYYY"))

    setTimeout(() => {
      if (this.$refs.datepicker) {
        this.$refs.datepicker.day = Number(
          moment(this.userProfileData.profile.dob).format("DD")
        );
        this.$refs.datepicker.month = Number(
          moment(this.userProfileData.profile.dob).format("MM")
        );
        this.$refs.datepicker.year = Number(
          moment(this.userProfileData.profile.dob).format("YYYY")
        );
      }
    }, 1);

    this.fields = {
      first_name: this.userProfileData.first_name,
      last_name: this.userProfileData.last_name,
      email: this.userProfileData.email,
      address: this.userProfileData.profile.address,
      city: this.userProfileData.profile.city,
      state: this.userProfileData.profile.state,
      zip: this.userProfileData.profile.zip,
      dob: this.userProfileData.profile.dob,
      mobile_phone: this.userProfileData.profile.mobile_phone,
      genres: this.userProfileData.genres,
      about_me: this.userProfileData.profile.about_me,
      gender: this.userProfileData.profile.gender,
      instagram_handle: this.userProfileData.profile.instagram_handle,
      lesson_block_min_price: this.userProfileData.profile
        .lesson_block_min_price,
      virtual_min_price: this.userProfileData.profile.virtual_min_price
    };
    setTimeout(function () {
      window.jQuery(".mask-input").mask("99/99/9999");
    }, 200);
  },
};
</script>