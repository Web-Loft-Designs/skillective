<?php
$linkText = isset($linkText) ? $linkText : 'Delete';
$confirmationText = isset($confirmationText) ? $confirmationText : 'Are you sure?';
$onConfirmSelector = isset($onConfirmSelector) ? $onConfirmSelector : '.form-delete-current-page-item';
?>
<a href="#" class="btn btn-deny btn-confirmation-trigger"
   data-modal-title="{{ $confirmationText }}"
   data-modal-on-approve="{{ $onConfirmSelector }}"
   data-modal-on-cancel="">
    {{ $linkText }}
</a>