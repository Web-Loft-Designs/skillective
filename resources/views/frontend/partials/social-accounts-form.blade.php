<?php
$currentUser = Auth::user();

$instagramAccount = null;
$facebookAccount = null;
$googleAccount = null;
if (isset($currentUser->social)){
    foreach ($currentUser->social as $social){
        if ($social->provider=='ig')
			$instagramAccount = $social;
		if ($social->provider=='facebook')
			$facebookAccount = $social;
		if ($social->provider=='google')
			$googleAccount = $social;
    }
}
?>
@if (!$currentUser->hasFakeEmail())
    {{--<p class="login-box-msg mb-1">Instagram account</p>--}}
    {{--@if ($instagramAccount)--}}
        {{--<div class="form-group">--}}
            {{--<form method="post" action="{{ route('social.detach',['provider' => 'ig']) }}" id="detach-instagram">--}}
                {{--@csrf--}}
                {{--<input class="btn btn-primary btn-social btn-blue btn-detach btn-instagram detach-instagram" type="submit" value="Disconnect from Instagram">--}}
            {{--</form>--}}
        {{--</div>--}}
    {{--@else--}}
        {{--<div  class="form-group">--}}
            {{--<a class="btn btn-primary btn-social btn-blue btn-attach btn-instagram" href="{{ route('social.redirect',['provider' => 'ig']) }}">Connect to Instagram</a>--}}
        {{--</div>--}}
    {{--@endif--}}

    <p class="login-box-msg mb-1">Enable login through Facebook and Google</p>
    @if ($facebookAccount)
        <div class="form-group">
            <form method="post" action="{{ route('social.detach',['provider' => 'facebook']) }}" id="detach-facebook">
                @csrf
                <input class="btn btn-primary btn-social btn-blue btn-detach btn-facebook detach-facebook" type="submit" value="Disconnect from Facebook">
            </form>
        </div>
    @else
        <div  class="form-group">
            <a class="btn btn-primary btn-social btn-blue btn-attach btn-facebook" href="{{ route('social.redirect',['provider' => 'facebook']) }}">Connect to Facebook</a>
        </div>
    @endif

    @if ($googleAccount)
        <div class="form-group">
            <form method="post" action="{{ route('social.detach',['provider' => 'google']) }}" id="detach-google">
                @csrf
                <input class="btn btn-primary btn-social btn-blue btn-detach btn-google detach-google" type="submit" value="Disconnect from Google">
            </form>
        </div>
    @else
        <div  class="form-group">
            <a class="btn btn-primary btn-social btn-blue btn-attach btn-google" href="{{ route('social.redirect',['provider' => 'google']) }}">Connect to Google</a>
        </div>
    @endif

@endif
