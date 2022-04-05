<?php
$pageMeta = $currentPage->getAllMeta();

$_image_about_us_block_bg= isset($pageMeta['_image_about_us_block_bg']) ? $pageMeta['_image_about_us_block_bg'] : '';
$about_us_block_title= isset($pageMeta['about_us_block_title']) ? $pageMeta['about_us_block_title'] : '';
$about_us_block_text = isset($pageMeta['about_us_block_text']) ? $pageMeta['about_us_block_text'] : '';

$booking_steps_block_title= isset($pageMeta['booking_steps_block_title']) ? $pageMeta['booking_steps_block_title'] : '';
$booking_steps= isset($pageMeta['booking_steps']) ? $pageMeta['booking_steps'] : [];

$_image_our_experience_block_bg= isset($pageMeta['_image_our_experience_block_bg']) ? $pageMeta['_image_our_experience_block_bg'] : '';
$our_experience_block_text = isset($pageMeta['our_experience_block_text']) ? $pageMeta['our_experience_block_text'] : '';

$start_now_block_text= isset($pageMeta['start_now_block_text']) ? $pageMeta['start_now_block_text'] : '';
$_image_start_now_block_bg = isset($pageMeta['_image_start_now_block_bg']) ? $pageMeta['_image_start_now_block_bg'] : '';

?>

@if($about_us_block_title || $about_us_block_text)
<section class="banner-about" >
    <div class="bg" @if($_image_about_us_block_bg) style="background-image: url({{ asset($_image_about_us_block_bg) }});" @endif></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                @if($about_us_block_title)
                    <h1 class="page-title">{{ $about_us_block_title }}</h1>
                @endif
                {!! $about_us_block_text !!}
            </div>
        </div>
    </div>
</section>
@endif

@if( $booking_steps_block_title || (is_array($booking_steps) && count($booking_steps)>0) )
<section class="four-box">
    <div class="container">
        <div class="row">
            @if($booking_steps_block_title)
            <div class="col-12">
                <h2 class="section-title">{{ $booking_steps_block_title }}</h2>
            </div>
            @endif
        </div>
        @if(is_array($booking_steps) && count($booking_steps)>0)
        <div class="row">
            @foreach ($booking_steps as $index=>$b)
                <div class="item col-lg-3 col-md-3 col-sm-6 col-12">
                    <div>
                        <span>{{ $index+1 }}</span>
                        <div class="img"><img src="{{ $b['_image_step'] }}" alt=""></div>
                        <h3>{{ $b['title'] }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

@if($our_experience_block_text)
<section class="two-content">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-12">
                {!! $our_experience_block_text !!}
            </div>
        </div>
    </div>
    <div class="bg" @if($_image_our_experience_block_bg) style="background-image: url({{ asset($_image_our_experience_block_bg) }});" @endif></div>
</section>
@endif

@if($start_now_block_text)
<div class="call-to-action" @if($_image_start_now_block_bg) style="background-image: url({{ asset($_image_start_now_block_bg) }});" @endif>
    <div class="container">
        <div class="row">
            <div class="col-12">
                {!! $start_now_block_text !!}
            </div>
        </div>
    </div>
</div>
@endif