@extends('layouts.app-frontend')

@section('content')

	<?php
	$pageMeta = (isset($currentPage) && $currentPage instanceof App\Models\Page) ? $currentPage->getAllMeta() : [];

	$_image_form_block_bg= isset($pageMeta['_image_form_block_bg']) ? $pageMeta['_image_form_block_bg'] : '';
	$form_block_welcome= isset($pageMeta['form_block_welcome']) ? $pageMeta['form_block_welcome'] : '';
	$form_block_benefits = isset($pageMeta['form_benefits']) ? $pageMeta['form_benefits'] : '';

	$_image_testimonial_block_bg= isset($pageMeta['_image_testimonial_block_bg']) ? $pageMeta['_image_testimonial_block_bg'] : '';
	$testimonial_block_name= isset($pageMeta['testimonial_block_name']) ? $pageMeta['testimonial_block_name'] : '';
	$testimonial_block_position= isset($pageMeta['testimonial_block_position']) ? $pageMeta['testimonial_block_position'] : '';
	$testimonial_block_text= isset($pageMeta['testimonial_block_text']) ? $pageMeta['testimonial_block_text'] : '';

	$benefits               = isset($pageMeta['benefits']) ? $pageMeta['benefits'] : [];
	?>

  <section class="register-banner" @if($_image_form_block_bg) style="background-image: url({{ asset($_image_form_block_bg) }});" @endif>
     <div class="container">
         <div class="row">
             <div class="col-md-6 col-12">
                 @if($form_block_welcome)
                     <h1 class="page-title">{{ $form_block_welcome }}</h1>
                 @endif
                     @if(is_array($form_block_benefits) && count($form_block_benefits)>0)
                         <ul>
                             @foreach ($form_block_benefits as $index=>$b)
                                 <li><img src="{{ $b['_image_benefit'] }}" alt="">{!! $b['title'] !!}</li>
                             @endforeach
                         </ul>
                     @endif
                 @if (is_array($benefits) && count($benefits))
                    <a href="#section-benefits" class="link-underline">More benefits</a>
                 @endif
             </div>
             <div class="col-md-6 col-12">
                 <div class="register-banner-wrapper">
                     <div class="form-wrapper">
                         <instructor-registration-form
                                 :us-states="{{  json_encode($usStates) }}"
                                 v-bind:categorized-genres="{{  json_encode($categorizedGenres) }}"
                                 :site-genres="{{  json_encode($siteGenres) }}"
                                 :initial-form-data="{{  json_encode($initialFormData) }}"
                         ></instructor-registration-form>
                         {{--v-bind:initial-form-data="{{ json_encode($initialFormData) }}"--}}
                     </div>
                 </div>
             </div>
         </div>
     </div>
  </section>

@if (is_array($featuredGenres) && count($featuredGenres)>0)
  <section class="slider-genres">
      <div class="container">
          <h2 class="section-title">Popular Genres</h2>
          <slick class="slider" :options="{
              slidesToShow: 7,
              slidesToScroll: 1,
              responsive: [
                  {
                    breakpoint: 1024,
                    settings: {
                      slidesToShow: 5,
                      slidesToScroll: 1,
                    }
                  },
                  {
                    breakpoint: 767,
                    settings: {
                      slidesToShow: 4,
                      slidesToScroll: 1
                    }
                  },
                  {
                    breakpoint: 600,
                    settings: {
                      slidesToShow: 3,
                      slidesToScroll: 1
                    }
                  },
                  {
                    breakpoint: 450,
                    settings: {
                      slidesToShow: 2,
                      slidesToScroll: 1
                    }
                  },
                  {
                    breakpoint: 360,
                    settings: {
                      slidesToShow: 1,
                      slidesToScroll: 1
                    }
                  }
              ]}"
          >
                @foreach($featuredGenres as $genre)
                    <div class="item">
                        <a href="/lessons?genre={{ $genre['id'] }}" class="wrap" style="background-image: url('{{ $genre['image'] }}')">
                           <span>{{ $genre['title'] }}</span>
                        </a>
                    </div>
                @endforeach
           </slick>
      </div>
  </section>
@endif

@if($testimonial_block_text)
  <section class="single-testimonial" @if($_image_testimonial_block_bg) style="background-image: url({{ asset($_image_testimonial_block_bg) }});" @endif>
      <div class="container">
          <div class="row">
              <div class="col-lg-6 col-12">
                  <div class="testimonial-item">
                      <p>{!! $testimonial_block_text !!}</p>
                      @if($testimonial_block_name)
                          <span class="name">{{ $testimonial_block_name }}</span>
                      @endif
                      @if($testimonial_block_position)
                          <span class="position">{{ $testimonial_block_position }}</span>
                      @endif
                  </div>
              </div>
          </div>
      </div>
  </section>
@endif

    @include('include/benefit', ['title_block' => 'More benefits', 'benefits'=>$benefits])
@endsection