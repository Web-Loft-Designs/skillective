@extends('layouts.app-frontend')
<?php
$isBookableNowByCurrentUser = $lesson->isBookableNowByCurrentUser();
?>
@section('content')
<div class="lesson-page">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-lg-5 col-md-6 col-12 lesson-info-block">
                <div class="content-block">
                    <ul>
                        <li>
                            <h3 class="d-flex align-items-center">
                                {{ $lesson->genre->title }}
                                    <span class="private-title ">

                                        @if($lesson->spots_count == 1)
                                            <span><img src="../../images/man-user.svg" alt=""></span>
                                        @elseif($lesson->spots_count > 1)
                                            <span><img src="../../images/multiple-users-silhouette.svg" alt=""></span>
                                        @endif
                                    </span>
                            </h3>
                            <p>{{ $lesson->start->format('M d, h:i A') }} - {{ $lesson->end->format('h:i A') }}</p>
                        </li>
                        <li><p><strong>${{ $lesson->spot_price }}</strong> per lesson</p></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7 col-md-6 col-12 lesson-right register-banner-wrapper">
                @if($isBookableNowByCurrentUser)
                    Book using <div id="venmo-button" class="btn btn-block" style="background: rgb(61, 149, 206) url('/images/venmo_logo_white.png') repeat scroll 0% 0%;display: none;background-repeat: no-repeat;background-size: 110px 21px;background-position:center;"></div>
                @else
                    <p>You are not allowed to book this lesson</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@if( $isBookableNowByCurrentUser )
@section('scripts')
    @include('frontend.partials.payment-processing-scripts')
<script>
    var orderData = {
			first_name : '{{ $user["first_name"] }}',
			last_name : '{{ $user["last_name"] }}',
			instagram_handle : '{{ $user["profile"]["instagram_handle"] }}',
			email : '{{ $user["email"] }}',
			address : '{{ $user["profile"]["address"] }}',
			city : '{{ $user["profile"]["city"] }}',
			state : '{{ $user["profile"]["state"] }}',
			zip : '{{ $user["profile"]["zip"] }}',
			dob : '1990-01-01',
			gender : '{{ $user["profile"]["gender"] }}',
			mobile_phone : '{{ $user["profile"]["mobile_phone"] }}',
			genres : [3,4],
			special_request : '',
			accept_terms : 1,
			payment_method_token : null,
			payment_method_nonce : null,
			device_data : ''
		}

    $(document).ready(function(){
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		initVenmo();
    });

	function collectDeviceDataForBraintree(clientInstance, forPayPal){
		braintree.dataCollector.create({
			client: clientInstance,
			paypal: forPayPal
		}, function (err, dataCollectorInstance) {
			if (err) {
				alert(err.message);
				dataCollectorInstance.teardown();
				return;
			}
			// At this point, you should access the dataCollectorInstance.deviceData value and provide it
			// to your server, e.g. by injecting it into your form as a hidden input.
			orderData.device_data = dataCollectorInstance.deviceData;
			dataCollectorInstance.teardown();
		});
	}

    function initVenmo(){
		braintree.client.create({
			authorization: '{{ $clientToken }}'
		}, function (clientErr, clientInstance) {
			if (clientErr) {
				alert(clientErr.message);
				return;
			}

            var forPayPal = true;
            collectDeviceDataForBraintree(clientInstance, forPayPal);

            braintree.venmo.create({
                client: clientInstance,
                allowNewBrowserTab: true
            }, function (venmoErr, venmoInstance) {
                // Stop if there was a problem creating Venmo. This could happen if there was a network error or if it's incorrectly configured.
                if (venmoErr) {
                    alert(venmoErr.message);
                    return;
                }

                // Verify browser support before proceeding.
                if (!venmoInstance.isBrowserSupported()) {
                    alert('Browser not supported')
                    return;
                }

                var venmoButton = document.getElementById('venmo-button');
                venmoButton.style.display = 'block'; // Assumes that venmoButton is initially display: none.
                venmoButton.addEventListener('click', function () {
                venmoButton.disabled = true;

                    venmoInstance.tokenize(function (tokenizeErr, payload) {
                        venmoButton.removeAttribute('disabled');
                        if (tokenizeErr) {
                            if (tokenizeErr.code === 'VENMO_CANCELED') {
                                alert('App is not available or user aborted payment flow');
                            } else if (tokenizeErr.code === 'VENMO_APP_CANCELED') {
                                alert('User canceled payment flow');
                            } else {
                                alert('An error occurred:'+ tokenizeErr.message);
                            }
                        } else {
                            // Send payload.nonce to your server.
                            // Display the Venmo username in your checkout UI.
                            if ( payload.nonce != undefined ) {
                                orderData.payment_method_nonce = payload.nonce;
                                book();
                            } else {
                                alert('Can\'t process your data.');
                            }
                        }
                    });
                });

                // Check if tokenization results already exist. This occurs when your checkout page is relaunched in a new tab. This step can be omitted if allowNewBrowserTab is false.
                if (venmoInstance.hasTokenizationResult()) {

                    venmoInstance.tokenize(function (tokenizeErr, payload) {
                        if (tokenizeErr) {
                            if (tokenizeErr.code === 'VENMO_CANCELED') {
                                alert('App is not available or user aborted payment flow');
                            } else if (tokenizeErr.code === 'VENMO_APP_CANCELED') {
                                alert('User canceled payment flow');
                            } else {
                                alert('An error occurred:'+ tokenizeErr.message);
                            }
                        } else {
                            // Send payload.nonce to your server.
                            // Display the Venmo username in your checkout UI.
                            if ( payload.nonce != undefined ) {
                                orderData.payment_method_nonce = payload.nonce;
                                book();
                            } else {
                                alert('Can\'t process your data.');
                            }
                        }
                    });
                    return;
                }
            });
		});
    }

    function book(){
		$.ajax({
			url : '/api/lesson/{{ $lesson->id }}/book',
			type: "POST",
			data : orderData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success:function(response, textStatus, jqXHR){
				alert('booking success')
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert('booking error ' + jqXHR.responseText)
			}
		});
    }
</script>
@endsection
@endif