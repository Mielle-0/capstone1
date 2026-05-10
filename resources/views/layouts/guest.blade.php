<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Feedback Portal')</title>
    
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .text-maroon { color: maroon; }
        .guest-navbar {
            background-color: #800000; /* Maroon theme to match your app */
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark guest-navbar shadow-sm mb-4">
        <div class="container" style="max-width: 900px;">
            <span class="navbar-brand mb-0 h1 fw-bold">
                <i class="fas fa-bullhorn me-2"></i> Feedback Portal
            </span>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>