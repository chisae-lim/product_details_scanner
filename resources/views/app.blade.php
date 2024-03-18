<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SchoolMS</title>
    <link href="{{ asset('images/logo.jpg') }}" rel="icon">
    <link href="{{ asset('images/logo.jpg') }}" rel="apple-touch-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed sidebar-collapse">
    <div class="wrapper" id="app">
        
    </div>
</body>

</html>
