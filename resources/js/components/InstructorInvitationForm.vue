<template>
  <div id='invade-form-container'>
    <div v-if='(maxInvitesEnabled-countSentInvites)>0'>
      <h3 class='form-title'>Invite an Instructor</h3>
      <form method='post' @submit.prevent='onSubmit'>
        <div class='d-flex flex-wrap'>
          <div class='form-group  has-feedback' :class="{ 'has-error' : errors.invited_name }">
            <input type='text' class='form-control' required value='' v-model='fields.invited_name' placeholder='Name'>
            <span class='help-block' v-if='errors.invited_name'>
                    <strong>{{ errors.invited_name[0] }}</strong>
                </span>
          </div>
          <div class='form-group  has-feedback' :class="{ 'has-error' : errors.invited_contact }">
            <input
              type='text'
              class='form-control'
              required
              value=''
              v-model='fields.invited_contact'
              placeholder='Email address'
            >
            <span class='help-block' v-if='errors.invited_contact'>
                    <strong>{{ errors.invited_contact[0] }}</strong>
                </span>
          </div>

          <div class='form-group'>
            <button type='submit' class='btn btn-block'>Send Invite</button>
          </div>
        </div>
        <div v-if='errorText' class='has-error'>{{ errorText }}</div>
        <div v-if='formSubmitted' class='has-success'>{{ successText }}</div>
      </form>
    </div>
    <div v-if='(maxInvitesEnabled-countSentInvites)<=0'>All {{ maxInvitesEnabled }} available invitations were already
      sent
    </div>

  </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import $ from 'jquery'

export default {
  mixins: [siteAPI],
  props: {
    maxInvitesEnabled: Number,
    countInvitationsSent: Number
  },
  data() {
    return {
      formSubmitted: false,
      fields: {
        invited_name: '',
        invited_contact: ''
      },
      countSentInvites: Number
    }
  },
  methods: {
    onSubmit() {
      this.apiPost('/api/invite-instructor', this.fields)
    },
    componentHandlePostResponse(responseData) {
      this.updateCountNotifications(responseData)
      this.clearSubmittedForm()
      this.formSubmitted = true
      setTimeout(() => {
        this.formSubmitted = false
      }, 1000)
    },
    updateCountNotifications(responseData) {
      this.countSentInvites++
      var _countSentContainer = $('.count-sent-instructor-invitations:first')
      if (_countSentContainer.length == 1) {
        var _countSent = parseInt(_countSentContainer.text())
        $('.count-sent-instructor-invitations').text(responseData.data)
      }

      var _countAvailableContainer = $('.count-available-instructor-invitations:first')
      if (_countAvailableContainer.length == 1) {
        var _countAvailable = parseInt(_countAvailableContainer.text())
        $('count-available-instructor-invitations').text(_countAvailable - 1)
      }
    }
  },
  created: function () {
    this.countSentInvites = this.countInvitationsSent
  }
}
</script>

<style lang='scss' scoped>
.form-title {
  margin-bottom: 20px;
}
</style>
