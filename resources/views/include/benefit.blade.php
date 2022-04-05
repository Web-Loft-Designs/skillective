@if(isset($benefits) && is_array($benefits))
<section class="section-benefit" id="section-benefits">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">{{ $title_block }}</h2>
            </div>
            @foreach ($benefits as $b)
            <div class="item col-lg-4 col-sm-6 col-12">
                <div>
                    <img src="{{ asset('images/leaf.svg') }}" alt="">
                    <h3>{{ $b['title'] }}</h3>
                    <p>{{ $b['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif