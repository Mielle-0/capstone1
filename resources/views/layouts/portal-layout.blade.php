<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<body class="bg-light">

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-header px-5">
    <div class="container-fluid ml-10vw d-block">
        <img src="{{ asset('images/um_logo.webp') }}" alt="Logo" height="40" class="me-1">
        <a class="navbar-brand" href="#"> | @yield('title')</a>
    </div>
</nav>

@yield('content')

</body>