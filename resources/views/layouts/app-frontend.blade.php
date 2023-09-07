<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>
        @if (isset($page_title)){{ $page_title }} | @elseif(isset($currentPage)){{ $currentPage->title }} | @endif @if (isset($settings['sitename']) && $settings['sitename'] != ''){{ $settings['sitename'] }}@else{{ config('app.name', '') }}@endif

    </title>
    @if (isset($settings['favicon']) && $settings['favicon'] != '')
        <link rel="shortcut icon" href="{{ $settings['favicon'] }}">
    @endif

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> --}}

    <!-- Theme style -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/AdminLTE.min.css"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/skins/_all-skins.min.css"> --}}

    <!-- iCheck -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css"> --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Ionicons -->
    {{-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css"> --}}




    <!-- Hotjar Tracking Code for https://skillective.com/ -->
    <script>
        (function(h, o, t, j, a, r) {
            h.hj = h.hj || function() {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {
                hjid: 2702387,
                hjsv: 6
            };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TV7TNV2');
    </script>
    <!-- End Google Tag Manager -->

    {{-- <link href="{{ asset('css/front.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/app-front.css') }}" rel="stylesheet">
    <link href="{{ asset('css/magnific-popup.css') }}" rel="stylesheet">
    @yield('css')
    @yield('pageSpecificHeadJS')
    <script type="text/javascript">
        window.lessonTypes = <?php echo json_encode(\App\Models\Lesson::getLessonTypes()); ?>;
        window.usTimezones = <?php echo json_encode(array_keys(getUsTimezones())); ?>;
    </script>
</head>

<body class="skin-blue sidebar-mini @if (isset($currentPage)){{ 'page-' . $currentPage->id }}@endif " @if ($currentPage?->id == 8 || $currentPage?->id == 15 || $currentPage?->id == 6 || $currentPage?->id == 14 || $currentPage?->id == 9) style="margin-top: 70px;" @endif  >

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TV7TNV2" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="page-content-wrapper">
        <div class="container-fluid">

            <div class="clearfix"></div>
            @include('flash::message')

            @include('frontend/partials/merchant-status')

            <div class="clearfix"></div>

            <div class="row">
                <div class="w-100" id="app">
                    @include('include/header-frontend')

                    @yield('content')
                    @if (!Auth::user())
                        @include('include/login-popup')
                    @endif

                    @if ( (Auth::user() && $loggedUserRole == \App\Models\User::ROLE_INSTRUCTOR && \Request::is('instructor/*'))
                            || (Auth::user() && $loggedUserRole == \App\Models\User::ROLE_INSTRUCTOR  && \Request::is('profile')))


                        <profile-lesson-form v-bind:user-genres="{{ json_encode($userGenres) }}"
                            v-bind:site-genres="{{ json_encode($siteGenres) }}"
                            :instructor-id="{{ Auth::user()->id }}" ref="addModal">
                        </profile-lesson-form>


                    @endif


                    @if (!Auth::user() && (Cookie::get('instructorRegistered') !== null))
                        <magnific-popup-modal-success class="panding-popup" v-bind:show="false"
                            v-bind:config="{closeOnBgClick:true,showCloseBtn:true,enableEscapeKey:false}" ref="modalSuccess">
                            <panding-popup
                                v-bind:modal-window="modalSuccess"
                                registration-confirmation-text='{{ (isset($settings["instructor_registration_confirmation_text"]) && $settings["instructor_registration_confirmation_text"]) ? $settings["instructor_registration_confirmation_text"] : "Your request is being reviewed by Skillective. You will be notified via email to complete profile once request approved." }}'
                            ></panding-popup>
                        </magnific-popup-modal-success>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('include/footer-frontend')
    @include('include/modal-success')

    <script src="https://twemoji.maxcdn.com/v/latest/twemoji.min.js" crossorigin="anonymous"></script>


    <!-- START: 69506232 pixels, are aggregated in consenTag container -->
    <script src="https://consentag.eu/public/3.0.1/consenTag.js"></script>
    <script type="text/javascript">
        consenTag.init({
            containerId: "69487421",
            silentMode: true
        }, true);
    </script>
    <!-- END: 69506232 pixels, are aggregated in consenTag container -->

    <!-- jQuery 3.1.1 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <!-- AdminLTE App -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.api_key') }}&libraries=places">
    </script>

    @yield('scripts')

    <script src="{{ asset('js/js.cookie.js') }}"></script>
    <script src="{{ asset('js/front.js') }}"></script>
    <script src="{{ asset('js/app.js?v=2') }}"></script>
    <script src="{{ asset('js/magnific-popup.min.js') }}"></script>

    @if (isset($settings['ga_tracking_code']) && $settings['ga_tracking_code'] != '')
        {!! $settings['ga_tracking_code'] !!}
    @endif
</body>

</html>
