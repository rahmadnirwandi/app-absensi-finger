<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Presensi | RS Permata Hati')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">

    <!-- Styles Bootstrap -->
    <link href="{{ asset('bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebars.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="{{ asset('fonts/fontawesome-free/font_awesome.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/datatables/1.10.11/css/jquery.dataTables.min.css' )}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/3.1.0/css/daterangepicker.css' )}}" />
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{ asset('libs/select2/4.1.0/css/select2.min.css' )}}" rel="stylesheet" />
    @stack('link-end-1')
    <link href="{{ asset('css/my_style.css') }}" rel="stylesheet">
    @stack('link-end-2')
    @stack('custom-style')
</head>

<body>
    <div class="wrapper">
        <div id="loading_black_screen">
            <div id="icon_loading"></div>
        </div>
        <input type='hidden' id='base_url'  value="{{ url('/') }}" />
        @include('layouts.sidebar')
        <div class="content-wrapper px-4 pb-5">
            @include('layouts.header')
            @include('layouts.modal')
            <x-alert></x-alert>
            @yield('content')
        </div>
    </div>


    <script type="text/javascript" src="{{ asset('libs\jquery\latest\jquery.min.js' )}}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('libs\datatables\1.10.11\js\jquery.dataTables.min.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('libs\inputmask\3.3.4\js\jquery.inputmask.bundle.min.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('libs\jquery\latest\moment.min.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('libs\daterangepicker\3.1.0\js\daterangepicker.min.js' )}}"></script>
    <script src="{{ asset('libs\select2\4.1.0\js\select2.min.js' )}}"></script>
    <script src="{{ asset('js/global/global_function.js' )}}"></script>
    @stack('script-end-1')
    <script src="{{ asset('js/globalScript.js') }}"></script>
    @stack('script-end-2')
</body>

</html>