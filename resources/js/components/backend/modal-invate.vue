<template>
  <div :style="
    inviteType === 'resend-instructors' ? 'margin-right: 5px' : null
  ">
    <button class='btn-green' @click.pervetn='OpenModal'>
      <img src='/images/add-user.png' alt=''>
      {{textButton }}
    </button>
    <magnific-popup-modal
      @close='clearFormAndClosePopup'
      class='modal-invade'
      :show='false'
      :config='{closeOnBgClick:true,showCloseBtn:true,enableEscapeKey:false}'
      ref='modal'
    >
      <div>
        <h2>{{ textTitle }}</h2>
        <p>We will send him an email invitation, you can enter multiple emails using a comma to separate</p>
        <div class='form-group p-0'>
          <textarea v-model='fields.contacts_to_invite' class='form-control' placeholder='Add email'></textarea>

        </div>

        <div v-if='errorText' class='has-error' style='white-space: nowrap;'>{{ errorText }}</div>
        <div v-if='formSubmitted' v-html='successText' class='has-success' style='white-space: nowrap;'></div>
        <a href='#' class='btn-green' @click.prevent='onSubmit'>Send invite</a>
      </div>
    </magnific-popup-modal>
  </div>
</template>

<script>
import siteAPI from '../../mixins/siteAPI.js'
import skillectiveHelper from '../../mixins/skillectiveHelper.js'
import MagnificPopupModal from '../external/MagnificPopupModal'

export default {
  components: {
    MagnificPopupModal
  },
  props: ['textButton', 'textTitle', 'inviteType'],
  mixins: [siteAPI, skillectiveHelper],
  data() {
    return {
      formSubmitted: false,
      fields: {
        contacts_to_invite: []
      },
      tag: '',
      tags: [],
      validation: [{
        classes: 'in-valid',
        rule: /^([a-z0-9_\+-]+\.+)*[a-z0-9_\+-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/,
        disableAdd: true
      }]
    }
  },
  methods: {
    OpenModal() {
      this.$refs.modal.open()
    },
    onSubmit() {
      this.tags.forEach(item => {
        this.fields.contacts_to_invite.push(item.text)
      })
      this.apiPost('/api/admin/invite-' + this.inviteType, this.fields)
    },
    clearFormAndClosePopup() {
      this.clearSubmittedForm()
      this.successText = null
      this.errorText = null
      this.formSubmitted = false
      this.$refs.modal.close()
    },
    clearSubmittedForm() {
      this.fields.contacts_to_invite = []
      this.tag = ''
      this.tags = []
    },
    componentHandlePostResponse(responseData) {
      this.clearSubmittedForm()
      this.formSubmitted = true
      this.fields.contacts_to_invite = []
    }
  },
  created: function () {

  },
  mounted() {

  }
}
</script>

<style scoped>
.btn-green {
  border: none;
  width: 100% !important;
}
</style>