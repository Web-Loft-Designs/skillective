@if (session('message'))
  <div class="alert alert-{{ Session::get('status') }} status-box alert-dismissable fade show" role="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;<span class="sr-only">Close</span></a>
    {{ session('message') }}
  </div>
@endif

@if (session('success'))
  <div class="alert alert-success alert-dismissable fade show" role="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <h4><i class="icon fa fa-check fa-fw" aria-hidden="true"></i> Success</h4>
    {{ session('success') }}
  </div>
@endif

@if(session()->has('status'))
    @if(session()->get('status') == 'wrong')
        <div class="alert alert-danger status-box alert-dismissable fade show" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;<span class="sr-only">Close</span></a>
            {{ session('message') }}
        </div>
    @endif
@endif

@if (session('error') || isset($error))
  <div class="alert alert-danger alert-dismissable fade show" role="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <h4>
      <i class="icon fa fa-warning fa-fw" aria-hidden="true"></i>
      Error
    </h4>
    @if (session('error'))
        {{ session('error') }}
    @endif
      @if (isset($error))
          {{ $error }}
      @endif
  </div>
@endif

@if (count($errors) > 0)
  <div class="alert alert-danger alert-dismissable fade show" role="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <h4>
      <i class="icon fa fa-warning fa-fw" aria-hidden="true"></i>
      <strong>Something Wrong</strong>
    </h4>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
