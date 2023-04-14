<template>
  <div>
    <form method="post" @submit.prevent="onSubmit" v-if="(maxInvitesEnabled-countSentInvites)>0">

      <input type="text" class="form-control" required v-model="fields.invited_contact" placeholder="Instructor's Instagram Handle">
      <button type="submit">Invite</button>

      <span class="help-block" v-if="errors.invited_contact">
                <strong>{{ errors.invited_contact[0] }}</strong>
            </span>

      <div v-if="errorText" class="has-error">{{ errorText }}</div>
      <div v-if="formSubmitted" class="has-success">{{ successText }}</div>
    </form>

    <div v-if="(maxInvitesEnabled-countSentInvites)<=0">All {{ maxInvitesEnabled }} available invitations were already sent</div>

  </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js';
import $ from 'jquery'

export default {
  mixins : [siteAPI],
  props : {
    maxInvitesEnabled : Number,
    countInvitationsSent : Number
  },
  data() {
    return {
      formSubmitted: false,
      fields: {
        invited_contact : ''
      },
      countSentInvites : Number
    }
  },
  methods: {
    onSubmit() {
      this.apiPost('/api/invite-instructor', this.fields);
    },
    componentHandlePostResponse(responseData) {
      this.updateCountNotifications();
      this.clearSubmittedForm();
      this.formSubmitted = true;
      setTimeout(() => {
        this.formSubmitted = false;
      },1000)
    },
    updateCountNotifications(){
      this.countSentInvites++;
      var _countSentContainer = $('.count-sent-instructor-invitations:first');
      if (_countSentContainer.length==1){
        var _countSent = parseInt(_countSentContainer.text());
        $('.count-sent-instructor-invitations').text( _countSent + 1 );
      }

      var _countAvailableContainer = $('.count-available-instructor-invitations:first')
      if (_countAvailableContainer.length==1){
        var _countAvailable = parseInt(_countAvailableContainer.text());
        $('count-available-instructor-invitations').text( _countAvailable - 1 );
      }
    }
  },
  created : function(){
    this.countSentInvites = this.countInvitationsSent;
  }
}
</script>
