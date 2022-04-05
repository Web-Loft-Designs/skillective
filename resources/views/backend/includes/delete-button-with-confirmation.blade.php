<?php
$linkText = isset($linkText) ? $linkText : 'Delete';
$confirmationText = isset($confirmationText) ? $confirmationText : 'Are you sure?';
$onConfirmSelector = isset($onConfirmSelector) ? $onConfirmSelector : '.delete-item-form';
?>
<button class="dropdown-item red btn-confirmation-trigger" value="Delete"
        data-modal-title="{{ $confirmationText }}"
        data-modal-on-approve="{{ $onConfirmSelector }}"
        data-modal-on-cancel="">
    {{ $linkText }}
</button>