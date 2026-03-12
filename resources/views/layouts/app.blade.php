<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tom-select.bootstrap5.min.css') }}">
    <style>
        body { padding-top: 56px; }
        .sidebar {
            width: 200px;
            position: fixed;
            top: 56px;
            left: 0;
            height: 100vh;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding-top: 1rem;
        }
        .main-content {
            margin-left: 210px;
            padding: 20px;
        }
        
    </style>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    @include('components.header')
    @include('components.sidebar')

    <main class="main-content">
        @yield('content')
    </main>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/tom-select.complete.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
