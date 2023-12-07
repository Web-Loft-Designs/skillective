<template>
  <div id='taxIdReminder' class='modal'>
    <div class='modal-dialog modal-dialog-centered'>
      <div class='modal-content'>
        <div class='modal-header'>
          <button class='close' type='button' @click='closePopup'>&times;</button>
        </div>
        <div class='modal-body px-4 py-4'>
            Great news! Skillective has partnered with PayPal to get you easily paid for your work with clients.<br> Please go to your settings and connect your PayPal business account.
        </div>
        <a href='/profile/edit#paypal-account-trigger' class='btn enter-tax'>Settings page</a>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'TaxIdReminderPopup',
  mounted() {
    $('#taxIdReminder').modal('show')
  },
  methods: {
    closePopup() {
      $('#taxIdReminder').modal('hide')
    }
  }
}
</script>

<style scoped>
.btn.enter-tax {
  background-image: linear-gradient(180deg, #11cf13, #01bd00);
  color: #fff;
  max-width: fit-content;
  margin-left: auto;
  margin-right: auto;
}
.learn-more {
  color: #8ada00;
}
</style>
