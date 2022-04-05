<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@if (isset($page_title)){{ $page_title }} | @elseif(isset($currentPage)){{ $currentPage->title }} | @endif @if (isset($settings['sitename']) && $settings['sitename']!=''){{ $settings['sitename'] }}@else{{ config('app.name', '') }}@endif</title>
    @if (isset($settings['favicon']) && $settings['favicon']!='')
        <link rel="shortcut icon" href="{{$settings['favicon']}}">
    @endif
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <!-- Font Awesome -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">--}}

    <!-- Theme style -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/AdminLTE.min.css">--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/skins/_all-skins.min.css">--}}

    <!-- iCheck -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css">--}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Ionicons -->
    {{--<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">--}}


    {{--<link href="{{ asset('css/front.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/app-front.css') }}" rel="stylesheet">
    <link href="{{ asset('css/magnific-popup.css') }}" rel="stylesheet">
    @yield('css')
    @yield('pageSpecificHeadJS')

    <script type="text/javascript">
        window.lessonTypes = <?php echo json_encode(\App\Models\Lesson::getLessonTypes())?>;
        window.usTimezones = <?php echo json_encode(array_keys(getUsTimezones()))?>;
    </script>
</head>

<body class="skin-blue sidebar-mini back-end">

@include('include/header-backend')
<div id="page-content-wrapper">
    <div class="container-fluid">

        <div class="clearfix"></div>
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="row">
            <div class="w-100" id="app">
                @yield('content')
            </div>
        </div>
    </div>
</div>

@include('include/footer-frontend')

<!-- jQuery 3.1.1 -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>--}}
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!-- AdminLTE App -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>--}}

{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.api_key') }}&libraries=places"></script>

<script src="{{ asset('js/js.cookie.js') }}"></script>
<script src="{{ asset( 'js/magnific-popup.min.js' ) }}"></script>

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.mjs.nestedSortable.js') }}"></script>
<script src="{{ asset('js/backend.js') }}"></script>
@yield('scripts')


</body>
</html>