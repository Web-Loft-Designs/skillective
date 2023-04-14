<template>
  <div id='taxIdReminder' class='modal'>
    <div class='modal-dialog modal-dialog-centered'>
      <div class='modal-content'>
        <div class='modal-header'>
          <button class='close' type='button' @click='closePopup'>&times;</button>
        </div>
        <div class='modal-body px-4 py-4'>
          Beginning January 1, 2022, the Internal Revenue Service (IRS) implemented new reporting requirements for
          payments received for goods and services, which will lowers the reporting threshold to $600 for the 2022 tax
          season. For more information please <a
          href='https://www.irs.gov/businesses/understanding-your-form-1099-k'
          class='learn-more' target='_blank'
        >visit the IRS website</a> to learn more about a 1099-K form.
        </div>
        <a href='/profile/edit#re-enter-bank' class='btn enter-tax'>Enter your Tax ID</a>
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
