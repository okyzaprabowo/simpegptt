<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="default-style layout-fixed layout-navbar-fixed">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('AppConfig.system.title') }}</title>

    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    @if(config('AppConfig.system.web_admin.assets_template.font.fontawesome'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/fontawesome.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.ionicons'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/ionicons.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.linearicons'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/linearicons.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.open-iconic'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/open-iconic.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.pe-icon-7-stroke'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/pe-icon-7-stroke.css') }}">
    @endif
    
    <link href="{{ asset('/dist/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/appwork.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/theme-app.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/colors.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/uikit.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/style.css') }}" rel="stylesheet">
    
    @if(config('AppConfig.system.web_admin.assets_link'))
    @foreach (config('AppConfig.system.web_admin.assets_link') as $value)
    <link rel="stylesheet" href="{{ asset($value) }}">
    @endforeach
    @endif

</head>
<body>

    <!-- Splash screen -->
    <div class="app-splash-screen" style="background: #fff; position: fixed; z-index: 99999999; top: 0; right: 0; bottom: 0; left: 0; opacity: 1; -webkit-transition: opacity .3s; transition: opacity .3s;">
      <div class="app-splash-screen-content" style="position: absolute; top: 50%; left: 50%; -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
        <span class="text-large font-weight-bolder">{{ config('AppConfig.system.title') }}</span>
      </div>
    </div>
    <!-- / Splash screen -->

    <div id="app"></div>

    @if(config('AppConfig.system.web_admin.assets_js'))
    @foreach (config('AppConfig.system.web_admin.assets_js') as $value)
    <script src="{{ asset($value) }}"></script>
    @endforeach
    @endif

    <!-- Layout helpers -->
    <script src="{{ asset('/dist/vendor/js/layout-helpers.js') }}"></script>
    <script src="{{ asset('/dist/app.js') }}"></script>

</body>
</html>
