@if(Auth::user())
<nav class="navbar navbar-default navbar-custom {{ (Route::current()->getName()=='lessons') ? 'header-z-index' : '' }}">
    <div class="container-fluid">
        <div class="row w-100 align-items-center">
            <div class="col-md-2">
                <div class="navbar-header">
                    <a class="navbar-brand" href="{!! url('/') !!}">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                    </a>

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                </div>
            </div>
            <div class="col-md-10">
                <div class="collapse navbar-collapse" id="app-navbar-collapse">

                    <ul class="nav navbar-nav user-menu-wrapper flex-row navbar-right">
                        <!-- User Account Menu -->
                        <li class="user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="user-menu-item">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span>
                                    <span class="header-name">{!! Auth::user()->first_name !!} {!! Auth::user()->last_name !!}</span>
                                </span>
                                <img src="{{ Auth::user()->profile->getImageUrl() }}"
                                     class="img-circle" id="profile-image-menu" alt="User Image" width="40"/>
                            </a>
                            <ul class="drop-menu">
                                @include('include/menu/admin-profile-menu')
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav flex-row navbar-right nav-custom">
                        <li class="{{ Request::is('backend/dashboard') ? 'current-menu' : '' }}">
                            <a href="{!! route('backend.dashboard') !!}">Dashboard</a>
                        </li>

                        <li class="{{ Request::is('backend/lessons*') ? 'current-menu' : '' }}">
                            <a href="{!! route('backend.lessons.index') !!}">Lessons</a>
                        </li>

                        <li class="{{ Request::is('backend/student*') ? 'current-menu' : '' }}">
                            <a href="{{ route('backend.students.index') }}">Clients</a>
                        </li>

                        <li class="{{ Request::is('backend/instructor*') ? 'current-menu' : '' }}">
                            <a href="{{ route('backend.instructors.index') }}">Instructors</a>
                        </li>

                        <li class="{{ Request::is('backend/payments*') ? 'current-menu' : '' }}">
                            <a href="{!! route('backend.payments.index') !!}">Payments</a>
                        </li>

                        <li class="{{ Request::is('backend/reports/*') ? 'current-menu' : '' }}">
                            <a href="{!! route('backend.reports.demographic') !!}">Reports</a>
                        </li>

                        <li class="dropdown {{ (Request::is('backend/page*') || Request::is('backend.genres*') || Request::is('backend/testimonial*') || Request::is('backend/genre-categor*')) ? 'current-menu' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Content</a>
                            <ul class="dropdown-menu">
                                <li class="{{ Request::is('backend/page*') ? 'current-menu' : '' }}">
                                    <a href="{{ route('backend.pages') }}">Pages</a>
                                </li>
                                <li class="{{ Request::is('backend/genre*') ? 'current-menu' : '' }}">
                                    <a href="{!! route('backend.genres.index') !!}">Genres</a>
                                </li>
                                <li class="{{ Request::is('backend/genre-categor*') ? 'current-menu' : '' }}">
                                    <a href="{!! route('backend.genre-categories.index') !!}">Genre Categories</a>
                                </li>
                                <li class="{{ Request::is('backend/testimonial*') ? 'current-menu' : '' }}">
                                    <a href="{{ route('backend.testimonials.index') }}">Testimonials</a>
                                </li>
                                <li class="{{ Request::is('backend/faq*') ? 'current-menu' : '' }}">
                                    <a href="{!! route('backend.faqs.index') !!}">FAQ</a>
                                </li>
                                <li class="{{ Request::is('backend/faq-categor*') ? 'current-menu' : '' }}">
                                    <a href="{!! route('backend.faq-categories.index') !!}">FAQ Categories</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('backend/settings') ? 'current-menu' : '' }}">
                            <a href="{{ route('backend.settings') }}" >Settings</a>
                        </li>

                        {{--<li class="dropdown {{ (Request::is('backend/settings') || Request::is('backend/notification*')) ? 'current-menu' : '' }}">--}}
                            {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>--}}
                            {{--<ul class="dropdown-menu">--}}
                                {{--<li class="{{ Request::is('backend/settings') ? 'current-menu' : '' }}">--}}
                                    {{--<a href="{{ route('backend.settings') }}" >Settings</a>--}}
                                {{--</li>--}}
                                {{--<li class="{{ Request::is('backend/notification*') ? 'current-menu' : '' }}">--}}
                                    {{--<a href="{{ route('backend.notifications.index') }}">Notifications</a>--}}
                                {{--</li>--}}

                            {{--</ul>--}}
                        {{--</li>--}}
                    </ul>

                </div>
            </div>
        </div>
    </div>
</nav>
@endif

@include('flash::message')