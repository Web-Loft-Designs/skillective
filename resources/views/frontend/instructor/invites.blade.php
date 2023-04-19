@extends('layouts.app-frontend')

@section('content')

    <?php
	$pageMeta = $currentPage->getAllMeta();

	$how_this_works_title= isset($pageMeta['how_this_works_title']) ? $pageMeta['how_this_works_title'] : '';
	$how_this_works_blocks= isset($pageMeta['how_this_works_blocks']) ? $pageMeta['how_this_works_blocks'] : [];
    ?>

    <section class="register-banner invites-banner" style="background-image: url({{ asset('images/bg-user-register.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <h1 class="page-title">{{ $currentPage->title }}</h1>
                    {!! $currentPage->content !!}
                    <p><strong>You have a total of <span class="count-sent-instructor-invitations">{{ Auth::user()->getMaxAllowedInstructorInvites() - $countInstructorInvitationsSent }}</span> invitations remaining.</strong></p>
                    {{--count-available-instructor-invitations--}}
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-banner-wrapper">
                        <div class="form-wrapper">
                            <instructor-invitation-form :count-invitations-sent="{{ $countInstructorInvitationsSent }}" :max-invites-enabled="{{ Auth::user()->getMaxAllowedInstructorInvites() }}"></instructor-invitation-form>
                            {{--<div class="form-wrapper-footer">--}}
                                {{--<p>Or share you invite link</p>--}}
                                {{--<div class="copy-input">--}}
                                    {{--<input id="copyTarget" value="https://www.skillective.com/c/username" readonly type="text">--}}
                                    {{--<span id="copyButton">Copy</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if( $how_this_works_title || (is_array($how_this_works_blocks) && count($how_this_works_blocks)>0) )
        <div class="section-step">
            <div class="container">
                <div class="row">
                    @if($how_this_works_title)
                        <div class="col-12">
                            <h2>{{ $how_this_works_title }}</h2>
                        </div>
                    @endif

                    @if(is_array($how_this_works_blocks) && count($how_this_works_blocks)>0)
                        @foreach ($how_this_works_blocks as $index=>$b)
                        <div class="item col-md-4 col-sm-6 col-12">
                            <div>
                                <h3>{{ $b['title'] }}</h3>
                                <p>{{ $b['text'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

@endsection
