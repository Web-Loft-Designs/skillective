<div class="static-page-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-12">
                <h1 class="title">{{ $page->title }}</h1>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>