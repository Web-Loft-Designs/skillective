@extends('layouts.app-frontend')
<?php
$isBookableNowByCurrentUser = $lesson->isBookableNowByCurrentUser();
?>
@section('content')
<div class="lesson-page">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-lg-5 col-md-6 col-12 lesson-info-block">
                <div class="avatar-block">
                    <div>
                        <div class="avatar-img" style="background-image: url('{{ $lesson->instructor->profile->getImageUrl() }}')"></div>
                        @if ($lesson->instructor->profile->instagram_handle)
                        <h3><a href="https://instagram.com/{{ $lesson->instructor->profile->instagram_handle }}" target="_blank">{{ '@' . $lesson->instructor->profile->instagram_handle }}</a></h3>
                        @endif
                        <p><a href="{{ route('profile', $lesson->instructor->id) }}">{{ $lesson->instructor->getName() }}</a></p>
                    </div>
                </div>
                <div class="content-block">
                    <ul>
                        <li>
                            <h3 class="d-flex align-items-center">
                                @if ($lesson->lesson_type=='virtual')
                                <img src='../../images/private-lesson-green.svg'>
                                @endif
                                @if ($lesson->lesson_type !=='virtual')
                                    <img src='../../images/in-person-green.svg'>
                                @endif
                                {{ $lesson->genre->title }}
                                <span class="private-title ">
                                    @if($lesson->spots_count == 1)
                                    <span><img src="../../images/man-user.svg" alt=""></span>
                                    @elseif($lesson->spots_count > 1)
                                    <span><img src="../../images/multiple-users-silhouette.svg" alt=""></span>
                                    @endif
                                </span>
                            </h3>
                            <p>{{ $lesson->start->format('M d, h:i A') }} - {{ $lesson->end->format('h:i A') }} ({{ $lesson->timezone_id }})</p>
                        </li>
                        <location-modal :lesson-details="{{ $lesson }}" :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}"></location-modal>
                        {{--<li class="location-li" @click="openLocationModal()"><p>{!! $lesson->location !!}</p></li>--}}
                        @if ($lesson->description)
                        <li>Note: <p>{{ $lesson->description }}</p>
                        </li>
                        @endif
                        <li>
                            <p><strong>${{ $lesson->spot_price }}</strong> Per Lesson</p>
                        </li>
                        <?php
                        $b = new \App\Models\Booking();
                        $totalFee = $b->getBookingTotalFeeAmount($lesson, $lesson->spot_price);
                        ?>
                        <li><p><strong>${{ $totalFee }}</strong> Skillective Fee </p></li>
                        <li><p><strong>${{ $lesson->spot_price + $totalFee }}</strong> Total</p></li>
                    </ul>
                </div>



                <div class="map">
                    @if($lesson->lat !== null || $lesson->lng !== null)
                    <google-map-single :current-user-can-book="{{ $currentUserCanBook?'true':'false' }}" :dataid="'name'" :center="{
                                    lat: {{ $lesson->lat  }},
                                    lng: {{ $lesson->lng  }}
                                }" :marker="[{
                                position: {
                                    latitude: {{ $lesson->lat  }},
                                    longitude: {{ $lesson->lng }}

                                },
                                 content: {{ $lesson  }},
                                 id:  {{ $lesson->id  }}
                            }]"></google-map-single>
                    @endif
                </div>
            </div>
            <div class="col-lg-7 col-md-6 col-12 lesson-right register-banner-wrapper">
                @if ( $lesson->private_for_student_id && (!Auth::user() || ($lesson->private_for_student_id!=Auth::id() && $lesson->instructor_id!=Auth::id() ) ) )
                <p>This is a private lesson. @if(!Auth::user())You must <a href="/login">login</a> to book it.@endif</p>
                @elseif($isBookableNowByCurrentUser)
                <lesson-booking-form :lesson-id="{{ $lesson->id }}" :us-states="{{  json_encode($usStates) }}" :user="{{ json_encode($user) }}" :user-payment-methods="{{ json_encode($userPaymentMethods) }}" :client-token="'{{ $clientToken }}'" :payment-environment="'{{ $paymentEnvironment }}'" :categorized-genres="{{  json_encode($categorizedGenres) }}" :site-genres="{{  json_encode($siteGenres) }}" :confirmation-text="'{{ str_replace(["\r", "\n"], '', $settings['booking_confirmation_text']) }}'"></lesson-booking-form>
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
@endsection
@endif