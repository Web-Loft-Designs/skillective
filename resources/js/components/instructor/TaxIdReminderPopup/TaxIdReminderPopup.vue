<template>
  <div id='taxInfoPopup' class='modal'>
    <div class='modal-dialog modal-dialog-centered'>
      <div class='modal-content'>
        <div class='modal-header'>
          <h5 class='modal-title'>Why do we need this?</h5>
          <button class='close' data-dismiss='modal' type='button'>&times;</button>
        </div>
        <div class='modal-body px-4 py-4'>
          Beginning January 1, 2022, the Internal Revenue Service (IRS) implemented new reporting requirements for
          payments received for goods and services, which will lowers the reporting threshold to $600 for the 2022 tax
          season. For more information please <a target='_blank' href='https://www.irs.gov/businesses/understanding-your-form-1099-k'>visit the IRS website</a> to learn more about a 1099-K form.
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'TaxIdReminderPopup'
}
</script>