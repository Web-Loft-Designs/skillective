<?php
?>
<!-- <section class="contact-us-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="title">{{ $currentPage->title }}</h1>

                {!! $currentPage->content !!}

                <ul>
                    @foreach ($categorizedFaqs as $categoryTitle=>$faqs)
                    <li>{{ $categoryTitle }}
                        <ol>
                            @foreach ($faqs as $faq)
                            <li>
                                <h5>{{ $faq->title }} </h5>
                                @if(isset($faq) && ($file=$faq->getAttachment())!=null)
                                <a href="{{ $file->getUrl() }}" class="@if(preg_match('/^image\//', $file->mime_type)){{ 'uploaded-faq-image' }}@elseif(preg_match('/^video\//', $file->mime_type)){{ 'uploaded-faq-video'  }}@elseif($file->mime_type=='application/pdf'){{ 'uploaded-faq-pdf'  }}@else{{ 'uploaded-faq-file'  }}@endif" @if(!preg_match('/^image\/|^video\//', $file->mime_type)) target="_blank" @endif>{{ $file->file_name }}</a>
                                @endif
                                {!! $faq->content !!}
                            </li>
                            @endforeach
                        </ol>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section> -->


<section class="contact-us-section how--it">
    <h1 class="title">{{ $currentPage->title }}</h1>


    @foreach ($categorizedFaqs as $categoryTitle=>$faqs)

    {{ logger(json_encode($categoryTitle)) }}

    @if($categoryTitle == 'Instructions')
    <div class="instruction--outer">
        <span class="title"> Instructions: </span>
        <div class="instruction-inner">

            @foreach ($faqs as $faq)

            <div class="intruction--item">
                <span class='instruction--longtitle'> {{ $faq->title }} </span>
                @if(isset($faq) && ($file=$faq->getAttachment())!=null)
                <a target='_blank' href="{{ $file->getUrl() }}" class='instruction--content'>
                    <span> Download pdf </span>
                    <img src="/images/publish.svg" alt="">
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif


    @if($categoryTitle == 'Video Tutorials')
    <div class="videos--outer">
        <div class="videos--inner">
            <span class="title">
                 	Video Tutorials:
            </span>
            <div class="videos-content">

                @foreach ($faqs as $faq)

                <iframe width="100%" height="480" src=" {{ $faq->video_url }} " frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <span class="video--title">
                    {{ $faq->title }}
                </span>
                @endforeach

            </div>

        </div>

    </div>

    @endif

    @endforeach




</section>