<?php
$pageMeta = $currentPage->getAllMeta();
$form_sent_confirmation= isset($pageMeta['form_sent_confirmation']) ? $pageMeta['form_sent_confirmation'] : '';

$formData = [];
if ( ($user = Auth::user())!=null ){
	$formData = [
		'first_name'    => $user->first_name,
		'last_name'     => $user->last_name,
        'email'         => $user->email,
		'mobile_phone'  => $user->profile->mobile_phone,
		'address'       => $user->profile->getFullAddress()
    ];
}
?>
  <section class="contact-us-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="title">{{ $currentPage->title }}</h1>

                {!! $currentPage->content !!}

                <contact-us-form v-bind:success-text="'{{ addslashes($form_sent_confirmation) }}'" v-bind:current-user-data="{{ json_encode($formData) }}"></contact-us-form>
            </div>
        </div>
    </div>
  </section>