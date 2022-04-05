<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>@if (isset($settings['sitename']) && $settings['sitename']!=''){{ $settings['sitename'] }}@else{{ config('app.name', '') }}@endif</title>
    @if (isset($settings['favicon']) && $settings['favicon']!='')
    <link rel="shortcut icon" href="{{$settings['favicon']}}">
    @endif

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/portal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/video-lesson.css') }}" rel="stylesheet">
    @yield('css')


    <style>
        
        html,
        body,
        #app {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            height: 100%;
            width: 100%;
        }

        #chat-page-container {
            width: 100%;
            height: 100%;
        }

        #online-conference-container {
            width: 100%;
            height: 100%;
        }

        #online-conference-container>div {
            width: 100%;
            height: 100%;
        }

        #online-conference-container div#localTrack {
            margin: 0;
        }

        #online-conference-container div.audiowave {
            max-width: 150px;
        }

        .spacing {
            padding: 20px;
            width: 100%;
            border: none;
        }
        
        .err-message {
            color: red;
        }

        @media screen and (max-height: 450px) {
            .sidebar-chat {
                padding-top: 15px;
            }

            .sidebar-chat a {
                font-size: 18px;
            }
        }
    </style>


</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    {{--<!-- jQuery 3.1.1 -->--}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>--}}
    {{--<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>--}}

    <script src="{{ asset('js/js.cookie.js') }}"></script>
    @yield('scripts')
    <script src="{{ asset( 'js/magnific-popup.min.js' ) }}"></script>
    <script src="{{ asset('js/app.js?v=2') }}"></script>
</body>

</html>